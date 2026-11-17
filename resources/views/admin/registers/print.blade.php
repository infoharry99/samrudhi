<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $monthlyRegister->title }} - Physical Register Printout</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Roboto+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 10px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 2px 0 0;
            font-size: 10px;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #444;
            padding: 4px 6px;
        }
        th {
            background-color: #e5e7eb;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            text-align: center;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Roboto Mono', monospace; }
        .bold { font-weight: 700; }
        tfoot tr td {
            background-color: #f3f4f6;
            font-weight: 700;
        }
        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
        }
        .sig-block {
            text-align: center;
            border-top: 1px solid #000;
            width: 180px;
            padding-top: 5px;
            font-weight: 600;
        }
        .no-print {
            background: #1e40af;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 12px;
        }
        @media print {
            .no-print-area { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print-area" style="padding: 10px; background: #f8fafc; border-bottom: 1px solid #cbd5e1; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: bold;">Print Preview Mode: Recommended Setting -> Landscape, Margins: Default or Minimum</span>
        <div>
            <button onclick="window.print()" class="no-print" style="cursor: pointer; border: none;">Print Now / Save as PDF</button>
            <button onclick="window.close()" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Close</button>
        </div>
    </div>

    <div class="header">
        <h1>SHRI SAMRUDDHI CO-OPERATIVE SOCIETY</h1>
        <p>MONTHLY COLLECTION & LOAN ACCOUNTING REGISTER</p>
        <p style="font-size: 11px; margin-top: 3px;">Register Month: <strong>{{ date('F Y', mktime(0, 0, 0, $monthlyRegister->month, 1, $monthlyRegister->year)) }}</strong> | Generated: {{ date('d-m-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">Sr. No.</th>
                <th style="width: 160px; text-align: left;">Member Name & Mobile</th>
                <th style="width: 70px;">Monthly Deposit (₹)</th>
                <th style="width: 70px;">Loan Amount (₹)</th>
                <th style="width: 60px;">Interest (₹)</th>
                <th style="width: 75px;">Monthly Installment (₹)</th>
                <th style="width: 55px;">Penalty (₹)</th>
                <th style="width: 80px;">Monthly Payable (₹)</th>
                <th style="width: 75px;">Loan Balance (₹)</th>
                <th style="width: 65px;">Amount Paid (₹)</th>
                <th style="width: 60px;">Status</th>
                <th style="width: 80px;">Signature / Ref</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totDep = 0; $totLoan = 0; $totInt = 0; $totInst = 0; $totPen = 0; $totPay = 0; $totPaid = 0;
            @endphp
            @foreach($dues as $idx => $due)
                @php
                    $totDep += $due->monthly_deposit;
                    $totLoan += $due->loan_amount;
                    $totInt += $due->interest;
                    $totInst += $due->monthly_installment;
                    $totPen += $due->penalty;
                    $totPay += $due->total_payable;
                    $totPaid += $due->amount_paid;
                @endphp
                <tr>
                    <td class="text-center font-mono">{{ $idx + 1 }}</td>
                    <td>
                        <strong style="font-size: 9.5px;">{{ $due->member->full_name }}</strong>
                        <div style="font-size: 8px; color: #555;">{{ $due->member->member_code }} • {{ $due->member->mobile_number }}</div>
                    </td>
                    <td class="text-right font-mono">{{ number_format($due->monthly_deposit, 0) }}</td>
                    <td class="text-right font-mono">{{ $due->loan_amount > 0 ? number_format($due->loan_amount, 0) : '-' }}</td>
                    <td class="text-right font-mono">{{ $due->interest > 0 ? number_format($due->interest, 0) : '-' }}</td>
                    <td class="text-right font-mono">{{ $due->monthly_installment > 0 ? number_format($due->monthly_installment, 0) : '-' }}</td>
                    <td class="text-right font-mono">{{ $due->penalty > 0 ? number_format($due->penalty, 0) : '0' }}</td>
                    <td class="text-right font-mono bold" style="background: #f8fafc;">₹{{ number_format($due->total_payable, 0) }}</td>
                    <td class="text-right font-mono">{{ $due->remaining_loan_balance > 0 ? number_format($due->remaining_loan_balance, 0) : '-' }}</td>
                    <td class="text-right font-mono bold">{{ $due->amount_paid > 0 ? number_format($due->amount_paid, 0) : '0' }}</td>
                    <td class="text-center bold" style="font-size: 8.5px;">{{ strtoupper($due->payment_status) }}</td>
                    <td style="font-size: 8px;" class="text-center font-mono">{{ $due->payment_reference ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right bold">TOTALS:</td>
                <td class="text-right font-mono bold">₹{{ number_format($totDep, 0) }}</td>
                <td class="text-right font-mono bold">-</td>
                <td class="text-right font-mono bold">₹{{ number_format($totInt, 0) }}</td>
                <td class="text-right font-mono bold">₹{{ number_format($totInst, 0) }}</td>
                <td class="text-right font-mono bold">₹{{ number_format($totPen, 0) }}</td>
                <td class="text-right font-mono bold">₹{{ number_format($totPay, 0) }}</td>
                <td class="text-right font-mono bold">-</td>
                <td class="text-right font-mono bold">₹{{ number_format($totPaid, 0) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="sig-block">Prepared By (Cashier)</div>
        <div class="sig-block">Verified By (Secretary)</div>
        <div class="sig-block">Approved By (President)</div>
    </div>
</body>
</html>
