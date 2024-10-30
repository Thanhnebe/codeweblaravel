<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function checkRole(Request $request, $requiredRole)
    {
        $user = Auth::user();

        // Kiểm tra xem người dùng có tồn tại hay không
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        // Kiểm tra xem vai trò của người dùng có khớp với vai trò yêu cầu hay không
        if ($user->role != $requiredRole) {
            return $this->forbiddenResponse();
        }

        return $this->authorizedResponse($user->role);
    }

    private function unauthorizedResponse()
    {
        return response()->json([
            'status' => false,
            'message' => 'Không được phép',
        ], 403);
    }

    private function forbiddenResponse()
    {
        return response()->json([
            'status' => false,
            'message' => 'Cấm: Vai trò không đủ',
        ], 403);
    }

    private function authorizedResponse($role)
    {
        return response()->json([
            'status' => true,
            'role' => $role,
        ], 200);
    }
}
