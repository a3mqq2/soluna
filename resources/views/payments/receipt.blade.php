<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال دفع - {{ $payment->invoice->invoice_number }} - سولونا</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
    
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.8;
            color: #000;
            background: white;
            direction: rtl;
            margin: 0 auto;
        }
        
        .receipt-container {
            width: 100%;
            max-width: 180mm;
            background: white;
            padding: 20px;
            margin: 0 auto;
        }
        
        /* Header */
        .receipt-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
            text-align: center;
        }
        
        .company-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
        }
        
        .company-logo {
            width: 200px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: 700;
            color: #000;
        }
        
        .receipt-title {
            font-size: 18px;
            font-weight: 700;
            margin-top: 10px;
            color: #000;
        }
        
        .receipt-number {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        /* Receipt Content */
        .receipt-content {
            font-size: 16px;
            line-height: 2.2;
            margin: 25px 0;
            text-align: right;
            padding: 0 15px;
        }
        
        .amount-line {
            font-size: 18px;
            font-weight: 700;
            margin: 20px 0;
            text-align: center;
            border: 2px solid #000;
            padding: 15px;
            background: #f8f8f8;
        }
        
        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 120px;
            padding: 0 8px;
            margin: 0 5px;
        }
        
        .amount-underline {
            border-bottom: 2px solid #000;
            display: inline-block;
            min-width: 180px;
            padding: 3px 8px;
            margin: 0 8px;
            font-weight: 700;
        }
        
        .details-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 13px;
        }
        
        .detail-row {
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
        }
        
        .detail-label {
            font-weight: 600;
            color: #333;
        }
        
        .detail-value {
            color: #000;
        }
        
        /* Footer */
        .receipt-footer {
            margin-top: 30px;
            border-top: 2px solid #000;
            padding-top: 20px;
        }
        
        .date-section {
            text-align: left;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .signature-box {
            text-align: center;
            width: 150px;
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
        
        /* Print Specific */
        @media print {
            .receipt-container {
                border: 2px solid #000;
                padding: 30px;
            }
            
            @page {
                margin: 20mm;
            }
        }
    </style>
</head>

<body>
    <script>
        setTimeout(function() {
            window.print();
        }, 1500);
    </script>
    
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="company-section">
                <img src="{{ asset('logo-primary.png') }}" alt="سولونا" class="company-logo">
            </div>
            <div class="receipt-title">إيصال استلام</div>
            <div class="receipt-number">رقم: {{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>

        <!-- Receipt Content -->
        <div class="receipt-content">
            <div>
                استلم من السيد / <span class="underline">{{ $payment->invoice->customer->name }}</span>
            </div>
            
            <div class="amount-line">
                مبلغ وقدره: <span class="amount-underline">{{ number_format($payment->amount, 3) }} دينار ليبي</span>
            </div>
            
            <div>
                وذلك عن: <span class="underline">دفعة من فاتورة رقم {{ $payment->invoice->invoice_number }}</span>
            </div>
            
            <div style="margin-top: 40px;">
                بطريقة: <span class="underline">{{ $payment->payment_method_name }}</span>
            </div>
            
            @if($payment->reference_number)
            <div>
                رقم المرجع: <span class="underline">{{ $payment->reference_number }}</span>
            </div>
            @endif
        </div>

        <!-- Additional Details -->
        <div class="details-section">
            <div class="detail-row">
                <span class="detail-label">رقم الهاتف:</span>
                <span class="detail-value">{{ $payment->invoice->customer->phone }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">تاريخ الفاتورة:</span>
                <span class="detail-value">{{ $payment->invoice->invoice_date->format('Y/m/d') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">إجمالي الفاتورة:</span>
                <span class="detail-value">{{ number_format($payment->invoice->total, 3) }} د.ل</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">المبلغ المتبقي:</span>
                <span class="detail-value">{{ number_format($payment->invoice->remaining_amount, 3) }} د.ل</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="date-section">
                التاريخ: {{ $payment->payment_date->format('Y/m/d') }}
            </div>
            
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">توقيع المستلم</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">توقيع المسؤول</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>