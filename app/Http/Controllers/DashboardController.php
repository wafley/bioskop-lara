<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $role = $user->role;

        if ($role->name === 'admin') {
            return $this->adminDashboard($request);
        } else if ($role->name === 'cashier') {
            return $this->cashierDashboard($request);
        }

        abort(403);
    }

    private function adminDashboard(Request $request)
    {
        return spaRender($request, 'dashboard.admin');
    }

    private function cashierDashboard(Request $request)
    {
        return spaRender($request, 'dashboard.cashier');
    }
}
