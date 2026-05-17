<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!Auth::check()) return redirect()->route('login');
    $user = Auth::user();
    if ($user->isSuperAdmin()) return redirect()->route('superadmin.dashboard');
    if ($user->isAdmin())      return redirect()->route('admin.dashboard');
    return redirect()->route('user.dashboard');
});

Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login',     [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',    [LoginController::class, 'login']);
Route::post('/logout',   [LoginController::class, 'logout'])->name('logout');
Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// ── User routes ──────────────────────────────────────────────────
Route::middleware(['auth', 'track.activity'])->prefix('user')->group(function () {
    Route::get('/dashboard',        [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/profile',          [UserController::class, 'editProfile'])->name('user.profile');
    Route::patch('/profile',        [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::patch('/password',       [UserController::class, 'updatePassword'])->name('user.password.update');
    Route::get('/wallet',           [WalletController::class, 'index'])->name('user.wallet');
    Route::get('/deposit',          [WalletController::class, 'depositForm'])->name('user.deposit.form');
    Route::post('/deposit',         [WalletController::class, 'depositSubmit'])->name('user.deposit.submit');
    Route::get('/deposit/history',  [WalletController::class, 'depositHistory'])->name('user.deposit.history');
    Route::get('/deposit/{deposit}', [WalletController::class, 'depositDetail'])->name('user.deposit.detail');
    Route::get('/withdraw',         [WithdrawalController::class, 'showForm'])->name('user.withdraw.form');
    Route::post('/withdraw',        [WithdrawalController::class, 'store'])->name('user.withdraw.store');
    Route::get('/withdraw/history', [WithdrawalController::class, 'history'])->name('user.withdraw.history');
    Route::get('/transactions',     [TransactionController::class, 'index'])->name('user.transactions');
    Route::get('/kyc',              [KycController::class, 'show'])->name('user.kyc');
    Route::post('/kyc',             [KycController::class, 'store'])->name('user.kyc.store');
    // Trading
    Route::get('/trade',             [TradeController::class, 'index'])->name('user.trade');
    Route::post('/trade/buy',        [TradeController::class, 'buy'])->name('user.trade.buy');
    Route::post('/trade/sell',       [TradeController::class, 'sell'])->name('user.trade.sell');
    Route::get('/trade/api/{coin}',  [TradeController::class, 'apiState'])->name('user.trade.api');
    Route::post('/trade/leverage/open',          [TradeController::class, 'leverageOpen'])->name('user.leverage.open');
    Route::post('/trade/leverage/{lt}/close',    [TradeController::class, 'leverageClose'])->name('user.leverage.close');
});

// ── Admin routes ─────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard',                          [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/notifications',                      [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::post('/notifications/read',                [AdminController::class, 'markNotificationsRead'])->name('admin.notifications.read');
    Route::get('/users',                              [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{user}',                       [AdminController::class, 'userDetail'])->name('admin.users.detail');
    Route::delete('/users/{user}',                    [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/deposits',                           [AdminController::class, 'deposits'])->name('admin.deposits');
    Route::get('/deposits/{deposit}',                 [AdminController::class, 'depositDetail'])->name('admin.deposits.detail');
    Route::patch('/deposits/{deposit}/approve',       [AdminController::class, 'approveDeposit'])->name('admin.deposits.approve');
    Route::patch('/deposits/{deposit}/reject',        [AdminController::class, 'rejectDeposit'])->name('admin.deposits.reject');
    Route::get('/withdrawals',                        [AdminController::class, 'withdrawals'])->name('admin.withdrawals');
    Route::patch('/withdrawals/{withdrawal}/approve', [AdminController::class, 'approveWithdrawal'])->name('admin.withdrawals.approve');
    Route::patch('/withdrawals/{withdrawal}/reject',  [AdminController::class, 'rejectWithdrawal'])->name('admin.withdrawals.reject');
    Route::get('/kyc',                                [AdminController::class, 'kyc'])->name('admin.kyc');
    Route::patch('/kyc/{kyc}/approve',                [AdminController::class, 'approveKyc'])->name('admin.kyc.approve');
    Route::patch('/kyc/{kyc}/reject',                 [AdminController::class, 'rejectKyc'])->name('admin.kyc.reject');

    Route::get('/users/{user}/credit',  [AdminController::class, 'creditForm'])->name('admin.users.credit');
    Route::post('/users/{user}/credit', [AdminController::class, 'creditSubmit'])->name('admin.users.credit.submit');
    // Trading
    Route::get('/trade',                            [TradeController::class, 'adminIndex'])->name('admin.trade');
    Route::get('/trade/api/{coin}',                 [TradeController::class, 'apiState'])->name('admin.trade.api');
    Route::post('/trade/market',                    [TradeController::class, 'updateMarket'])->name('admin.trade.market');
    Route::post('/trade/reset',                     [TradeController::class, 'resetMarket'])->name('admin.trade.reset');
    Route::get('/trade/leverage',                   [TradeController::class, 'adminLeverageIndex'])->name('admin.leverage.index');
    Route::post('/trade/order-book',                [TradeController::class, 'createOrderBook'])->name('admin.trade.order-book');
    Route::post('/trade/order-book/{order}/cancel', [TradeController::class, 'cancelOrderBook'])->name('admin.trade.order-book.cancel');
    Route::post('/trade/{trade}/fill-buy',          [TradeController::class, 'fillBuy'])->name('admin.trade.fill-buy');
    Route::post('/trade/{trade}/fill-sell',         [TradeController::class, 'fillSell'])->name('admin.trade.fill-sell');
    Route::post('/trade/{trade}/cancel',            [TradeController::class, 'cancelTrade'])->name('admin.trade.cancel');
});

// ── Super Admin routes ───────────────────────────────────────────
Route::middleware(['auth', 'super_admin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard',              [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/admins',                 [SuperAdminController::class, 'admins'])->name('superadmin.admins');
    Route::patch('/users/{user}/promote', [SuperAdminController::class, 'promoteToAdmin'])->name('superadmin.promote');
    Route::patch('/users/{user}/demote',  [SuperAdminController::class, 'demoteToUser'])->name('superadmin.demote');
    Route::get('/notifications',          [AdminController::class, 'notifications'])->name('superadmin.notifications');
    Route::post('/notifications/read',    [AdminController::class, 'markNotificationsRead'])->name('superadmin.notifications.read');
    Route::get('/users/{user}/credit',  [AdminController::class, 'creditForm'])->name('superadmin.users.credit');
    Route::post('/users/{user}/credit', [AdminController::class, 'creditSubmit'])->name('superadmin.users.credit.submit');
});
