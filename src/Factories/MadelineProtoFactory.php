<?php

namespace AkostDev\LaravelMadelineProto\Factories;

use danog\MadelineProto\API;
use AkostDev\LaravelMadelineProto\MadelineProto;
use danog\MadelineProto\Exception;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Logger;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;

class MadelineProtoFactory
{
    /**
     * @var Connection
     */
    private Connection $database;

    /**
     * Table name.
     *
     * @var string
     */
    private string $table;

    /**
     * SessionFactory constructor.
     *
     * @param DatabaseManager $manager
     * @param string $table
     */
    public function __construct(DatabaseManager $manager, string $table)
    {
        $this->database = $manager->connection();
        $this->table = $table;
    }

    /**
     * Get the MadelineProto (session) instance from session table.
     *
     * @param int|Model $session can be either <b>id</b> or model instance of <b>MadelineProtoSession</b> which
     *                           generated from <u>madeline-proto:multi-session --model</u> command
     * @param array $config if this parameter is null, then the config from <b>madeline-proto.php</b>
     *                           file will be used
     * @return MadelineProto
     * @throws Exception
     */
    public function get(Model|int $session, array $config = []): MadelineProto
    {
        if (is_int($session)) {
            $session = $this->database->table($this->table)
                ->find($session);
        }

        $sessionFile = $session->session_file;

        $config += [
            'api_id' => $session->api_id,
            'api_hash' => $session->api_hash,
        ];

        return $this->make($sessionFile, $config);
    }

    /**
     * Generating MadelineProto (session) instance.
     *
     * @param string $sessionFile
     * @param array $config if this parameter is null, then the config from <b>madeline-proto.php</b>
     *                           file will be used
     * @return MadelineProto
     * @throws Exception
     */
    public function make(string $sessionFile, array $config = []): MadelineProto
    {
        $defaultConfig = config('madeline-proto.settings');

        $apiId = $config['api_id'] ?? $defaultConfig['app_info']['api_id'];
        $apiHash = $config['api_hash'] ?? $defaultConfig['app_info']['api_hash'];

        $appInfoSettings = (new AppInfo())
            ->setApiId(intval($apiId))
            ->setApiHash($apiHash);

        $loggingSettings = (new Logger())
            ->setType($defaultConfig['logger']['logger'])
            ->setExtra($defaultConfig['logger']['logger_param'])
            ->setMaxSize(50 * 1024 * 1024);

        $settings = new Settings();
        $settings
            ->setAppInfo($appInfoSettings)
            ->setLogger($loggingSettings);

        $client = new API(storage_path("app/madeline-proto/$sessionFile"), $settings);

        return new MadelineProto($client);
    }
}
