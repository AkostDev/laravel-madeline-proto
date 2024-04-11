# Laravel MadelineProto
[![Latest Stable Version](https://poser.pugx.org/akostdev/laravel-madeline-proto/v)](//packagist.org/packages/akostdev/laravel-madeline-proto)
[![Total Downloads](https://poser.pugx.org/akostdev/laravel-madeline-proto/downloads)](//packagist.org/packages/akostdev/laravel-madeline-proto)
[![License](https://poser.pugx.org/akostdev/laravel-madeline-proto/license)](//packagist.org/packages/akostdev/laravel-madeline-proto)

A third party Telegram client library [danog/MadelineProto](https://github.com/danog/MadelineProto) wrapper for Laravel.

# Getting Started

Add the laravel-madeline-proto to the project dependency:

```shell script
composer require akostdev/laravel-madeline-proto
```

Then publish the `telegram.php` config file:

```shell script
php artisan vendor:publish --provider="AkostDev\LaravelMadelineProto\MadelineProtoServiceProvider"
```

Set up the Telegram API key by providing env variables:

```dotenv
MP_TELEGRAM_API_ID=... //your telegram api id here
MP_TELEGRAM_API_HASH=... //your telegram api hash here
```

# Notes

* This wrapper package is still not wrapping all the apis yet, I'm still focusing on wrapping the messages api.

* If you can't find the method that you want in Messages facade or need to use the default danog/MadelineProto api, you might want to use `MadelineProto::getClient()` facade method. It will return `danog\MadelineProto\API` object where you can call all the method provided by the [danog/MadelineProto](https://github.com/danog/MadelineProto) library.
