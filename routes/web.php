<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Models\Plan;
use App\Http\Controllers\{
    AdminController,
    DepositController,
    HomeController,
    InvestmentController,
    PlanController,
    ProfileController,
    UserController,
    WalletController,
    UserCardController,
    Auth\LoginController,
    Auth\ResetPasswordController,
    Auth\ForgotPasswordController,
    CopyTradingController,
    MessageController,
    StrategyController,
    UserAuthController,
    UserKycController,
    WithdrawalController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC ROUTES ====================
Route::get('/', function () {
    $plans = Cache::remember('homepage_plans', 60, function () {
        return Plan::latest()->get();
    });

    return view('welcome', compact('plans'));
});

// Auth scaffolding
Auth::routes();

// ==================== LANGUAGE ====================
Route::post('/set-language', function (Request $request) {
    session(['locale' => $request->lang]);
    return response()->json(['status' => 'ok']);
});



// ==================== AUTH PAGES PREFIX ====================
Route::prefix('auth')->group(function () {
    // Auth pages
    Route::get('/signup', [UserController::class, 'signup'])->name('signup');
    Route::get('/planlists', [UserController::class, 'products'])->name('plans.header');
    Route::get('/about-us', [UserController::class, 'About_Us'])->name('about.us');
    Route::get('/affiliates', [UserController::class, 'affiliates'])->name('affiliates');
    Route::get('/ContactUs', [UserController::class, 'contactus'])->name('contact.us');
    Route::get('/payouts', [UserController::class, 'payouts'])->name('payouts');
    Route::get('/helpdesk', [UserController::class, 'helpdesk'])->name('helpdesk');

    /* =========================
       EDUCATION / LEARN HUB
    ==========================*/
    Route::get('/education/funding', [UserController::class, 'education_funding'])->name('education.funding');
    Route::get('/education/evaluations', [UserController::class, 'education_evaluations'])->name('education.evaluations');
    Route::get('/education/prop-firms', [UserController::class, 'education_prop_firms'])->name('education.prop-firms');
    Route::get('/education/trailing-drawdown', [UserController::class, 'education_trailing_drawdown'])->name('education.trailing-drawdown');
    Route::get('/education/consistency-rules', [UserController::class, 'education_consistency_rules'])->name('education.consistency-rules');
    Route::get('/education/challenge-cost-vs-risk', [UserController::class, 'education_challenge_cost_vs_risk'])->name('education.challenge-cost-vs-risk');
    Route::get('/education/psychology1', [UserController::class, 'education_psychology1'])->name('education.psychology1');
    Route::get('/education/psychology2', [UserController::class, 'education_psychology2'])->name('education.psychology2');
    Route::get('/education/make-living-trading', [UserController::class, 'education_make_living_trading'])->name('education.make-living-trading');

    /* =========================
       COMPANY
    ==========================*/
    Route::get('/press', [UserController::class, 'press'])->name('press');
    Route::get('/terms-privacy', [UserController::class, 'terms_privacy'])->name('terms.privacy');

    // contact us form submission
    Route::post('/contact/send', [UserController::class, 'send'])->name('user.contact.send');
});

// ==================== AUTHENTICATION ROUTES ====================
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

// ==================== PASSWORD RESET & OTP ROUTES ====================
Route::get('password/reset', function () {
    return redirect()->route('password.otp.request');
})->name('password.request');

// OTP password reset routes
Route::get('password/forgot', [ForgotPasswordController::class, 'showOtpRequestForm'])->name('password.otp.request');
Route::post('password/otp-send', [ForgotPasswordController::class, 'sendOtp'])->name('password.otp.send');
Route::get('password/otp-verify', [ForgotPasswordController::class, 'showOtpVerifyForm'])->name('password.otp.verify.form');
Route::post('password/otp-verify', [ForgotPasswordController::class, 'verifyOtpAndReset'])->name('password.otp.verify');
Route::get('/password/verify-otp', fn() => view('auth.passwords.otp_verify'))->name('password.otp.form');

// OTP verification routes
Route::get('/verify-otp/{token}', [UserController::class, 'showVerifyOtpForm'])->name('verify.otp');
Route::post('/verify-otp', [UserController::class, 'submitOtp'])->name('otp.submit');
Route::post('/resend-otp', [UserController::class, 'resendOtp'])->name('otp.resend');

// ==================== USER CREATION ====================
Route::post('/create-user', [UserController::class, 'createUser'])->name('user.create');

// Registration success and additional info
Route::get('/registration/success/{user}', [UserController::class, 'registrationSuccess'])->name('registration.success');
Route::get('/additional-info', [UserController::class, 'showAdditionalInfo'])->name('user.additional.info');
Route::post('/additional-info/save', [UserController::class, 'saveAdditionalInfo'])->name('user.additional.info.save');
Route::get('/registration/complete', [UserController::class, 'registrationComplete'])->name('registration.complete');
// user psychology
Route::get('/user/psychology', [UserController::class, 'psychology'])->name('user.psychology');

// AJAX route for admin search // Copy Trading Admin Routes
Route::get('/get-admins', [UserController::class, 'getAdmins'])->name('get.admins');
Route::post('/change-admin', [UserController::class, 'changeAdmin'])
    ->name('copy-trading.change-admin');
// ==================== OVERLAY CONTROL ====================
Route::middleware('auth')->group(function () {
    // Hide overlay when user clicks close
    Route::post('/hide-overlay', [UserController::class, 'hideOverlay'])->name('user.hide-overlay');
});

// ==================== AUTH PROTECTED ROUTES ====================
Route::middleware(['auth'])->group(function () {

    // ==================== HOME & COMMON ====================
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/signout', [HomeController::class, 'signout'])->name('signout');
    Route::post('/user/take-profit', [UserController::class, 'takeProfit'])->name('user.take-profit');
    Route::get('/user/notifications', fn() => view('dashboard.user.notifications'))->name('user.notifications');
    Route::get('/rules&guildlines', [UserController::class, 'rules_regulations'])->name('rules.regulations');
    Route::get('/discordcommunitycopyingtrades', [UserController::class, 'all_server'])->name('allserver');
    
    Route::get('/terms-and-privacy-policy', [planController::class, 'termsprivacy'])->name('termsprivacy');
    Route::get('/membership-locked', [UserController::class, 'lockedPage'])->name('membership.locked');
    Route::get('/check-membership-status', [UserController::class, 'checkMembership'])->name('check.membership');



    // Dismiss banner
    Route::post('/dismiss-banner', function () {
        session(['dismissed_zero_investment_banner' => true]);
        return response()->json(['success' => true]);
    })->name('user.dismiss-banner');

    // ==================== PROFILE ROUTES ====================
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'userProfile'])->name('profile.show');
        Route::put('/', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });

    // ==================== INVESTMENT ROUTES ====================
    Route::get('/investments', [InvestmentController::class, 'index'])->name('user.investments');
    Route::get('/withdrawn-investments', [InvestmentController::class, 'withdrawnInvestments'])->name('user.withdrawn.investments');
    Route::post('/user/investments/{id}/withdraw', [InvestmentController::class, 'withdraw'])->name('investments.withdraw');
    Route::post('/investments/{id}/take-profit', [InvestmentController::class, 'takeProfit'])->name('investments.takeProfit');



    // ==================== INVESTMENT ROUTES ====================
