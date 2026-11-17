<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\MonthlyDue;
use App\Models\MonthlyRegister;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. System Settings
        $settings = [
            'society_name' => 'SHRI SAMRUDDHI SOCIETY',
            'society_tagline' => 'Society Management & Loan Accounting System',
            'registration_number' => 'REG/GUJ/AHM/2018/14892',
            'society_address' => 'Shop No. 4-6, Samruddhi Commercial Complex, Near S.P. Ring Road, Ahmedabad, Gujarat - 380058',
            'contact_phone' => '+91 98765 43210',
            'contact_email' => 'contact@samruddhisociety.in',
            'financial_year' => '2026-2027',
            'default_monthly_deposit' => '1000.00',
            'default_interest_rate' => '1.00', // 1% per month = 12% p.a.
            'interest_calculation_method' => 'reducing_balance',
            'default_late_penalty' => '100.00',
            'penalty_grace_days' => '10',
            'currency_symbol' => '₹',
            'upi_id' => 'samruddhisociety@upi',
            'upi_payee_name' => 'Shri Samruddhi Co-op Society',
        ];

        foreach ($settings as $key => $val) {
            Setting::set($key, $val);
        }

        // 2. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@samruddhi.org'],
            [
                'name' => 'Society Admin (President)',
                'mobile' => '9876543210',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 3. 28 Members Data from Physical Register
        $membersData = [
            ['name' => 'Rajeshbhai K. Sharma',     'mobile' => '9825000001', 'dep_bal' => 36000, 'loan' => 100000, 'inst' => 10000, 'rate' => 1.0, 'rem_loan' => 70000],
            ['name' => 'Sureshbhai M. Patel',      'mobile' => '9825000002', 'dep_bal' => 24000, 'loan' => 50000,  'inst' => 5000,  'rate' => 1.0, 'rem_loan' => 35000],
            ['name' => 'Amitbhai R. Kumar',        'mobile' => '9825000003', 'dep_bal' => 48000, 'loan' => 0,      'inst' => 0,     'rate' => 1.0, 'rem_loan' => 0],
            ['name' => 'Rameshbhai J. Shah',       'mobile' => '9825000004', 'dep_bal' => 30000, 'loan' => 80000,  'inst' => 8000,  'rate' => 1.0, 'rem_loan' => 48000],
            ['name' => 'Vijaybhai P. Verma',       'mobile' => '9825000005', 'dep_bal' => 18000, 'loan' => 40000,  'inst' => 4000,  'rate' => 1.0, 'rem_loan' => 24000],
            ['name' => 'Deepakbhai S. Joshi',      'mobile' => '9825000006', 'dep_bal' => 42000, 'loan' => 120000, 'inst' => 10000, 'rate' => 1.0, 'rem_loan' => 90000],
            ['name' => 'Sanjaybhai D. Mehta',      'mobile' => '9825000007', 'dep_bal' => 50000, 'loan' => 0,      'inst' => 0,     'rate' => 1.0, 'rem_loan' => 0],
            ['name' => 'Manojbhai C. Chauhan',     'mobile' => '9825000008', 'dep_bal' => 26000, 'loan' => 60000,  'inst' => 6000,  'rate' => 1.0, 'rem_loan' => 42000],
            ['name' => 'Nileshbhai A. Dave',       'mobile' => '9825000009', 'dep_bal' => 32000, 'loan' => 75000,  'inst' => 7500,  'rate' => 1.0, 'rem_loan' => 52500],
            ['name' => 'Pravinbhai K. Rathod',     'mobile' => '9825000010', 'dep_bal' => 28000, 'loan' => 50000,  'inst' => 5000,  'rate' => 1.0, 'rem_loan' => 30000],
            ['name' => 'Dineshbhai T. Solanki',    'mobile' => '9825000011', 'dep_bal' => 34000, 'loan' => 0,      'inst' => 0,     'rate' => 1.0, 'rem_loan' => 0],
            ['name' => 'Maheshbhai L. Vaghela',    'mobile' => '9825000012', 'dep_bal' => 22000, 'loan' => 30000,  'inst' => 3000,  'rate' => 1.0, 'rem_loan' => 18000],
            ['name' => 'Arvindbhai G. Parmar',     'mobile' => '9825000013', 'dep_bal' => 45000, 'loan' => 90000,  'inst' => 9000,  'rate' => 1.0, 'rem_loan' => 54000],
            ['name' => 'Hiteshbhai N. Bhatt',      'mobile' => '9825000014', 'dep_bal' => 38000, 'loan' => 0,      'inst' => 0,     'rate' => 1.0, 'rem_loan' => 0],
            ['name' => 'Nareshbhai V. Pandya',     'mobile' => '9825000015', 'dep_bal' => 29000, 'loan' => 65000,  'inst' => 6500,  'rate' => 1.0, 'rem_loan' => 39000],
            ['name' => 'Bhaveshbhai B. Patel',     'mobile' => '9825000016', 'dep_bal' => 31000, 'loan' => 50000,  'inst' => 5000,  'rate' => 1.0, 'rem_loan' => 25000],
            ['name' => 'Ketanbhai M. Trivedi',     'mobile' => '9825000017', 'dep_bal' => 40000, 'loan' => 100000, 'inst' => 10000, 'rate' => 1.0, 'rem_loan' => 60000],
            ['name' => 'Pankajbhai H. Soni',       'mobile' => '9825000018', 'dep_bal' => 35000, 'loan' => 0,      'inst' => 0,     'rate' => 1.0, 'rem_loan' => 0],
            ['name' => 'Jayeshbhai S. Modi',       'mobile' => '9825000019', 'dep_bal' => 27000, 'loan' => 45000,  'inst' => 4500,  'rate' => 1.0, 'rem_loan' => 27000],
            ['name' => 'Bharatbhai K. Rawal',      'mobile' => '9825000020', 'dep_bal' => 33000, 'loan' => 70000,  'inst' => 7000,  'rate' => 1.0, 'rem_loan' => 49000],
            ['name' => 'Kiranbhai R. Barot',       'mobile' => '9825000021', 'dep_bal' => 25000, 'loan' => 0,      'inst' => 0,     'rate' => 1.0, 'rem_loan' => 0],
            ['name' => 'Ashokbhai M. Thakor',      'mobile' => '9825000022', 'dep_bal' => 20000, 'loan' => 35000,  'inst' => 3500,  'rate' => 1.0, 'rem_loan' => 21000],
            ['name' => 'Nitinbhai C. Desai',       'mobile' => '9825000023', 'dep_bal' => 46000, 'loan' => 110000, 'inst' => 10000, 'rate' => 1.0, 'rem_loan' => 77000],
            ['name' => 'Chetanbhai P. Vyas',       'mobile' => '9825000024', 'dep_bal' => 37000, 'loan' => 0,      'inst' => 0,     'rate' => 1.0, 'rem_loan' => 0],
            ['name' => 'Pareshbhai K. Jani',       'mobile' => '9825000025', 'dep_bal' => 30000, 'loan' => 55000,  'inst' => 5500,  'rate' => 1.0, 'rem_loan' => 33000],
            ['name' => 'Mahendrabhai D. Prajapati','mobile' => '9825000026', 'dep_bal' => 28000, 'loan' => 40000,  'inst' => 4000,  'rate' => 1.0, 'rem_loan' => 24000],
            ['name' => 'Jagdishbhai R. Rajput',    'mobile' => '9825000027', 'dep_bal' => 44000, 'loan' => 85000,  'inst' => 8500,  'rate' => 1.0, 'rem_loan' => 59500],
            ['name' => 'Hareshbhai V. Panchal',    'mobile' => '9825000028', 'dep_bal' => 39000, 'loan' => 0,      'inst' => 0,     'rate' => 1.0, 'rem_loan' => 0],
        ];

        $createdMembers = [];

        foreach ($membersData as $idx => $m) {
            $code = 'SSS-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT);

            // Create Member User Login
            $user = User::create([
                'name' => $m['name'],
                'email' => 'member' . ($idx + 1) . '@samruddhi.org',
                'mobile' => $m['mobile'],
                'password' => Hash::make('member123'),
                'role' => 'member',
                'status' => 'active',
            ]);

            // Create Member Record
            $member = Member::create([
                'member_code' => $code,
                'user_id' => $user->id,
                'full_name' => $m['name'],
                'mobile_number' => $m['mobile'],
                'alternate_mobile' => '98980' . str_pad($idx + 1, 5, '0', STR_PAD_LEFT),
                'address' => 'House #' . (10 + $idx) . ', Samruddhi Nagar, Ahmedabad',
                'joining_date' => Carbon::now()->subMonths(24 - ($idx % 12)),
                'monthly_deposit_amount' => 1000.00,
                'initial_deposit_balance' => $m['dep_bal'],
                'initial_loan_balance' => $m['rem_loan'],
                'current_deposit_balance' => $m['dep_bal'],
                'current_loan_balance' => $m['rem_loan'],
                'status' => 'active',
            ]);

            $user->update(['member_id' => $member->id]);

            // Opening deposit ledger entry
            MemberDeposit::create([
                'member_id' => $member->id,
                'transaction_type' => 'opening_balance',
                'amount' => $m['dep_bal'],
                'balance_after' => $m['dep_bal'],
                'transaction_date' => Carbon::now()->startOfYear()->toDateString(),
                'description' => 'Migrated initial deposit opening balance from manual register',
            ]);

            // If member has a loan, create loan record & opening transaction
            if ($m['loan'] > 0) {
                $loan = Loan::create([
                    'loan_number' => 'LN-2026-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                    'member_id' => $member->id,
                    'application_date' => Carbon::now()->subMonths(5),
                    'approval_date' => Carbon::now()->subMonths(5),
                    'loan_amount' => $m['loan'],
                    'interest_rate' => $m['rate'], // 1% monthly
                    'interest_calculation_method' => 'reducing_balance',
                    'monthly_installment' => $m['inst'],
                    'loan_duration_months' => 12,
                    'first_installment_date' => Carbon::now()->subMonths(4)->startOfMonth(),
                    'loan_purpose' => 'Personal / Household Need',
                    'remarks' => 'Migrated from manual register with remaining principal ₹' . number_format($m['rem_loan'], 2),
                    'status' => 'active',
                    'remaining_principal' => $m['rem_loan'],
                    'total_interest_charged' => 0.00,
                    'total_interest_paid' => 0.00,
                    'total_principal_paid' => $m['loan'] - $m['rem_loan'],
                ]);

                // Loan disbursement ledger record
                LoanTransaction::create([
                    'loan_id' => $loan->id,
                    'member_id' => $member->id,
                    'transaction_type' => 'opening_balance',
                    'principal_amount' => $m['loan'],
                    'interest_amount' => 0.00,
                    'penalty_amount' => 0.00,
                    'principal_balance_after' => $m['rem_loan'],
                    'transaction_date' => Carbon::now()->subMonths(5)->toDateString(),
                    'remarks' => 'Opening loan balance migration',
                ]);
            }

            $createdMembers[] = $member;
        }

        // 4. Generate Current Month Register (November 2026)
        $currentMonth = 11;
        $currentYear = 2026;
        $regDate = Carbon::create($currentYear, $currentMonth, 1);

        $register = MonthlyRegister::create([
            'month' => $currentMonth,
            'year' => $currentYear,
            'title' => 'November 2026 Society Collection Register',
            'generated_date' => $regDate->toDateString(),
            'status' => 'active',
            'notes' => 'Official monthly collection register for November 2026',
        ]);

        $totalExpected = 0;
        $totalCollected = 0;

        foreach ($createdMembers as $idx => $member) {
            $activeLoan = $member->loans()->where('status', 'active')->first();
            $deposit = $member->monthly_deposit_amount;
            $loanAmt = $activeLoan ? $activeLoan->loan_amount : 0;
            $remBalance = $activeLoan ? $activeLoan->remaining_principal : 0;
            $rate = $activeLoan ? $activeLoan->interest_rate : 1.0;
            $interest = $activeLoan ? round(($remBalance * $rate) / 100, 2) : 0;
            $installment = $activeLoan ? $activeLoan->monthly_installment : 0;
            $penalty = ($idx === 5) ? 100.00 : 0.00; // member 6 has a minor late penalty

            $totalPayable = round($deposit + $interest + $installment + $penalty, 2);

            // Mark some members as Paid to demonstrate the register replica with colors & status
            $isPaid = ($idx < 12);
            $isPartial = ($idx === 14);

            $amountPaid = 0;
            $depositPaid = 0;
            $interestPaid = 0;
            $instPaid = 0;
            $penPaid = 0;
            $status = 'pending';
            $payMode = null;
            $payDate = null;
            $payRef = null;

            if ($isPaid) {
                $status = 'paid';
                $amountPaid = $totalPayable;
                $depositPaid = $deposit;
                $interestPaid = $interest;
                $instPaid = $installment;
                $penPaid = $penalty;
                $payMode = ($idx % 2 === 0) ? 'cash' : 'online';
                $payDate = Carbon::create(2026, 11, 5 + ($idx % 5))->toDateString();
                $payRef = ($payMode === 'online') ? 'UPI/' . rand(100000000, 999999999) : 'CSH-' . rand(1000, 9999);
            } elseif ($isPartial) {
                $status = 'partial';
                $amountPaid = $deposit + $interest;
                $depositPaid = $deposit;
                $interestPaid = $interest;
                $payMode = 'cash';
                $payDate = Carbon::create(2026, 11, 8)->toDateString();
                $payRef = 'CSH-PART-' . rand(1000, 9999);
            }

            $due = MonthlyDue::create([
                'monthly_register_id' => $register->id,
                'member_id' => $member->id,
                'loan_id' => $activeLoan ? $activeLoan->id : null,
                'monthly_deposit' => $deposit,
                'loan_amount' => $loanAmt,
                'interest' => $interest,
                'monthly_installment' => $installment,
                'penalty' => $penalty,
                'total_payable' => $totalPayable,
                'remaining_loan_balance' => $remBalance,
                'amount_paid' => $amountPaid,
                'deposit_paid' => $depositPaid,
                'interest_paid' => $interestPaid,
                'installment_paid' => $instPaid,
                'penalty_paid' => $penPaid,
                'payment_status' => $status,
                'payment_date' => $payDate,
                'payment_mode' => $payMode,
                'payment_reference' => $payRef,
            ]);

            if ($amountPaid > 0) {
                $receipt = 'SSS-202611-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
                Payment::create([
                    'receipt_number' => $receipt,
                    'member_id' => $member->id,
                    'monthly_due_id' => $due->id,
                    'loan_id' => $activeLoan ? $activeLoan->id : null,
                    'payment_date' => $payDate,
                    'payment_month' => '2026-11',
                    'total_received' => $amountPaid,
                    'deposit_portion' => $depositPaid,
                    'interest_portion' => $interestPaid,
                    'installment_portion' => $instPaid,
                    'penalty_portion' => $penPaid,
                    'payment_mode' => $payMode,
                    'payment_status' => 'paid',
                    'transaction_reference' => $payRef,
                    'recorded_by' => $admin->id,
                    'remarks' => 'Monthly collection payment for November 2026',
                ]);

                // Update deposit ledger
                if ($depositPaid > 0) {
                    $newDepBal = $member->current_deposit_balance + $depositPaid;
                    MemberDeposit::create([
                        'member_id' => $member->id,
                        'transaction_type' => 'monthly_deposit',
                        'amount' => $depositPaid,
                        'balance_after' => $newDepBal,
                        'transaction_date' => $payDate,
                        'description' => 'November 2026 Monthly Deposit contribution',
                    ]);
                    $member->current_deposit_balance = $newDepBal;
                }

                // Update loan balance & transactions if principal installment paid
                if ($instPaid > 0 && $activeLoan) {
                    $newPrincipal = max(0, $activeLoan->remaining_principal - $instPaid);
                    $activeLoan->remaining_principal = $newPrincipal;
                    $activeLoan->total_principal_paid += $instPaid;
                    $activeLoan->total_interest_paid += $interestPaid;
                    $activeLoan->save();

                    LoanTransaction::create([
                        'loan_id' => $activeLoan->id,
                        'member_id' => $member->id,
                        'transaction_type' => 'principal_repayment',
                        'principal_amount' => $instPaid,
                        'interest_amount' => $interestPaid,
                        'penalty_amount' => $penPaid,
                        'principal_balance_after' => $newPrincipal,
                        'transaction_date' => $payDate,
                        'remarks' => 'Monthly principal repayment & interest paid',
                    ]);

                    $member->current_loan_balance = $newPrincipal;
                }

                $member->save();
            }

            $totalExpected += $totalPayable;
            $totalCollected += $amountPaid;
        }

        $register->recalculateTotals();

        // 5. Welcome notification
        Notification::create([
            'title' => 'Welcome to Shri Samruddhi Society Portal',
            'message' => 'The digital management and loan accounting system is now active. Members can view statements and pay dues online.',
            'type' => 'general',
            'channel' => 'in_app',
            'is_read' => false,
        ]);

        AuditLog::log('system_seeded', 'System initialized with 28 members, initial register, and administrator account', $admin->id);
    }
}
