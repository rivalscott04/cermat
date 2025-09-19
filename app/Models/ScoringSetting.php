<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoringSetting extends Model
{
	protected $fillable = [
		'weight_kecermatan',
		'weight_kecerdasan',
		'weight_kepribadian',
		'passing_grade',
	];

	/**
	 * Get the single settings row, creating default in memory if none exists.
	 */
	public static function current(): self
	{
		return static::query()->latest('id')->first() ?? new static([
			'weight_kecermatan' => 40,
			'weight_kecerdasan' => 35,
			'weight_kepribadian' => 25,
			'passing_grade' => 61,
		]);
	}
}


