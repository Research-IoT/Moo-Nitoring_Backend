<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Helpers\ApiHelpers;

use App\Models\OpenWeatherConfig;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OpenWeatherConfigController extends Controller
{
    public function info()
    {
        try {
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 403);
            }

            $data = OpenWeatherConfig::orderBy('created_at', 'desc')->first();
            
            if ($data->isEmpty()) {
                return ApiHelpers::badRequest('Data tidak ditemukan');
            }

            $data = [
                'api_key' => $data['api_key']
            ];

            return ApiHelpers::ok($data, 'Berhasil API Key Open Weather!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function add(Request $request)
    {
        try {
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 403);
            }

            $validator = Validator::make($request->all(), [
                'api_key' => 'required|string|max:125',
            ]);

            if ($validator->fails()) {
                return ApiHelpers::badRequest($validator->errors(), 'Ada data yang tidak valid!', 403);
            }

            $validated = $validator->validated();

            OpenWeatherConfig::create($validated);

            $created = OpenWeatherConfig::where('api_key', $validated['api_key'])->orderBy('created_at', 'desc')->first();

            $data = $created;

            return ApiHelpers::ok($data, 'Berhasil mengambil terkini data!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }
}
