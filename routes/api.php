<?php

use App\Http\Controllers\Api\DatabaseOverviewController;
use Illuminate\Support\Facades\Route;

Route::get('/kiem-tra-du-lieu', DatabaseOverviewController::class);