Route::get('/investments', [InvestmentController::class, 'index'])->name('user.investments');
Route::get('/withdrawn-investments', [InvestmentController::class, 'withdrawnInvestments'])->name('user.withdrawn.investments');
Route::post('/user/investments/{id}/withdraw', [InvestmentController::class, 'withdraw'])->name('investments.withdraw');
Route::post('/user/investments/{id}/early-withdraw', [InvestmentController::class, 'earlyWithdraw'])->name('investments.earlyWithdraw');
Route::post('/investments/{id}/take-profit', [InvestmentController::class, 'takeProfit'])->name('investments.takeProfit');
    // ==================== USER PREFIX ROUTES ====================
    Route::prefix('user')->group(function () {
        // Dashboard
        Route::get('/dashboard', [UserController::class, 'user_dashboard'])->name('user_dashboard');

        // Live trading
        Route::get('/live', [UserController::class, 'user_live'])->name('user_live');
        Route::post('/activate-membership', [UserController::class, 'activateMembership'])->name('user.activate.membership');
        Route::post('/live-trading/start', [UserController::class, 'liveTradingStart'])->name('live.trading.start');
        Route::post('/live-trading/update', [UserController::class, 'liveTradingUpdate'])->name('live.trading.update');
        Route::post('/live-trading/claim', [UserController::class, 'liveTradingClaim'])->name('live.trading.claim');
        Route::post('/live-trading/message', [UserController::class, 'liveTradingSendMessage'])->name('live.trading.message');
        Route::get('/live-trading/messages', [UserController::class, 'liveTradingGetMessages'])->name('live.trading.messages');

        // Deposit routes
        Route::controller(DepositController::class)->group(function () {
            Route::get('/deposit', 'userDeposit')->name('user.deposit');
            Route::post('/make-deposit', 'userMakeDeposit')->name('user.make-deposit');
            Route::get('/confirm-deposit', 'confirmDeposit')->name('deposit.confirm');
            Route::post('/submit-deposit', 'submitDeposit')->name('deposit.submit');
            Route::get('/deposit-history', 'depositHistory')->name('user.deposit-history');
            

        });
Route::post('/deposit/giftcard', [DepositController::class, 'submitGiftCard'])->name('deposit.giftcard.submit');

        // Reinvestment
        Route::post('/initiate-reinvestment', [UserController::class, 'initiateReinvestment'])->name('initiate.reinvestment');

     

        // Plans
        Route::get('/planlist', [PlanController::class, 'plan_dashboard'])->name('plan.dashboard');

        // Withdrawals
        Route::get('/withdraw/form', [WithdrawalController::class, 'showWithdrawForm'])->name('user.withdraw.form');
        Route::post('/withdraw/request', [WithdrawalController::class, 'submitWithdrawRequest'])->name('balance.withdraw.request');
        Route::get('/withdrawals/list', [WithdrawalController::class, 'withdrawalList'])->name('user.withdrawals.list');

        
         Route::post('/withdrawal/calculate-fees', [WithdrawalController::class, 'calculateFees'])->name('withdrawal.calculate-fees');


        Route::prefix('withdrawals')->controller(WithdrawalController::class)->group(function () {
            Route::get('/', 'index')->name('withdrawals.index');
            Route::post('/generate-card', 'generateCard')->name('withdrawals.generateCard');
            Route::get('/view-card', 'viewCard')->name('withdrawals.view-card');
        });

    
    });

    // Withdraw from balance
    Route::post('/dashboard/withdraw', [WithdrawalController::class, 'withdrawFromBalance'])->name('user.balance.withdraw');

    // ==================== KYC ROUTES ====================
    Route::middleware(['auth'])->group(function () {
        Route::get('/user/kyc', [UserKycController::class, 'create'])->name('user.kyc.upload');
        Route::post('/user/kyc', [UserKycController::class, 'store'])->name('user.kyc.submit');
    });

    // Dismiss KYC alert
    Route::post('/id-verification/alert-dismiss', [UserKycController::class, 'dismissAlert'])->name('user.kyc.dismiss-alert');

    // ==================== PLANS MANAGEMENT ====================
    Route::prefix('plans')->group(function () {
        Route::get('/', [PlanController::class, 'planList'])->name('plan.list');
        Route::get('/create', [PlanController::class, 'addPlan'])->name('create_plan');
        Route::post('/store', [PlanController::class, 'store'])->name('plans.store');
        Route::get('/edit/{id}', [PlanController::class, 'editPlan'])->name('plan.edit');
        Route::post('/update/{id}', [PlanController::class, 'updatePlan'])->name('plan.update');
        Route::get('/delete/{id}', [PlanController::class, 'deletePlan'])->name('plan.delete');
    });

    // ==================== WALLETS MANAGEMENT ====================
    Route::prefix('wallets')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('wallet.index');
        Route::get('/create', [WalletController::class, 'addWallet'])->name('create_wallet');
        Route::post('/store', [WalletController::class, 'storeWallet'])->name('wallet.create');
        Route::delete('/{id}', [WalletController::class, 'destroy'])->name('wallet.delete');
        Route::post('/generate-wallets', [WalletController::class, 'generate'])
    ->name('user.wallets.generate');
    });

    // ==================== ADMIN ROUTES ====================
    Route::prefix('admin')->middleware('isAdmin')->group(function () {
        // Admin dashboard
        Route::get('/dashboard', [AdminController::class, 'admin_dashboard'])->name('admin_dashboard');
        
        // Admin profile
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

        // Messages
        Route::get('/messages', [AdminController::class, 'index'])->name('admin.messages.index');
        Route::delete('/messages/{message}', [AdminController::class, 'destroy'])->name('admin.messages.destroy');

        // Membership
        Route::post('/generate-membership-code', [AdminController::class, 'generateMembershipCode'])->name('admin.generate.membership.code');
        Route::patch('/membership/lock/{user}', [AdminController::class, 'toggleMembershipLock'])->name('admin.membership.lock');

        // Send message to user
        Route::get('/user/{id}/message', function ($id) {
            $user = \App\Models\User::findOrFail($id);
            return view('admin.send-message', compact('user'));
        })->name('admin.user.message.form');
        Route::post('/send-message', [AdminController::class, 'sendMessage'])->name('admin.send.message');

        // Server feeds
        Route::get('/server-feeds', [AdminController::class, 'serverindex'])->name('admin.feeds');
        Route::post('/server-feeds', [AdminController::class, 'store'])->name('admin.feeds.store');
        Route::put('/server-feeds/{id}', [AdminController::class, 'update'])->name('admin.feeds.update');
        Route::delete('/server-feeds/{id}', [AdminController::class, 'deleteServerFeed'])->name('admin.feeds.delete');
        Route::get('/server-feeds/{id}/edit', [AdminController::class, 'serveredit'])->name('admin.feeds.edit');

    // This creates URLs like: /admin/payouts
    Route::get('/payouts', [AdminController::class, 'payoutIndex'])->name('admin.payouts.index');
    // This creates URLs like: /admin/payouts/create  
    Route::get('/payouts/create', [AdminController::class, 'payoutcreate'])->name('admin.payouts.create');
    // This creates URLs like: /admin/payouts (POST request)
    Route::post('/payouts', [AdminController::class, 'payoutstore'])->name('admin.payouts.store');
    // This creates URLs like: /admin/payouts/5/edit
    Route::get('/payouts/{payout}/edit', [AdminController::class, 'payoutedit'])->name('admin.payouts.edit');
    // This creates URLs like: /admin/payouts/5 (PUT request)
    Route::put('/payouts/{payout}', [AdminController::class, 'payoutupdate'])->name('admin.payouts.update');
    // This creates URLs like: /admin/payouts/5 (DELETE request)
    Route::delete('/payouts/{payout}', [AdminController::class, 'payoutdestroy'])->name('admin.payouts.destroy');
    // This creates URLs like: /admin/payouts/quick-add (POST request)
    Route::post('/payouts/quick-add', [AdminController::class, 'quickAdd'])->name('admin.payouts.quick-add');

    });

    // ==================== ADMIN USERS MANAGEMENT ====================
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminController::class, 'userIndex'])->name('user.index');
        Route::get('/hidden', [AdminController::class, 'hiddenuser'])->name('hidden.user');
        Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('user.edit');
        Route::put('/{id}/update-balance', [AdminController::class, 'updateBalance'])->name('admin.users.updateBalance');
        Route::delete('/{id}', [AdminController::class, 'userDestroy'])->name('user.destroy');
    });

    // ==================== ADMIN DEPOSITS MANAGEMENT ====================
    Route::prefix('deposits')->controller(AdminController::class)->group(function () {
        Route::get('/pending', 'pendingDeposits')->name('admin.deposits.pending');
        Route::get('/approved', 'approvedDeposits')->name('admin.deposits.approved');
        Route::post('/approve/{id}', 'approveDeposit')->name('admin.approve.deposit');
        Route::delete('/reject/{id}', 'rejectDeposit')->name('admin.reject.deposit');
    });

    // ==================== ADMIN WITHDRAWALS MANAGEMENT ====================
    Route::prefix('withdrawals')->group(function () {
        Route::get('/pending', [AdminController::class, 'adminViewWithdrawals'])->name('withdrawals.pending');
        Route::get('/approved', [AdminController::class, 'showApprovedWithdrawals'])->name('admin.withdrawals.approved');
        Route::post('/{id}/approve', [AdminController::class, 'approveBalanceWithdrawal'])->name('admin.approve.withdrawal');
        Route::post('/{id}/reject', [AdminController::class, 'rejectBalanceWithdrawal'])->name('admin.withdraw.reject');
        Route::post('/{id}/unapprove', [AdminController::class, 'unapproveBalanceWithdrawal'])->name('withdraw.unapprove');
        Route::delete('/{id}', [AdminController::class, 'withdrawaldestroy'])->name('withdraw.delete');
    });

    // ==================== ADMIN KYC MANAGEMENT ====================
   // ==================== ADMIN KYC MANAGEMENT ====================
