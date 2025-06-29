<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - {{ $transaction->transaction_id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 30px;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #facc15;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #111827;
            font-size: 28px;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 14px;
        }
        
        .receipt-info {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .receipt-info h2 {
            margin: 0 0 15px 0;
            color: #111827;
            font-size: 18px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 8px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            padding: 8px 0;
            font-weight: bold;
            color: #374151;
            width: 35%;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #111827;
        }
        
        .event-details {
            background: #fef3c7;
            border: 2px solid #facc15;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .payment-summary {
            background: #ecfdf5;
            border: 2px solid #10b981;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .amount-highlight {
            text-align: center;
            background: #111827;
            color: #facc15;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        
        .amount-highlight .amount {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        
        .amount-highlight .label {
            font-size: 12px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-paid {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #10b981;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
        
        .custom-data {
            margin-top: 20px;
        }
        
        .custom-data h3 {
            margin: 0 0 10px 0;
            color: #111827;
            font-size: 14px;
            font-weight: bold;
        }
        
        .custom-data-item {
            margin: 5px 0;
            font-size: 13px;
        }
        
        .custom-data-item strong {
            color: #374151;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SIMIKO</h1>
        <p>Event Registration & Payment Receipt</p>
    </div>

    <!-- Receipt Information -->
    <div class="receipt-info">
        <h2>Receipt Information</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Transaction ID:</div>
                <div class="info-value">{{ $transaction->transaction_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Receipt Date:</div>
                <div class="info-value">{{ now()->format('d M Y, H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Date:</div>
                <div class="info-value">{{ $transaction->paid_at ? $transaction->paid_at->format('d M Y, H:i') : '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-paid">{{ ucfirst($transaction->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Participant Information -->
    <div class="receipt-info">
        <h2>Participant Information</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $transaction->getUserName() }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $transaction->getUserEmail() }}</div>
            </div>
            @if($transaction->getUserPhone())
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $transaction->getUserPhone() }}</div>
            </div>
            @endif
        </div>

        @if($transaction->custom_data && count($transaction->custom_data) > 0)
        <div class="custom-data">
            <h3>Additional Information</h3>
            @foreach($transaction->custom_data as $key => $value)
            <div class="custom-data-item">
                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Event Details -->
    <div class="event-details">
        <h2>Event Details</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Event Name:</div>
                <div class="info-value">{{ $transaction->feed->title }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Organizer:</div>
                <div class="info-value">{{ $transaction->feed->unitKegiatan->name }}</div>
            </div>
            @if($transaction->feed->event_date)
            <div class="info-row">
                <div class="info-label">Event Date:</div>
                <div class="info-value">{{ $transaction->feed->event_date->format('d M Y') }}</div>
            </div>
            @endif
            @if($transaction->feed->location)
            <div class="info-row">
                <div class="info-label">Location:</div>
                <div class="info-value">{{ $transaction->feed->location }}</div>
            </div>
            @endif
            @if($transaction->feed->event_type)
            <div class="info-row">
                <div class="info-label">Event Type:</div>
                <div class="info-value">{{ ucfirst($transaction->feed->event_type) }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="payment-summary">
        <h2>Payment Summary</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Payment Method:</div>
                <div class="info-value">{{ $transaction->payment_method ?? 'Bank Transfer' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Configuration:</div>
                <div class="info-value">{{ $transaction->paymentConfiguration->name ?? 'Event Registration' }}</div>
            </div>
        </div>
        
        <div class="amount-highlight">
            <p class="label">Total Amount Paid</p>
            <p class="amount">{{ $transaction->getFormattedAmountAttribute() }}</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is an official receipt for your event registration payment.</p>
        <p>Generated on {{ now()->format('d M Y, H:i') }} | Simiko - Student Activity Management System</p>
        <p>For any inquiries, please contact the event organizer: {{ $transaction->feed->unitKegiatan->name }}</p>
    </div>
</body>
</html> 