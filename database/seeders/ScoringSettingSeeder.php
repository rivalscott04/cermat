<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScoringSetting;

class ScoringSettingSeeder extends Seeder
{
	public function run(): void
	{
		if (!ScoringSetting::query()->exists()) {
			ScoringSetting::create([
				'weight_kecermatan' => 40,
				'weight_kecerdasan' => 35,
				'weight_kepribadian' => 25,
				'passing_grade' => 61,
			]);
		}
	}
}