Route::prefix('kyc')->group(function () {
    Route::get('/', [AdminController::class, 'kycindex'])->name('admin.kyc.index');
    Route::patch('/{id}/approve', [AdminController::class, 'approve'])->name('admin.kyc.approve');
    Route::patch('/{id}/reject', [AdminController::class, 'reject'])->name('admin.kyc.reject');
});


// web.php
// Copy Trading Routes (User)
Route::prefix('copy-trading')->name('copy-trading.')->group(function () {
    Route::get('/', [CopyTradingController::class, 'index'])->name('index');
    Route::post('/store', [CopyTradingController::class, 'store'])->name('store');
    Route::get('/history', [CopyTradingController::class, 'history'])->name('history');
    Route::post('/cancel/{id}', [CopyTradingController::class, 'cancel'])->name('cancel');
      Route::get('/check-plan-limit/{planId}', [CopyTradingController::class, 'checkPlanLimit'])->name('check.plan.limit');
// In web.php - Update the routes



});


Route::prefix('admin/copy-trading')->name('admin.copy-trading.')->group(function () {
    // Main view for all requests (unified tab view)
    Route::get('/all', [AdminController::class, 'pendingCopyRequests'])->name('pendingCopyRequests');
    Route::get('/pending', [AdminController::class, 'copypending'])->name('pending');
    Route::get('/approved', [AdminController::class, 'copyapproved'])->name('approved');
    Route::get('/rejected', [AdminController::class, 'copyrejected'])->name('rejected');
    Route::post('/approve/{id}', [AdminController::class, 'copyapprove'])->name('approve');
    Route::post('/reject/{id}', [AdminController::class, 'copyreject'])->name('reject');
    Route::get('/show/{id}', [AdminController::class, 'show'])->name('show');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});

