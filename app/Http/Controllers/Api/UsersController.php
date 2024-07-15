<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\Users;
use App\Helpers\ApiHelpers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'role' => 'required|string|max:10|',
                'no_hp' => 'required|string|max:15|unique:users',
                'alamat' => 'required|string|max:255',
                'password' => ['required', 'string', Password::defaults()],
            ]);

            if($validator->fails())
            {
                return ApiHelpers::badRequest($validator->errors(), 'Ada data yang tidak valid!', 403);
            }

            $validated = $validator->validated();

            $validated['password'] = Hash::make($validated['password']);

            Users::create($validated);
            event(new Registered($validated));

            $users = Users::where('no_hp', $validated['no_hp'])->first();
            $token = $users->createToken($request->name, ['users'])->plainTextToken;

            $response = [
                'token' => "Bearer $token",
                'users' => $users
            ];

            return ApiHelpers::success($response, 'Berhasil Mendaftarkan Pengguna Baru!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'no_hp' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails())
            {
                return ApiHelpers::badRequest($validator->errors(), 'Ada data yang tidak valid!', 403);
            }

            $validated = $validator->validated();

            $users = Users::where('no_hp', $validated['no_hp'])->first();

            if (!$users || !Hash::check($validated['password'], $users->password)) {
                return ApiHelpers::badRequest([], 'Data Tidak Ditemukan atau Password Salah!', 401);
            }

            $users->tokens()->delete();
            $token = $users->createToken($users->name, ['users'])->plainTextToken;

            $data = [
                'token' => "Bearer $token",
                'users' => $users
            ];

            return ApiHelpers::ok($data, 'Berhasil Masuk!');
        } catch (\Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function profile(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 401);
            }

            $data = Auth::user();

            return ApiHelpers::ok($data, 'Berhasil Mengambil Data Pengguna!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function logout(Request $request)
    {
        try {
            $users = Auth::user();

            if (!$users)
            {
                return ApiHelpers::badRequest([], 'Unauthorized', 401);
            }

            $data = $request->user()->currentAccessToken()->delete();

            return ApiHelpers::success([], 'Token sudah dihapus!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm(): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: 'Account Activated',
            body: 'Your account has been activated.'
        )))
//            ->data(['data1' => 'value', 'data2' => 'value2'])
//            ->custom([
//                'android' => [
//                    'notification' => [
//                        'color' => '#0A0A0A',
//                    ],
//                    'fcm_options' => [
//                        'analytics_label' => 'analytics',
//                    ],
//                ],
//        ])
            ;
    }
}
