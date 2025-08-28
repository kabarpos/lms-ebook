<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /**
     * Verify user account with token
     */
    public function verify(Request $request, $id, $token)
    {
        try {
            // Find user by ID
            $user = User::findOrFail($id);
            
            // Check if token matches
            if ($user->verification_token !== $token) {
                return redirect()->route('login')
                    ->with('error', 'Token verifikasi tidak valid atau sudah kadaluarsa.');
            }
            
            // Check if already verified
            if ($user->isFullyVerified()) {
                return redirect()->route('login')
                    ->with('info', 'Akun Anda sudah terverifikasi sebelumnya.');
            }
            
            // Mark email as verified
            $user->verifyEmail();
            
            // Mark WhatsApp as verified (assuming the user clicked the link from WhatsApp)
            $user->verifyWhatsapp();
            
            // Clear verification token
            $user->update([
                'verification_token' => null,
                'is_account_active' => true
            ]);
            
            Log::info('User account verified successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            return redirect()->route('login')
                ->with('success', 'Akun Anda berhasil diverifikasi! Silakan login untuk mengakses platform.');
                
        } catch (\Exception $e) {
            Log::error('Account verification failed', [
                'user_id' => $id,
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat memverifikasi akun. Silakan hubungi administrator.');
        }
    }
    
    /**
     * Resend verification link
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        
        try {
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return back()->with('error', 'Email tidak ditemukan.');
            }
            
            if ($user->isFullyVerified()) {
                return back()->with('info', 'Akun Anda sudah terverifikasi.');
            }
            
            // Generate new verification token if not exists
            if (!$user->verification_token) {
                $user->generateVerificationToken();
            }
            
            // Send verification notification
            $whatsappService = app(\App\Services\WhatsappNotificationService::class);
            $result = $whatsappService->sendRegistrationVerification($user);
            
            if ($result['success']) {
                return back()->with('success', 'Link verifikasi telah dikirim ulang ke WhatsApp Anda.');
            } else {
                return back()->with('warning', 'Link verifikasi dibuat, namun gagal dikirim ke WhatsApp. Silakan hubungi administrator.');
            }
            
        } catch (\Exception $e) {
            Log::error('Resend verification failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }
    
    /**
     * Show verification status page
     */
    public function status()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        return view('auth.verification-status', compact('user'));
    }
}
