@extends('admin.dashboard-layout')

@section('title','Halaman Mekanik - Kelola Booking')

@section('styles')
    <style>
        .mechanic-menu{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px;}
        .mechanic-menu a{padding:10px 14px;border-radius:12px;text-decoration:none;font-weight:900;font-size:13px;background:#f8fafc;color:#334155;box-shadow:inset 0 0 0 1px rgba(15,23,42,0.06);}
        .mechanic-menu a.active{background:#dc2626;color:#fff;box-shadow:none;}
    </style>
@endsection

@section('content')
    <div class="mechanic-menu">
        <a href="/mekanik/dashboard" class="active">Kelola Booking</a>
        <a href="/admin/home-service">Kelola Home Service</a>
        <a href="/admin/laporan-mekanik">Laporan Mekanik</a>
    </div>

    <style>
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 18px;
        }
        .card-task {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 8px 20px rgba(15,23,42,0.04);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .badge { padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px; }
        .badge-antrean{background:#fef3c7;color:#92400e}
        .badge-diagnosa{background:#fef3c7;color:#92400e}
        .badge-dikerjakan{background:#fbfdff;color:#0369a1}
        .badge-testdrive{background:#f97316;color:#ffffff}
        .badge-selesai{background:#16a34a;color:#ffffff}

        .stepper{display:flex; gap:10px; flex-wrap:wrap}
        .step{display:inline-flex; align-items:center; gap:8px; padding:10px 12px; border-radius:12px; background:#fff; border:1px solid #e6eef6; cursor:pointer}
        .step.active{background:#eff6ff; border-color:#0ea5e9}
        .step.done{background:#dcfce7; border-color:#16a34a}
        .detail-panel{display:none; background:#fbfeff; border:1px solid #e6f5ff; border-radius:12px; padding:14px;}
        .detail-footer{position:sticky; bottom:0; background:linear-gradient(180deg, rgba(248,250,252,0), #fbfeff 60%); padding-top:12px}
        .button-primary{background:#0ea5e9; color:#fff; border:none; padding:10px 12px; border-radius:10px; cursor:pointer; font-weight:700}
        .button-success{background:#16a34a; color:#fff; border:none; padding:10px 12px; border-radius:10px; cursor:pointer}
        input[type=text], textarea, input[type=number]{width:100%; padding:10px; border:1px solid #e6edf3; border-radius:8px}
    </style>
@endsection

@section('content')
    @if(session('success'))
        <div style="background:#dcfce7; border:1px solid #bbf7d0; color:#166534; padding:12px; border-radius:10px; margin-bottom:12px; font-weight:700;">
            {{ session('success') }}
        </div>
    @endif

    @php
        $countSemua = count($bookings ?? []);
        $countAntrean = collect($bookings ?? [])->where('status','dikirim_ke_mekanik')->count();
        $countDikerjakan = collect($bookings ?? [])->where('status','sedang_dikerjakan')->count();
        $countSelesai = collect($bookings ?? [])->where('status','selesai')->count();
    @endphp

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px">
        <div style="background:#fff; border:1px solid rgba(15,23,42,0.06); padding:12px 16px; border-radius:12px; min-width:120px">
            <div style="font-size:12px; color:#64748b">Semua</div>
            <div style="font-weight:800; font-size:20px">{{ $countSemua }}</div>
        </div>
        <div style="background:#fef3c7; padding:12px 16px; border-radius:12px; min-width:120px">
            <div style="font-size:12px; color:#92400e">Antrean</div>
            <div style="font-weight:800; font-size:20px">{{ $countAntrean }}</div>
        </div>
        <div style="background:#dbf4ff; padding:12px 16px; border-radius:12px; min-width:120px">
            <div style="font-size:12px; color:#0369a1">Dikerjakan</div>
            <div style="font-weight:800; font-size:20px">{{ $countDikerjakan }}</div>
        </div>
        <div style="background:#dcfce7; padding:12px 16px; border-radius:12px; min-width:120px">
            <div style="font-size:12px; color:#166534">Selesai</div>
            <div style="font-weight:800; font-size:20px">{{ $countSelesai }}</div>
        </div>
    </div>

    <div class="grid-cards">
        @forelse($bookings ?? [] as $booking)
            @php
                $status = $booking->status ?? 'dikirim_ke_mekanik';
                $progress = $booking->latest_progress ?? 0;
                $statusLabel = ['dikirim_ke_mekanik'=>'Antrean','sedang_dikerjakan'=>'Dikerjakan','selesai'=>'Selesai'][$status] ?? ucfirst($status);
                $statusClass = ['dikirim_ke_mekanik'=>'badge-antrean','sedang_dikerjakan'=>'badge-dikerjakan','selesai'=>'badge-selesai'][$status] ?? 'badge-antrean';
                $diagnosisDone = in_array($status,['sedang_dikerjakan','selesai']);
                $isWorking = in_array($status,['sedang_dikerjakan','selesai']);
                $isFinished = $status === 'selesai';
            @endphp

            <div class="card-task">
                <div style="display:flex; justify-content:space-between; align-items:flex-start">
                    <div>
                        <div style="font-size:13px; color:#64748b">Nomor Polisi</div>
                        <div style="font-weight:800; font-size:20px">{{ $booking->nopol ?? '-' }}</div>
                        <div style="color:#475569; margin-top:6px">{{ $booking->kendaraan ?? 'Motor' }}</div>
                        <div style="color:#475569; margin-top:4px">Pemilik: {{ $booking->nama ?? '-' }}</div>
                    </div>
                    <div style="text-align:right">
                        <div><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></div>
                        <div style="margin-top:8px; color:#64748b">Progress: {{ $progress }}%</div>
                    </div>
                </div>

                <div style="display:flex; gap:10px; align-items:center; justify-content:space-between; margin-top:6px">
                    @if($status === 'dikirim_ke_mekanik')
                        <form method="POST" action="{{ route('mekanik.update-status', $booking->kode_booking) }}" style="margin:0">
                            @csrf
                            <input type="hidden" name="new_status" value="sedang_dikerjakan">
                            <button type="submit" class="button-primary">Mulai Kerjakan</button>
                        </form>
                    @elseif($status === 'sedang_dikerjakan')
                        <button type="button" class="button-primary" onclick="openWorkStep('{{ $booking->kode_booking }}','dikerjakan')">Update Progres</button>
                    @elseif($status === 'selesai')
                        <button type="button" class="button-success" onclick="openWorkStep('{{ $booking->kode_booking }}','selesai')">Lihat Detail</button>
                    @else
                        <button type="button" class="button-success" onclick="openWorkStep('{{ $booking->kode_booking }}','test_drive')">Tes Drive</button>
                    @endif

                    <div style="font-size:12px; color:#94a3b8">Layanan: {{ $booking->layanan ?? '-' }}</div>
                </div>

                <div id="detail-{{ $booking->kode_booking }}" class="detail-panel" data-status="{{ $status }}">
                    <div class="stepper">
                        {{-- 1 Diagnosa: kuning --}}
                        <button type="button" class="step" onclick="openWorkStep('{{ $booking->kode_booking }}','diagnosis')"
                                style="background:{{ $status === 'dikirim_ke_mekanik' ? '#fef3c7' : '#fde68a' }}; border-color:{{ $status === 'dikirim_ke_mekanik' ? '#f59e0b' : '#b45309' }}; color:#92400e;">
                            <span>1</span><span>Diagnosa</span>
                        </button>

                        {{-- 2 Dikerjakan: biru --}}
                        <button type="button" class="step" onclick="openWorkStep('{{ $booking->kode_booking }}','dikerjakan')"
                                style="background:{{ $status === 'sedang_dikerjakan' ? '#dbf4ff' : '#dbeafe' }}; border-color:{{ $status === 'sedang_dikerjakan' ? '#0284c7' : '#1d4ed8' }}; color:#0369a1;">
                            <span>2</span><span>Dikerjakan</span>
                        </button>

                        {{-- 3 Test Drive: oranye --}}
                        <button type="button" class="step" onclick="openWorkStep('{{ $booking->kode_booking }}','test_drive')"
                                style="background:{{ $status === 'selesai' ? '#fdba74' : ($status === 'butuh_konfirmasi_biaya' ? '#f97316' : '#fff') }}; border-color:#f97316; color:{{ $status === 'butuh_konfirmasi_biaya' ? '#ffffff' : '#7c2d12' }};">
                            <span>3</span><span>Test Drive</span>
                        </button>

                        {{-- 4 Selesai: hijau --}}
                        <button type="button" class="step" onclick="openWorkStep('{{ $booking->kode_booking }}','selesai')"
                                style="background:{{ $status === 'selesai' ? '#16a34a' : '#fff' }}; border-color:{{ $status === 'selesai' ? '#16a34a' : '#e6eef6' }}; color:{{ $status === 'selesai' ? '#ffffff' : '#334155' }};">
                            <span>4</span><span>Selesai</span>
                        </button>
                    </div>


                    <div style="margin-top:12px">
                        <div style="margin-bottom:8px"><strong>Keluhan:</strong> {{ $booking->catatan ?? '-' }}</div>
                        <div style="display:grid; gap:10px">
                            <label>Catatan Tambahan</label>
                            <textarea form="progress-form-{{ $booking->kode_booking }}" name="mechanic_note" rows="3" placeholder="Catatan mekanik..."></textarea>
                            <label>Rekomendasi Part</label>
                                    <div id="recommended-list-{{ $booking->kode_booking }}" style="display:grid; gap:8px">
                                        @if(!empty($booking->recommended_parts))
                                            @php
                                                $parts = json_decode($booking->recommended_parts, true) ?: [];
                                            @endphp
                                            @foreach($parts as $p)
                                                <div style="display:flex; gap:8px; align-items:center">
                                                    <input type="text" value="{{ $p }}" readonly style="flex:1" />
                                                    <input type="hidden" name="recommended_parts[]" value="{{ $p }}" form="progress-form-{{ $booking->kode_booking }}">
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                            <button type="button" class="button-primary" style="width:max-content" onclick="addRecommendedPart('{{ $booking->kode_booking }}')">+ Tambah Part</button>
                        </div>
                    </div>

                    <div class="detail-footer">
                        <div style="display:flex; gap:8px; justify-content:flex-end;">
                            @if($status === 'sedang_dikerjakan')
                                <button type="button" class="button-primary" onclick="document.getElementById('progress-panel-{{ $booking->kode_booking }}').style.display='block'">Simpan Progres</button>
                                <button type="button" class="button-success" onclick="openWorkStep('{{ $booking->kode_booking }}','selesai')">Upload Bukti</button>
                            @elseif($status === 'selesai')
                                <button type="button" class="button-success" onclick="openWorkStep('{{ $booking->kode_booking }}','selesai')">Lihat Ringkasan</button>
                            @endif
                        </div>
                    </div>

                    @php
                        $recommendedParts = [];
                        if (!empty($booking->recommended_parts)) {
                            try { $recommendedParts = json_decode($booking->recommended_parts, true) ?: []; } catch (\Exception $e) { $recommendedParts = []; }
                        }
                    @endphp

                    @if($status === 'sedang_dikerjakan')
                        <div id="progress-panel-{{ $booking->kode_booking }}" style="display:none; margin-top:12px; background:#fff; border:1px solid #e6f5ff; padding:12px; border-radius:10px">
                            <form id="progress-form-{{ $booking->kode_booking }}" method="POST" action="{{ route('mekanik.progress-update', $booking->kode_booking) }}">
                                @csrf
                                <div style="display:grid; gap:10px">
                                    <label>Detail Progress</label>
                                    <textarea name="update_text" rows="4" placeholder="Contoh: Sedang mengganti kampas rem..."></textarea>
                                    <label>Persentase Progress (%)</label>
                                    <input type="number" name="progress_percentage" min="0" max="100" value="{{ $progress }}">
                                    @foreach($recommendedParts as $p)
                                        <input type="hidden" name="recommended_parts[]" value="{{ $p }}">
                                    @endforeach
                                    <div style="display:flex; gap:8px; justify-content:flex-end">
                                        <button type="button" class="button-primary" onclick="saveProgress('{{ $booking->kode_booking }}')">Simpan Progres</button>
                                        <button type="submit" class="button-primary">Kirim Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div id="final-summary-panel-{{ $booking->kode_booking }}" style="display:none; margin-top:12px; background:#ffffff; border:1px solid #e2e8f0; padding:16px; border-radius:14px">
                            <form method="POST" action="{{ route('mekanik.upload-bukti-kerja', $booking->kode_booking) }}" enctype="multipart/form-data">
                                @csrf
                                <div style="display:grid; gap:14px">
                                    <div style="font-size:14px; font-weight:800; color:#0f172a">RINGKASAN FINAL PENGERJAAN</div>
                                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px; color:#0f172a">
                                        <div style="font-weight:700; margin-bottom:8px">Layanan:</div>
                                        <div style="margin-bottom:10px">{{ $booking->layanan ?? 'Spooring & Balancing 4 Roda' }}</div>
                                        <div style="font-weight:700; margin-bottom:8px">Sparepart Tambahan:</div>
                                        <div style="margin-left:12px; color:#334155">
                                            @if(!empty($recommendedParts))
                                                @foreach($recommendedParts as $p)
                                                    <div style="margin-bottom:4px">- {{ $p }} (Approved)</div>
                                                @endforeach
                                            @else
                                                <div style="margin-bottom:4px">- Tidak ada sparepart tambahan</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px; color:#0f172a">
                                        <div style="font-weight:700; margin-bottom:8px">CHECKLIST QUALITY CONTROL (WAJIB)</div>
                                        <div style="display:grid; gap:6px; color:#334155">
                                            <label><input type="checkbox" checked disabled> Tekanan ban sudah sesuai standar (PSI)</label>
                                            <label><input type="checkbox" checked disabled> Semua baut roda telah dikencangkan</label>
                                            <label><input type="checkbox" checked disabled> Mesin/Area kerja telah dibersihkan</label>
                                        </div>
                                    </div>

                                    <div>
                                        <div style="font-weight:700; margin-bottom:8px">CATATAN REKOMENDASI UNTUK PELANGGAN</div>
                                        <textarea name="customer_recommendation" rows="4" placeholder="Masukkan saran servis berikutnya di sini..." style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:10px; resize:vertical">{{ $booking->mechanic_note ?? '' }}</textarea>
                                    </div>

                                    <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:12px; padding:14px; color:#334155">
                                        <div style="font-weight:700; margin-bottom:8px">FOTO BUKTI HASIL AKHIR (SESUAI SCREENSHOT)</div>
                                        @if(!empty($booking->bukti_pengerjaan_path))
                                            <div style="margin-bottom:10px">
                                                <img src="{{ asset('storage/' . $booking->bukti_pengerjaan_path) }}" alt="Bukti Pengerjaan" style="max-width:100%; border-radius:10px; border:1px solid #cbd5e1" />
                                            </div>
                                        @endif
                                        <div style="display:flex; gap:10px; align-items:center;">
                                            <label style="font-size:13px; font-weight:700;">Pilih File</label>
                                            <input type="file" name="bukti_pengerjaan" accept="image/*" style="flex:1" {{ $booking->status === 'selesai' ? 'disabled' : 'required' }}>
                                        </div>
                                        <div style="font-size:12px; color:#64748b; margin-top:6px">Pastikan foto hasil pengerjaan terlihat jelas</div>
                                    </div>

                                    <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:6px">
                                        <button type="button" class="button-primary" onclick="resetFinalSummary('{{ $booking->kode_booking }}')">Reset Form</button>
                                        <button type="submit" class="button-success">SELESAIKAN & KIRIM KE ADMIN</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div style="margin-top:12px; padding:12px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; color:#475569">
                            Progress update hanya dapat dikirim ketika booking berada dalam status <strong>sedang_dikerjakan</strong>.
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; padding:20px; background:#fff; border:1px solid rgba(15,23,42,0.04); border-radius:10px">Belum ada booking untuk mekanik.</div>
        @endforelse
    </div>

    <script>
        function toggleDetail(kode) {
            const el = document.getElementById('detail-' + kode);
            if (!el) return;
            el.style.display = el.style.display === 'block' ? 'none' : 'block';
            if (el.style.display === 'block') el.scrollIntoView({behavior:'smooth', block:'center'});
        }

        function addRecommendedPart(kodeBooking) {
            const list = document.getElementById('recommended-list-' + kodeBooking);
            const form = document.getElementById('progress-form-' + kodeBooking);
            if (!list || !form) return;
            const temp = document.createElement('div'); temp.style.display='flex'; temp.style.gap='8px'; temp.style.alignItems='center';
            const input = document.createElement('input'); input.type='text'; input.placeholder='Nama part...'; input.style.flex='1';
            const addBtn = document.createElement('button'); addBtn.type='button'; addBtn.className='button-primary'; addBtn.textContent='Tambah';
            addBtn.onclick = function(){ if(!input.value.trim()) return; const read=document.createElement('input'); read.type='text'; read.value=input.value.trim(); read.readOnly=true; read.style.flex='1'; const hidden=document.createElement('input'); hidden.type='hidden'; hidden.name='recommended_parts[]'; hidden.value=input.value.trim(); hidden.setAttribute('form', form.id); const del=document.createElement('button'); del.type='button'; del.textContent='Hapus'; del.className='button-success'; del.style.background='#ef4444'; const row=document.createElement('div'); row.style.display='flex'; row.style.gap='8px'; row.style.alignItems='center'; del.onclick=function(){ list.removeChild(row); }; row.appendChild(read); row.appendChild(hidden); row.appendChild(del); list.appendChild(row); input.value=''; };
            temp.appendChild(input); temp.appendChild(addBtn); list.appendChild(temp); input.focus();
        }

        function saveProgress(kodeBooking) {
            const form = document.getElementById('progress-form-' + kodeBooking);
            if (!form) return;
            const ta = form.querySelector('textarea[name="update_text"]');
            if (ta && ta.value.trim().length > 0) { form.submit(); return; }
            const panel = document.getElementById('progress-panel-' + kodeBooking);
            if (panel) { panel.style.display='block'; const t = panel.querySelector('textarea[name="update_text"]'); if (t) t.focus(); panel.scrollIntoView({behavior:'smooth', block:'center'}); }
        }

        function openWorkStep(kodeBooking, step) {
            const detail = document.getElementById('detail-' + kodeBooking);
            const progress = document.getElementById('progress-panel-' + kodeBooking);
            const finalSummary = document.getElementById('final-summary-panel-' + kodeBooking);
            const status = detail ? detail.dataset.status : null;
            if (detail) detail.style.display='block';
            if (progress) progress.style.display='none';
            if (finalSummary) finalSummary.style.display='none';
            if ((step === 'dikerjakan' || step === 'test_drive') && status === 'sedang_dikerjakan' && progress) {
                progress.style.display='block';
                progress.scrollIntoView({behavior:'smooth', block:'center'});
                return;
            }
            if (step === 'selesai' && finalSummary) {
                finalSummary.style.display='block';
                finalSummary.scrollIntoView({behavior:'smooth', block:'center'});
                return;
            }
        }

        function resetFinalSummary(kodeBooking) {
            const panel = document.getElementById('final-summary-panel-' + kodeBooking);
            if (!panel) return;
            const inputs = panel.querySelectorAll('input[type=file], textarea');
            inputs.forEach(el => {
                if (el.type === 'file') el.value = '';
                if (el.tagName.toLowerCase() === 'textarea') el.value = '';
            });
            panel.scrollIntoView({behavior:'smooth', block:'center'});
        }
    </script>
@endsection
