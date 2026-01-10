<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CounselorController extends Controller
{
    public function index()
    {
        $counselors = User::where('role_id', 2)->get();
        return view('admin.counselors.index', compact('counselors'));
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
            'role_id' => 2,
        ]);

        return redirect()->back()->with('success', 'Konselor berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $counselor = User::findOrFail($id);
        $counselor->delete();

        return redirect()->back()->with('success', 'Akun Konselor berhasil dihapus.');
    }
}