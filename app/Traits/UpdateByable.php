<?php
namespace App\Traits;

trait UpdateByable
{
	/**
	 * Add the user id to the updated_by field
	 */
	public static function bootUpdateByable()
	{
		static::saving(function ($model) {
			$model->updated_by = auth()->user()->person_id;
		});
	}
}