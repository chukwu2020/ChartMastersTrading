<?php

namespace App\Http\Controllers;

use App\Models\ContactUSMessage;
use App\Models\Deposit;
use App\Models\Investment;
use App\Models\Message;
use App\Models\Plan;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WithdrawalCard;
use App\Mail\OtpMail;
use App\Models\Payout;
use App\Models\ServerFeed;
use App\Models\UserTradingInfo;
use App\Notifications\WelcomeNotification; // ✅ Added this
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\CopyTradingRequest;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Rules\BlockDisposableEmail;

class UserController extends Controller
{
    private function normalizeEmail($email)
    {
        $email = strtolower(trim($email));

        if (str_ends_with($email, '@gmail.com') || str_ends_with($email, '@googlemail.com')) {
            [$name, $domain] = explode('@', $email);

            // Remove dots
            $name = str_replace('.', '', $name);

            // Remove +alias
            $name = explode('+', $name)[0];

            return $name . '@gmail.com'; // force gmail.com
        }

        return $email;
    }

    public function signup()
    {
        $feeds = ServerFeed::latest()->get();
        return view('auth.register', compact('feeds'));
    }

    /**
     * Create user account (Step 1 submission - NO EMAIL VERIFICATION)
     */
    /**
     * Show registration success page (Step 1 complete - with Proceed button)
     */
    /**
     * Show registration success page (Step 1 complete - with Proceed button)
     * NO AUTO-LOGIN HERE - they need to complete Step 2 first
     */

    public function createUser(Request $request)
    {
        // ✅ STEP 1: Clean + normalize BEFORE validation
        $request->merge([
            'name' => trim($request->name),
            'username' => strtolower(trim($request->username)),
            'email' => $this->normalizeEmail($request->email),
        ]);

        // ✅ STEP 2: Validate
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                new BlockDisposableEmail(),
            ],

            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',

            'password' => 'required|string|min:8',

            'referral_id' => 'nullable',

           

            'join_source' => 'required|string|in:discord,telegram,other',
'join_source_other' => 'required_if:join_source,other|nullable|string|max:255',

