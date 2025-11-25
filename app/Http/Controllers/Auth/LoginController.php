<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Observers\ActivityObserver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    protected $activityObserver;

    public function __construct()
    {
        $this->activityObserver = new ActivityObserver();
    }

    public function index(Request $request)
    {
        return spaRender($request, "auth.login");
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|max:30',
            'password' => 'required|min:3',
        ]);

        $credentials = $request->only('username', 'password');
        $remember = $request->boolean('remember');

        $credentials['status'] = true;

        $user = User::where('username', $credentials['username'])->where('status', true)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan.',
            ], 404);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password yang Anda masukkan salah.',
            ], 422);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $this->activityObserver->logCustom(
            message: 'User logged in',
            event: 'logged_in',
            logName: 'auth',
            properties: [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil login.',
            'redirect' => route($user->role->redirect ?? 'dashboard'),
            'redirect_type' => 'http',
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $this->activityObserver->logCustom(
                message: 'User logged out',
                event: 'logged_out',
                logName: 'auth',
                properties: [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'user_id' => $user->id,
                    'username' => $user->username,
                ],
            );

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login');
    }
}
