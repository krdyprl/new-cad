<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice Pemesanan - Ceramic Art Dinoyo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
        }
        .invoice-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .logo img {
            width: 120px;
            height: auto;
        }
        .address {
            text-align: right;
            font-size: 14px;
            color: #666;
        }
        .content {
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .bill-to { 
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .bill-to h3 { 
            margin-bottom: 15px; 
            color: #333; 
            font-size: 16px;
            text-transform: uppercase;
        }
        .customer-info {
            font-size: 14px;
            line-height: 1.8;
        }
        .invoice-details { 
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px; 
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
        }
        .invoice-info {
            padding: 20px;
            flex: 1;
        }
        .invoice-info:first-child {
            background: #f8f9fa;
            border-right: 1px solid #eee;
        }
        .invoice-info div { 
            margin-bottom: 8px; 
            font-size: 14px;
        }
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 30px;
            background: white;
            border-radius: 8px;
        }
        .items-table th, .items-table td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid #eee;
        }
        .items-table th { 
            background: #666; 
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }
        .items-table td {
            font-size: 14px;
        }
        .totals { 
            text-align: right; 
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .totals div { 
            margin-bottom: 8px; 
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }
        .total-due { 
            background: #666; 
            color: white; 
            padding: 15px 20px; 
            font-size: 18px; 
            font-weight: bold;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .total-words {
            text-align: center;
            color: #666;
            font-size: 12px;
            font-style: italic;
            margin-bottom: 30px;
        }
        .payment-details { 
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .payment-details h4 { 
            color: #333; 
            margin-bottom: 15px;
            font-size: 16px;
            text-transform: uppercase;
        }
        .payment-details div {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .footer { 
            text-align: center; 
            color: #666; 
            padding: 30px;
            background: #f8f9fa;
            margin-top: 40px;
            border-top: 3px solid #666;
        }
        .footer div {
            margin-bottom: 8px;
        }
        .footer .contact-info {
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="invoice-container">        <div class="header">
            <div>
                <h1>INVOICE</h1>
                <p>Ceramic Art Dinoyo</p>
            </div>
            <div class="address">
                <div>Jl. MT Haryono 9 Kec. Dinoyo,</div>
                <div>Kec. Lowokwaru, Kota Malang,</div>
                <div>Jawa Timur 65145</div>
            </div>
        </div><div class="content">
            <div class="bill-to">
                <h3>Tagihan Kepada:</h3>
                <div class="customer-info">
                    <strong>{{ $name }}</strong><br>
                    {{ $email }}<br>
                    {{ $phone }}
                </div>
            </div>            <div class="invoice-details">
                <div class="invoice-info">
                    <div><strong>Tanggal Pemesanan:</strong></div>
                    <div>{{ $created_at->format('d M Y') }}</div>
                </div>
                <div class="invoice-info">
                    <div><strong>ID Pemesanan:</strong></div>
                    <div>{{ $booking_id }}</div>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Jenis Paket</th>
                        <th>Qty</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ $service_name }}</strong><br>
                            <small>Tanggal: {{ date('d F Y', strtotime($visit_date)) }}</small><br>
                            <small>Waktu: {{ $visit_time }}</small><br>
                            @if($participants > 1)
                                <small>Peserta: {{ $participants }} orang</small>
                            @endif
                            @if(!empty($notes))
                                <br><small>Catatan: {{ $notes }}</small>
                            @endif
                        </td>
                        <td>
                            @if(in_array($service, ['family', 'group']))
                                1 paket
                            @else
                                {{ $participants }} orang
                            @endif
                        </td>
                        <td>Rp {{ number_format($price_per_unit, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="totals">
                <div><span>Subtotal:</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                <div><span>PPN (11%):</span><span>Rp {{ number_format($tax, 0, ',', '.') }}</span></div>
                <div style="border-top: 2px solid #666; padding-top: 10px; margin-top: 10px; font-weight: bold;">
                    <span>Total:</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="total-due">
                Total yang Harus Dibayar: Rp {{ number_format($total, 0, ',', '.') }}
            </div>
            
            @php
                $formatter = new \NumberFormatter('id', \NumberFormatter::SPELLOUT);
                $totalWords = ucwords($formatter->format($total)) . ' Rupiah';
            @endphp
            
            <div class="total-words">
                {{ $totalWords }}
            </div>

            <div class="payment-details">
                <h4>Detail Pembayaran</h4>
                <div><strong>Bank BCA</strong></div>
                <div>No. Rekening: 1234567890</div>
                <div>Atas Nama: Ceramic Art Dinoyo</div>
                <div class="mt-2">
                    <strong>Bank Mandiri</strong>
                </div>
                <div>No. Rekening: 0987654321</div>
                <div>Atas Nama: Ceramic Art Dinoyo</div>
            </div>
        </div>

        <div class="footer">
            <div><strong>Terima kasih atas bisnis Anda!</strong></div>
            <div>Harap lakukan pembayaran dalam waktu 15 hari setelah menerima invoice ini.</div>
            <br>
            <div class="contact-info">+91 00000 00000 &nbsp;&nbsp; hello@ceramicartdinoyo.com</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
