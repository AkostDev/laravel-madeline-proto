<?php

namespace AkostDev\LaravelMadelineProto;

use AkostDev\LaravelMadelineProto\Commands\MultiSessionCommand;
use AkostDev\LaravelMadelineProto\Commands\TelegramAccountLoginCommand;
use AkostDev\LaravelMadelineProto\Factories\MadelineProtoFactory;
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
            return new MadelineProtoFactory($app->make('db'), config('madeline-proto.sessions.multiple.table'));
        });
        $this->app->alias('madeline-proto-factory', MadelineProtoFactory::class);

        //Only for single Telegram session.

        $this->app->singleton('madeline-proto', function (Application $app) {
            $sessionFactory = $app->make('madeline-proto-factory');

            return $sessionFactory->make(config('madeline-proto.sessions.single.session_file'), config('madeline-proto.settings'));
        });
        $this->app->alias('madeline-proto', MadelineProto::class);

        $this->app->singleton('madeline-proto-messages', function (Application $app) {
            $sessionFactory = $app->make('madeline-proto-factory');

            return $sessionFactory->make(
                config('madeline-proto.sessions.single.session_file'),
                config('madeline-proto.settings')
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
                __DIR__ . '/../config/madeline-proto.php' => config_path('madeline-proto.php')
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
        if (!file_exists(storage_path("app/madeline-proto/"))) {
            mkdir(storage_path("app/madeline-proto"), 0755);
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
