<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\Devices;
use App\Helpers\ApiHelpers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class DevicesController extends Controller
{

    public function index(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 403);
            }

            $devices = Devices::all();

            return ApiHelpers::ok($devices, 'Berhasil mengambil seluruh data Devices!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function register(Request $request)
    {
        try {
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:125',
                'automatic' => 'required|boolean',
                'heater' => 'required|boolean',
                'blower' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return ApiHelpers::badRequest($validator->errors(), 'Ada data yang tidak valid!', 403);
            }

            $validated = $validator->validated();

            $existingDevice = Devices::where('name', $validated['name'])->first();
            if ($existingDevice) {
                return ApiHelpers::badRequest([], 'Device dengan nama tersebut sudah terdaftar!', 400);
            }

            Devices::create($validated);
            event(new Registered($validated));

            $device = Devices::where('name', $validated['name'])->first();
            if (!$device) {
                return ApiHelpers::badRequest([], 'Device tidak ditemukan setelah pendaftaran!', 500);
            }

            $token = $device->createToken($request->name, ['devices'])->plainTextToken;

            $data = [
                'token' => "Bearer $token",
                'device' => $device
            ];

            return ApiHelpers::ok($data, 'Berhasil Mendaftarkan Device Baru!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function renew(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 401);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);

            if ($validator->fails())
            {
                return ApiHelpers::badRequest($validator->errors(), 'Ada data yang tidak valid!');
            }

            $validated = $validator->validated();

            $devices = Devices::where('name', $validated['name'])->first();
            if(!$devices)
            {
                return ApiHelpers::badRequest([], 'Data Tidak Ditemukan atau Password Salah!', 401);
            }

            $devices->tokens()->delete();
            $token = $devices->createToken($devices->name, ['devices'])->plainTextToken;

            $data = [
                'token' => "Bearer $token",
                'device' => $devices,
            ];

            return ApiHelpers::ok($data, 'Token di update!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }


    public function details(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 401);
            }

            $devices_id = $request->header('device_id');
            $data = Devices::findOrFail($devices_id);

            return ApiHelpers::ok($data, 'Ini adalah Detail Devices!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function current_users(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!');
            }

            $devices = Devices::findOrFail($request->header('device_id'));

            if (!$devices)
            {
                return ApiHelpers::badRequest([], 'Devices tidak ditemukan!', 404);
            }

            $data = [
                'id' => $devices->id,
                'automatic' => $devices->automatic,
                'heater' => $devices->heater,
                'blower' => $devices->blower,
            ];

            return ApiHelpers::ok($data, 'Ini adalah Detail Devices!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function current_devices(Request $request)
    {
        try {
            $devices = Auth::check();

            if(!$devices)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 401);
            }

            $devices = Devices::findOrFail(Auth::user()->id);

            if (!$devices)
            {
                return ApiHelpers::badRequest([], 'Devices tidak ditemukan!', 404);
            }

            $data = [
                'id' => $devices->id,
                'automatic' => $devices->automatic,
                'heater' => $devices->heater,
                'blower' => $devices->blower,
            ];

            return ApiHelpers::ok($data, 'Ini adalah Detail Devices!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function changes(Request $request)
    {
        try {
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 401);
            }

            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'automatic' => 'required|boolean',
                'heater' => 'required|boolean',
                'blower' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return ApiHelpers::badRequest($validator->errors(), 'Ada data yang tidak valid!');
            }

            $validated = $validator->validated();

            $devices = Devices::where('id', $validated['id'])->first();

            if (!$devices) {
                return ApiHelpers::badRequest([], 'Device tidak ditemukan atau tidak memiliki izin!', 404);
            }

            $devices->update([
                'automatic' => $validated['automatic'],
                'heater' => $validated['heater'],
                'blower' => $validated['blower']
            ]);

            $data = [
                'id' => $devices->id,
                'automatic' => $devices->automatic,
                'heater' => $devices->heater,
                'blower' => $devices->blower,
            ];

            return ApiHelpers::success($data, 'Berhasil Memperbarui Data Sensor!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }
}
