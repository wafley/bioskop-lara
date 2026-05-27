<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Observers\ActivityObserver;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $activities = $user->activities()->latest()->take(10)->get();

        return spaRender($request, 'profile.index', [
            'user' => $user,
            'activities' => $activities,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore(Auth::id()),
            ],
        ]);

        return DB::transaction(function () use ($validated, $user) {
            $user->name = $validated['name'];
            $user->username = $validated['username'];

            $user->save();

            (new ActivityObserver())->logCustom(
                message: 'Profil diperbarui',
                event: 'profile_updated',
                logName: 'profile',
                properties: [
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'username' => $validated['username'],
                ],
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui.',
                'redirect_type' => 'reload',
            ]);
        });
    }
}
