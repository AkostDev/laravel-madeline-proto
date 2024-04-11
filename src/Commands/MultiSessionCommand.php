<?php

namespace AkostDev\LaravelMadelineProto\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MultiSessionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'madeline-proto:multi-session
                    { --model : Including the mp_telegram_session model }
                    { --force : Overwrite the existing migration file }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishing the multi session telegram migration table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->option('model')) {
            $user = $this->ask('Telegram user model (for relation)', 'App/User');

            if (file_exists(app_path("TelegramSession.php")) && !$this->option('force')) {
                if (!$this->confirm("The App/TelegramSession model is already exist. Replace it?")) {
                    $this->info('Multi session export aborted.');
                    return;
                }
            }

            $this->exportModel($user);

            $this->info('TelegramSession model generated.');
        }

        $tableName = config('telegram.sessions.multiple.table');
        $migration = "2024_04_10_000000_create_{$tableName}_table.php";

        if (file_exists(database_path("migrations/$migration")) && !$this->option('force')) {
            if (!$this->confirm("The {$migration} migration file is already exist. Replace it?")) {
                $this->info('Multi session export aborted.');
                return;
            }
        }

        $this->exportMigration($tableName, $user ?? null);

        $this->info('Migration file generated.');
    }

    /**
     * Export the telegram_session migration file.
     *
     * @param string $tableName
     * @param string|null $relation
     */
    public function exportMigration(string $tableName, string $relation = null): void
    {
        if (is_null($relation)) {
            $relation = 'App/User';
        }

        file_put_contents(
            database_path("migrations/2024_04_10_000000_create_{$tableName}_table.php"),
            $this->compileMigrationStub($tableName, $relation)
        );
    }

    /**
     * Export the TelegramSession model file.
     *
     * @param string $relation
     */
    public function exportModel(string $relation): void
    {
        file_put_contents(
            app_path('TelegramSession.php'),
            $this->compileModelStub($relation)
        );
    }

    /**
     * Compile the TelegramSession stub.
     *
     * @param string $user
     * @return string
     */
    public function compileModelStub(string $user): string
    {
        $stub = file_get_contents(__DIR__ . '/stubs/mp_telegram_session.stub');

        return str_replace(
            ['{{user}}', '{{package}}'],
            [Str::snake(class_basename($user)), 'App'],
            $stub
        );
    }

    /**
     * Compile the TelegramSession migration stub.
     *
     * @param string $tableName
     * @param string $user
     * @return string
     */
    public function compileMigrationStub(string $tableName, string $user): string
    {
        return str_replace(
            ['{{table}}', '{{user}}'],
            [$tableName, Str::snake(class_basename($user))],
            file_get_contents(__DIR__ . '/stubs/migration.stub')
        );
    }
}
