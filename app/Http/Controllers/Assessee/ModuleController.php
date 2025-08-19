<?php

namespace App\Http\Controllers\Assessee;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModuleController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        return Inertia::render('panel/assessee/module/Index', [
            'items' => Module::with(['assessees' => fn ($query) => $query->where('assessee_id', $userId)])
                ->sort($request->query('sorts'))
                ->filter($request->query('filters'))
                ->get(),
        ]);
    }
}
