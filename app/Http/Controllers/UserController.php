<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function show($userId)
    {
        $user = User::findOrFail($userId);

        try {
            // Get cached provinces
            $provinces = Cache::remember('provinces', 60 * 24, function () {
                $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json");
                if (!$response->successful()) {
                    Log::error('Failed to fetch provinces');
                    return [];
                }
                return $response->json();
            });

            // Get regencies if province is selected
            $regencies = null;
            if ($user->province) {
                $provinceData = collect($provinces)->first(function ($province) use ($user) {
                    return $province['name'] === $user->province;
                });

                if ($provinceData) {
                    $regencies = Cache::remember("regencies.{$provinceData['id']}", 60 * 24, function () use ($provinceData) {
                        $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceData['id']}.json");
                        if (!$response->successful()) {
                            Log::error('Failed to fetch regencies');
                            return [];
                        }
                        return $response->json();
                    });
                }
            }

            // Get latest subscription
            $subscription = DB::table('subscriptions')
                ->where('user_id', $userId)
                ->select('id', 'user_id', 'end_date', 'payment_method', 'payment_details', 'payment_status')
                ->latest('end_date')
                ->first();

            return view('user.profile', compact('user', 'provinces', 'regencies', 'subscription'));
        } catch (\Exception $e) {
            Log::error('Error loading profile page', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return view('user.profile', [
                'user' => $user,
                'provinces' => [],
                'regencies' => [],
                'subscription' => null
            ])->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }
    public function update(Request $request)
    {
        try {
            Log::info('Profile update request received', $request->all());

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'nullable|string|max:20',
                'province' => 'nullable|string',
                'regency' => 'nullable|string',
            ]);

            $user = auth()->user();

            // Get province and regency names from their IDs
            if ($request->filled('province')) {
                $provinces = Cache::get('provinces', []);
                $selectedProvince = collect($provinces)->firstWhere('id', $request->province);
                if ($selectedProvince) {
                    $validated['province'] = $selectedProvince['name'];

                    if ($request->filled('regency')) {
                        $regencies = Cache::get("regencies.{$request->province}", []);
                        $selectedRegency = collect($regencies)->firstWhere('id', $request->regency);
                        if ($selectedRegency) {
                            $validated['regency'] = $selectedRegency['name'];
                        }
                    }
                }
            }

            $user->update($validated);

            Log::info('Profile updated successfully', ['user_id' => $user->id]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profil berhasil diperbarui!'
                ]);
            }

            return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getProvinces()
    {
        $provinces = Cache::remember('provinces', 60 * 24, function () {
            return Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json")->json();
        });

        return response()->json($provinces);
    }

    public function getRegencies($provinceId)
    {
        $regencies = Cache::remember("regencies.{$provinceId}", 60 * 24, function () use ($provinceId) {
            return Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceId}.json")->json();
        });

        return response()->json($regencies);
    }
}
