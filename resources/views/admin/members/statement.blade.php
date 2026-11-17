<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Statement: {{ $member->member_code }} - {{ $member->full_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Roboto+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            padding: 20px;
            max-width: 850px;
            margin: auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; color: #0f172a; }
        .header p { margin: 3px 0 0; font-size: 11px; color: #475569; }
        .member-info {
            display: flex;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .balance-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .balance-card {
            border: 1px solid #cbd5e1;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }
        .balance-card h4 { margin: 0 0 4px; font-size: 10px; text-transform: uppercase; color: #64748b; }
        .balance-card p { margin: 0; font-size: 16px; font-weight: bold; font-family: 'Roboto Mono', monospace; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 700;
            font-size: 10px;
            text-align: left;
        }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Roboto Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #1e40af; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Print Statement / Save PDF
        </button>
    </div>

    <div class="header">
        <h1>SHRI SAMRUDDHI CO-OPERATIVE SOCIETY</h1>
        <p>Member Financial Statement & Savings Passbook</p>
        <p style="font-size: 10px; margin-top: 2px;">Shop No. 4-6, Samruddhi Commercial Complex, Ahmedabad | Phone: +91 98765 43210</p>
    </div>

    <div class="member-info">
        <div>
            <p style="margin: 0 0 3px;"><strong>Member:</strong> {{ $member->full_name }}</p>
            <p style="margin: 0 0 3px;"><strong>Member Code:</strong> <span class="font-mono">{{ $member->member_code }}</span></p>
            <p style="margin: 0;"><strong>Mobile:</strong> {{ $member->mobile_number }}</p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0 0 3px;"><strong>Joining Date:</strong> {{ $member->joining_date->format('d-m-Y') }}</p>
            <p style="margin: 0 0 3px;"><strong>Status:</strong> {{ strtoupper($member->status) }}</p>
            <p style="margin: 0;"><strong>Statement Date:</strong> {{ date('d-m-Y H:i') }}</p>
        </div>
    </div>

    <div class="balance-cards">
        <div class="balance-card">
            <h4>Total Savings Balance</h4>
            <p style="color: #b45309;">₹{{ number_format($member->current_deposit_balance, 2) }}</p>
        </div>
        <div class="balance-card">
            <h4>Outstanding Loan Balance</h4>
            <p style="color: #be123c;">₹{{ number_format($member->current_loan_balance, 2) }}</p>
        </div>
        <div class="balance-card">
            <h4>Total Collections Paid</h4>
            <p style="color: #047857;">₹{{ number_format($member->payments->sum('total_received'), 2) }}</p>
        </div>
    </div>

    <h3 style="font-size: 12px; margin-bottom: 6px; text-transform: uppercase;">1. Monthly Savings Deposit Passbook</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Date</th>
                <th>Particulars / Description</th>
                <th style="width: 100px;" class="text-right">Deposit (₹)</th>
                <th style="width: 110px;" class="text-right">Balance (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($member->deposits as $dep)
                <tr>
                    <td class="font-mono">{{ $dep->transaction_date->format('d-m-Y') }}</td>
                    <td>{{ $dep->description }}</td>
                    <td class="text-right font-mono" style="color: #047857; font-weight: bold;">+{{ number_format($dep->amount, 2) }}</td>
                    <td class="text-right font-mono" style="font-weight: bold;">₹{{ number_format($dep->balance_after, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="font-size: 12px; margin-bottom: 6px; text-transform: uppercase;">2. Loan Accounts & Transactions</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Date</th>
                <th>Loan No & Description</th>
                <th style="width: 85px;" class="text-right">Principal Repaid</th>
                <th style="width: 80px;" class="text-right">Interest Paid</th>
                <th style="width: 100px;" class="text-right">Remaining Principal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($member->loans as $loan)
                <tr style="background: #f8fafc; font-weight: bold;">
                    <td colspan="2">Loan Account: {{ $loan->loan_number }} (Disbursed: ₹{{ number_format($loan->loan_amount, 2) }} @ {{ $loan->interest_rate }}% monthly)</td>
                    <td colspan="2" class="text-right">Status: {{ strtoupper($loan->status) }}</td>
                    <td class="text-right font-mono">₹{{ number_format($loan->remaining_principal, 2) }}</td>
                </tr>
                @foreach($loan->transactions as $tx)
                    <tr>
                        <td class="font-mono">{{ $tx->transaction_date->format('d-m-Y') }}</td>
                        <td>{{ $tx->remarks ?? $tx->transaction_type }}</td>
                        <td class="text-right font-mono">{{ $tx->principal_amount > 0 ? '₹' . number_format($tx->principal_amount, 2) : '-' }}</td>
                        <td class="text-right font-mono">{{ $tx->interest_amount > 0 ? '₹' . number_format($tx->interest_amount, 2) : '-' }}</td>
                        <td class="text-right font-mono" style="color: #be123c;">₹{{ number_format($tx->principal_balance_after, 2) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="5" style="text-align: center; color: #888;">No loans taken by this member.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 40px; display: flex; justify-content: space-between; padding: 0 30px;">
        <div style="text-align: center; border-top: 1px solid #000; width: 160px; padding-top: 4px;">Member Signature</div>
        <div style="text-align: center; border-top: 1px solid #000; width: 160px; padding-top: 4px;">Authorized Signatory</div>
    </div>
</body>
</html>
