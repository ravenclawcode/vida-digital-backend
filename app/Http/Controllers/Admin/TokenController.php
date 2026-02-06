<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationToken;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    public function index()
    {
        $tokens = RegistrationToken::orderBy('created_at', 'desc')->get();
        return view('admin.tokens.index', compact('tokens'));
    }

    public function store()
    {
        do {
            $code = 'VIDA-' . strtoupper(Str::random(6));
        } while (RegistrationToken::where('token_code', $code)->exists());

        RegistrationToken::create([
            'token_code' => $code,
            'is_used' => false,
        ]);

        return redirect()->back()->with('success', 'Token Baru Berhasil Dibuat!');
    }

    public function destroy($id)
    {
        $token = RegistrationToken::findOrFail($id);

        if ($token->is_used) {
            return redirect()->back()->with('error', 'Token yang sudah terpakai tidak bisa dihapus.');
        }

        $token->delete();
        return redirect()->back()->with('success', 'Token berhasil dihapus.');
    }
}
