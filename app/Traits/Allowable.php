<?php
namespace App\Traits;

trait Allowable
{
	/**
	 * If the user does not have permission to view this page, show 'Unauthorised' page
	 */
	static private function userAllowable($page)
	{
        $user = auth()->user();
        if (!($user->hasPermissionTo('admin')) and !($user->hadPermissionTo('data entry'))) {
			return view('notAuthorised');
		} else {
			return view($page);
		}
	}
}