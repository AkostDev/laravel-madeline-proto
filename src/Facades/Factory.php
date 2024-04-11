<?php

namespace AkostDev\LaravelMadelineProto\Facades;

use Illuminate\Support\Facades\Facade;
use AkostDev\LaravelMadelineProto\MadelineProto;

/**
 * Facade for MadelineProtoFactory class.
 *
 * @package AkostDev\LaravelMadelineProto\Facades
 *
 * @method static MadelineProto get(mixed $session, array $config = null)
 * @method static MadelineProto make(string $sessionFile, array $config)
 */
class Factory extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return 'madeline-proto-factory';
    }
}
