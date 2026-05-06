<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            ]);

            $user = $request->user();

            // Update data user
            $user->fill($validated);

            // Reset email verification jika email berubah
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            Log::info('Profile updated successfully for user: ' . $user->id);

            return redirect()->route('profile.edit')->with('status', 'profile-updated');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return back()->withErrors(['general' => 'An error occurred while updating profile.']);
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        try {
            // Simple validation - no current password required
            $validated = $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'password_confirmation' => ['required']
            ]);

            $user = $request->user();

            // Direct password update without checking current password
            $user->password = Hash::make($validated['password']);
            $user->save();

            Log::info('Password updated successfully for user: ' . $user->id, [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            return redirect()->route('profile.edit')
                ->with('status', 'password-updated')
                ->with('message', 'Password has been updated successfully!');
        } catch (ValidationException $e) {
            Log::warning('Password update validation failed', [
                'errors' => $e->errors(),
                'user_id' => $request->user()->id
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput($request->except(['password', 'password_confirmation']));
        } catch (\Exception $e) {
            Log::error('Password update error: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['general' => 'An error occurred while updating password. Please try again.']);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
