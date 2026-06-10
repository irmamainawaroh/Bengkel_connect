<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Perbaikan - Bengkel Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Poppins', sans-serif; background:#f8fafc; color:#0f172a; line-height:1.5; }
        .page-wrap { max-width:660px; margin:20px auto; padding:0 14px; }
        .topbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
        .topbar h1 { font-size:18px; font-weight:800; }
        .btn-back { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:7px 12px; border-radius:12px; border:1px solid rgba(15,23,42,.12); background:#fff; color:#0f172a; text-decoration:none; font-weight:700; font-size:12px; }
        .invoice-card { background:#fff; border-radius:18px; box-shadow:0 16px 36px rgba(15,23,42,.08); border:1px solid rgba(148,163,184,.16); overflow:hidden; }
        .invoice-header { padding:20px 22px; border-bottom:1px solid rgba(148,163,184,.15); }
        .invoice-brand { display:flex; flex-wrap:wrap; align-items:flex-start; justify-content:space-between; gap:12px; }
        .invoice-brand h2 { font-size:17px; letter-spacing:1px; margin-bottom:5px; }
        .invoice-brand p { color:#475569; font-size:12px; }
        .status-chip { display:inline-flex; align-items:center; gap:6px; padding:7px 10px; border-radius:999px; background:#fef3c7; color:#92400e; font-weight:800; font-size:11px; }
        .row { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; margin-top:20px; }
        .row-item { background:#f8fafc; border-radius:14px; padding:16px; border:1px solid rgba(148,163,184,.18); }
        .row-item span { display:block; color:#64748b; font-size:11px; margin-bottom:5px; font-weight:700; }
        .row-item strong { display:block; font-size:13px; color:#0f172a; font-weight:800; }
        .section { padding:20px 22px; }
        .section-title { font-size:14px; font-weight:900; margin-bottom:12px; color:#0f172a; }
        .invoice-table { width:100%; border-collapse:collapse; margin-bottom:14px; }
        .invoice-table th, .invoice-table td { padding:10px 12px; text-align:left; border-bottom:1px solid rgba(148,163,184,.15); }
        .invoice-table th { font-size:12px; color:#475569; font-weight:700; }
        .invoice-table td { color:#0f172a; font-weight:700; }
        .invoice-table td.amount { text-align:right; font-feature-settings:'tnum'; }
        .invoice-total { display:flex; justify-content:space-between; align-items:center; padding:12px 0; font-size:14px; font-weight:900; border-top:2px solid rgba(148,163,184,.15); }
        .payment-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-top:8px; }
        .payment-box { background:#f8fafc; border-radius:14px; padding:16px; border:1px solid rgba(148,163,184,.18); }
        .payment-box h3 { margin-bottom:10px; font-size:13px; color:#0f172a; }
        .payment-box p, .payment-box li { color:#475569; font-size:12px; margin-bottom:6px; }
        .payment-box ul { list-style:none; padding-left:0; margin:0; }
        .payment-box li::before { content:'•'; margin-right:8px; color:#0f172a; }
        .btn-upload { display:inline-flex; align-items:center; justify-content:center; width:100%; padding:9px 12px; margin-top:12px; border-radius:12px; background:#cc3a2b; color:#fff; text-decoration:none; font-size:12px; font-weight:700; }
        .btn-upload:hover { opacity:0.95; }
        .qris-card { background:#111827; color:#f8fafc; border-radius:18px; padding:16px; display:grid; grid-template-columns:1fr auto; gap:14px; align-items:center; }
        .qris-art { width:120px; height:120px; background:#0f172a; display:flex; align-items:center; justify-content:center; border-radius:14px; font-size:44px; color:#d1fae5; letter-spacing:4px; }
        .qris-image { width:120px; height:120px; object-fit:contain; border-radius:14px; border:2px solid rgba(255,255,255,.12); background:#fff; }
        .qris-info { display:flex; flex-direction:column; gap:5px; }
        .qris-info span { font-size:11px; color:#e2e8f0; }
        .note-card { background:#f8fafc; border-radius:14px; padding:16px; border:1px solid rgba(148,163,184,.18); margin-top:14px; }
        .note-card p { white-space:pre-line; color:#475569; font-size:12px; }
        .note-card strong { color:#0f172a; font-weight:800; }
        .footer-note { padding:18px 22px 20px; background:#ecfdf5; color:#166534; font-size:13px; font-weight:800; border-top:1px solid rgba(16,185,129,.15); }
        @media (max-width:900px) { .row, .payment-grid { grid-template-columns:1fr; } .invoice-brand { flex-direction:column; } }
    </style>
</head>
<body>
    @php
        $recommendedParts = $booking->recommended_parts ?? [];
        if (is_string($recommendedParts)) {
            $decoded = json_decode($recommendedParts, true);
            $recommendedParts = is_array($decoded) ? $decoded : [];
        }

        $mainServiceFee = 150000;
        $parts = [];
        $partsTotal = 0;

        foreach ((array)$recommendedParts as $part) {
            if (is_array($part)) {
                $name = $part['name'] ?? ($part[0] ?? 'Item Tambahan');
                $qty = intval($part['qty'] ?? 1);
                $price = floatval($part['price'] ?? 0);
            } else {
                $name = (string)$part;
                $qty = 1;
                $price = 0;
            }
            $subtotal = $price * max($qty, 1);
            $parts[] = ['name' => $name, 'qty' => $qty, 'price' => $price, 'subtotal' => $subtotal];
            $partsTotal += $subtotal;
        }

        $invoiceNumber = 'INV/' . ($booking->created_at ? $booking->created_at->format('Ymd') : date('Ymd')) . '/' . strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $booking->kode_booking), -4));
        $invoiceDate = $booking->created_at ? $booking->created_at->format('d F Y') : date('d F Y');
        $customerName = $booking->nama ?? 'Pelanggan';
        $mechanicName = optional($booking->mechanic)->name ?? 'Agus Saputra';
        $vehicle = $booking->kendaraan ?? '-';
        $serviceLabel = $booking->layanan ?? 'Home Service';
        $statusText = strtoupper(str_replace('_', ' ', $booking->status ?? 'MENUNGGU PEMBAYARAN'));
        $canUploadProof = in_array($booking->status ?? '', ['menunggu_pembayaran', 'menunggu_pembayaran_final', 'menunggu_konfirmasi_bukti', 'menunggu_konfirmasi_bukti_final']);
        $formatted = fn($value) => 'Rp ' . number_format($value, 0, ',', '.');
        $displayTotal = $booking->total_biaya_perbaikan ?? ($mainServiceFee + $partsTotal);
    @endphp

    <div class="page-wrap">
        <div class="topbar">
            <a href="/customer/dashboard" class="btn-back">← Kembali ke Dashboard</a>
            <h1>Invoice Perbaikan</h1>
        </div>

        <div class="invoice-card">
            <div class="invoice-header">
                <div class="invoice-brand">
                    <div>
                        <h2>BENGKEL CONNECT</h2>
                        <p>Sistem Tata Kelola & Solusi Otomotif Terintegrasi</p>
                    </div>
                    <div class="status-chip">🟠 {{ $statusText }}</div>
                </div>

                <div class="row" style="margin-top:28px;">
                    <div class="row-item">
                        <span>No. Nota</span>
                        <strong>{{ $invoiceNumber }}</strong>
                    </div>
                    <div class="row-item">
                        <span>Tanggal</span>
                        <strong>{{ $invoiceDate }}</strong>
                    </div>
                    <div class="row-item">
                        <span>Pelanggan</span>
                        <strong>{{ $customerName }}</strong>
                    </div>
                    <div class="row-item">
                        <span>Mekanik</span>
                        <strong>{{ $mechanicName }}</strong>
                    </div>
                    <div class="row-item">
                        <span>Unit Mobil</span>
                        <strong>{{ $vehicle }}</strong>
                    </div>
                    <div class="row-item">
                        <span>Layanan</span>
                        <strong>{{ $serviceLabel }}</strong>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">RINCIAN JASA & SUKU CADANG</div>

                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th style="width:8%;">No.</th>
                            <th>Deskripsi</th>
                            <th style="width:18%;">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1.</td>
                            <td>Jasa {{ $serviceLabel }} (Layanan Utama)</td>
                            <td class="amount">{{ $formatted($mainServiceFee) }}</td>
                        </tr>
                        @foreach($parts as $index => $part)
                            <tr>
                                <td>{{ $index + 2 }}.</td>
                                <td>{{ $part['name'] }}{{ $part['qty'] > 1 ? ' x'.$part['qty'] : '' }} {{ strlen($part['name']) ? '(⚠️ Temuan Mekanik)' : '' }}</td>
                                <td class="amount">{{ $formatted($part['subtotal']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="invoice-total">
                    <span>TOTAL TAGIHAN AKHIR</span>
                    <span>{{ $formatted($displayTotal) }}</span>
                </div>
            </div>

            <div class="section">
                <div class="payment-grid">
                    <div class="payment-box">
                        <h3>💳 OPSI PEMBAYARAN RESMI BENGKEL</h3>
                        <ul>
                            <li>Tunai: Melalui kasir langsung saat serah terima unit kendaraan.</li>
                            <li>Transfer Bank: Bank Mandiri Virtual Account 8877082606041122 a.n Bengkel Connect.</li>
                            <li>QRIS (Instan): Pindai QRIS Statis di samping kanan menggunakan m-Banking atau E-Wallet.</li>
                        </ul>
                        @if($canUploadProof)
                            <a href="{{ route('booking.showUpload', ['kode_booking' => $booking->kode_booking]) }}" class="btn-upload">Upload Bukti Pembayaran</a>
                        @endif
                    </div>
                    <div class="qris-card">
                        <div>
                            @php
                                // Hubungkan "QRIS statis DP" ke nota customer.
                                // - Jika customer sudah upload QRIS (tersimpan di qris_path), tampilkan itu.
                                // - Jika belum ada qris_path saat status DP, tampilkan kode DP statis agar tetap terhubung.
                                $isDp = in_array($booking->status ?? '', ['menunggu_pembayaran', 'menunggu_konfirmasi_bukti']);
                                $qrisCode = $isDp
                                    ? ('BengkelConnect-HomeService-DP-' . ($booking->kode_booking ?? ''))
                                    : ('BengkelConnect-HomeService-FINAL-' . ($booking->kode_booking ?? ''));
                            @endphp

                            <img class="qris-image" src="{{ !empty($booking->qris_path) ? asset('storage/' . $booking->qris_path) : asset('images/qris.png') }}" alt="QRIS Pembayaran">

                            @if($isDp && empty($booking->qris_path))
                                <div style="margin-top:10px; font-size:12px; color:#e5e7eb; font-weight:800;">
                                    Kode DP (statis): <span style="color:#fef3c7;">{{ $qrisCode }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="qris-info">
                            <span>GPN / NATIVE</span>
                            <span>BENGKEL CONNECT</span>
                            <span>NMID: ID102026142890</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="note-card">
                    <strong>CATATAN REKOMENDASI MEKANIK:</strong>
                    <p>{{ $booking->mechanic_note ?? 'Tidak ada catatan tambahan.' }}</p>
                </div>
            </div>

            <div class="footer-note">
                Terima kasih atas kepercayaan Anda menggunakan layanan prima Bengkel Connect.
            </div>
        </div>
    </div>
</body>
</html>
