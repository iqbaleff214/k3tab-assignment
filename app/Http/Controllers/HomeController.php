<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard(Request $request): RedirectResponse
    {
        return match ($request->user()->type) {
            'assessor' => redirect()->route('assessor.dashboard'),
            'assessee' => redirect()->route('assessee.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }
}
