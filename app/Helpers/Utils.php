<?php

namespace App\Helpers;

/**
 * returns the year that the user is looking at.
 * In order of priority, the year (called currentYear) can be set
 * - as a sessions variable
 * - as a request variable
 * - in myconfig (in app/config)
 * - this year
 */
class Utils
{
    static function currentYear()
    {
        return session('currentYear') ?? (request('currentYear') ?? config('myconfig.currentYear', date("Y")));
    }
}