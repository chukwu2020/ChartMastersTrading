<?php

namespace App\Http\Controllers;

use App\Mail\BankTransferDetailsMail;
use App\Models\BankTransfer;
use App\Models\ContactUSMessage;
use App\Models\CopyTradingRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Deposit;
use App\Models\Idverification;
use App\Models\Investment;
use App\Models\Message;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserKyc;
use App\Models\Withdrawal;
use App\Models\ServerFeed;
use App\Models\WithdrawalCard;
use App\Notifications\AdminMessageNotification;
use App\Notifications\IDVerificationSubmitted;
use App\Models\Payout;
use App\Models\Strategy;
use App\Models\StrategyEnrollment;
use App\Notifications\TransactionNotification;
use App\Notifications\AdminCopyTradingController;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function userIndex(Request $request)
    {
        $query = User::with([
            'investments',
            'profile',
            'withdrawalCard',
            'tradingInfo'
        ])
            ->withSum('investments', 'amount_invested')
            ->where('role_as', 0);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'Active' ? 1 : 0);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }
    public function hiddenuser(Request $request)
    {
        $query = User::with(['profile', 'withdrawalCard', 'tradingInfo']) // This will now work
            ->withSum('investments', 'amount_invested')
            ->where('role_as', 0);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        if ($request->filled('copy_preference')) {
            $query->where('copy_preference', $request->copy_preference);
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'Active' ? 1 : 0);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.indexmain', compact('users'));
    }
    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.index')->with('success', 'User deleted successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }


   public function pendingDeposits()
{
    $deposits = Deposit::with(['user', 'plan', 'wallet', 'bankTransfer'])
        ->where('status', 0)
        ->where(function($query) {
            $query->where('payment_method', '!=', 'bank_transfer')
                  ->orWhere(function($q) {
                      $q->where('payment_method', 'bank_transfer')
                        ->whereNotNull('proof');
                  });
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin.deposits.pending', compact('deposits'));
}

    public function approvedDeposits()
    {
        $deposits = Deposit::with(['user', 'plan', 'wallet'])
            ->where('status', 1)
            ->where('admin_deleted', 0)   // <-- added
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.deposits.approved', compact('deposits'));
    }
    public function adminDeleteDeposit($id)
    {
        $deposit = Deposit::findOrFail($id);
        $deposit->update(['admin_deleted' => true]);

        return back()->with('success', 'Deposit removed from admin view.');
    }
    public function rejectDeposit(Request $request, $id)
    {
        $request->validate([
            'rejection_note' => 'required|string|min:5',
        ]);

        $deposit = Deposit::findOrFail($id);


        if ($deposit->status == 1) {
            return back()->with('error', 'Cannot reject approved deposit.');
        }

        $deposit->update([
            'status' => 2, // rejected
            'rejection_note' => $request->rejection_note,
        ]);

        // Optional notification
        $deposit->user->notify(new TransactionNotification(
            'Deposit Rejected',
            'Your deposit was rejected. Please check the reason in your dashboard.'
        ));

        return back()->with('success', 'Deposit rejected with reason.');
    }



    // In your AdminController.php - update the approveDeposit method

    public function approveDeposit($id)
    {
        DB::transaction(function () use ($id) {
            $deposit = Deposit::findOrFail($id);

            if ($deposit->status == 1) {
                throw new \Exception('Already approved');
            }

            // Update deposit status
            $deposit->update(['status' => 1]);

            // Add to user's available balance
            $user = User::find($deposit->user_id);
            $user->increment('available_balance', $deposit->amount_deposited);

            // Notify user
            try {
                $user->notify(new \App\Notifications\TransactionNotification(
                    'Deposit Approved',
                    'Your deposit of $' . number_format($deposit->amount_deposited, 2) .
                        ' has been approved and added to your available balance.'
                ));
            } catch (\Exception $e) {
                \Log::error('Notification failed: ' . $e->getMessage());
            }
        });

        return back()->with('success', 'Deposit approved and added to available balance');
    }



    // In AdminController.php
    public function showApprovedWithdrawals()
    {
        // Get all approved withdrawals (both balance and investment transfers)
        $approvedWithdrawals = Withdrawal::whereIn('status', ['approved', 'rejected'])
            ->with(['user'])
            ->latest()
            ->get();

        $withdrawalCards = WithdrawalCard::all();

        return view('admin.deposits.withdrawal_approved', compact('approvedWithdrawals', 'withdrawalCards'));
    }

// app/Http/Controllers/AdminController.php

public function unapproveBalanceWithdrawal(Request $request, $id)
{
    $request->validate([
        'admin_note' => 'required|string|max:500',
    ]);

    return DB::transaction(function () use ($request, $id) {

        $withdrawal = Withdrawal::where('id', $id)->lockForUpdate()->firstOrFail();

        if ($withdrawal->status !== 'approved') {
            return back()->with('error', 'Only approved withdrawals can be marked as failed.');
        }

        $withdrawal->status     = 'failed';          // <-- MUST be 'failed'
        $withdrawal->admin_note = $request->admin_note;
        $withdrawal->save();

        // Refund the money back to the user
        $user = $withdrawal->user;
        if ($user) {
            $user->available_balance += $withdrawal->amount;
            $user->save();
        }

        return back()->with('success', 'Withdrawal marked as failed and balance restored.');
    });
}
    /**
     * Toggle withdrawal lock for a user
     */
    public function toggleWithdrawalLock($id)
    {
        $user = User::findOrFail($id);

        $user->withdrawal_locked = !$user->withdrawal_locked;

        if ($user->withdrawal_locked) {
            $user->withdrawal_lock_reason = 'Admin locked withdrawal on ' . now()->toDateTimeString();
        } else {
            $user->withdrawal_lock_reason = null;
        }

        $user->save();

        $status = $user->withdrawal_locked ? '🔒 locked' : '🔓 unlocked';

        return back()->with('success', "User {$user->name}'s withdrawal has been {$status}.");
    }
    // ✅ FIXED: Generate Membership Code



    // Add this to AdminController.php

    public function generateMembershipCode(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user already has a code
        if ($user->membership_code) {
            return response()->json([
                'success' => false,
                'message' => 'User already has code: ' . $user->membership_code
            ], 400);
        }

        // Generate unique code
        do {
            $membershipCode = 'VIP' . date('Y') . strtoupper(Str::random(6));
        } while (DB::table('membership_codes')->where('code', $membershipCode)->exists());

        // Insert into membership_codes table
        DB::table('membership_codes')->insert([
            'code' => $membershipCode,
            'is_used' => false,
            'used_by' => null,
            'used_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Assign to user (but don't activate yet)
        $user->update([
            'membership_code' => $membershipCode,
            'has_membership' => false
        ]);

        return response()->json([
            'success' => true,
            'membership_code' => $membershipCode,
            'user_name' => $user->name
        ]);
    }

    // live

    public function toggleMembershipLock(User $user)
    {
        $user->membership_locked = !$user->membership_locked;
        $user->save();

        $status = $user->membership_locked ? 'locked' : 'unlocked';

        return back()->with('success', "Membership code has been {$status}.");
    }



    public function withdrawaldestroy($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'approved') {
            return back()->with('error', 'Only approved withdrawals can be deleted.');
        }

        $withdrawal->delete();

        return back()->with('success', 'Approved withdrawal deleted successfully.');
    }

    public function updateBalance(Request $request, $id)
    {
        $request->validate([
            'available_balance' => 'required|numeric|min:0',
            'investments.*' => 'nullable|numeric|min:0',
        ]);

        $user = User::findOrFail($id);
        $user->available_balance = $request->input('available_balance');
        $user->save();

        return redirect()->route('user.index')->with('success', 'User balance updated successfully.');
    }

    public function admin_dashboard()
    {
        $totalUsers = User::count();
        $totalDeposits = User::sum('available_balance');
        $pendingDepositsCount = Deposit::where('status', 'pending')->count();

        $pendingWithdrawalsCount = Withdrawal::where('status', 'pending')->count();
        $pendingCopyCount = CopyTradingRequest::where('status', 'pending')->count();

        $totalWithdrawals = Withdrawal::where('status', 'approved')
            ->where('type', 'balance') // 🔥 avoid counting internal transfers
            ->sum('amount');
        $amount_invested = Investment::sum('amount_invested');
        $user = auth()->user();
        return view('admin.index', compact(
            'totalUsers',
            'totalDeposits',
            'totalWithdrawals',
            'amount_invested',
            'user',
            'pendingDepositsCount',
            'pendingCopyCount',
            'pendingWithdrawalsCount'

        ));
    }

    public function adminViewWithdrawals()
    {

        $withdrawals = Withdrawal::with(['user.profile', 'user.withdrawalCard'])
            ->where('status', 'pending')
            ->where('type', 'balance') // 🔥 ONLY real withdrawals
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.deposits.withdrawal_pending', compact('withdrawals'));
    }



public function approveBalanceWithdrawal($id)
{
$withdrawal = Withdrawal::findOrFail($id);

if ($withdrawal->status === 'approved') {
    return back()->with('error', 'This withdrawal has already been approved.');
}

// Approve the withdrawal first
$withdrawal->status = 'approved';
$withdrawal->admin_note = null;
$withdrawal->save();

$user = $withdrawal->user;

// Send notification, but do not let an email/SMTP failure
// prevent the withdrawal approval from completing.
try {

    $user->notify(new TransactionNotification(
        'Withdrawal Approved',
        'Your withdrawal request of $' . number_format($withdrawal->amount, 2) . ' has been approved.'
    ));

} catch (\Throwable $e) {

    \Log::error('Withdrawal approval notification failed', [
        'withdrawal_id' => $withdrawal->id,
        'user_id'       => $user->id ?? null,
        'error'         => $e->getMessage(),
    ]);

}

return back()->with(
    'success',
    'Withdrawal approved successfully.'
);

}

    public function index()
    {
        $messages = ContactUSMessage::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.contactUs.index', compact('messages'));
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted successfully.');
    }

    public function profile()
    {
        $user = User::with('profile')->find(auth()->id());
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);

            $profile = $user->profile ?? new \App\Models\UserProfile();
            $profile->user_id = $user->id;
            $profile->profile_pic = $filename;
            $profile->save();
        }

        $user->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
        ]);

        if ($user->profile) {
            $user->profile->update([
                'address' => $request->input('address'),
                'bitcoin_address' => $request->input('bitcoin_address'),
                'etherium_address' => $request->input('etherium_address'),
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function kycindex()
    {
        $kycs = UserKyc::with('user')->latest()->get();
        return view('admin.admin_approve_id_verification', compact('kycs'));
    }

    public function approve($id)
    {
        $kyc = UserKyc::with('user')->findOrFail($id);

        if ($kyc->status === 'approved') {
            return redirect()->back()->with('error', 'This KYC is already approved.');
        }

        if ($kyc->status === 'rejected') {
            return redirect()->back()->with('error', 'This KYC has already been rejected and cannot be approved.');
        }

        $kyc->status = 'approved';
        $kyc->admin_note = 'Approved by admin';
        $kyc->save();

        $user = $kyc->user;
        $user->notify(new TransactionNotification(
            'KYC Approved',
            'Your KYC documents have been reviewed and approved. You have unlocked additional features for your trading.'
        ));

        return redirect()->back()->with('success', 'KYC approved.');
    }

    public function reject($id)
    {
        $kyc = UserKyc::with('user')->findOrFail($id);

        if ($kyc->status === 'rejected') {
            return redirect()->back()->with('error', 'This KYC is already rejected.');
        }

        if ($kyc->status === 'approved') {
            return redirect()->back()->with('error', 'This KYC has already been approved and cannot be rejected.');
        }

        $kyc->status = 'rejected';
        $kyc->admin_note = 'Rejected by admin';
        $kyc->save();

        $user = $kyc->user;
        $user->notify(new TransactionNotification(
            'KYC Rejected',
            'Your KYC documents were reviewed and rejected. Please upload a clearer image of your ID for verification.'
        ));

        return redirect()->back()->with('success', 'KYC rejected.');
    }



    // * Get language code based on country
    //      */
    private function getLanguageCode($country)
    {
        $countryLanguageMap = [
            'Afghanistan' => 'ps',
            'Albania' => 'sq',
            'Algeria' => 'ar',
            'Argentina' => 'es',
            'Australia' => 'en',
            'Austria' => 'de',
            'Bangladesh' => 'bn',
            'Belarus' => 'be',
            'Belgium' => 'nl',
            'Brazil' => 'pt',
            'Bulgaria' => 'bg',
            'Cambodia' => 'km',
            'Canada' => 'en',
            'Chile' => 'es',
            'China' => 'zh-CN',
            'Colombia' => 'es',
            'Croatia' => 'hr',
            'Czech Republic' => 'cs',
            'Denmark' => 'da',
            'Egypt' => 'ar',
            'Estonia' => 'et',
            'Finland' => 'fi',
            'France' => 'fr',
            'Germany' => 'de',
            'Ghana' => 'en',
            'Greece' => 'el',
            'Hong Kong' => 'zh-TW',
            'Hungary' => 'hu',
            'Iceland' => 'is',
            'India' => 'hi',
            'Indonesia' => 'id',
            'Iran' => 'fa',
            'Iraq' => 'ar',
            'Ireland' => 'en',
            'Israel' => 'he',
            'Italy' => 'it',
            'Japan' => 'ja',
            'Jordan' => 'ar',
            'Kazakhstan' => 'kk',
            'Kenya' => 'sw',
            'Kuwait' => 'ar',
            'Latvia' => 'lv',
            'Lebanon' => 'ar',
            'Lithuania' => 'lt',
            'Luxembourg' => 'lb',
            'Malaysia' => 'ms',
            'Maldives' => 'dv',
            'Mexico' => 'es',
            'Morocco' => 'ar',
            'Nepal' => 'ne',
            'Netherlands' => 'nl',
            'New Zealand' => 'en',
            'Nigeria' => 'en',
            'Norway' => 'no',
            'Pakistan' => 'ur',
            'Palestine' => 'ar',
            'Peru' => 'es',
            'Philippines' => 'tl',
            'Poland' => 'pl',
            'Portugal' => 'pt',
            'Qatar' => 'ar',
            'Romania' => 'ro',
            'Russia' => 'ru',
            'Saudi Arabia' => 'ar',
            'Serbia' => 'sr',
            'Singapore' => 'en',
            'Slovakia' => 'sk',
            'Slovenia' => 'sl',
            'South Africa' => 'af',
            'South Korea' => 'ko',
            'Spain' => 'es',
            'Sri Lanka' => 'si',
            'Sweden' => 'sv',
            'Switzerland' => 'de',
            'Syria' => 'ar',
            'Taiwan' => 'zh-TW',
            'Tanzania' => 'sw',
            'Thailand' => 'th',
            'Tunisia' => 'ar',
            'Turkey' => 'tr',
            'Ukraine' => 'uk',
            'United Arab Emirates' => 'ar',
            'United Kingdom' => 'en',
            'United States' => 'en',
            'Uruguay' => 'es',
            'Uzbekistan' => 'uz',
            'Venezuela' => 'es',
            'Vietnam' => 'vi',
            'Yemen' => 'ar',
            'Zambia' => 'en',
            'Zimbabwe' => 'en',
        ];

        return $countryLanguageMap[$country] ?? 'en';
    }

    /**
     * Translate message to target language using Google Cloud Translate
     */

    private function translateMessage($text, $targetLanguage)
    {
        try {
            if ($targetLanguage === 'en' || empty($targetLanguage)) {
                return $text;
            }

            $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl={$targetLanguage}&dt=t&q=" . urlencode($text);

            $response = file_get_contents($url);

            if (!$response) {
                return $text;
            }

            $result = json_decode($response, true);

            if (!isset($result[0])) {
                return $text;
            }

            $translatedText = '';

            foreach ($result[0] as $sentence) {
                $translatedText .= $sentence[0];
            }

            return $translatedText;
        } catch (\Exception $e) {
            Log::error('Translation failed: ' . $e->getMessage());
            return $text;
        }
    }
    /**
     * Send message with auto-translation
     */
    /**
     * Send message with auto-translation
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'message' => 'required',
            'users' => 'required|array'
        ]);

        if (in_array('all', $request->users)) {
            $users = User::all();
        } else {
            $users = User::whereIn('id', $request->users)->get();
        }

        $originalTitle = $request->title;
        $originalMessage = $request->message;

        $translationSuccess = true;
        $failedTranslations = [];

        foreach ($users as $user) {
            $languageCode = $this->getLanguageCode($user->country ?? '');

            try {
                $translatedTitle = $this->translateMessage($originalTitle, $languageCode);
                $translatedMessage = $this->translateMessage($originalMessage, $languageCode);

                $user->notify(new AdminMessageNotification(
                    $translatedTitle,
                    $translatedMessage,
                    $originalTitle,
                    $originalMessage,
                    $languageCode,
                    $user->country
                ));
            } catch (\Exception $e) {
                // If translation fails, send the original message
                Log::warning('Translation failed for user ' . $user->id . ': ' . $e->getMessage());
                $failedTranslations[] = $user->name;

                $user->notify(new AdminMessageNotification(
                    $originalTitle,
                    $originalMessage,
                    $originalTitle,
                    $originalMessage,
                    'en',
                    $user->country
                ));
            }
        }

        $message = 'Messages sent successfully!';
        if (!empty($failedTranslations)) {
            $message = 'Messages sent, but translation failed for: ' . implode(', ', $failedTranslations);
        }

        Log::info('Messages sent', [
            'total_users' => $users->count(),
            'failed_translations' => $failedTranslations
        ]);

        return back()->with('success', $message);
    }




    //copying trades




    public function serverindex()
    {
        $feeds = ServerFeed::latest()->get();
        return view('admin.serverfeeds.index', compact('feeds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'server_name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'active_members' => 'required|integer|min:0',
            'copying_trades' => 'required|integer|min:0',

            // BIG profit support
            'profit_margin' => 'required|numeric|min:0|max:999999999999.99',

            // NEW
            'win_rate' => 'nullable|numeric|min:0|max:100',

            // Images
            'server_profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'admin_profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'server_description' => 'nullable|string',
            'admin_bio' => 'nullable|string',
        ]);

        $data = [
            'server_name' => $request->server_name,
            'admin_name' => $request->admin_name,
            'active_members' => $request->active_members,
            'copying_trades' => $request->copying_trades,
            'profit_margin' => $request->profit_margin,

            // NEW
            'win_rate' => $request->win_rate ?? 0,
            'copy_trading_enabled' => $request->has('copy_trading_enabled'),

            'server_description' => $request->server_description,
            'admin_bio' => $request->admin_bio,
        ];

        // Server Image
        if ($request->hasFile('server_profile_image')) {
            $path = $request->file('server_profile_image')->store('servers', 'public');
            $data['server_profile_image'] = basename($path);
        }

        // Admin Image
        if ($request->hasFile('admin_profile_image')) {
            $path = $request->file('admin_profile_image')->store('admins', 'public');
            $data['admin_profile_image'] = basename($path);
        }

        ServerFeed::create($data);

        return back()->with('success', 'Server added successfully');
    }

    public function update(Request $request, $id)
    {
        $feed = ServerFeed::findOrFail($id);

        $request->validate([
            'server_name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'active_members' => 'required|integer|min:0',
            'copying_trades' => 'required|integer|min:0',

            'profit_margin' => 'required|numeric|min:0|max:999999999999.99',

            // NEW
            'win_rate' => 'nullable|numeric|min:0|max:100',

            'server_profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'admin_profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'server_description' => 'nullable|string',
            'admin_bio' => 'nullable|string',
        ]);

        $data = [
            'server_name' => $request->server_name,
            'admin_name' => $request->admin_name,
            'active_members' => $request->active_members,
            'copying_trades' => $request->copying_trades,
            'profit_margin' => $request->profit_margin,

            // NEW
            'win_rate' => $request->win_rate ?? 0,
            'copy_trading_enabled' => $request->has('copy_trading_enabled'),

            'server_description' => $request->server_description,
            'admin_bio' => $request->admin_bio,
        ];

        if ($request->hasFile('server_profile_image')) {
            $path = $request->file('server_profile_image')->store('servers', 'public');
            $data['server_profile_image'] = basename($path);
        }

        if ($request->hasFile('admin_profile_image')) {
            $path = $request->file('admin_profile_image')->store('admins', 'public');
            $data['admin_profile_image'] = basename($path);
        }

        $feed->update($data);

        return back()->with('success', 'Server updated successfully');
    }


    public function deleteServerFeed($id)
    {
        ServerFeed::findOrFail($id)->delete();
        return back()->with('success', 'Server info deleted');
    }
    public function serveredit($id)
    {
        $feed = ServerFeed::findOrFail($id);
        return view('admin.serverfeeds.edit', compact('feed'));
    }



    /**
     * Display a listing of the payouts.
     */
    public function payoutIndex()
    {
        $payouts = Payout::with('plan')
            ->orderBy('pay_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $plans = Plan::where('status', 'active')->get();

        return view('admin.payout', compact('payouts', 'plans'));
    }


    /**
     * Show the form for creating a new payout.
     */
    public function payoutcreate()
    {
        $plans = \App\Models\Plan::where('status', 'active')->get();

        $payouts = \App\Models\Payout::with('plan')
            ->latest()
            ->paginate(25);

        return view('admin.payout', compact('plans', 'payouts'));
    }

    /**
     * Store a newly created payout in storage.
     */
    public function payoutstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pay_date' => 'required|date',
            'fullname' => 'required|string|max:255',
            'amount' => 'required|string|max:50',
            'processing_time' => 'required|string|max:100',
            'plan_id' => 'required|exists:plans,id',
            'account_type' => 'required|string|max:100', // FIX
            'location' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'flag_code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $payout = new Payout();

        $payout->pay_date = $request->pay_date;
        $payout->fullname = $request->fullname;
        $payout->amount = $request->amount;
        $payout->processing_time = $request->processing_time;
        $payout->plan_id = $request->plan_id;
        $payout->account_type = $request->account_type; // FIX
        $payout->location = $request->location;
        $payout->country = $request->country;
        $payout->flag_code = $request->flag_code;
        $payout->is_active = 1;
        $payout->sort_order = $request->sort_order ?? 0;

        $payout->save();

        $payout->encrypted_account = $payout->generateEncryptedAccount();
        $payout->save();

        return redirect()->route('admin.payouts.index')
            ->with('success', 'Payout created successfully!');
    }


    /**
     * Show the form for editing the specified payout.
     */
    public function payoutedit(Payout $payout)
    {
        $plans = Plan::where('status', 'active')->get();

        return view('admin.payoutedit', compact('payout', 'plans'));
    }


    /**
     * Update the specified payout in storage.
     */
    public function payoutupdate(Request $request, Payout $payout)
    {
        $validator = Validator::make($request->all(), [
            'pay_date' => 'required|date',
            'fullname' => 'required|string|max:255',
            'amount' => 'required|string|max:50',
            'processing_time' => 'required|string|max:100',
            'plan_id' => 'required|exists:plans,id',
            'account_type' => 'required|string|max:100', // FIX
            'location' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'flag_code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $payout->update([
            'pay_date' => $request->pay_date,
            'fullname' => $request->fullname,
            'amount' => $request->amount,
            'processing_time' => $request->processing_time,
            'plan_id' => $request->plan_id,
            'account_type' => $request->account_type, // FIX
            'location' => $request->location,
            'country' => $request->country,
            'flag_code' => $request->flag_code,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->route('admin.payouts.index')
            ->with('success', 'Payout updated successfully!');
    }


    /**
     * Remove the specified payout from storage.
     */
    public function payoutdestroy(Payout $payout)
    {
        $payout->delete();

        return redirect()->route('admin.payouts.index')
            ->with('success', 'Payout deleted successfully!');
    }


    /**
     * Quick add payout from dashboard
     */
    public function quickAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pay_date' => 'required|date',
            'fullname' => 'required|string|max:255',
            'amount' => 'required|string|max:50',
            'processing_time' => 'required|string|max:100',
            'plan_id' => 'required|exists:plans,id',
            'account_type' => 'required|string|max:100', // FIX
            'location' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $payout = new Payout();

        $payout->pay_date = $request->pay_date;
        $payout->fullname = $request->fullname;
        $payout->amount = $request->amount;
        $payout->processing_time = $request->processing_time;
        $payout->plan_id = $request->plan_id;
        $payout->account_type = $request->account_type; // FIX
        $payout->location = $request->location;
        $payout->is_active = true;

        $payout->save();

        $payout->encrypted_account = $payout->generateEncryptedAccount();
        $payout->save();

        return response()->json([
            'success' => true,
            'message' => 'Payout added successfully!',
            'payout' => $payout
        ]);
    }




    /**
     * Show pending copy trading requests
     */
    public function pending()
    {
        $requests = CopyTradingRequest::with(['user', 'plan', 'admin'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_pending' => $requests->count(),
            'total_amount' => $requests->sum('amount'),
            'unique_admins' => $requests->pluck('copy_admin_id')->unique()->count(),
        ];

        return view('admin.copy-trading.pending', compact('requests', 'stats'));
    }

    /**
     * Show approved copy trading requests
     */
    public function approved()
    {
        $requests = CopyTradingRequest::with(['user', 'plan', 'admin', 'processor'])
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->paginate(15);

        return view('admin.copy-trading.approved', compact('requests'));
    }

    /**
     * Show rejected copy trading requests
     */
    public function rejected()
    {
        $requests = CopyTradingRequest::with(['user', 'plan', 'admin', 'processor'])
            ->where('status', 'rejected')
            ->orderBy('rejected_at', 'desc')
            ->paginate(15);

        return view('admin.copy-trading.rejected', compact('requests'));
    }

    /**
     * Approve a copy trading request
     */
    // In AdminController.php, add these methods:

    // In AdminController.php



    public function pendingCopyRequests()
    {
        return view('admin.copytrading.pending', [
            'pendingRequests' => CopyTradingRequest::with(['user', 'plan', 'admin'])
                ->where('status', 'pending')
                ->latest()
                ->get(),

            'approvedRequests' => CopyTradingRequest::with(['user', 'plan', 'admin', 'processor'])
                ->where('status', 'approved')
                ->latest('approved_at')
                ->get(),

            'rejectedRequests' => CopyTradingRequest::with(['user', 'plan', 'admin', 'processor'])
                ->where('status', 'rejected')
                ->latest('rejected_at')
                ->get(),
        ]);
    }

    ////////////////////////////////////////////////////////////

    public function copypending()
    {
        return redirect()->route('admin.copy-trading.pendingCopyRequests');
    }

    public function copyapproved()
    {
        return redirect()->route('admin.copy-trading.pendingCopyRequests', ['tab' => 'approved']);
    }

    public function copyrejected()
    {
        return redirect()->route('admin.copy-trading.pendingCopyRequests', ['tab' => 'rejected']);
    }

    ////////////////////////////////////////////////////////////

    public function copyapprove($id)
    {
        return DB::transaction(function () use ($id) {

            $copyRequest = CopyTradingRequest::with(['user', 'plan'])
                ->where('id', $id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->firstOrFail();

            // Check participation limit
            $userParticipations = Investment::where('user_id', $copyRequest->user_id)
                ->where('plan_id', $copyRequest->plan_id)
                ->where('type', 'copy_trading')
                ->count(); // ✅ all-time count, not just active

            $planLimit = $copyRequest->plan->max_participations ?? 3;

            if ($planLimit > 0 && $userParticipations >= $planLimit) {
                return response()->json([
                    'success' => false,
                    'message' => "User has reached the maximum of {$planLimit} participations for this plan."
                ], 422);
            }

            $copyRequest->update([
                'status'       => 'approved',
                'approved_at'  => now(),
                'processed_by' => auth()->id(),
            ]);

            // ✅ Read duration_unit directly from plan — no fallback to 'days'
            $durationUnit  = $copyRequest->plan->duration_unit; // minutes, hours, or days
            $durationValue = $copyRequest->plan->duration;
            $interestRate  = $copyRequest->plan->interest_rate;

            // ✅ Correctly calculate end date based on actual unit
            $endDate = match ($durationUnit) {
                'minutes' => now()->addMinutes($durationValue),
                'hours'   => now()->addHours($durationValue),
                'days'    => now()->addDays($durationValue),
                default   => now()->addDays($durationValue), // safe fallback
            };

            $expectedProfit = round(($copyRequest->amount * $interestRate) / 100, 2);

            $investment = Investment::create([
                'user_id'                => $copyRequest->user_id,
                'plan_id'                => $copyRequest->plan_id,
                'type'                   => 'copy_trading',
                'amount_invested'        => $copyRequest->amount,
                'expected_profit'        => $expectedProfit,
                'total_profit'           => 0,
                'current_value'          => $copyRequest->amount,
                'profit_loss'            => 0,
                'status'                 => 'active',
                'start_date'             => now(),
                'end_date'               => $endDate,
                'copy_admin_id'          => $copyRequest->copy_admin_id,
                'copy_admin_name'        => $copyRequest->copy_admin_name,
                'copy_server_name'       => $copyRequest->copy_server_name,
                // ✅ Snapshots use the real unit from the plan
                'snapshot_duration_unit'  => $durationUnit,
                'snapshot_duration_value' => $durationValue,
                'snapshot_interest_rate'  => $interestRate,
                'snapshot_plan_name'      => $copyRequest->plan->name,
                'snapshot_min_amount'     => $copyRequest->plan->minimum_amount,
                'snapshot_max_amount'     => $copyRequest->plan->maximum_amount,
                'snapshot_features'       => $copyRequest->plan->features,
                'snapshot_assets_traded'  => $copyRequest->plan->assets_traded,
            ]);

            $investment->updateValue();

            try {
                $copyRequest->user->notify(new TransactionNotification(
                    'Copy Trading Approved',
                    "Your copy trade of \${$copyRequest->amount} has been approved!\n" .
                        "Expected Profit: \${$expectedProfit}\n" .
                        "Duration: {$durationValue} {$durationUnit}\n" .
                        "Your investment is now active."
                ));
            } catch (\Exception $e) {
                \Log::error('Notification failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Request approved successfully'
            ]);
        });
    }

    ////////////////////////////////////////////////////////////

    public function copyreject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        DB::transaction(function () use ($request, $id) {

            $copyRequest = CopyTradingRequest::with('user')
                ->where('id', $id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->firstOrFail();

            if (!$copyRequest) {
                throw new \Exception('Request not found or already processed.');
            }

            // Update request status
            $copyRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'rejected_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            // ✅ IMPORTANT: Refund the user's balance
            $user = $copyRequest->user;
            $user->available_balance += $copyRequest->amount;
            $user->save();

            // Send notification
            try {
                $user->notify(new TransactionNotification(
                    'Copy Trading Request Rejected',
                    "Your copy trading request of \${$copyRequest->amount} has been rejected.\n" .
                        "Reason: {$request->rejection_reason}\n" .
                        "Your funds have been refunded to your available balance."
                ));
            } catch (\Exception $e) {
                \Log::error('Notification failed: ' . $e->getMessage());
            }
        });

        return back()->with('success', 'Copy trading request rejected and funds refunded.');
    }

    ////////////////////////////////////////////////////////////

    public function show($id)
    {
        $copyRequest = CopyTradingRequest::with(['user', 'plan', 'admin', 'processor'])
            ->findOrFail($id);

        return view('admin.copytrading.show', compact('copyRequest')); // ✅ FIXED VIEW
    }

    ////////////////////////////////////////////////////////////

    public function dashboard()
    {
        return view('admin.copy-trading.dashboard', [
            'stats' => [
                'pending_count' => CopyTradingRequest::where('status', 'pending')->count(),
                'approved_today' => CopyTradingRequest::where('status', 'approved')
                    ->whereDate('approved_at', today())
                    ->count(),
                'total_approved' => CopyTradingRequest::where('status', 'approved')->count(),
                'total_amount' => CopyTradingRequest::where('status', 'approved')->sum('amount'),
            ],

            'recentRequests' => CopyTradingRequest::with(['user', 'plan'])
                ->latest()
                ->limit(10)
                ->get()
        ]);
    }




    public function rejectBalanceWithdrawal(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be rejected.');
        }

        DB::transaction(function () use ($request, $withdrawal) {
            $user = $withdrawal->user;

            // REFUND the amount back to user's balance
            $user->available_balance += $withdrawal->amount;
            $user->save();

            // Update withdrawal status
            $withdrawal->status = 'rejected';
            $withdrawal->admin_note = $request->admin_note;
            $withdrawal->save();

            // Send notification to user
            try {
                $user->notify(new TransactionNotification(
                    'Withdrawal Rejected',
                    'Your withdrawal request of $' . number_format($withdrawal->amount, 2) .
                        ' has been rejected. Reason: ' . $request->admin_note
                ));
            } catch (\Exception $e) {
                \Log::error('Notification failed: ' . $e->getMessage());
            }
        });

        return back()->with('success', 'Withdrawal rejected, amount refunded to user.');
    }


// strategy enrollments

    /**
     * Display a listing of strategies.
     */
    public function strategyindex()
    {
        $strategies = Strategy::withCount('enrollments')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_strategies' => Strategy::count(),
            'active_strategies' => Strategy::where('is_active', true)->count(),
            'total_enrollments' => StrategyEnrollment::count(),
            'active_enrollments' => StrategyEnrollment::where('status', 'active')->count(),
            'total_revenue' => StrategyEnrollment::sum('amount_paid'),
        ];

        return view('admin.strategies.index', compact('strategies', 'stats'));
    }

    /**
     * Show form for creating a new strategy.
     */
    public function strategycreate()
    {
        return view('admin.strategies.create');
    }

    /**
     * Store a newly created strategy.
     */
    /**
     * Store a newly created strategy.
     */
    public function strategystore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:strategies,slug',
            'description' => 'required|string',
            'long_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'modules' => 'nullable|array',
            'modules.*.title' => 'required|string',
            'modules.*.content' => 'required|string',
            'modules.*.video_url' => 'nullable|url',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'string',
            'instructor_name' => 'nullable|string|max:255',
            'instructor_bio' => 'nullable|string',
            'instructor_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'badge_text' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'estimated_hours' => 'nullable|integer|min:0',
        ]);

        // Prepare data array
        $data = [
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']),
            'description' => $validated['description'],
            'long_description' => $validated['long_description'] ?? null,
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'] ?? null,
            'difficulty_level' => $validated['difficulty_level'] ?? null,
            'instructor_name' => $validated['instructor_name'] ?? null,
            'instructor_bio' => $validated['instructor_bio'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'is_active' => $request->has('is_active'),
            'is_popular' => $request->has('is_popular'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'estimated_hours' => $validated['estimated_hours'] ?? null,
        ];

        // Handle JSON fields - convert arrays to JSON
        if (isset($validated['features']) && is_array($validated['features'])) {
            $data['features'] = json_encode(array_values(array_filter($validated['features'])));
        }

        if (isset($validated['modules']) && is_array($validated['modules'])) {
            $data['modules'] = json_encode(array_values($validated['modules']));
        }

        if (isset($validated['learning_objectives']) && is_array($validated['learning_objectives'])) {
            $data['learning_objectives'] = json_encode(array_values(array_filter($validated['learning_objectives'])));
        }

        if (isset($validated['prerequisites']) && is_array($validated['prerequisites'])) {
            $data['prerequisites'] = json_encode(array_values(array_filter($validated['prerequisites'])));
        }

        // Handle instructor image upload
        if ($request->hasFile('instructor_image')) {
            $path = $request->file('instructor_image')->store('strategies/instructors', 'public');
            $data['instructor_image'] = $path;
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('strategies/covers', 'public');
            $data['cover_image'] = $path;
        }

        Strategy::create($data);

        return redirect()->route('admin.strategies.strategyindex')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Show form for editing a strategy.
     */
    public function strategyedit($id)
    {
        $strategy = Strategy::findOrFail($id);
        return view('admin.strategies.edit', compact('strategy'));
    }

    /**
     * Update the specified strategy.
     */
    public function strategyupdate(Request $request, $id)
    {
        $strategy = Strategy::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:strategies,slug,' . $id,
            'description' => 'required|string',
            'long_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'modules' => 'nullable|array',
            'modules.*.title' => 'required|string',
            'modules.*.content' => 'required|string',
            'modules.*.video_url' => 'nullable|url',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'string',
            'instructor_name' => 'nullable|string|max:255',
            'instructor_bio' => 'nullable|string',
            'instructor_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'badge_text' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'estimated_hours' => 'nullable|integer|min:0',
        ]);

        $data = [
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']),
            'description' => $validated['description'],
            'long_description' => $validated['long_description'] ?? null,
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'] ?? null,
            'difficulty_level' => $validated['difficulty_level'] ?? null,
            'instructor_name' => $validated['instructor_name'] ?? null,
            'instructor_bio' => $validated['instructor_bio'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'estimated_hours' => $validated['estimated_hours'] ?? null,

            'is_active' => $request->has('is_active'),
            'is_popular' => $request->has('is_popular'),
        ];

        // Convert arrays to JSON
        $data['features'] = isset($validated['features'])
            ? json_encode(array_values(array_filter($validated['features'])))
            : null;

        $data['learning_objectives'] = isset($validated['learning_objectives'])
            ? json_encode(array_values(array_filter($validated['learning_objectives'])))
            : null;

        $data['prerequisites'] = isset($validated['prerequisites'])
            ? json_encode(array_values(array_filter($validated['prerequisites'])))
            : null;

        $data['modules'] = isset($validated['modules'])
            ? json_encode(array_values($validated['modules']))
            : null;

        if ($request->hasFile('instructor_image')) {

            // delete old image
            if (
                $strategy->instructor_image &&
                \Illuminate\Support\Facades\Storage::disk('public')->exists($strategy->instructor_image)
            ) {

                \Illuminate\Support\Facades\Storage::disk('public')->delete($strategy->instructor_image);
            }

            $path = $request->file('instructor_image')
                ->store('strategies/instructors', 'public');

            $data['instructor_image'] = $path;
        }

        if ($request->hasFile('cover_image')) {

            // delete old image
            if (
                $strategy->cover_image &&
                \Illuminate\Support\Facades\Storage::disk('public')->exists($strategy->cover_image)
            ) {

                \Illuminate\Support\Facades\Storage::disk('public')->delete($strategy->cover_image);
            }

            $path = $request->file('cover_image')
                ->store('strategies/covers', 'public');

            $data['cover_image'] = $path;
        }





        $strategy->update($data);

        return redirect()->route('admin.strategies.strategyindex')
            ->with('success', 'Strategy updated successfully.');
    }

    /**
     * Delete a strategy.
     */
    /**
     * Delete a strategy.
     */
    public function strategydestroy($id)
    {
        $strategy = Strategy::findOrFail($id);

        // delete related enrollments
        $strategy->enrollments()->delete();

        // delete cover image
        if ($strategy->cover_image && Storage::disk('public')->exists($strategy->cover_image)) {
            Storage::disk('public')->delete($strategy->cover_image);
        }

        // delete instructor image
        if ($strategy->instructor_image && Storage::disk('public')->exists($strategy->instructor_image)) {
            Storage::disk('public')->delete($strategy->instructor_image);
        }

        // delete strategy
        $strategy->delete();

        return redirect()->route('admin.strategies.strategyindex')
            ->with('success', 'Strategy deleted successfully.');
    }
    /**
     * Display enrollments for a specific strategy.
     */
    public function strategyenrollments($id)
    {
        $strategy = Strategy::with(['enrollments.user'])->findOrFail($id);

        $enrollments = $strategy->enrollments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.strategies.enrollments', compact('strategy', 'enrollments'));
    }




    // Add these methods to your AdminController.php

// ─────────────────────────────────────────
// GET BANK TRANSFER NOTIFICATIONS
// ─────────────────────────────────────────
public function getBankTransferNotifications()
{
    $pendingCount = BankTransfer::where('status', 'pending')
        ->where('expires_at', '>', now())
        ->count();

    return response()->json([
        'count' => $pendingCount,
        'has_pending' => $pendingCount > 0,
    ]);
}

// ─────────────────────────────────────────
// SHOW BANK TRANSFER REQUESTS
// ─────────────────────────────────────────
public function bankTransferRequests()
{
    $bankTransfers = BankTransfer::with(['user', 'deposit'])
        ->whereIn('status', ['pending', 'details_sent'])
        ->orderBy('created_at', 'desc')
        ->get();

    $pendingCount = $bankTransfers->where('status', 'pending')->count();

    return view('admin.bank-transfers.index', compact('bankTransfers', 'pendingCount'));
}

// ─────────────────────────────────────────
// SHOW SEND BANK DETAILS FORM
// ─────────────────────────────────────────
public function showSendBankDetailsForm($bankTransferId)
{
    $bankTransfer = BankTransfer::with('user')->findOrFail($bankTransferId);

    return view('admin.bank-transfers.send-details', compact('bankTransfer'));
}

// ─────────────────────────────────────────
// SEND BANK DETAILS (Admin submits the form)
// ─────────────────────────────────────────
public function sendBankDetails(Request $request, $bankTransferId)
{
    $request->validate([
        'bank_name' => 'required|string|max:255',
        'account_name' => 'required|string|max:255',
        'account_number' => 'required|string|max:255',
        'swift_code' => 'nullable|string|max:50',
        'routing_number' => 'nullable|string|max:50',
        'iban' => 'nullable|string|max:50',
        'sort_code' => 'nullable|string|max:50',
        'bank_address' => 'nullable|string|max:500',
        'instructions' => 'nullable|string|max:1000',
    ]);

    $bankTransfer = BankTransfer::with('user')->findOrFail($bankTransferId);

    // Create deposit NOW (when admin sends details)
    $deposit = Deposit::create([
        'user_id' => $bankTransfer->user_id,
        'amount_deposited' => $bankTransfer->amount,
        'payment_method' => 'bank_transfer',
        'status' => 0,
        'bank_details' => [
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'swift_code' => $request->swift_code,
            'routing_number' => $request->routing_number,
            'iban' => $request->iban,
            'sort_code' => $request->sort_code,
            'bank_address' => $request->bank_address,
            'instructions' => $request->instructions,
            'sent_by_admin' => auth()->id(),
            'sent_at' => now()->toDateTimeString(),
        ],
        'bank_details_sent_at' => now(),
    ]);

    $bankTransfer->update([
        'deposit_id' => $deposit->id,
        'status' => 'details_sent',
        'bank_details_sent_at' => now(),
    ]);

    // Send email to user with bank details
    try {
        Mail::to($bankTransfer->user->email)->send(new BankTransferDetailsMail(
            $bankTransfer->user,
            $bankTransfer,
            $bankTransfer->amount,
            $request->all()
        ));
    } catch (\Exception $e) {
        Log::error('Failed to send bank transfer details email: ' . $e->getMessage());
    }

    // Notify user
    try {
        $bankTransfer->user->notify(new TransactionNotification(
            'Bank Details Received',
            'Bank details for your deposit of $' . number_format($bankTransfer->amount, 2) . ' have been sent. 
            Please check your email and make the transfer.'
        ));
    } catch (\Exception $e) {
        Log::error('Notification failed: ' . $e->getMessage());
    }

    return redirect()->route('admin.bank-transfers.requests')
        ->with('success', 'Bank details sent to user successfully!');
}
}