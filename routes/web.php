<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dedicated Order & Checkout Page
Route::get('/order', [OrderController::class, 'create'])->name('order.create');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

// Find / Lookup Order by Code
Route::get('/track', [OrderController::class, 'lookup'])->name('order.lookup');

// Interactive Payment & Status Tracking Page
Route::get('/track/{code}', [OrderController::class, 'track'])->name('order.track');
Route::post('/track/{code}/upload-proof', [OrderController::class, 'uploadProof'])->name('order.upload');
Route::post('/track/{code}/withdraw-proof', [OrderController::class, 'withdrawProof'])->name('order.withdraw');

// Admin Dashboard for Payment Verification & Order Flow
Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->name('admin.orders');
Route::post('/admin/orders/{code}/status', [OrderController::class, 'adminUpdateStatus'])->name('admin.orders.status');