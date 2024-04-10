<?php

namespace AkostDev\MadelineProto\Facades;

use danog\MadelineProto\API;
use AkostDev\MadelineProto\TelegramObject;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for MadelineProto class.
 *
 * @package AkostDev\MadelineProto\Facades
 *
 * @method static TelegramObject phoneLogin(string $phone)
 * @method static TelegramObject completePhoneLogin(string $code)
 * @method static TelegramObject submit2FA(string $password)
 * @method static TelegramObject completeSignUp(string $firstName, string $lastName = '')
 * @method static API getClient()
 * @method static bool logout()
 * @method static bool isLoggedIn()
 *
 * @see \AkostDev\MadelineProto\MadelineProto
 */
class MadelineProto extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return 'madeline-proto';
    }
}
