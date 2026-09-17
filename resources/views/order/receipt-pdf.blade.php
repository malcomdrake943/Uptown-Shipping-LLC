<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            font-size: 16px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f9f9f9;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
        }
        .tracking-number {
            font-size: 18px;
            font-weight: bold;
            color: #d946ef; /* Brand color */
            margin-bottom: 15px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>UpTown Cargo Services</h1>
        <p>Order Receipt & Tracking Details</p>
    </div>

    <div class="section">
        <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
        <p class="tracking-number">Tracking / Order Number: {{ $order->order_number }}</p>
        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
    </div>

    <div class="section">
        <h2>Customer Details</h2>
        <p>
            <strong>Name:</strong> {{ $order->customer_name }}<br>
            <strong>Email:</strong> {{ $order->customer_email }}<br>
            <strong>Phone:</strong> {{ $order->customer_phone }}<br>
            <strong>Shipping Address:</strong><br>
            {{ $order->shipping_address['line1'] ?? '' }}<br>
            @if(!empty($order->shipping_address['line2']))
                {{ $order->shipping_address['line2'] }}<br>
            @endif
            {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}<br>
            {{ $order->shipping_address['country'] ?? '' }}
        </p>
    </div>

    <div class="section">
        <h2>Order Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order->product_name ?: 'Product from ' . ucfirst($order->source_platform) }}<br><small>{{ $order->product_url }}</small></td>
                    <td>{{ $order->quantity }}</td>
                    <td>${{ number_format($order->estimated_product_price, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Charges Breakdown</h2>
        <table>
            <tbody>
                <tr>
                    <td>Subtotal (Price &times; Qty)</td>
                    <td style="text-align: right;">${{ number_format($order->estimated_product_price * $order->quantity, 2) }}</td>
                </tr>
                <tr>
                    <td>Service Fee</td>
                    <td style="text-align: right;">${{ number_format($order->service_fee, 2) }}</td>
                </tr>
                <tr>
                    <td>Package Handling Fee ({{ ucfirst($order->size_tier) }})</td>
                    <td style="text-align: right;">${{ number_format($order->size_handling_fee, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Paid</td>
                    <td style="text-align: right;">${{ number_format($order->total_charged, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for choosing UpTown Cargo Services!</p>
        <p>You can track your order at any time on our website using your Tracking / Order Number.</p>
    </div>
</body>
</html>
