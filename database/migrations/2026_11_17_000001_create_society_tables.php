<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update users table for society authentication
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->nullable()->unique()->after('email');
            $table->string('role')->default('member')->after('mobile'); // 'admin', 'member'
            $table->string('status')->default('active')->after('role'); // 'active', 'inactive'
            $table->unsignedBigInteger('member_id')->nullable()->after('status');
        });

        // 2. Members table
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_code')->unique(); // e.g. SSS-001
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('full_name');
            $table->string('mobile_number')->unique();
            $table->string('alternate_mobile')->nullable();
            $table->text('address')->nullable();
            $table->date('joining_date');
            $table->decimal('monthly_deposit_amount', 12, 2)->default(1000.00);
            $table->decimal('initial_deposit_balance', 12, 2)->default(0.00);
            $table->decimal('initial_loan_balance', 12, 2)->default(0.00);
            $table->decimal('current_deposit_balance', 12, 2)->default(0.00);
            $table->decimal('current_loan_balance', 12, 2)->default(0.00);
            $table->string('photo_path')->nullable();
            $table->string('status')->default('active'); // 'active', 'inactive'
            $table->timestamps();
        });

        // 3. Loans table
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number')->unique(); // e.g. LN-2026-001
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->date('application_date');
            $table->date('approval_date')->nullable();
            $table->decimal('loan_amount', 12, 2); // Initial principal
            $table->decimal('interest_rate', 5, 2)->default(1.00); // 1% monthly or configured %
            $table->string('interest_calculation_method')->default('reducing_balance'); // 'flat', 'reducing_balance', 'fixed_monthly'
            $table->decimal('monthly_installment', 12, 2); // Principal repayment or EMI
            $table->integer('loan_duration_months')->default(12);
            $table->date('first_installment_date');
            $table->string('loan_purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected', 'active', 'completed'
            $table->decimal('remaining_principal', 12, 2);
            $table->decimal('total_interest_charged', 12, 2)->default(0.00);
            $table->decimal('total_interest_paid', 12, 2)->default(0.00);
            $table->decimal('total_principal_paid', 12, 2)->default(0.00);
            $table->timestamps();
        });

        // 4. Monthly Registers (Parent)
        Schema::create('monthly_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month'); // 1 - 12
            $table->unsignedSmallInteger('year'); // e.g. 2026
            $table->string('title'); // e.g. 'January 2026 Register'
            $table->date('generated_date');
            $table->string('status')->default('active'); // 'active', 'closed'
            $table->decimal('total_deposit_expected', 12, 2)->default(0.00);
            $table->decimal('total_interest_expected', 12, 2)->default(0.00);
            $table->decimal('total_installment_expected', 12, 2)->default(0.00);
            $table->decimal('total_penalty_expected', 12, 2)->default(0.00);
            $table->decimal('total_payable_expected', 12, 2)->default(0.00);
            $table->decimal('total_collected', 12, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['month', 'year']);
        });

        // 5. Monthly Dues (Member row in monthly register)
        Schema::create('monthly_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_register_id')->constrained('monthly_registers')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('loan_id')->nullable()->constrained('loans')->nullOnDelete();
            $table->decimal('monthly_deposit', 12, 2)->default(1000.00);
            $table->decimal('loan_amount', 12, 2)->default(0.00); // active loan amount
            $table->decimal('interest', 12, 2)->default(0.00); // monthly interest
            $table->decimal('monthly_installment', 12, 2)->default(0.00); // principal repayment
            $table->decimal('penalty', 12, 2)->default(0.00);
            $table->decimal('total_payable', 12, 2)->default(0.00); // deposit + interest + installment + penalty
            $table->decimal('remaining_loan_balance', 12, 2)->default(0.00);
            
            // Payment tracking
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->decimal('deposit_paid', 12, 2)->default(0.00);
            $table->decimal('interest_paid', 12, 2)->default(0.00);
            $table->decimal('installment_paid', 12, 2)->default(0.00);
            $table->decimal('penalty_paid', 12, 2)->default(0.00);
            $table->string('payment_status')->default('pending'); // 'pending', 'partial', 'paid'
            $table->date('payment_date')->nullable();
            $table->string('payment_mode')->nullable(); // 'cash', 'online'
            $table->string('payment_reference')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['monthly_register_id', 'member_id']);
        });

        // 6. Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique(); // e.g. REC-202611-0001
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('monthly_due_id')->nullable()->constrained('monthly_dues')->nullOnDelete();
            $table->foreignId('loan_id')->nullable()->constrained('loans')->nullOnDelete();
            $table->date('payment_date');
            $table->string('payment_month'); // e.g. '2026-11'
            $table->decimal('total_received', 12, 2);
            $table->decimal('deposit_portion', 12, 2)->default(0.00);
            $table->decimal('interest_portion', 12, 2)->default(0.00);
            $table->decimal('installment_portion', 12, 2)->default(0.00);
            $table->decimal('penalty_portion', 12, 2)->default(0.00);
            $table->string('payment_mode')->default('cash'); // 'cash', 'online'
            $table->string('payment_status')->default('paid'); // 'paid', 'pending', 'partial', 'failed', 'refunded'
            $table->string('transaction_reference')->nullable();
            $table->string('gateway_order_id')->nullable();
            $table->string('gateway_payment_id')->nullable();
            $table->string('gateway_signature')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 7. Member Deposits Ledger
        Schema::create('member_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('transaction_type')->default('monthly_deposit'); // 'opening_balance', 'monthly_deposit', 'withdrawal', 'adjustment'
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->date('transaction_date');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 8. Loan Transactions Ledger
        Schema::create('loan_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('transaction_type'); // 'disbursement', 'principal_repayment', 'interest_payment', 'penalty_charge', 'opening_balance', 'adjustment'
            $table->decimal('principal_amount', 12, 2)->default(0.00);
            $table->decimal('interest_amount', 12, 2)->default(0.00);
            $table->decimal('penalty_amount', 12, 2)->default(0.00);
            $table->decimal('principal_balance_after', 12, 2);
            $table->date('transaction_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 9. Penalties
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('monthly_due_id')->nullable()->constrained('monthly_dues')->nullOnDelete();
            $table->date('penalty_date');
            $table->decimal('amount', 12, 2);
            $table->string('reason');
            $table->string('status')->default('pending'); // 'pending', 'paid', 'waived'
            $table->foreignId('waived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('waiver_reason')->nullable();
            $table->timestamps();
        });

        // 10. Settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // 11. Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('general'); // 'payment_reminder', 'payment_receipt', 'loan_status', 'general'
            $table->string('channel')->default('in_app'); // 'in_app', 'whatsapp', 'sms'
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // 12. Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('penalties');
        Schema::dropIfExists('loan_transactions');
        Schema::dropIfExists('member_deposits');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('monthly_dues');
        Schema::dropIfExists('monthly_registers');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('members');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'role', 'status', 'member_id']);
        });
    }
};
