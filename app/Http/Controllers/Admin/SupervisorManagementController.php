<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SupervisorManagementController extends Controller
{
    public function index()
    {
        $supervisors = User::where('role_id', 4)->get();
        return view('admin.supervisors.index', compact('supervisors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 4,
        ]);

        return redirect()->back()->with('success', 'Akun Supervisor berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $supervisor = User::findOrFail($id);

        if ($supervisor->id === Auth::user()->id) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $supervisor->delete();
        return redirect()->back()->with('success', 'Akun Supervisor berhasil dihapus.');
    }
}
