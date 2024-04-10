<?php

namespace AkostDev\MadelineProto\Facades;

use Illuminate\Support\Facades\Facade;
use AkostDev\MadelineProto\MadelineProto;

/**
 * Facade for MadelineProtoFactory class.
 *
 * @package AkostDev\MadelineProto\Facades
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
