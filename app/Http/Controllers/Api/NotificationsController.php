<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Helpers\ApiHelpers;
use App\Http\Controllers\Controller;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{

    public function list(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 403);
            }

            $data = Notifications::all();

            return ApiHelpers::ok($data, 'Berhasil mengambil seluruh data Devices!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }
}