            'copy_preference' => 'nullable|string',
            'copy_admin_id' => 'nullable|integer|required_if:copy_preference,specific_admin',
            'copy_admin_name' => 'nullable|string',
            'copy_server_name' => 'nullable|string',
        ]);

        try {
            $user = DB::transaction(function () use ($validated) {
                return User::create([
                    'name' => $validated['name'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'country' => $validated['country'],
                    'password' => Hash::make($validated['password']),

                    'referral_id' => $validated['referral_id'] ?? null,

                    'join_source' => $validated['join_source'] ?? null,
                    'join_source_other' => ($validated['join_source'] ?? null) === 'other'
                        ? $validated['join_source_other']
                        : null,

                    'copy_preference' => $validated['copy_preference'] ?? null,
                    'copy_admin_id' => $validated['copy_admin_id'] ?? null,
                    'copy_admin_name' => $validated['copy_admin_name'] ?? null,
                    'copy_server_name' => $validated['copy_server_name'] ?? null,

                    'account_status' => 'pending',
                    'registration_step' => 1,
                ]);
            });

            session()->put('registration_user_id', $user->id);

            return redirect()->route('registration.success', ['user' => $user->id]);
        } catch (\Throwable $e) {
            \Log::error('User registration failed: ' . $e->getMessage());

            return back()
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }


    public function registrationSuccess($userId)
    {
        $user = User::findOrFail($userId);

        // Check if this is the correct user session

        if (
            session('registration_user_id') != $userId ||
            $user->registration_step !== 1
        ) {
            return redirect()->route('signup');
        }
        return view('auth.register_success', compact('user'));
    }
    /**
     * Show additional info form (Step 2)
     */
    public function showAdditionalInfo()
    {
        $userId = session('registration_user_id');
        if (!$userId) {
            return redirect()->route('signup');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('signup');
        }

        // Get feeds for corporate admin selection
        $feeds = ServerFeed::latest()->get();

        return view('auth.additional_info', compact('user', 'feeds')); // ✅ Fixed path
    }

  
    /**
     * Save additional trading info (Step 2 submission)
     */
  public function saveAdditionalInfo(Request $request)
    {
        $userId = session('registration_user_id');
        if (!$userId) {
            return redirect()->route('signup');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('signup');
        }

        $request->validate([
            // Trading experience questions (Page 2)
            'stock_experience' => 'required|in:yes,no,novice',
            'trading_frequency' => 'required|string',
            'transaction_volume' => 'required|string',

            // Investment goals (Page 3)
            'investment_goal' => 'required|array',
            'investment_goal.*' => 'string',

            // Asset classes (Page 5)
            'asset_classes' => 'required|array',
            'asset_classes.*' => 'string',

            // Account type selection (Page 6 - CRITICAL)
          
            'account_type' => 'required|in:corporate',  

            // If corporate, require admin selection
            'copy_admin_id' => 'required_if:account_type,corporate|nullable|integer',
            'copy_admin_name' => 'required_if:account_type,corporate|nullable|string',
            'copy_server_name' => 'required_if:account_type,corporate|nullable|string',

            // Financial info (Page 7)
            'investment_amount' => 'required|numeric|min:0',
           'learning_style' => 'required|string',
            'deposit_source' => 'required|string',
          
        ]);

        DB::beginTransaction();
        try {
            // Create trading info record
            UserTradingInfo::create([
                'user_id' => $user->id,
                'stock_experience' => $request->stock_experience,
                'trading_frequency' => $request->trading_frequency,
                'transaction_volume' => $request->transaction_volume,
                'investment_goals' => json_encode($request->investment_goal),
                'asset_classes' => json_encode($request->asset_classes),
                'account_type' => $request->account_type,
                'copy_admin_id' => $request->copy_admin_id,
                'copy_admin_name' => $request->copy_admin_name,
                'copy_server_name' => $request->copy_server_name,
                'investment_amount' => $request->investment_amount,
               
                'learning_style' => $request->learning_style,  
                'deposit_source' => $request->deposit_source,
                 'annual_income' => $request->annual_income ?? null,
                'ongoing_deposit_source' => $request->ongoing_deposit_source ?? null,

            ]);

            // If corporate account, update user with copy admin info
        // Always corporate account - update user with copy admin info
$user->update([
    'copy_preference' => 'specific_admin',
    'copy_admin_id' => $request->copy_admin_id,
    'copy_admin_name' => $request->copy_admin_name,
    'copy_server_name' => $request->copy_server_name,
]);

            // Update user status
            $user->update([
                'account_status' => 'active',
                'registration_step' => 2, // Step 2 complete
            ]);

            DB::commit();

            // Clear session
            session()->forget('registration_user_id');

            // Send welcome notification with email
            $user->notify(new WelcomeNotification(
                $user->name,
                $user->email,
                $request->account_type,
                $request->copy_admin_name ?? null
            ));

            // 🔐 AUTO LOGIN THE USER
            Auth::login($user);

            // Set session for overlay if needed
            $today = now()->toDateString();
            session([
                'overlayDate' => $today,
                'overlayCount' => 0,
                'showTradingOverlay' => true,
                'overlayShowAt' => now()->addSeconds(40)->timestamp,
            ]);

            // Redirect to dashboard instead of registration complete page
            return redirect()->route('user_dashboard')->with('success', 'Welcome! Your account is fully set up.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save information: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Get admins for corporate account (AJAX search)
     */
    public function getAdmins(Request $request)
    {
        $search = $request->get('search', '');
        $query = ServerFeed::where('status', 1);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('admin_name', 'like', "%{$search}%")
                    ->orWhere('server_name', 'like', "%{$search}%");
            });
        }

        $admins = $query->get();

        return response()->json($admins);
    }


    public function changeAdmin(Request $request)
{
    $request->validate([
        'admin_id' => 'required|exists:server_feeds,id'
    ]);

    $user = auth()->user();

    $admin = ServerFeed::findOrFail($request->admin_id);

    $user->update([
        'copy_admin_id' => $admin->id,
        'copy_admin_name' => $admin->admin_name,
        'copy_server_name' => $admin->server_name,
        'copy_preference' => 'specific_admin',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Admin updated successfully'
    ]);
}


    
    // ... rest of your existing methods remain exactly the same ...
    public function user_dashboard()
{
    $user = auth()->user();

    // ✅ BEST PRACTICE: Use accessor from User model
    $totalInvested = $user->amount_invested;

    $cardExists = WithdrawalCard::where('user_id', $user->id)->exists();
    $verification = $user->idverification;
    $feeds = ServerFeed::latest()->get();

    if ($user->registration_step < 2 || $user->account_status !== 'active') {
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'email' => 'You must complete registration before logging in.'
        ]);
    }

    // Deposits
    $deposits = Deposit::where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($deposit) {
            return [
                'type' => 'Deposit',
                'amount' => $deposit->amount_deposited,
                'status' => $deposit->status ? 'Completed' : 'Pending',
                'date' => $deposit->created_at,
                'reference' => 'DEP-' . $deposit->id,
                'icon' => 'bank-transfer-in',
                'action_url' => null,
                'action_text' => null
            ];
        });

    // Withdrawals
    $withdrawals = Withdrawal::where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($withdrawal) {
            return [
                'type' => 'Withdrawal',
                'amount' => $withdrawal->amount,
                'status' => match ($withdrawal->status) {
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'rejected' => 'Rejected',
                    default => ucfirst($withdrawal->status),
                },
                'date' => $withdrawal->created_at,
                'reference' => 'WD-' . $withdrawal->id,
                'icon' => 'bank-transfer-out',
                'action_url' => null,
                'action_text' => null
            ];
        });

    // Copy Trading Requests
    $copyRequests = CopyTradingRequest::with('plan')
        ->where('user_id', $user->id)
        ->latest()
        ->take(10)
        ->get();

    $copyActivities = [];

    foreach ($copyRequests as $req) {
        $copyActivities[] = [
            'type' => 'Copy Trading',
            'icon' => 'copy',
            'amount' => $req->amount ?? 0,
            'status' => $req->status,
            'date' => $req->created_at,
            'reference' => 'CT-' . $req->id,
            'plan_name' => $req->plan->name ?? 'N/A',
            'action_url' => null,
            'action_text' => null,
        ];
    }

    // Active Investments
    $allInvestments = $user->investments()
        ->where('status', 'active')
        ->with('plan')
        ->get();

    $activeInvestments = $allInvestments->take(5)->map(function ($investment) {
        return [
            'type' => 'Investment Active',
            'amount' => $investment->amount_invested,
            'status' => 'Active',
            'date' => $investment->created_at,
            'reference' => 'INV-' . $investment->id,
            'icon' => 'chart-line',
            'plan_name' => $investment->plan->name ?? 'N/A',
            'action_url' => null,
            'action_text' => null
        ];
    });

    // Matured Investments
    $maturedInvestments = Investment::with('plan')
        ->where('user_id', $user->id)
        ->where('status', 'completed')
        ->latest()
        ->get()
        ->filter(fn($inv) => $inv->is_withdrawable)
        ->take(5)
        ->map(function ($investment) {
            return [
                'type' => 'Investment Matured',
                'amount' => $investment->amount_invested + $investment->total_profit,
                'status' => 'Ready to Withdraw',
                'date' => $investment->updated_at,
                'reference' => 'MAT-' . $investment->id,
                'icon' => 'cash-check',
                'plan_name' => $investment->plan->name ?? 'N/A',
                'action_url' => route('investments.withdraw', $investment->id),
                'action_text' => 'Withdraw Now'
            ];
        });

    // Combine Activities
    $recentActivities = collect()
        ->merge($deposits)
        ->merge($withdrawals)
        ->merge($activeInvestments)
        ->merge($maturedInvestments)
        ->merge($copyActivities)
        ->sortByDesc('date')
        ->take(10);

    if (session('certShowAt') && session('certShowAt') < now()->timestamp) {
        session()->forget('certShowAt');
    }

    $overlayCountToday = session('overlayCountToday', 0);

    return view('dashboard.index', compact(
        'user',
        'cardExists',
        'totalInvested',
        'recentActivities',
        'overlayCountToday',
        'allInvestments',
        'verification',
        'feeds'
    ));
}


