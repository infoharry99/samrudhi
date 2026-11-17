<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrationController extends Controller
{
    public function index()
    {
        $existingCount = Member::count();
        return view('admin.migration.index', compact('existingCount'));
    }

    public function downloadTemplate()
    {
        $filename = "society_member_migration_template.csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, [
                'Full Name',
                'Mobile Number',
                'Alternate Mobile',
                'Address',
                'Joining Date (YYYY-MM-DD)',
                'Monthly Deposit Amount',
                'Initial Savings Balance',
                'Existing Loan Amount',
                'Existing Loan Remaining Balance',
                'Monthly Installment',
                'Monthly Interest Rate %'
            ]);

            // Sample rows
            fputcsv($file, ['Ramesh Patel', '9898011223', '9898011224', '12 Samruddhi Nagar', '2024-01-01', '1000', '24000', '50000', '35000', '5000', '1.0']);
            fputcsv($file, ['Suresh Shah', '9898011225', '', '15 Samruddhi Nagar', '2024-02-01', '1000', '18000', '0', '0', '0', '1.0']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        $rows = [];
        $header = null;
        $rowIdx = 0;

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $rowIdx++;
            if ($rowIdx === 1) {
                $header = $data;
                continue;
            }
            if (empty(array_filter($data))) continue;

            $fullName = trim($data[0] ?? '');
            $mobile = preg_replace('/[^0-9]/', '', trim($data[1] ?? ''));
            $altMobile = trim($data[2] ?? '');
            $address = trim($data[3] ?? '');
            $joiningDate = trim($data[4] ?? date('Y-m-d'));
            $monthlyDeposit = floatval($data[5] ?? 1000);
            $initialSavings = floatval($data[6] ?? 0);
            $loanAmount = floatval($data[7] ?? 0);
            $remLoan = floatval($data[8] ?? 0);
            $installment = floatval($data[9] ?? 0);
            $rate = floatval($data[10] ?? 1.0);

            // Validation checks
            $errors = [];
            if (empty($fullName)) $errors[] = 'Full name missing';
            if (strlen($mobile) < 10) $errors[] = 'Invalid mobile number';
            if (Member::where('mobile_number', $mobile)->exists()) {
                $errors[] = 'Mobile already exists in database';
            }

            $rows[] = [
                'name' => $fullName,
                'mobile' => $mobile,
                'alt_mobile' => $altMobile,
                'address' => $address,
                'joining_date' => $joiningDate,
                'monthly_deposit' => $monthlyDeposit,
                'initial_savings' => $initialSavings,
                'loan_amount' => $loanAmount,
                'rem_loan' => $remLoan,
                'installment' => $installment,
                'rate' => $rate,
                'errors' => $errors,
                'valid' => empty($errors),
            ];
        }

        fclose($handle);

        session(['pending_migration_rows' => $rows]);

        return view('admin.migration.preview', compact('rows'));
    }

    public function process(Request $request)
    {
        $rows = session('pending_migration_rows', []);
        if (empty($rows)) {
            return redirect()->route('admin.migration.index')->with('error', 'No migration data found in session.');
        }

        DB::beginTransaction();
        try {
            $importedCount = 0;
            $lastMember = Member::orderBy('id', 'desc')->first();
            $nextNum = $lastMember ? ((int) str_replace('SSS-', '', $lastMember->member_code) + 1) : 1;

            foreach ($rows as $row) {
                if (!$row['valid']) continue;

                $memberCode = 'SSS-' . str_pad($nextNum++, 3, '0', STR_PAD_LEFT);

                // Create user login
                $user = User::create([
                    'name' => $row['name'],
                    'email' => strtolower(str_replace(' ', '', $memberCode)) . '@samruddhi.org',
                    'mobile' => $row['mobile'],
                    'password' => Hash::make('member123'),
                    'role' => 'member',
                    'status' => 'active',
                ]);

                // Create member
                $member = Member::create([
                    'member_code' => $memberCode,
                    'user_id' => $user->id,
                    'full_name' => $row['name'],
                    'mobile_number' => $row['mobile'],
                    'alternate_mobile' => $row['alt_mobile'],
                    'address' => $row['address'],
                    'joining_date' => $row['joining_date'],
                    'monthly_deposit_amount' => $row['monthly_deposit'],
                    'initial_deposit_balance' => $row['initial_savings'],
                    'initial_loan_balance' => $row['rem_loan'],
                    'current_deposit_balance' => $row['initial_savings'],
                    'current_loan_balance' => $row['rem_loan'],
                    'status' => 'active',
                ]);

                $user->update(['member_id' => $member->id]);

                // Opening deposit ledger
                if ($row['initial_savings'] > 0) {
                    MemberDeposit::create([
                        'member_id' => $member->id,
                        'transaction_type' => 'opening_balance',
                        'amount' => $row['initial_savings'],
                        'balance_after' => $row['initial_savings'],
                        'transaction_date' => $row['joining_date'],
                        'description' => 'Migrated initial savings opening balance',
                    ]);
                }

                // Opening loan ledger if member has loan
                if ($row['loan_amount'] > 0 || $row['rem_loan'] > 0) {
                    $loan = Loan::create([
                        'loan_number' => 'LN-' . date('Y') . '-' . str_pad($member->id, 3, '0', STR_PAD_LEFT),
                        'member_id' => $member->id,
                        'application_date' => $row['joining_date'],
                        'approval_date' => $row['joining_date'],
                        'loan_amount' => $row['loan_amount'] > 0 ? $row['loan_amount'] : $row['rem_loan'],
                        'interest_rate' => $row['rate'],
                        'interest_calculation_method' => 'reducing_balance',
                        'monthly_installment' => $row['installment'] > 0 ? $row['installment'] : round($row['rem_loan'] / 10, 2),
                        'loan_duration_months' => 12,
                        'first_installment_date' => Carbon::parse($row['joining_date'])->addMonth()->toDateString(),
                        'loan_purpose' => 'Migrated register loan',
                        'status' => 'active',
                        'remaining_principal' => $row['rem_loan'],
                        'total_principal_paid' => max(0, $row['loan_amount'] - $row['rem_loan']),
                    ]);

                    LoanTransaction::create([
                        'loan_id' => $loan->id,
                        'member_id' => $member->id,
                        'transaction_type' => 'opening_balance',
                        'principal_amount' => $loan->loan_amount,
                        'interest_amount' => 0.00,
                        'penalty_amount' => 0.00,
                        'principal_balance_after' => $row['rem_loan'],
                        'transaction_date' => $row['joining_date'],
                        'remarks' => 'Opening balance migration from register',
                    ]);
                }

                $importedCount++;
            }

            session()->forget('pending_migration_rows');

            AuditLog::log('data_migrated', "Successfully migrated {$importedCount} members from CSV file.");

            DB::commit();
            return redirect()->route('admin.members.index')->with('success', "Migration complete! {$importedCount} members successfully digitized and added to the system.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error executing migration: ' . $e->getMessage());
        }
    }
}
