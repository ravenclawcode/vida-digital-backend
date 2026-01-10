<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\RegistrationToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{

    public function checkToken(Request $request)
    {
        $request->validate(['token_code' => 'required|string']);

        $token = RegistrationToken::where('token_code', $request->token_code)
            ->where('is_used', false)
            ->first();

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak sah atau sudah digunakan.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Token tersedia.', 'token_id' => $token->id]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'token_code' => 'required|string',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $token = RegistrationToken::where('token_code', $request->token_code)
            ->where('is_used', false)
            ->first();

        if (!$token)
            return response()->json(['message' => 'Token tidak valid'], 422);

        return DB::transaction(function () use ($request, $token) {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 3,
                'token_id' => $token->id,
            ]);

            $token->update(['is_used' => true]);
            $accessToken = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['success' => true, 'message' => 'Registrasi berhasil', 'access_token' => $accessToken, 'user' => $user], 201);
        });
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Kredensial tidak valid.'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => ['id' => $user->id, 'username' => $user->username, 'role_id' => $user->role_id]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Berhasil logout']);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
        ]);

        $data = [
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        $user->profile_photo_url = $user->profile_photo ? asset('storage/' . $user->profile_photo) : null;

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'profile_photo' => $user->profile_photo,
                'profile_photo_url' => $user->profile_photo_url,
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password saat ini tidak cocok.'], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah.']);
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(10000, 99999);
        $user = User::where('email', $request->email)->first();

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        Mail::raw("Kode OTP Vida Anda adalah: $otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Kode Reset Password Vida');
        });

        return response()->json(['success' => true, 'message' => 'OTP telah dikirim ke email Anda.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:5'
        ]);

        $user = User::where('email', $request->email)
            ->where('otp_code', $request->otp)
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'OTP salah.'], 422);
        }

        if (now()->gt($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'OTP sudah kadaluarsa.'], 422);
        }

        return response()->json(['success' => true, 'message' => 'OTP valid, silakan ganti password.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:5',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::where('email', $request->email)->where('otp_code', $request->otp)->first();

        if (!$user)
            return response()->json(['message' => 'Proses tidak valid'], 422);

        $user->update(['password' => Hash::make($request->password), 'otp_code' => null, 'otp_expires_at' => null]);

        return response()->json(['success' => true, 'message' => 'Password berhasil diperbarui.']);
    }

    public function getUser(Request $request)
    {
        $user = $request->user();

        $user->profile_photo_url = $user->profile_photo
            ? asset('storage/' . $user->profile_photo)
            : null;

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
}