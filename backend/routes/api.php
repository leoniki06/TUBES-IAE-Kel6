<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\LoanController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('me',     [AuthController::class, 'me']);
        Route::post('logout',[AuthController::class, 'logout']);
    });
});

Route::middleware('auth:api')->group(function () {

    // ✅ Books: semua user login boleh lihat
    Route::get('books',        [BookController::class, 'index']);
    Route::get('books/{book}', [BookController::class, 'show']);

    // ✅ Books: hanya librarian yang boleh CRUD
    Route::middleware('role:librarian')->group(function () {
        Route::post('books',          [BookController::class, 'store']);
        Route::put('books/{book}',    [BookController::class, 'update']);
        Route::delete('books/{book}', [BookController::class, 'destroy']);
    });

    // Transactions (sesuaikan kebutuhanmu)
    Route::get('transactions',                    [TransactionController::class, 'index']);
    Route::get('transactions/{transaction}',      [TransactionController::class, 'show']);
    Route::post('transactions/borrow',            [TransactionController::class, 'borrow']);
    Route::post('transactions/{transaction}/return',[TransactionController::class, 'returnBook']);

    // ✅ Members: hanya librarian
    Route::middleware('role:librarian')->group(function () {
        Route::get('members',                 [MemberController::class, 'index']);
        Route::post('members',                [MemberController::class, 'store']);
        Route::get('members/{member}',        [MemberController::class, 'show']);
        Route::put('members/{member}',        [MemberController::class, 'update']);
        Route::delete('members/{member}',     [MemberController::class, 'destroy']);
    });

    Route::get('/loans', [LoanController::class, 'index']);
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);

    Route::patch('/loans/{id}/approve', [LoanController::class, 'approve']);
    Route::patch('/loans/{id}/reject', [LoanController::class, 'reject']);
    Route::patch('/loans/{id}/checkout', [LoanController::class, 'checkout']);
    Route::patch('/loans/{id}/return', [LoanController::class, 'returnBook']);
    Route::patch('/loans/{id}/cancel', [LoanController::class, 'cancel']);
});
