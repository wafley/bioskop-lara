<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PasswordController extends Controller
{
    /**
     * Show change password page
     */
    public function edit(Request $request)
    {
        return spaRender($request, 'auth.change-password');
    }

    /**
     * Update authenticated user's password
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!password_verify($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password saat ini salah.',
            ], 422);
        }

        $user->update([
            'password' => $request->new_password,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah.',
            'redirect_type' => 'reload',
        ]);
    }

    /**
     * Reset user's password
     */
    public function reset(User $user)
    {
        $user->update([
            'password' => '12345678',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil direset ke "12345678".',
            'redirect_type' => 'reload',
        ]);
    }
}
