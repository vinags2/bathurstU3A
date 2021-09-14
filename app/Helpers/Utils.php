<?php

namespace App\Helpers;

use Carbon\Carbon;

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
        // dd(Carbon::now()->toDateString());
        // \Carbon\Carbon::parse($contact->birthdate)->year(now()->format('Y'))->format('Y-m-d') }}
        return session('currentYear') ?? (request('currentYear') ?? config('myconfig.currentYear', date("Y")));
    }

    static function today() {
        return Carbon::parse(now()->year(Utils::currentYear()))->format('Y-m-d');
    }
}