<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
   public function create()
    {
        abort_unless(
            auth()->user()->isSuperAdmin() || auth()->user()->isAdmin(),
            403
        );
        return view('invitations.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:admin,member,sales,manager',
                'company_id' => 'required|exists:companies,id',
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('12345678'),
                'role' => $request->role,
                'company_id' => $request->company_id,
            ]);

            return redirect()->route('dashboard')->with('success', 'User invited.');
        }

        if ($user->isAdmin()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => ['required', Rule::in(['sales', 'manager'])],
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('12345678'),
                'role' => $request->role,
                'company_id' => $user->company_id,
            ]);

            return redirect()->route('dashboard')->with('success', 'User invited.');
        }

        abort(403);
    }
    
}
