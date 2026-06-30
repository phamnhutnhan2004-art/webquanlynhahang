<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminAccountController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedAccountData($request);

        DB::transaction(function () use ($data): void {
            $user = User::create([
                'name' => $data['name'],
                'full_name' => $data['name'],
                'email' => $data['email'],
                'email_verified_at' => Carbon::now(),
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'password' => Hash::make($data['password']),
                'role_id' => $data['role_id'],
                'status' => $data['status'],
            ]);

            $this->syncRoleProfile($user, $data);
        });

        return back()->with('status', 'Đã thêm tài khoản mới.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validatedAccountData($request, $user);

        if ((int) $user->role_id === 1 && (int) $data['role_id'] !== 1) {
            $this->ensureAnotherActiveAdmin($user, 'Không thể hạ cấp admin duy nhất trong hệ thống.');
        }

        if ((int) $user->role_id === 1 && $data['status'] === 'tạm khóa') {
            $this->ensureAnotherActiveAdmin($user, 'Không thể khóa admin duy nhất đang hoạt động.');
        }

        DB::transaction(function () use ($user, $data): void {
            $user->update([
                'name' => $data['name'],
                'full_name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'role_id' => $data['role_id'],
                'status' => $data['status'],
            ]);

            $this->syncRoleProfile($user->fresh(['customer', 'employee']), $data);
        });

        return back()->with('status', 'Đã cập nhật tài khoản.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['đang hoạt động', 'tạm khóa'])],
        ], $this->messages());

        if ((int) $user->role_id === 1 && $data['status'] === 'tạm khóa') {
            $this->ensureAnotherActiveAdmin($user, 'Không thể khóa admin duy nhất đang hoạt động.');
        }

        $user->update(['status' => $data['status']]);

        return back()->with('status', $data['status'] === 'tạm khóa' ? 'Đã khóa tài khoản.' : 'Đã mở khóa tài khoản.');
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->messages());

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', 'Đã đổi mật khẩu tài khoản.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'send_email' => ['nullable', 'boolean'],
        ], $this->messages());

        $plainPassword = $data['password'] ?: Str::password(12);

        $user->update(['password' => Hash::make($plainPassword)]);

        if ($request->boolean('send_email')) {
            try {
                Mail::raw(
                    "Xin chào {$user->name},\n\nMật khẩu mới của bạn là:\n\n{$plainPassword}\n\nVui lòng đăng nhập và đổi lại mật khẩu để bảo mật tài khoản.\n\nNhà hàng Hoa Sen.",
                    fn ($message) => $message
                        ->to($user->email)
                        ->subject('Mật khẩu mới - Nhà hàng Hoa Sen')
                );
            } catch (Throwable $exception) {
                report($exception);

                return back()->withErrors(['email' => "Đã đổi mật khẩu nhưng chưa gửi được email. Mật khẩu mới: {$plainPassword}"]);
            }
        }

        return back()->with('status', $request->boolean('send_email')
            ? 'Đã đặt lại mật khẩu và gửi email cho người dùng.'
            : "Đã đặt lại mật khẩu. Mật khẩu mới: {$plainPassword}");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) $user->role_id === 1) {
            $this->ensureAnotherActiveAdmin($user, 'Không thể xóa admin duy nhất trong hệ thống.');
        }

        if (auth()->id() === $user->id) {
            return back()->withErrors(['account' => 'Bạn không thể tự xóa tài khoản đang đăng nhập.']);
        }

        $user->delete();

        return back()->with('status', 'Đã xóa tài khoản.');
    }

    private function validatedAccountData(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user)],
            'address' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', Rule::in([1, 2, 3])],
            'status' => ['required', Rule::in(['đang hoạt động', 'tạm khóa'])],
            'password' => $passwordRules,
            'employee_code' => ['nullable', 'string', 'max:30', Rule::unique('employees', 'employee_code')->ignore($user?->employee)],
            'position' => ['nullable', 'string', 'max:80'],
            'shift' => ['nullable', 'string', 'max:80'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date'],
        ], $this->messages());
    }

    private function syncRoleProfile(User $user, array $data): void
    {
        if ((int) $data['role_id'] === 3) {
            Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => $data['name'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'address' => $data['address'] ?? null,
                ]
            );
        }

        if ((int) $data['role_id'] === 2) {
            $employee = $user->employee;

            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'employee_code' => ($data['employee_code'] ?? null) ?: ($employee?->employee_code ?: 'NV'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT)),
                    'position' => ($data['position'] ?? null) ?: ($employee?->position ?: 'Nhân viên'),
                    'shift' => ($data['shift'] ?? null) ?: ($employee?->shift ?: 'Ca linh hoạt'),
                    'salary' => $data['salary'] ?? $employee?->salary ?? 0,
                    'hire_date' => $data['hire_date'] ?? $employee?->hire_date?->toDateString() ?? Carbon::today()->toDateString(),
                    'status' => $data['status'] === 'tạm khóa' ? 'tạm nghỉ' : 'đang làm',
                ]
            );
        }
    }

    private function ensureAnotherActiveAdmin(User $user, string $message): void
    {
        $activeAdmins = User::query()
            ->where('role_id', 1)
            ->where('status', 'đang hoạt động')
            ->whereKeyNot($user->id)
            ->count();

        if ($activeAdmins < 1) {
            throw ValidationException::withMessages(['account' => $message]);
        }
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'email' => ':attribute không đúng định dạng.',
            'unique' => ':attribute đã tồn tại trong hệ thống.',
            'confirmed' => 'Xác nhận :attribute không khớp.',
            'min' => ':attribute phải có ít nhất :min ký tự.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'attributes.name' => 'Họ và tên',
            'attributes.email' => 'Email',
            'attributes.phone' => 'Số điện thoại',
            'attributes.password' => 'Mật khẩu',
            'attributes.role_id' => 'Vai trò',
            'attributes.status' => 'Trạng thái',
        ];
    }
}
