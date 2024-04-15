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
        $foreignModel = config('madeline-proto.sessions.multiple.foreign_model', '\App\Models\User');

        if ($this->option('model')) {
            if (file_exists(app_path('Models/MadelineProtoSession.php')) && ! $this->option('force')) {
                if (! $this->confirm('The App/Models/MadelineProtoSession model is already exist. Replace it?')) {
                    $this->info('Multi session export aborted.');

                    return;
                }
            }

            $this->exportModel($foreignModel);

            $this->info('MadelineProtoSession model generated.');
        }

        $tableName = config('madeline-proto.sessions.multiple.table');
        $migration = "2024_04_10_000000_create_{$tableName}_table.php";

        if (file_exists(database_path("migrations/$migration")) && !$this->option('force')) {
            if (! $this->confirm("The $migration migration file is already exist. Replace it?")) {
                $this->info('Multi session export aborted.');

                return;
            }
        }

        $this->exportMigration($tableName, $foreignModel);

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
            app_path('Models/MadelineProtoSession.php'),
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
            [Str::snake(class_basename($user)), 'App\Models'],
            $stub
        );
    }

    /**
     * Compile the TelegramSession migration stub.
     *
     * @param string $tableName
     * @param string $foreignModel
     * @return string
     */
    public function compileMigrationStub(string $tableName, string $foreignModel): string
    {
        $foreignColumn = config('madeline-proto.sessions.multiple.foreign_column', 'user_id');

        return str_replace(
            ['{{table}}', '{{model}}', '{{column}}'],
            [$tableName, "$foreignModel::class", $foreignColumn],
            file_get_contents(__DIR__ . '/stubs/migration.stub')
        );
    }
}
