<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\Member;
use App\Models\MonthlyDue;
use App\Models\MonthlyRegister;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocietyAccountingTest extends TestCase
{
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SHRI SAMRUDDHI SOCIETY');
    }

    public function test_admin_can_access_dashboard_and_register(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Society Operational Overview');

        $register = MonthlyRegister::first();
        $regResponse = $this->get("/admin/registers/{$register->id}");
        $regResponse->assertStatus(200);
        $regResponse->assertSee('Physical Register Layout');
    }

    public function test_member_portal_access_and_isolation(): void
    {
        $memberUser1 = User::where('role', 'member')->first();
        $memberUser2 = User::where('role', 'member')->skip(1)->first();

        // Login as member 1
        $this->actingAs($memberUser1);

        $response = $this->get('/member/dashboard');
        $response->assertStatus(200);
        $response->assertSee($memberUser1->member->member_code);

        // Member cannot access admin dashboard
        $forbiddenAdmin = $this->get('/admin/dashboard');
        $forbiddenAdmin->assertRedirect(route('member.dashboard'));

        // Member 1 payment receipt
        $payment1 = Payment::where('member_id', $memberUser1->member_id)->first();
        if ($payment1) {
            $receiptResponse = $this->get("/member/receipt/{$payment1->id}");
            $receiptResponse->assertStatus(200);

            // Member 2 should be forbidden from accessing Member 1 receipt
            $this->actingAs($memberUser2);
            $isolatedResponse = $this->get("/member/receipt/{$payment1->id}");
            $isolatedResponse->assertStatus(403);
        }
    }

    public function test_accounting_rule_installment_reduces_principal_interest_does_not(): void
    {
        $member = Member::whereHas('loans', fn($q) => $q->where('status', 'active'))->first();
        $loan = $member->loans()->where('status', 'active')->first();

        $initialPrincipal = (float) $loan->remaining_principal;
        $initialSavings = (float) $member->current_deposit_balance;

        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // Record a cash payment with: Deposit 1000, Interest 500, Installment 5000, Penalty 0 => Total 6500
        $postData = [
            'member_id' => $member->id,
            'payment_date' => date('Y-m-d'),
            'payment_month' => date('Y-m'),
            'deposit_portion' => 1000,
            'interest_portion' => 500,
            'installment_portion' => 5000,
            'penalty_portion' => 0,
            'total_received' => 6500,
            'payment_mode' => 'cash',
            'transaction_reference' => 'TEST-ACC-001',
            'remarks' => 'Automated test payment',
        ];

        $response = $this->post('/admin/collections', $postData);
        $response->assertSessionHasNoErrors();

        $loan->refresh();
        $member->refresh();

        // Outstanding principal must be reduced by exactly 5000 (NOT by interest of 500)
        $this->assertEquals($initialPrincipal - 5000, (float) $loan->remaining_principal);

        // Savings balance must increase by deposit portion (1000)
        $this->assertEquals($initialSavings + 1000, (float) $member->current_deposit_balance);
    }
}
