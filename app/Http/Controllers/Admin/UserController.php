<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users', ['users' => User::latest()->paginate(15)]);
    }

    public function update(Request $request, User $user)
    {
        $attributes = $request->validate(['role' => ['required', 'in:admin,user']]);

        abort_if($request->user()->id === $user->id && $attributes['role'] !== 'admin', 422, 'You cannot remove your own admin access.');

        $user->update($attributes);

        return back()->with('success', 'User role updated.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($request->user()->id === $user->id, 422, 'You cannot delete your own account.');

        $user->delete();

        return back()->with('success', 'User removed.');
    }
}
