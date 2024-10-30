<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function apiRegister(Request $req): JsonResponse
    {
        $messages = [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'confirmPassword.required' => 'Xác nhận mật khẩu là bắt buộc.',
            'confirmPassword.same' => 'Xác nhận mật khẩu không khớp.',
        ];

        $validator = Validator::make($req->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'confirmPassword' => 'required|same:password',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $isFirstUser = User::count() === 0;

        $data = [
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'role' => $isFirstUser ? 0 : 1,
        ];

        $newUser = User::create($data);

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => [
                'id' => $newUser->id,
                'name' => $newUser->name,
                'email' => $newUser->email,
                'role' => $newUser->role,
            ],
            'status_code' => 201
        ], 201);
    }

    public function apiLogin(Request $req): JsonResponse
    {
        $messages = [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Mật khẩu là bắt buộc.',
        ];

        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $user = User::where('email', $req->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email không tồn tại',
                'status_code' => 400
            ], 400);
        }

        if (!Hash::check($req->password, $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu không đúng',
                'status_code' => 401
            ], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
            'status_code' => 200
        ], 200);
    }

    public function apiLogout(Request $req): JsonResponse
    {
        $user = $req->user();
        $tokensBefore = $user->tokens()->get();

        if ($tokensBefore->isEmpty()) {
            return response()->json([
                'message' => 'Đăng xuất thất bại: Người dùng không có token',
                'status_code' => 400
            ], 400);
        }

        $user->tokens()->delete();
        $tokensAfter = $user->tokens()->get();

        return response()->json([
            'message' => 'Đăng xuất thành công',
            'tokens_before' => $tokensBefore,
            'tokens_after' => $tokensAfter,
            'status_code' => 200
        ], 200);
    }
}
