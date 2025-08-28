<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة {{ $invoice->invoice_number }} - سولونا</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
    
        
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            font-size: 20px;
            line-height: 1.5;
            color: #000;
            background: white;
            direction: rtl;
            margin: 0 auto;
        }
        
        .invoice-container {
            width: 100%;
            background: white;
            border: 2px solid #000;
            padding: 20px;
        }
        
        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 10px;
        }
        
        .company-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .company-logo {
            width: 200px;
            object-fit: contain;
            padding: 5px;
        }
        
        .company-details h1 {
            font-size: 28px;
            font-weight: 700;
            color: #000;
            margin-bottom: 5px;
        }
        
        .company-details p {
            font-size: 19px;
            color: #333;
        }
        
        .invoice-info {
            text-align: center;
            border: 2px solid #000;
            padding: 15px;
            min-width: 180px;
        }
        
        .invoice-number {
            font-size: 18px;
            font-weight: 700;
            color: #000;
            margin-bottom: 10px;
        }
        
        .invoice-date {
            font-size: 14px;
            color: #000;
            margin-bottom: 10px;
        }
        
        .invoice-status {
            padding: 5px 10px;
            border: 1px solid #000;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            background: #f5f5f5;
            color: #000;
        }
        
        /* Customer Section */
        .customer-section {
            border: 2px solid #666;
            margin-bottom: 10px;
        }
        
        .section-header {
            background: #f5f5f5;
            padding: 10px 15px;
            border-bottom: 2px solid #000;
            font-weight: 700;
            font-size: 16px;
            color: #000;
        }
        
        .customer-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .customer-table tr {
            border-bottom: 1px solid #ddd;
        }
        
        .customer-table tr:last-child {
            border-bottom: none;
        }
        
        .customer-table td {
            padding: 8px 15px;
            vertical-align: top;
            font-size: 15px;
        }
        
        .customer-table td:first-child {
            width: 20%;
            font-weight: 600;
            color: #b48b1e;
            background: #faf8f6;
            border-left: 1px solid #ddd;
        }
        
        /* Items Section */
        .items-section {
            margin-bottom: 10px;
            border: 2px solid #666;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items-table thead {
            background: #000;
            color: white;
        }
        
        .items-table th {
            padding: 14px 10px;
            font-weight: 700;
            font-size: 16px;
            text-align: center;
            border-left: 1px solid white;
        }
        
        .items-table th:last-child {
            border-left: none;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #ddd;
        }
        
        .items-table tbody tr:last-child {
            border-bottom: none;
        }
        
        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .items-table td {
            padding: 12px 10px;
            text-align: center;
            vertical-align: top;
            border-left: 1px solid #ddd;
            font-size: 15px;
        }
        
        .items-table td:last-child {
            border-left: none;
        }
        
        .service-cell {
            text-align: right !important;
            padding-right: 12px;
        }
        
        .service-name {
            font-weight: 600;
            margin-bottom: 3px;
            font: 23px;
        }
        
        .service-description {
            font-size: 15px;
            color: #666;
        }
        
        .amount-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            font-size: 15px;
        }
        
        /* Summary Section */
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 50px;
        }
        
        .summary-table {
            width: 300px;
            border: 2px solid #666;
        }
        
        .summary-table .section-header {
            text-align: center;
            background: linear-gradient(135deg, #b48b1e 0%, #8a6817 100%);
            color: white;
        }
        
        .summary-body {
            padding: 0;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
        }
        
        .summary-row:last-child {
            border-bottom: none;
            background: #f0f0f0;
            font-weight: 700;
            font-size: 18px;
        }
        
        .summary-amount {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        /* Payment Section */
        .payments-section {
            margin-bottom: 25px;
            border: 2px solid #666;
        }
        
        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .payments-table thead {
            background: #333;
            color: white;
        }
        
        .payments-table th {
            padding: 10px 8px;
            font-weight: 600;
            font-size: 13px;
            text-align: center;
            border-left: 1px solid white;
        }
        
        .payments-table th:last-child {
            border-left: none;
        }
        
        .payments-table tbody tr {
            border-bottom: 1px solid #ddd;
        }
        
        .payments-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .payments-table td {
            padding: 8px;
            text-align: center;
            font-size: 12px;
            border-left: 1px solid #ddd;
        }
        
        .payments-table td:last-child {
            border-left: none;
        }
        
        .payment-method {
            background: #f0f0f0;
            color: #000;
            padding: 2px 6px;
            border: 1px solid #000;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .payment-summary {
            margin-top: 15px;
            padding: 10px 15px;
            background: #f8f8f8;
            border: 2px solid #000;
        }
        
        .payment-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .payment-summary-row:last-child {
            margin-bottom: 0;
            font-weight: 700;
            color: #000;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        /* Footer */
        .invoice-footer {
            margin-top:10px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }
        
        .footer-note {
            text-align: center;
            font-size: 16px;
            margin-bottom: 30px;
            color: #333;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature-box {
            width: 150px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 2px solid #000;
            margin-bottom: 8px;
            margin-top: 30px;
        }
        
        .signature-label {
            font-weight: 600;
            font-size: 13px;
        }
        
        .company-stamp {
            text-align: center;
            font-weight: 700;
            color: #000;
            font-size: 16px;
            margin-top: 20px;
        }
        
        /* Print Specific */
        @media print {
            body {
                font-size: 13px;
            }
            
            .invoice-container {
                border: none;
                padding: 0;
            }
            
            @page {
                margin: 15mm;
            }
        }
    </style>
</head>

<body>
    <script>
        // Auto print after 1 second
        setTimeout(function() {
            window.print();
        }, 1000);
    </script>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-section">
                <img src="{{ asset('logo-primary.png') }}" alt="سولونا" class="company-logo">
            
            </div>
            
            <div class="invoice-info">
                <div class="invoice-number">فاتورة رقم: {{ $invoice->invoice_number }}</div>
                <div class="invoice-date">التاريخ: {{ $invoice->invoice_date->format('Y/m/d') }}</div>
                <div class="invoice-status status-{{ $invoice->status }}">
                    @switch($invoice->status)
                        @case('cancelled') ملغية @break
                        @case('partial') مدفوعة جزئي @break
                        @case('unpaid') غير مدفوعة @break
                        @case('paid')  مدفوعة @break
                        @default {{ $invoice->status }} @break
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="customer-section">
            <table class="customer-table">
                <tr>
                    <td>اسم العميل</td>
                     <td>{{ $invoice->customer->name }}</td>
                </tr>
                <tr>
                  <td>رقم الهاتف</td>
                    <td>{{ $invoice->customer->phone }}</td>
                </tr>
            </table>
        </div>

        <!-- Invoice Items -->
        <div class="items-section">
            <div class="section-header">تفاصيل الفاتورة</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="50%">الخدمة</th>
                        <th width="15%">الكمية</th>
                        <th width="17.5%">سعر الوحدة</th>
                        <th width="17.5%">المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="service-cell">
                            <div class="service-name">{{ $item->service->name }}</div>
                            @if($item->service->description)
                                <div class="service-description">{{ $item->service->description }}</div>
                            @endif
                        </td>
                        <td>{{ number_format($item->quantity, 0) }}</td>
                        <td class="amount-cell">{{ number_format($item->unit_price, 3) }} د.ل</td>
                        <td class="amount-cell">{{ number_format($item->total_price, 3) }} د.ل</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-table">
                <div class="section-header">ملخص الفاتورة</div>
                <div class="summary-body">
                    <div class="summary-row">
                        <span>المجموع الفرعي:</span>
                        <span class="summary-amount">{{ number_format($invoice->subtotal ?? $invoice->items->sum('total_price'), 3) }} د.ل</span>
                    </div>
                    @if($invoice->discount > 0)
                    <div class="summary-row">
                        <span>الخصم:</span>
                        <span class="summary-amount">- {{ number_format($invoice->discount, 3) }} د.ل</span>
                    </div>
                    @endif
                    <div class="summary-row">
                        <span>إجمالي الفاتورة:</span>
                        <span class="summary-amount">{{ number_format($invoice->total, 3) }} د.ل</span>
                    </div>
                </div>
            </div>
        </div>



        <!-- Footer -->
        <div class="invoice-footer">
            <div class="footer-note">
                شكراً لثقتكم في خدمات سولونا
            </div>
            
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">توقيع العميل</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">الختم والتوقيع</div>
                </div>
            </div>
            
            <div class="company-stamp">
                سولونا - خدمات احترافية متميزة
            </div>
        </div>
    </div>
</body>
</html>