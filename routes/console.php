<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('du-lieu:kiem-tra', function () {
    $this->info('Lệnh kiểm tra dữ liệu sẽ được bổ sung ở giai đoạn API.');
})->purpose('Kiểm tra nhanh dữ liệu hệ thống nhà hàng');
