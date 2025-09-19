<?php

namespace App\Services;

use App\Models\ScoringSetting;

class ScoringService
{
	public function calculateFinalScore(float $kecermatan, float $kecerdasan, float $kepribadian): array
	{
		$setting = ScoringSetting::current();
		$w1 = $setting->weight_kecermatan / 100;
		$w2 = $setting->weight_kecerdasan / 100;
		$w3 = $setting->weight_kepribadian / 100;

		$final = ($w1 * $kecermatan) + ($w2 * $kecerdasan) + ($w3 * $kepribadian);
		$passed = $final >= (float) $setting->passing_grade;

		return [
			'score' => round($final, 2),
			'passed' => $passed,
			'passing_grade' => (int) $setting->passing_grade,
			'weights' => [
				'kecermatan' => (int) $setting->weight_kecermatan,
				'kecerdasan' => (int) $setting->weight_kecerdasan,
				'kepribadian' => (int) $setting->weight_kepribadian,
			],
		];
	}
}


