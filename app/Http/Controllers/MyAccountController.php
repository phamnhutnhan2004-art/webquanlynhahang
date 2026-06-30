<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MyAccountController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $lockedRedirect = $this->redirectIfLocked($request);

        if ($lockedRedirect) {
            return $lockedRedirect;
        }

        return view('account.show', [
            'user' => $request->user()->load(['role', 'customer', 'employee']),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $lockedRedirect = $this->redirectIfLocked($request);

        if ($lockedRedirect) {
            return $lockedRedirect;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user)],
            'address' => ['nullable', 'string', 'max:255'],
        ], $this->messages());

        $user->update([
            'name' => $data['name'],
            'full_name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
        ]);

        $user->customer?->update([
            'full_name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
        ]);

        return back()->with('status', 'Đã cập nhật thông tin cá nhân.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $lockedRedirect = $this->redirectIfLocked($request);

        if ($lockedRedirect) {
            return $lockedRedirect;
        }

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->messages());

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', 'Đã đổi mật khẩu cá nhân.');
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'email' => ':attribute không đúng định dạng.',
            'unique' => ':attribute đã tồn tại trong hệ thống.',
            'confirmed' => 'Xác nhận :attribute không khớp.',
            'min' => ':attribute phải có ít nhất :min ký tự.',
            'attributes.name' => 'Họ và tên',
            'attributes.email' => 'Email',
            'attributes.phone' => 'Số điện thoại',
            'attributes.current_password' => 'Mật khẩu hiện tại',
            'attributes.password' => 'Mật khẩu mới',
        ];
    }

    private function redirectIfLocked(Request $request): ?RedirectResponse
    {
        if (! $request->user()?->isLocked()) {
            return null;
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors(['login' => 'Tài khoản của bạn đang bị khóa.']);
    }
}
