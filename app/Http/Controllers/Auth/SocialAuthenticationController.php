<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthenticationController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google', 'facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        return Socialite::driver($provider)
            ->redirectUrl(route('social.callback', ['provider' => $provider]))
            ->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        $socialUser = Socialite::driver($provider)->user();

        Session::put('social_user', [
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'name' => $socialUser->getName() ?? $socialUser->getNickname(),
            'email' => $socialUser->getEmail(),
        ]);

        if (! $socialUser->getEmail()) {
            return redirect()->route('social.email');
        }

        return $this->loginOrCreateSocialUser(
            $request,
            $provider,
            $socialUser->getId(),
            $socialUser->getName() ?? $socialUser->getNickname(),
            $socialUser->getEmail(),
        );
    }

    public function connectRedirect(string $provider): RedirectResponse
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        return Socialite::driver($provider)
            ->redirectUrl(route('social.connect.callback', ['provider' => $provider]))
            ->redirect();
    }

    public function connectCallback(Request $request, string $provider): RedirectResponse
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        $socialUser = Socialite::driver($provider)->user();
        $user = $request->user();

        $existing = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->where('id', '<>', $user->id)
            ->first();

        if ($existing) {
            return redirect()->route('dashboard')->withErrors(['social' => 'This social account is already connected to another user.']);
        }

        if ($user->provider && $user->provider !== $provider) {
            return redirect()->route('dashboard')->withErrors(['social' => 'Your account is already connected to a different social provider.']);
        }

        $user->update([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
        ]);

        return redirect()->route('dashboard')->with('status', 'Connected your account with '.ucfirst($provider).'.');
    }

    public function emailForm(): View
    {
        return view('auth.social-email');
    }

    public function storeEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $socialUser = Session::get('social_user');

        if (! is_array($socialUser) || ! isset($socialUser['provider'], $socialUser['provider_id'])) {
            abort(400);
        }

        $socialUser['email'] = $request->input('email');
        Session::put('social_user', $socialUser);

        return $this->loginOrCreateSocialUser(
            $request,
            $socialUser['provider'],
            $socialUser['provider_id'],
            $socialUser['name'] ?? null,
            $socialUser['email'],
        );
    }

    private function loginOrCreateSocialUser(Request $request, string $provider, string $providerId, ?string $name, ?string $email): RedirectResponse
    {
        $user = User::firstOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $providerId,
            ],
            [
                'name' => $name ?? $email,
                'email' => $email,
                'password' => Hash::make(bin2hex(random_bytes(16))),
            ]
        );

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }
}
