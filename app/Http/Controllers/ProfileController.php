<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return spaRender($request, 'profile.index');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore(Auth::id()),
            ],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        return DB::transaction(function () use ($validated) {
            $user = User::findOrFail(Auth::id());

            $user->name = $validated['name'];
            $user->username = $validated['username'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui.',
                'redirect_type' => 'reload',
            ]);
        });
    }
}
