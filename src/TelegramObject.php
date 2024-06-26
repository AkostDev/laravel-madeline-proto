<?php

namespace AkostDev\LaravelMadelineProto;

use danog\MadelineProto\Namespace\Updates;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class TelegramObject
 *
 * @package AkostDev\LaravelMadelineProto
 *
 * @property string return_type
 */
class TelegramObject implements Arrayable
{
    /**
     * Contains the Telegram object fields.
     *
     * @var array
     */
    private array $fields;

    /**
     * TelegramObject constructor.
     *
     * @param array|Updates $fields request / response payloads
     */
    public function __construct(array|Updates $fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * Getter magic method.
     *
     * @param $key
     * @return mixed|null
     */
    public function __get($key)
    {
        if ($key === 'return_type') {
            $key = '_';
        }

        return $this->fields[$key] ?? null;
    }

    /**
     * Setter magic method.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    /**
     * is triggered by calling isset() or empty() on inaccessible members.
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->convertToArray($this->fields);
    }

    /**
     * Convert all objects to array recursively.
     *
     * @param array $fields
     * @return array
     */
    private function convertToArray(array $fields): array
    {
        foreach ($fields as &$value) {
            if (is_array($value)) {
                $value = $this->convertToArray($value);
            }

            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }
        }

        return $fields;
    }
}
