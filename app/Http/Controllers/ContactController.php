<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
            'attachment' => ['nullable', 'image', 'max:4096'],
        ], [
            'required' => ':attribute là bắt buộc.',
            'email' => ':attribute không đúng định dạng.',
            'image' => ':attribute phải là tệp hình ảnh.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'attachment.max' => 'Hình ảnh đính kèm không được vượt quá 4MB.',
            'attributes.full_name' => 'Họ và tên',
            'attributes.phone' => 'Số điện thoại',
            'attributes.address' => 'Địa chỉ',
            'attributes.email' => 'Email',
            'attributes.message' => 'Nội dung',
            'attributes.attachment' => 'Hình ảnh đính kèm',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('contacts', 'public');
        }

        Contact::create($data);

        return back()->with('status', 'Cảm ơn anh/chị đã liên hệ. Nhà hàng Hoa Sen sẽ phản hồi trong thời gian sớm nhất.');
    }
}
