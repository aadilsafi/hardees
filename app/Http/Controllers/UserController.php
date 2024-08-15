<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'super') {
            $users = User::where('id', '!=', $user->id)->where('role','!=','super')->get();
        } else {
            $users = User::where('role', '!=', 'super')->where('role', '!=', 'admin')->where('id','!=',$user->id)->get();
        }
        return view('users.index', compact('users'));
    }
    public function create()
    {
        $regions = Store::distinct()
        ->pluck('Region')
        ->sort()
        ->values();
        return view('users.create', compact('regions'));
    }

    // Store a new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:tblUsers,email',
            'password' => 'required|min:6|confirmed',
            'regions' => 'nullable|array',
            'role' => 'required|string'
        ]);
        $auth_user = auth()->user();

        if ($auth_user->role !== 'super' && $request->role === 'super') {
            return redirect()->route('users.index')->with('error', 'You cannot update a super user.');
        }
        if ($auth_user->role === 'admin' && $request->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'You cannot update an admin user.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'regions' => implode(',', $request->regions ?? []),
            'role' => $request->role
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
    public function edit($id)
    {
        $regions = Store::distinct()
        ->pluck('Region')
        ->sort()
        ->values();
        $user = User::findOrFail($id); // Find the user or fail
        return view('users.edit', compact('user', 'regions'));
    }

    // Update the specified user in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:tblUsers,email,'     . $id,
            'regions' => 'sometimes|nullable|array',
            'role' => 'sometimes|string',
            'password' => 'required|min:6|confirmed',
        ]);

        $auth_user = auth()->user();
        $user = User::findOrFail($id);
        if ($auth_user->role !== 'super' && $user->role === 'super' && $user->id !== $auth_user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot update a super user.');
        }
        if ($auth_user->role === 'admin' && $user->role === 'admin' && $user->id !== $auth_user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot update an admin user.');
        }
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('regions')) {
            $user->regions = implode(',', $request->regions ?? []);
        }
        if ($request->filled('role')) {
            $user->role = $request->role;
        }

        if ($request->filled('password') && $request->password !== $user->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        if($user->id === $auth_user->id){
            return redirect()->route('profile')->with('success', 'Profile updated successfully.');
        }
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
    public function destroy($id)
    {
        $auth_user = auth()->user();
        $user = User::findOrFail($id);
        if ($auth_user->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }
        if ($auth_user->role !== 'super' && $user->role === 'super') {
            return redirect()->route('users.index')->with('error', 'You cannot delete a super user.');
        }
        if ($auth_user->role === 'admin' && $user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'You cannot delete an admin user.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
    public function profile()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }
}
