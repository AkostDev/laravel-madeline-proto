<?php

namespace AkostDev\MadelineProto;

use AkostDev\MadelineProto\Commands\MultiSessionCommand;
use AkostDev\MadelineProto\Commands\TelegramAccountLoginCommand;
use AkostDev\MadelineProto\Factories\MadelineProtoFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MadelineProtoServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('madeline-proto-factory', function (Application $app) {
            return new MadelineProtoFactory($app->make('db'), config('telegram.sessions.multiple.table'));
        });
        $this->app->alias('madeline-proto-factory', MadelineProtoFactory::class);

        //Only for single Telegram session.

        $this->app->singleton('madeline-proto', function (Application $app) {
            $sessionFactory = $app->make('madeline-proto-factory');

            return $sessionFactory->make(config('telegram.sessions.single.session_file'), config('telegram.settings'));
        });
        $this->app->alias('madeline-proto', MadelineProto::class);

        $this->app->singleton('madeline-proto-messages', function (Application $app) {
            $sessionFactory = $app->make('madeline-proto-factory');

            return $sessionFactory->make(
                config('telegram.sessions.single.session_file'),
                config('telegram.settings')
            )->messages();
        });
        $this->app->alias('madeline-proto-messages', ClientMessages::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();

            $this->generateTelegramSessionFolder();

            $this->publishes([
                __DIR__ . '/../config/telegram.php' => config_path('telegram.php')
            ]);
        }
    }

    /**
     * Register laravel madeline proton login prompt command.
     *
     * @return void
     */
    public function registerCommands(): void
    {
        $this->commands([
            TelegramAccountLoginCommand::class,
            MultiSessionCommand::class
        ]);
    }

    /**
     * Create telegram session folder at storage path.
     *
     * @return void
     */
    public function generateTelegramSessionFolder(): void
    {
        if (!file_exists(storage_path("app/telegram/"))) {
            mkdir(storage_path("app/telegram"), 0755);
        }
    }

    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            'madeline-proto',
            'madeline-proto-messages',
            'madeline-proto-factory'
        ];
    }
}
