<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $payment->receipt_number }} - Print</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Roboto+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }
        .receipt-box {
            border: 2px solid #000;
            padding: 20px;
            border-radius: 6px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0 0; font-size: 10px; }
        .grid-2 {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px 8px;
        }
        th {
            background-color: #f3f4f6;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Roboto Mono', monospace; }
        .signatures {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
        }
        .sig-line {
            text-align: center;
            border-top: 1px solid #000;
            width: 140px;
            padding-top: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 14px; background: #1e40af; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Print Receipt</button>
    </div>

    <div class="receipt-box">
        <div class="header">
            <h1>SHRI SAMRUDDHI CO-OPERATIVE SOCIETY</h1>
            <p><strong>OFFICIAL PAYMENT RECEIPT</strong></p>
            <p>Registration No: REG/GUJ/AHM/2018/14892 | Ahmedabad, Gujarat</p>
        </div>

        <div class="grid-2">
            <div>
                <p style="margin: 0 0 3px;"><strong>Receipt No:</strong> <span class="font-mono">{{ $payment->receipt_number }}</span></p>
                <p style="margin: 0;"><strong>Date:</strong> {{ $payment->payment_date->format('d-m-Y') }}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0 0 3px;"><strong>Mode:</strong> {{ strtoupper($payment->payment_mode) }}</p>
                <p style="margin: 0;"><strong>Ref:</strong> <span class="font-mono">{{ $payment->transaction_reference }}</span></p>
            </div>
        </div>

        <div style="margin-bottom: 12px; background: #f9fafb; padding: 8px; border: 1px solid #e5e7eb; border-radius: 4px;">
            <p style="margin: 0 0 2px;"><strong>Received with thanks from:</strong> {{ $payment->member->full_name }}</p>
            <p style="margin: 0; font-size: 10px; color: #555;">Member Code: <span class="font-mono">{{ $payment->member->member_code }}</span> | Mobile: {{ $payment->member->mobile_number }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Particulars</th>
                    <th class="text-right" style="width: 120px;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @if($payment->deposit_portion > 0)
                    <tr>
                        <td style="text-align: center;">{{ $i++ }}</td>
                        <td>Monthly Savings Deposit</td>
                        <td class="text-right font-mono">{{ number_format($payment->deposit_portion, 2) }}</td>
                    </tr>
                @endif
                @if($payment->interest_portion > 0)
                    <tr>
                        <td style="text-align: center;">{{ $i++ }}</td>
                        <td>Loan Interest</td>
                        <td class="text-right font-mono">{{ number_format($payment->interest_portion, 2) }}</td>
                    </tr>
                @endif
                @if($payment->installment_portion > 0)
                    <tr>
                        <td style="text-align: center;">{{ $i++ }}</td>
                        <td>Loan Principal Installment</td>
                        <td class="text-right font-mono">{{ number_format($payment->installment_portion, 2) }}</td>
                    </tr>
                @endif
                @if($payment->penalty_portion > 0)
                    <tr>
                        <td style="text-align: center;">{{ $i++ }}</td>
                        <td>Penalty / Late Fee</td>
                        <td class="text-right font-mono">{{ number_format($payment->penalty_portion, 2) }}</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #f3f4f6;">
                    <td colspan="2" class="text-right">TOTAL AMOUNT RECEIVED:</td>
                    <td class="text-right font-mono" style="font-size: 13px;">₹{{ number_format($payment->total_received, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        @if($payment->remarks)
            <p style="font-size: 10px; margin: 6px 0;"><strong>Note:</strong> {{ $payment->remarks }}</p>
        @endif

        <div class="signatures">
            <div class="sig-line">Member's Signature</div>
            <div class="sig-line">Cashier / Authorized Signatory</div>
        </div>
    </div>
</body>
</html>
