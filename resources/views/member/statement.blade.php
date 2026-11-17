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
            max-width: 800px;
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
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
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
    </div>

    <div class="member-info">
        <div>
            <p style="margin: 0 0 3px;"><strong>Member:</strong> {{ $member->full_name }}</p>
            <p style="margin: 0;"><strong>Member ID:</strong> <span class="font-mono">{{ $member->member_code }}</span> | <strong>Mobile:</strong> {{ $member->mobile_number }}</p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0 0 3px;"><strong>Savings Balance:</strong> <strong style="color: #b45309;" class="font-mono">₹{{ number_format($member->current_deposit_balance, 2) }}</strong></p>
            <p style="margin: 0;"><strong>Outstanding Loan:</strong> <strong style="color: #be123c;" class="font-mono">₹{{ number_format($member->current_loan_balance, 2) }}</strong></p>
        </div>
    </div>

    <h3 style="font-size: 12px; margin-bottom: 6px; text-transform: uppercase;">Savings Deposit Passbook</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Date</th>
                <th>Particulars</th>
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

    <h3 style="font-size: 12px; margin-bottom: 6px; text-transform: uppercase;">Payment Receipts History</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 110px;">Receipt No</th>
                <th style="width: 80px;">Date</th>
                <th style="width: 60px;">Mode</th>
                <th class="text-right">Total Paid (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($member->payments as $pay)
                <tr>
                    <td class="font-mono font-bold">{{ $pay->receipt_number }}</td>
                    <td>{{ $pay->payment_date->format('d-m-Y') }}</td>
                    <td style="text-transform: uppercase;">{{ $pay->payment_mode }}</td>
                    <td class="text-right font-mono" style="font-weight: bold;">₹{{ number_format($pay->total_received, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
