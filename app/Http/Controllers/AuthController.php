<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationOtpMail;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login', ['activeAuthTab' => 'login']);
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string'],
        ], $this->messages());

        $login = trim($data['login']);
        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials = [
            $loginField => $login,
            'password' => $data['password'],
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'Email, số điện thoại hoặc mật khẩu không đúng.']);
        }

        $user = Auth::user();

        if ($user?->isLocked()) {
            Auth::logout();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'Tài khoản của bạn đang bị khóa. Vui lòng liên hệ quản trị viên.']);
        }

        if ($user?->isCustomer() && ! $user->email_verified_at) {
            Auth::logout();
            $request->session()->regenerateToken();

            return redirect()
                ->route('verification.notice', ['email' => $user->email])
                ->withErrors(['login' => 'Vui lòng xác thực email trước khi đăng nhập.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath());
    }

    public function showRegister(): View
    {
        return view('auth.login', ['activeAuthTab' => 'register']);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->messages());

        try {
            $user = DB::transaction(function () use ($data): User {
                $user = User::create([
                    'name' => $data['name'],
                    'full_name' => $data['name'],
                    'email' => $data['email'],
                    'email_verified_at' => null,
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? null,
                    'password' => Hash::make($data['password']),
                    'role_id' => 3,
                ]);

                Customer::create([
                    'user_id' => $user->id,
                    'full_name' => $data['name'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'address' => $data['address'] ?? null,
                ]);

                $this->sendVerificationOtp($user);

                return $user;
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['email' => 'Không gửi được email xác thực. Vui lòng kiểm tra cấu hình SMTP và thử lại.']);
        }

        $request->session()->put('verification_email', $user->email);

        return redirect()
            ->route('verification.notice', ['email' => $user->email])
            ->with('status', 'Mã xác thực đã được gửi đến email của bạn.');
    }

    public function showVerifyEmail(Request $request): View|RedirectResponse
    {
        $email = (string) ($request->query('email') ?: $request->session()->get('verification_email'));

        if ($email === '') {
            return redirect()->route('register');
        }

        $request->session()->put('verification_email', $email);

        return view('auth.verify-email', ['email' => $email]);
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp_code' => ['required', 'digits:6'],
        ], $this->messages());

        $user = User::where('email', $data['email'])->firstOrFail();

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('status', 'Email đã được xác thực. Vui lòng đăng nhập.');
        }

        if (! $user->otp_code || ! $user->otp_expired_at || $user->otp_expired_at->isPast()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['otp_code' => 'Mã xác thực đã hết hạn. Vui lòng gửi lại mã xác thực.']);
        }

        if (! hash_equals($user->otp_code, $data['otp_code'])) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['otp_code' => 'Mã xác thực không đúng.']);
        }

        $user->forceFill([
            'email_verified_at' => Carbon::now(),
            'otp_code' => null,
            'otp_expired_at' => null,
        ])->save();

        $request->session()->forget('verification_email');

        return redirect()->route('login')->with('status', 'Xác thực email thành công. Vui lòng đăng nhập.');
    }

    public function resendVerificationOtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], $this->messages());

        $user = User::where('email', $data['email'])->firstOrFail();

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('status', 'Email đã được xác thực. Vui lòng đăng nhập.');
        }

        try {
            DB::transaction(function () use ($user): void {
                $this->sendVerificationOtp($user);
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Không gửi được mã xác thực. Vui lòng kiểm tra cấu hình SMTP và thử lại.']);
        }

        $request->session()->put('verification_email', $user->email);

        return back()->with('status', 'Mã xác thực mới đã được gửi đến email của bạn.');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): View
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], $this->messages());

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $data['email']],
            ['token' => Hash::make($token), 'created_at' => Carbon::now()]
        );

        return view('auth.reset-link', [
            'resetUrl' => route('password.reset', ['token' => $token, 'email' => $data['email']]),
        ]);
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->messages());

        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (! $record || ! Hash::check($data['token'], $record->token)) {
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ.']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu đã hết hạn.']);
        }

        User::where('email', $data['email'])->update([
            'password' => Hash::make($data['password']),
        ]);

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return redirect()->route('login')->with('status', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Đăng xuất thành công.');
    }

    private function sendVerificationOtp(User $user): void
    {
        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'otp_code' => $otpCode,
            'otp_expired_at' => Carbon::now()->addMinutes(5),
        ])->save();

        Mail::to($user->email)->send(new EmailVerificationOtpMail($user->name ?: $user->full_name, $otpCode));
    }

    private function redirectPath(): string
    {
        $user = Auth::user();

        if ($user?->isAdmin()) {
            return route('admin.dashboard');
        }

        if ($user?->isStaff()) {
            return route('staff.dashboard');
        }

        return route('customer.dashboard');
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'email' => ':attribute không đúng định dạng email.',
            'unique' => ':attribute đã tồn tại trong hệ thống.',
            'exists' => ':attribute không tồn tại trong hệ thống.',
            'confirmed' => 'Xác nhận :attribute không khớp.',
            'digits' => ':attribute phải gồm đúng :digits chữ số.',
            'min' => ':attribute phải có ít nhất :min ký tự.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'attributes.email' => 'Email',
            'attributes.login' => 'Email hoặc số điện thoại',
            'attributes.password' => 'Mật khẩu',
            'attributes.name' => 'Họ và tên',
            'attributes.phone' => 'Số điện thoại',
            'attributes.address' => 'Địa chỉ',
            'attributes.otp_code' => 'Mã xác thực',
        ];
    }
}
