<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 0;
        }
        .company-details, .client-details {
            margin-bottom: 30px;
        }
        .company-details p, .client-details p {
            margin: 2px 0;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .invoice-table th, .invoice-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .invoice-table th {
            background-color: #f5f5f5;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals table {
            width: 300px;
            float: right;
        }
        .totals td {
            padding: 5px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <h1>Invoice</h1>
    <div class="company-details">
        <strong>{{ config('profile.company_name') }}</strong><br>
        <p>{{ config('profile.address') }}</p>
        <p>{{ config('profile.city') }}</p>
        <p>{{ config('profile.email') }} | {{ config('profile.phone') }}</p>
    </div>

    <div class="client-details">
        <strong>Bill To:</strong><br>
        <p>{{ $user->name }}</p>
        <p>{{ $user->email }}</p>
    </div>

    <p><strong>Invoice #: </strong>{{ $invoice->invoice_number }}</p>
    <p><strong>Issue Date: </strong>{{ $invoice->created_at->format('F j, Y') }}</p>
    <p><strong>Due Date: </strong>{{ $invoice->due_date->format('F j, Y') }}</p>

    <table class="invoice-table">
        <thead>
        <tr>
            <th>Description</th>
            <th style="text-align:right;">Qty</th>
            <th style="text-align:right;">Unit Price</th>
            <th style="text-align:right;">Total</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $plan->title }} @ {{ $invoice->created_at->format('M Y') }}</td>
            <td style="text-align:right;">1 month</td>
            <td style="text-align:right;">Rp{{ number_format($plan->price, 0, ',', '.') }}</td>
            <td style="text-align:right;">Rp{{ number_format($plan->price, 0, ',', '.') }}</td>
        </tr>
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Total:</strong></td>
                <td style="text-align:right;"><strong>Rp{{ number_format($invoice->amount, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Thank you for your business!
    </div>
</div>
</body>
</html>
