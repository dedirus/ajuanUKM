<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'kemahasiswaan') {
            return redirect()->route('kemahasiswaan.dashboard');
        }

        if ($user->role === 'pembina') {
            return redirect()->route('pembina.dashboard');
        }

        return redirect()->route('mahasiswa.dashboard');
    }
}