// user psychology

public function psychology(){
    return view('dashboard.user.psychology');
}
    public function hideOverlay(Request $request)
    {
        // Mark overlay as closed for this user/session
        session(['showTradingOverlay' => false]);

        // Optionally increment overlay count if you want daily limit
        session(['overlayCountToday' => session('overlayCountToday', 0) + 1]);

        return response()->json(['success' => true]);
    }

    // lives methods
    public function user_live()
    {
        $session = DB::table('live_trading_sessions')
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        $messages = DB::table('live_trading_messages')
            ->join('users', 'live_trading_messages.user_id', '=', 'users.id')
            ->select('live_trading_messages.*', 'users.name as user_name')
            ->orderBy('live_trading_messages.created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse();

        return view('dashboard.livetrading', compact('session', 'messages'));
    }

    public function activateMembership(Request $request)
    {
        $request->validate([
            'membership_code' => 'required|string'
        ]);

        $user = Auth::user();
        $inputCode = trim($request->membership_code);

        // 1. Check if already active
        if ($user->has_membership) {
            return response()->json([
                'success' => false,
                'message' => '✅ Your membership is already active!'
            ], 422);
        }

        // 2. Check if code matches user's assigned code
        if ($user->membership_code !== $inputCode) {
            return response()->json([
                'success' => false,
                'message' => '❌ This code does not match your account. Expected: ' . $user->membership_code
            ], 422);
        }

        // 3. Check if code exists in database
        $validCode = DB::table('membership_codes')
            ->where('code', $inputCode)
            ->first();

        if (!$validCode) {
            return response()->json([
                'success' => false,
                'message' => '❌ Code not found in database. Contact admin.'
            ], 422);
        }

        // 4. Check if code already used by someone else
        if ($validCode->is_used && $validCode->used_by != $user->id) {
            return response()->json([
                'success' => false,
                'message' => '❌ This code has been used by another user.'
            ], 422);
        }

        // 5. Activate membership
        DB::table('membership_codes')
            ->where('id', $validCode->id)
            ->update([
                'is_used' => true,
                'used_by' => $user->id,
                'used_at' => now()
            ]);

        $user->update([
            'has_membership' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => '🎉 Membership activated! Welcome to Live Trading!'
        ]);
    }

    public function liveTradingStart(Request $request)
    {
        // Check if user has approved deposit (status = 1)
        $hasDeposit = auth()->user()->deposits()->where('status', 1)->exists();

        if (!$hasDeposit) {
            return response()->json([
                'success' => false,
                'message' => 'You need an approved deposit first.'
            ], 403);
        }

        // Check for existing active session
        $existingSession = DB::table('live_trading_sessions')
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if ($existingSession) {
            return response()->json([
                'success' => true,
                'message' => 'Session already active.',
                'session_id' => $existingSession->id
            ]);
        }

        // Create new session
        $sessionId = DB::table('live_trading_sessions')->insertGetId([
            'user_id' => auth()->id(),
            'started_at' => now(),
            'last_activity' => now(),
            'is_active' => true,
            'watch_time_seconds' => 0,
            'earnings' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session started!',
            'session_id' => $sessionId
        ]);
    }

    public function liveTradingUpdate(Request $request)
    {
        $session = DB::table('live_trading_sessions')
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'No active session.'
            ], 404);
        }
        if (Carbon::parse($session->last_activity)->diffInSeconds(now()) < 55) {
            return response()->json([
                'success' => false,
                'message' => 'Too fast'
            ], 429);
        }
        $newWatchTime = $session->watch_time_seconds + 60;
        $newEarnings = ($newWatchTime / 60) * 0.01;

        DB::table('live_trading_sessions')
            ->where('id', $session->id)
            ->update([
                'watch_time_seconds' => $newWatchTime,
                'earnings' => $newEarnings,
                'last_activity' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'watch_time' => $newWatchTime,
            'earnings' => number_format($newEarnings, 2)
        ]);
    }

    public function liveTradingClaim(Request $request)
    {
        $session = DB::table('live_trading_sessions')
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$session || $session->earnings <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No earnings to claim.'
            ], 400);
        }

        $user = auth()->user();
        $newBalance = $user->available_balance + $session->earnings;

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'available_balance' => $newBalance
            ]);

        DB::table('live_trading_sessions')
            ->where('id', $session->id)
            ->update([
                'earnings' => 0,
                'is_active' => false,
                'ended_at' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'claimed' => number_format($session->earnings, 2),
            'new_balance' => number_format($newBalance, 2)
        ]);
    }

    public function liveTradingSendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        DB::table('live_trading_messages')->insert([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent!'
        ]);
    }

    public function liveTradingGetMessages()
    {
        $messages = DB::table('live_trading_messages')
            ->join('users', 'live_trading_messages.user_id', '=', 'users.id')
            ->select(
                'live_trading_messages.id',
                'live_trading_messages.message',
                'live_trading_messages.created_at',
                'users.name as user_name'
            )
            ->orderBy('live_trading_messages.created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'user_name' => $msg->user_name,
                    'time_ago' => Carbon::parse($msg->created_at)->diffForHumans()
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function checkMembership()
    {
        $user = auth()->user();

        return response()->json([
            'locked' => $user->membership_locked || !$user->membership_code
        ]);
    }

    public function lockedPage()
    {
  $user = auth()->user();

$copyAdmin = \App\Models\ServerFeed::find($user->copy_admin_id);

 return view('dashboard.lockedpage', compact(
    'user',
    'copyAdmin'
));
    }



    public function products()
    {
        $plans = Plan::where('status', 'active')->get();
        return view('snippets.plans_header', compact('plans'));
    }

    public function About_Us()
    {
        return view('snippets.AboutUs_header');
    }

    public function affiliates()
    {
        return view('snippets.affiliates_header');
    }

    public function helpdesk()
    {
        return view('snippets.helpdesk_header');
    }

    public function payouts()
    {
        $payouts = Payout::with('plan')
            ->orderBy('pay_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('snippets.payouts_header', compact('payouts'));
    }

    /* =========================
       LEARN HUB PAGES
    ==========================*/

    public function education_funding()
    {
        return view('snippets.educationfunding');
    }

    public function education_evaluations()
    {
        return view('snippets.educationevaluation');
    }

    public function education_prop_firms()
    {
        return view('snippets.educationpropfirms');
    }

    public function education_trailing_drawdown()
    {
        return view('snippets.education_trailing_drawdown');
    }

    public function education_consistency_rules()
    {
        return view('snippets.education_consistencyrules');
    }

    public function education_challenge_cost_vs_risk()
    {
        return view('snippets.education_challengecost_vs_risk');
    }

    public function education_psychology1()
    {
        return view('snippets.education_psychology1');
    }

    public function education_psychology2()
    {
        return view('snippets.education_phychology2');
    }

    public function education_make_living_trading()
    {
        return view('snippets.education_make_living_trading');
    }

    /* =========================
       COMPANY PAGES
    ==========================*/

    public function press()
    {
        return view('snippets.press');
    }

    public function terms_privacy()
    {
        return view('snippets.terms_privacy');
    }

    public function contactus()
    {
        return view('snippets.contactus_header');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();

        ContactUSMessage::create($validated);

        return back()->with('success', 'Message sent successfully!');
    }

    // reinvestment
    public function initiateReinvestment(Request $request)
    {
        $user = auth()->user();

        // Validate user has sufficient balance
        if ($user->available_balance <= 0) {
            return redirect()->route('user_dashboard')->with('error', 'Insufficient balance for reinvestment');
        }

        // Set reinvestment session flag with expiration
        session([
            'reinvestment_mode' => true,
            'reinvestment_expires' => now()->addMinutes(30),
            'reinvestment_balance' => $user->available_balance
        ]);

        return redirect()->route('user.deposit')->with('info', 'You are in reinvestment mode. Your available balance will be used.');
    }

    public function rules_regulations()
    {
        return view('dashboard.informations.rules_guildelines');
    }

    public function all_server()
    {
        $feeds = ServerFeed::latest()->get();
        return view('dashboard.allserver', compact('feeds'));
    }


    // user dashboard payouts

    public function dashboardpayouts() {  
    
        $payouts = Payout::with('plan')
            ->orderBy('pay_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('dashboard.user.payouts', compact('payouts'));
}



}