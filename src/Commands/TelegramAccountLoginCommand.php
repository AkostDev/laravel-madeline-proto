<?php

namespace AkostDev\MadelineProto\Commands;

use AkostDev\MadelineProto\Exceptions\NeedTwoFactorAuthException;
use AkostDev\MadelineProto\Exceptions\SignUpNeededException;
use AkostDev\MadelineProto\Facades\MadelineProto;
use Illuminate\Console\Command;

class TelegramAccountLoginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'madeline-proto:login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do Telegram account login attempt.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->ensureIsLoggedOut();

        $phoneNumber = $this->ask('Phone number with country code (e.g: +6282112345678)?');

        MadelineProto::phoneLogin($phoneNumber);

        $code = $this->ask('Phone code?');

        try {
            MadelineProto::completePhoneLogin($code);
        } catch (NeedTwoFactorAuthException $e) {
            $password = $this->secret("2FA Password (hint '{$e->account->hint}')");

            MadelineProto::submit2FA($password);
        } catch (SignUpNeededException $e) {
            $this->info('Sign up needed.');

            $firstName = $this->ask('First name');
            $lastName = $this->ask('Last name (optional)', '');

            MadelineProto::completeSignUp($firstName, $lastName);
        }

        $this->info('Telegram account successfully logged in.');
    }

    /**
     * Ensuring if there's no current active Telegram session.
     *
     * @return void
     */
    protected function ensureIsLoggedOut(): void
    {
        if (MadelineProto::isLoggedIn()) {
            MadelineProto::logout();

            $this->info('Logging out authenticated account.');
        }
    }
}
