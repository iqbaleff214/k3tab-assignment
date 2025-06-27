<?php

namespace App\Http\Controllers;

use App\Enum\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConfigurationController extends Controller
{
    public function index(): RedirectResponse
    {
        if (Gate::allows(Permission::ViewApprovalFlow))
            return redirect()->route('configuration.approval-flow.index');

        if(Gate::allows(Permission::ViewWhatsApp))
            return redirect()->route('configuration.whatsapp.index');

        abort(403);
    }
}
