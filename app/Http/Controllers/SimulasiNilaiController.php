<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ScoringService;
use App\Models\ScoringSetting;

class SimulasiNilaiController extends Controller
{
	public function index()
	{
		$setting = ScoringSetting::current();
		return view('simulasi.nilai', compact('setting'));
	}

	public function calculate(Request $request, ScoringService $service)
	{
		$request->validate([
			'kecermatan' => 'required|numeric|min:0|max:100',
			'kecerdasan' => 'required|numeric|min:0|max:100',
			'kepribadian' => 'required|numeric|min:0|max:100',
		]);

		$result = $service->calculateFinalScore(
			(float) $request->kecermatan,
			(float) $request->kecerdasan,
			(float) $request->kepribadian
		);

		$setting = ScoringSetting::current();
		return view('simulasi.nilai', compact('setting', 'result'));
	}
}


