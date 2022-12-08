<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DetaiController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TravelPackageController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['preventBack'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get("/travel-packages/list", [TravelPackageController::class, "frontIndex"])->name("travel-packages.front.index");
        Route::get('/travel-packages/detail/{slug}', [TravelPackageController::class, 'frontShow'])->name('travel-packages.front.detail');
        Route::get('/terms-conditions', function () {
            return view('pages.frontend.terms-conditions');
        })->name('terms-conditions');
        Route::get('/contact-us', [ContactController::class, 'index'])->name('contact');

        Auth::routes(['verify' => true]);

        Route::middleware(['auth'])
            ->group(function () {

                Route::middleware(['verified', 'hasFullProfile'])
                    ->group(function () {
                        Route::get('/checkout/success/', [CheckoutController::class, 'success'])
                            ->name('checkout.success');
                        Route::get('/checkout/{transaction:invoice_number}', [CheckoutController::class, 'index'])->name('checkout.index');
                        Route::post('/checkout/proccess/{travel_package:slug}', [CheckoutController::class, 'proccess'])
                            ->name('checkout.proccess');
                        Route::post('/checkout/create/{transaction:invoice_number}', [CheckoutController::class, 'create'])
                            ->name('checkout.create');
                        Route::delete('/checkout/remove/{transaction:invoice_number}/{transaction_detail:username}', [CheckoutController::class, 'delete'])
                            ->name('checkout.remove');
                        Route::delete('/checkout/cancel/{transaction:invoice_number}', [CheckoutController::class, 'cancel'])
                            ->name('checkout.cancel');
                        Route::post('/checkout/send-mail/{transaction:invoice_number}', [CheckoutController::class, 'sendMail'])
                            ->name('checkout.send-mail');
                        require __DIR__ . '/adminRoutes.php';
                    });

                Route::get('/profile', [ProfileController::class, 'frontProfile'])->name('front-profile');
                Route::patch('/profile', [ProfileController::class, 'updateProfile'])->name('update-profile');
                Route::post("/profile/delete-avatar", [ProfileController::class, "deleteAvatar"])->name("delete-avatar");
                Route::get("/profile/change-password", [ProfileController::class, "frontChangePassword"])->name("front-change-password");
                Route::patch("/profile/update-password", [ProfileController::class, "updatePassword"])->name("update-password");
                Route::get("/profile/detail/{user:username}/{invoice_number?}", [UserController::class, "show"])->name("detail-profile");
            });
    });

// NOTE:
// - Integrasi Halaman Utama (Munculkan data2 pada halaman frontend) buat sebaik mungkin. Contoh buat lazy load pada halaman daftar travel package, munculkan 4 data travel package pada halaman home berdasarkan sorting/jumlah pemesanan terbanyak pada travel package, pertimbangkan fitur testimonial, terkahir cek pada halaman checkout apakah ada perlu ditambah/diperbaiki atau tidak.
// - Ikuti tahap/step selanjutnya pada materi video