Route::post('/withdrawals/{id}/bank-fee', [AdminController::class, 'updateBankFee'])->name('admin.withdraw.bank-fee');






   // user dashboard payouts
 Route::get('/payouts', [UserController::class, 'dashboardpayouts'])->name('dashboardpayouts');


// In routes/web.php - add these routes inside the auth middleware group

// Strategy/Course Routes
Route::prefix('strategies')->name('strategies.')->group(function () {
    Route::get('/', [StrategyController::class, 'strategyindex'])->name('strategyindex');
    Route::get('/{id}', [StrategyController::class, 'strategyshow'])->name('strategyshow');
    Route::post('/{id}/enroll', [StrategyController::class, 'strategyenroll'])->name('strategyenroll');
    Route::post('/upgrade/{currentId}/{newId}', [StrategyController::class, 'strategyupgrade'])->name('strategyupgrade');
    Route::get('/{id}/learn', [StrategyController::class, 'strategylearn'])->name('strategylearn');
});

// Admin Strategy Routes
Route::prefix('admin/strategies')->name('admin.strategies.')->middleware('isAdmin')->group(function () {
    Route::get('/', [AdminController::class, 'strategyindex'])->name('strategyindex');
    Route::get('/create', [AdminController::class, 'strategycreate'])->name('strategycreate');
    Route::post('/', [AdminController::class, 'strategystore'])->name('strategystore');
    Route::get('/{id}/edit', [AdminController::class, 'strategyedit'])->name('strategyedit');
    Route::put('/{id}', [AdminController::class, 'strategyupdate'])->name('strategyupdate');
    Route::delete('/{id}', [AdminController::class, 'strategydestroy'])->name('strategydestroy');
    Route::get('/{id}/enrollments', [AdminController::class, 'strategyenrollments'])->name('strategyenrollments');
});

});
