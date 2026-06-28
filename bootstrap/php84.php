<?php

/*
|--------------------------------------------------------------------------
| PHP 8.4 Compatibility
|--------------------------------------------------------------------------
|
| Laravel 9 and several dependencies were written before PHP 8.4's stricter
| nullable-parameter deprecations. Hide vendor deprecations on PHP 8.4+ until
| you upgrade Laravel or switch to PHP 8.2/8.3.
|
*/

if (PHP_VERSION_ID >= 80400) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    ini_set('display_errors', '0');

    set_error_handler(static function (int $severity, string $message): bool {
        if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
            return true;
        }

        return false;
    }, E_DEPRECATED | E_USER_DEPRECATED);
}
