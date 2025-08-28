<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كوبون {{ $coupon->code }} - سولونا</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: 400px 250px;
            margin: 0;
        }
        
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            background: white;
            direction: rtl;
            margin: 0;
            padding: 0;
            width: 400px;
            height: 250px;
        }
        
        .coupon-card {
            width: 400px;
            height: 250px;
            background: white;
            border: 2px solid #b48b1e;
            border-radius: 15px;
            padding: 20px;
            position: relative;
            box-sizing: border-box;
        }
        
        .company-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .company-logo {
            width: 100px;
            object-fit: contain;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: 700;
            color: #b48b1e;
        }
        
        .coupon-content {
            text-align: center;
        }
        
        .coupon-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .discount-value {
            font-size: 32px;
            font-weight: 700;
            color: #b48b1e;
            margin: 15px 0;
        }
        
        .coupon-code {
            background: #f8f8f8;
            border: 1px dashed #b48b1e;
            padding: 8px 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: 700;
            color: #b48b1e;
            letter-spacing: 2px;
        }
        
        .coupon-details {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
        
        .status-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-expired {
            background: #fff3cd;
            color: #856404;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
                width: 400px;
                height: 250px;
            }
            
            .coupon-card {
                border: 2px solid #000;
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <script>
        setTimeout(function() {
            window.print();
        }, 1000);
    </script>
    
    <div class="coupon-card">
        @php
            $statusClass = 'status-active';
            $statusText = 'نشط';
            if (!$coupon->is_active) {
                $statusClass = 'status-inactive';
                $statusText = 'غير نشط';
            } elseif ($coupon->is_expired) {
                $statusClass = 'status-expired'; 
                $statusText = 'منتهي';
            }
        @endphp
        <div class="status-badge {{ $statusClass }}">{{ $statusText }}</div>
        
        <div class="company-header">
            <img src="{{ asset('logo-primary.png') }}" alt="سولونا" class="company-logo">
        </div>

        <div class="coupon-content">
            <div class="coupon-name">{{ $coupon->name }}</div>
            
            <div class="discount-value">{{ $coupon->formatted_discount }}</div>
            
            <div class="coupon-code">{{ $coupon->code }}</div>
            
            <div class="coupon-details">
                @if($coupon->minimum_amount)
                    حد أدنى: {{ number_format($coupon->minimum_amount, 0) }} د.ل
                @endif
                
                @if($coupon->end_date)
                    • صالح حتى {{ $coupon->end_date->format('Y/m/d') }}
                @endif
                
                @if($coupon->usage_limit)
                    • {{ $coupon->used_count }}/{{ $coupon->usage_limit }}
                @endif
            </div>
        </div>
    </div>
</body>
</html>