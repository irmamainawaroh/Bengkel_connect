@extends('admin.dashboard-layout')

@section('title','Stok Gudang - Admin')
@section('heading','Stok Gudang')
@section('subheading','Manajemen Inventaris & Stok Gudang')

@section('content')
<style>
    body { background:#f4f4f5; }
    .inventory-header {
        display:flex;
        flex-direction:column;
        gap:10px;
        margin-bottom:20px;
    }

    .inventory-actions {
        display:flex;
        flex-wrap:wrap;
        gap:12px;
        justify-content:space-between;
        align-items:center;
        margin-bottom:18px;
    }

    .inventory-actions .button-row {
        display:flex;
        flex-wrap:wrap;
        gap:10px;
    }

    .btn-secondary, .btn-primary, .btn-outline {
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:10px 14px;
        border-radius:12px;
        border:1px solid transparent;
        font-weight:700;
        text-decoration:none;
        transition:0.2s;
    }

    .btn-primary { background:#dc2626; color:#fff; }
    .btn-primary:hover { opacity:.95; }
    .btn-secondary { background:#f8fafc; color:#1f2937; border-color:rgba(15,23,42,.08); }
    .btn-secondary:hover { background:#e2e8f0; }
    .btn-outline { background:#fff; color:#1f2937; border-color:#d1d5db; }

    .search-box {
        display:flex;
        align-items:center;
        gap:10px;
        flex:1;
        min-width:220px;
    }

    .search-box input {
        width:100%;
        padding:10px 12px;
        border-radius:12px;
        border:1px solid #d1d5db;
        color:#0f172a;
        background:#f8fafc;
    }

    .section-title-small {
        font-size:14px;
        color:#475569;
        margin-bottom:12px;
        font-weight:700;
    }

    .inventory-table, .activity-table {
        width:100%;
        border-collapse:collapse;
        margin-bottom:20px;
    }

    .inventory-table th, .inventory-table td,
    .activity-table th, .activity-table td {
        padding:12px 14px;
        border-bottom:1px solid #e2e8f0;
        font-size:13px;
        color:#1f2937;
        text-align:left;
    }

    .inventory-table th, .activity-table th {
        background:#f8fafc;
        color:#475569;
        font-weight:700;
    }

    .badge-status {
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:6px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:700;
    }

    .badge-success { background:#dcfce7; color:#166534; }
    .badge-warning { background:#fef3c7; color:#b45309; }
    .badge-danger { background:#fee2e2; color:#b91c1c; }

    .table-wrapper {
        border-radius:18px;
        overflow:hidden;
        border:1px solid #e2e8f0;
        background:#fff;
        box-shadow:0 8px 20px rgba(15,23,42,.05);
    }

    .table-section {
        padding:18px 20px;
    }

    .table-caption {
        font-size:14px;
        color:#475569;
        margin-bottom:14px;
        font-weight:700;
    }

    .footnote {
        font-size:13px;
        color:#475569;
        background:#f8fafc;
        padding:14px 18px;
        border-radius:14px;
        border:1px solid #e2e8f0;
    }

    @media (max-width: 900px) {
        .inventory-actions { flex-direction:column; align-items:flex-start; }
        .search-box { width:100%; }
    }
</style>

<div class="inventory-header">
    <div style="font-size:18px; font-weight:800; color:#0f172a;">BENGKEL CONNECT [ADMIN]</div>
    <div style="color:#475569; font-size:14px;">Manajemen Inventaris & Stok Gudang</div>
</div>

<div class="inventory-actions">
    <div class="button-row">
        <a href="#" id="btn-add" class="btn-primary"><i class="bi bi-plus-lg"></i> Tambah Barang Baru</a>
        <form action="/admin/stok-gudang/import" method="POST" enctype="multipart/form-data" style="display:inline-block;">
            @csrf
            <label class="btn-secondary" style="cursor:pointer;">
                <i class="bi bi-file-earmark-arrow-down"></i>
                <input type="file" name="import_file" accept="text/csv" style="display:none;" onchange="this.form.submit()">
                Import Excel
            </label>
        </form>
        <a href="/admin/stok-gudang/export" class="btn-secondary"><i class="bi bi-file-earmark-arrow-up"></i> Export Laporan</a>
    </div>
    <div class="search-box">
        <form action="/admin/stok-gudang" method="GET" style="display:flex; width:100%; gap:10px; align-items:center;">
            <label style="font-weight:700; color:#334155;">Cari Part:</label>
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama atau kode part..." />
            <button type="submit" class="btn-outline">Cari</button>
        </form>
    </div>
</div>

<div class="table-wrapper">
    <div class="table-section">
        <div class="table-caption">📦 STATUS KETERSEDIAAN SUKU CADANG</div>
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID Part</th>
                    <th>Nama Suku Cadang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Harga Jual</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $it)
                    @php
                        $statusClass = 'badge-success';
                        $statusLabel = '🟢 Aman';
                        if ($it['stock'] <= 3) { $statusClass = 'badge-danger'; $statusLabel = '🔴 KRITIS'; }
                        elseif ($it['stock'] <= 10) { $statusClass = 'badge-warning'; $statusLabel = '🟡 Menipis'; }
                    @endphp
                    <tr>
                        <td>{{ $it['id'] }}</td>
                        <td>{{ $it['name'] }}</td>
                        <td>{{ $it['category'] }}</td>
                        <td>{{ $it['stock'] }}</td>
                        <td>{{ $it['unit'] }}</td>
                        <td>Rp {{ number_format($it['price'],0,',','.') }}</td>
                        <td><span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="table-section">
        <div class="table-caption">📈 AKTIVITAS STOK TERAKHIR (LOG KELUAR/MASUK)</div>
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Suku Cadang</th>
                    <th>Perubahan</th>
                    <th>Referensi Transaksi</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>04 Juni 2026</td>
                    <td>Timbal Balancing</td>
                    <td>-2 pcs</td>
                    <td>Nota: INV/20260604/0142</td>
                    <td>Admin (Auto)</td>
                </tr>
                <tr>
                    <td>04 Juni 2026</td>
                    <td>Pentil Ban Tubeless</td>
                    <td>-1 pcs</td>
                    <td>Nota: INV/20260604/0142</td>
                    <td>Admin (Auto)</td>
                </tr>
                <tr>
                    <td>03 Juni 2026</td>
                    <td>Oli MPX2 0.8 Liter</td>
                    <td>+48 pcs</td>
                    <td>Restock Supplier PT. A</td>
                    <td>Admin (Manual)</td>
                </tr>
            </tbody>
        </table>

        <div class="footnote">*Catatan: Stok otomatis berkurang setiap kali Admin mengonfirmasi pengiriman Nota Tagihan.</div>
    </div>
</div>
 
    <!-- Add item modal (simple toggle) -->
    <div id="addForm" style="display:none; margin-top:16px;">
        <form action="/admin/stok-gudang/add" method="POST">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:8px;">
                <input name="id" placeholder="ID Part (ex: PRT-006)" required class="form-control">
                <input name="name" placeholder="Nama Suku Cadang" required class="form-control">
                <input name="category" placeholder="Kategori" class="form-control">
                <input name="stock" placeholder="Stok" type="number" required class="form-control">
                <input name="unit" placeholder="Satuan" class="form-control">
                <input name="price" placeholder="Harga Jual (angka)" type="number" required class="form-control">
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn-primary">Simpan</button>
                <button type="button" class="btn-outline" onclick="toggleAdd(false)">Batal</button>
            </div>
        </form>
    </div>

    <script>
        function toggleAdd(show){
            document.getElementById('addForm').style.display = show ? 'block' : 'none';
        }
        document.getElementById('btn-add').addEventListener('click', function(e){ e.preventDefault(); toggleAdd(true); });
    </script>
@endsection
