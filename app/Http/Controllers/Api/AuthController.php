<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\RegistrationToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\OtpMail;

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
                'password' => $request->password,
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
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'gender' => $user->gender,
                'profile_photo_url' => $user->profile_photo_url
            ]
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
            'gender' => 'nullable|string',
            'profile_photo' => 'nullable',
        ]);

        $data = $request->only(['username', 'email', 'gender']);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('profile-photos', 'public');
            $data['profile_photo'] = $path;
        } elseif ($request->filled('profile_photo')) {
            $data['profile_photo'] = $request->profile_photo;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'user' => $user->fresh()
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
        $email = strtolower($request->email);

        $request->merge(['email' => $email]);

        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = random_int(10000, 99999);
        $user = User::where('email', $request->email)->first();

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->username));

        return response()->json([
            'success' => true,
            'message' => 'OTP telah dikirim ke email Gmail Anda.'
        ]);
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

        if ($user->profile_photo && str_contains($user->profile_photo, 'assets/')) {
            $user->profile_photo_url = $user->profile_photo;
        } else if ($user->profile_photo) {
            $user->profile_photo_url = asset('storage/' . $user->profile_photo);
        } else {
            $user->profile_photo_url = null;
        }

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate(['is_online' => 'required|boolean']);

        $user = Auth::user();
        $user->update([
            'is_online' => $request->is_online,
            'last_seen' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
