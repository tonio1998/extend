<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Department;
use App\Models\SaaS\InstitutionSetting;
use App\Models\SaaS\Tenant;
use App\Models\SaaS\ThemeSetting;
use App\Models\User;
use App\Services\DSS\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dss.dashboard');
        }

        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        return view('auth.login', compact('institution', 'theme'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        // 1. Verify that user exists in the system
        $user = User::where('email', $credentials['email'])->first();
        if (! $user) {
            $msg = 'This account does not exist in the system. Access is restricted to registered institutional users.';

            if ($request->wantsJson()) {
                return response()->json([
                    'result' => false,
                    'message' => $msg,
                ], 422);
            }

            return back()->withErrors(['email' => $msg])->onlyInput('email');
        }

        // 2. Check if account is archived or inactive before attempting password
        $status = strtolower($user->status ?? 'active');
        if ($status === 'inactive' || $status === 'archived') {
            $msg = $status === 'archived'
                ? 'Your account has been archived by the Administrator. Access is restricted.'
                : 'Your account is currently inactive. Please contact your system administrator.';

            if ($request->wantsJson()) {
                return response()->json([
                    'result' => false,
                    'message' => $msg,
                ], 403);
            }

            return back()->withErrors(['email' => $msg])->onlyInput('email');
        }

        // 3. Authenticate credentials
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $authUser = Auth::user();
            if ($authUser instanceof User && $authUser->isDesignatedSuperAdmin()) {
                $authUser->ensureSuperAdminState();
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'result' => true,
                    'message' => 'Authenticated successfully! Redirecting to Executive Intelligence...',
                    'redirect' => route('dss.dashboard'),
                ]);
            }

            return redirect()->intended(route('dss.dashboard'));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'result' => false,
                'message' => 'Incorrect password. Please verify your credentials and try again.',
            ], 422);
        }

        return back()->withErrors([
            'email' => 'Incorrect password. Please verify your credentials and try again.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dss.dashboard');
        }

        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        return view('auth.register', compact('institution', 'theme'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'institution_name' => 'required|string|max:191',
            'school_acronym' => 'required|string|max:30',
            'school_type' => 'required|string|max:50',
            'campus_name' => 'required|string|max:191',
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:191|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|max:50',
        ]);

        $slug = Str::slug($validated['school_acronym'] ?: $validated['institution_name']);
        if (Tenant::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(4));
        }

        // 1. Create Tenant
        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'name' => $validated['institution_name'],
            'slug' => $slug,
            'plan' => 'Institutional Enterprise',
            'status' => 'active',
        ]);

        // 2. Create Institution Profile
        InstitutionSetting::create([
            'tenant_id' => $tenant->id,
            'school_name' => $validated['institution_name'],
            'school_acronym' => $validated['school_acronym'],
            'school_type' => $validated['school_type'],
            'current_fy' => date('Y'),
        ]);

        // 3. Create Default Theme
        ThemeSetting::create([
            'tenant_id' => $tenant->id,
            'primary_color' => '#047857',
            'secondary_color' => '#0284c7',
            'accent_color' => '#f59e0b',
            'sidebar_theme' => 'brand',
            'navbar_theme' => 'light',
            'border_radius' => '12px',
        ]);

        // 4. Create Main Campus
        $campus = Campus::create([
            'tenant_id' => $tenant->id,
            'campus_code' => 'MAIN',
            'campus_name' => $validated['campus_name'],
            'campus_type' => 'Main Campus',
            'status' => 'active',
        ]);

        // 5. Create Default Department
        $dept = Department::create([
            'tenant_id' => $tenant->id,
            'campus_id' => $campus->id,
            'dept_code' => 'OP',
            'dept_name' => 'Office of the President',
            'division' => 'Executive Management',
            'status' => 'active',
        ]);

        // 6. Create Super Admin User
        $user = User::create([
            'tenant_id' => $tenant->id,
            'campus_id' => $campus->id,
            'dept_id' => $dept->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?: 'SuperAdmin',
            'designation' => 'Institutional Executive Director',
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->wantsJson()) {
            return response()->json([
                'result' => true,
                'message' => 'Institution registered successfully! Welcome to IIPM-DSS.',
                'redirect' => route('dss.dashboard'),
            ]);
        }

        return redirect()->route('dss.dashboard')->with('success', 'Welcome to your IIPM-DSS institutional workspace!');
    }

    public function redirectToGoogle()
    {
        if (empty(config('services.google.client_id'))) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google Workspace SSO is not configured in .env yet. Please sign in with your institutional email and password.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Unable to authenticate with Google: '.$e->getMessage()]);
        }

        // 1. Check if user already exists with this google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        // 2. Or check by registered institutional email
        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();
            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar_url' => $googleUser->getAvatar(),
                ]);
            }
        }

        // 3. Do NOT allow login if not existed in the system
        if (! $user) {
            return redirect()->route('login')->withErrors([
                'email' => "The Google account ({$googleUser->getEmail()}) does not exist in our institutional records. Access is restricted to registered personnel only.",
            ]);
        }

        // 4. Verify account status
        $status = strtolower($user->status ?? 'active');
        if ($status === 'inactive' || $status === 'archived') {
            $msg = $status === 'archived'
                ? 'Your account has been archived by the Administrator. Access is restricted.'
                : 'Your account is currently inactive. Please contact your system administrator.';

            return redirect()->route('login')->withErrors(['email' => $msg]);
        }

        Auth::login($user, true);

        if ($user->isDesignatedSuperAdmin()) {
            $user->ensureSuperAdminState();
        }

        request()->session()->regenerate();

        return redirect()->route('dss.dashboard')->with('success', 'Successfully signed in with Google Workspace!');
    }

    public function showForgotPassword()
    {
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        return view('auth.forgot-password', compact('institution', 'theme'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
