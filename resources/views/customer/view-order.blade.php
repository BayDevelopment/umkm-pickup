@extends('layouts.customer')

@section('customer')
    <section class="order-detail-page">
        <div id="order-container" data-order-id="{{ $order->id }}"></div>

        <div class="container py-5">

            {{-- BACK BUTTON --}}
            <div class="mb-4">
                <a href="{{ route('customer.orders') }}" class="btn-back">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Pesanan</span>
                </a>
            </div>

            {{-- ORDER HEADER --}}
            <div class="glass-card order-header-card mb-4">

                @php
                    $statusColor = match ($order->status) {
                        'pending' => 'badge-pending',
                        'process' => 'badge-process',
                        'done' => 'badge-done',
                        'cancel' => 'badge-cancel',
                        default => 'badge-default',
                    };

                    $paymentColor = match ($order->payment_status) {
                        'pending' => 'badge-pending',
                        'paid' => 'badge-done',
                        'rejected' => 'badge-cancel',
                        default => 'badge-default',
                    };

                    $statusText = match ($order->status) {
                        'pending' => 'Pesanan Menunggu Konfirmasi',
                        'process' => 'Pesanan Sedang Diproses',
                        'done' => 'Pesanan Selesai',
                        'cancel' => 'Pesanan Dibatalkan',
                        default => ucfirst($order->status),
                    };

                    $statusDescription = match ($order->status) {
                        'pending' => 'Pesanan kamu sudah dibuat dan sedang menunggu konfirmasi dari toko.',
                        'process' => 'Pesanan kamu sedang disiapkan oleh toko.',
                        'done' => 'Pesanan sudah selesai dan berhasil diterima.',
                        'cancel' => 'Pesanan dibatalkan oleh sistem atau toko.',
                        default => '',
                    };

                    $paymentText = match ($order->payment_status) {
                        'pending' => 'Pembayaran Menunggu Verifikasi',
                        'paid' => 'Pembayaran Berhasil',
                        'rejected' => 'Pembayaran Ditolak',
                        default => ucfirst($order->payment_status),
                    };

                    $statusIcon = match ($order->status) {
                        'pending' => 'fa-clock',
                        'process' => 'fa-gear',
                        'done' => 'fa-circle-check',
                        'cancel' => 'fa-circle-xmark',
                        default => 'fa-box',
                    };

                    $statusGradient = match ($order->status) {
                        'pending' => 'linear-gradient(135deg, #f59e0b, #d97706)',
                        'process' => 'linear-gradient(135deg, #3b82f6, #2563eb)',
                        'done' => 'linear-gradient(135deg, #10b981, #059669)',
                        'cancel' => 'linear-gradient(135deg, #ef4444, #dc2626)',
                        default => 'linear-gradient(135deg, #64748b, #475569)',
                    };
                @endphp

                {{-- TOP BANNER STATUS --}}
                <div
                    style="background: {{ $statusGradient }}; border-radius: 12px 12px 0 0; padding: 20px 24px; margin: -1px -1px 0 -1px;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="width:48px; height:48px; background:rgba(255,255,255,0.2); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid {{ $statusIcon }} fa-lg" style="color:#fff;"></i>
                            </div>
                            <div>
                                <div style="color:rgba(255,255,255,0.8); font-size:12px; margin-bottom:2px;">Status Pesanan
                                </div>
                                <div style="color:#fff; font-size:16px; font-weight:700;" id="order-status-text">
                                    {{ $statusText }}</div>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="color:rgba(255,255,255,0.7); font-size:11px;">Kode Pesanan</div>
                            <div style="color:#fff; font-size:13px; font-weight:700; letter-spacing:0.5px;">
                                {{ $order->order_code }}</div>
                        </div>
                    </div>

                    @if ($statusDescription)
                        <div
                            style="margin-top:12px; background:rgba(255,255,255,0.15); border-radius:8px; padding:10px 14px; font-size:13px; color:rgba(255,255,255,0.9);">
                            <i class="fa-solid fa-circle-info me-2"></i>{{ $statusDescription }}
                        </div>
                    @endif
                </div>

                {{-- BODY --}}
                <div style="padding: 24px;">

                    {{-- ORDER META --}}
                    <div class="d-flex flex-wrap gap-3 mb-4">

                        {{-- Tanggal --}}
                        <div
                            style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:12px 16px; flex:1; min-width:160px;">
                            <div style="font-size:11px; color:#64748b; margin-bottom:4px;">
                                <i class="fa-solid fa-calendar me-1"></i>Tanggal Pesanan
                            </div>
                            <div style="font-size:13px; font-weight:600; color:#e2e8f0;">
                                {{ $order->created_at->format('d M Y • H:i') }}
                            </div>
                        </div>

                        {{-- Pembayaran --}}
                        <div
                            style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:12px 16px; flex:1; min-width:160px;">
                            <div style="font-size:11px; color:#64748b; margin-bottom:4px;">
                                <i class="fa-solid fa-credit-card me-1"></i>Metode Pembayaran
                            </div>
                            <div style="font-size:13px; font-weight:600; color:#e2e8f0;">
                                {{ $order->paymentMethod->name ?? '-' }}
                                <span style="color:#64748b; font-weight:400;">·
                                    {{ $order->paymentMethod->bank_name ?? '' }}</span>
                            </div>
                        </div>

                        {{-- Status Pembayaran --}}
                        <div
                            style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:12px 16px; flex:1; min-width:160px;">
                            <div style="font-size:11px; color:#64748b; margin-bottom:6px;">
                                <i class="fa-solid fa-receipt me-1"></i>Status Pembayaran
                            </div>
                            <span id="payment-status-badge" class="badge-modern {{ $paymentColor }}">
                                <i class="fa-solid fa-credit-card"></i>
                                <span id="payment-status-text">{{ $paymentText }}</span>
                            </span>
                        </div>

                    </div>

                    {{-- NOTIFIKASI PROSES / DONE --}}
                    @switch($order->status)
                        @case('process')
                            <div
                                style="background:rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.3); border-radius:10px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:12px;">
                                <i class="fa-solid fa-bag-shopping fa-lg" style="color:#3b82f6;"></i>
                                <span style="font-size:13px; color:#93c5fd;">
                                    Silahkan datangi toko terdekat, tunjukan bukti, dan ambil pesanan anda.
                                </span>
                            </div>
                        @break

                        @case('done')
                            <div
                                style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); border-radius:10px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:12px;">
                                <i class="fa-solid fa-party-horn fa-lg" style="color:#10b981;"></i>
                                <span style="font-size:13px; color:#6ee7b7;">
                                    Terimakasih sudah belanja ditoko kami dengan aplikasi ini, semoga harimu menyenangkan 🎉
                                </span>
                            </div>
                        @break
                    @endswitch

                    {{-- CABANG --}}
                    @if ($order->branch)
                        <div
                            style="background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.2); border-radius:12px; padding:18px 20px;">

                            <div
                                style="font-size:12px; color:#a5b4fc; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
                                <i class="fa-solid fa-store me-2"></i>Cabang Pengambilan
                            </div>

                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:#e2e8f0; margin-bottom:4px;">
                                        {{ $order->branch->name }}
                                    </div>
                                    <div style="font-size:13px; color:#64748b;">
                                        <i class="fa-solid fa-location-dot me-1"></i>
                                        {{ $order->branch->address ?? 'Alamat tidak tersedia' }}
                                    </div>
                                </div>

                                @if ($order->branch->latitude && $order->branch->longitude)
                                    <a href="https://www.google.com/maps?q={{ $order->branch->latitude }},{{ $order->branch->longitude }}"
                                        target="_blank"
                                        style="display:inline-flex; align-items:center; gap:8px; background:rgba(99,102,241,0.2); color:#a5b4fc; border:1px solid rgba(99,102,241,0.4); border-radius:8px; padding:8px 14px; font-size:13px; text-decoration:none; white-space:nowrap; transition:all 0.2s;"
                                        onmouseover="this.style.background='rgba(99,102,241,0.35)'"
                                        onmouseout="this.style.background='rgba(99,102,241,0.2)'">
                                        <i class="fa-solid fa-map-location-dot"></i>
                                        Lihat di Maps
                                    </a>
                                @endif
                            </div>

                        </div>
                    @endif

                </div>

            </div>


            {{-- DATA REKENING --}}
            <div id="payment-wrapper">

                @if ($order->payment_status === 'pending')
                    <div id="payment-section" class="glass-card mb-4">

                        {{-- HEADER --}}
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
                            <div
                                style="width:40px; height:40px; background:linear-gradient(135deg,#f59e0b,#d97706); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fa-solid fa-building-columns" style="color:#fff;"></i>
                            </div>
                            <div>
                                <div style="font-size:15px; font-weight:700; color:#e2e8f0;">Informasi Rekening</div>
                                <div style="font-size:12px; color:#64748b;">Transfer ke rekening berikut</div>
                            </div>
                        </div>

                        {{-- REKENING CARD --}}
                        <div
                            style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:12px; padding:20px; margin-bottom:16px;">

                            <div style="display:flex; flex-direction:column; gap:14px;">

                                <div
                                    style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.06);">
                                    <span style="font-size:12px; color:#94a3b8;">Bank</span>
                                    <span
                                        style="font-size:14px; font-weight:700; color:#fbbf24;">{{ $order->bank_name }}</span>
                                </div>

                                <div
                                    style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.06);">
                                    <span style="font-size:12px; color:#94a3b8;">Nomor Rekening</span>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <span style="font-size:15px; font-weight:800; color:#e2e8f0; letter-spacing:1px;"
                                            id="rekeningNumber">
                                            {{ $order->bank_account_number }}
                                        </span>
                                        <button type="button"
                                            onclick="copyRekening('{{ $order->bank_account_number }}', this)"
                                            style="background:rgba(245,158,11,0.2); border:1px solid rgba(245,158,11,0.3); color:#fbbf24; border-radius:6px; padding:4px 8px; font-size:11px; cursor:pointer; transition:all 0.2s;"
                                            onmouseover="this.style.background='rgba(245,158,11,0.35)'"
                                            onmouseout="this.style.background='rgba(245,158,11,0.2)'">
                                            <i class="fa-solid fa-copy me-1"></i>Copy
                                        </button>
                                    </div>
                                </div>

                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; color:#94a3b8;">Atas Nama</span>
                                    <span
                                        style="font-size:14px; font-weight:600; color:#e2e8f0;">{{ $order->bank_account_name }}</span>
                                </div>

                            </div>

                        </div>

                        {{-- TOTAL TRANSFER --}}
                        <div
                            style="background:linear-gradient(135deg,#f59e0b,#d97706); border-radius:10px; padding:14px 18px; display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                            <span style="font-size:13px; color:rgba(255,255,255,0.85);">Total Transfer</span>
                            <span style="font-size:18px; font-weight:800; color:#fff;">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- WARNING --}}
                        <div
                            style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:8px; padding:10px 14px; margin-bottom:20px; font-size:12px; color:#fca5a5; display:flex; align-items:center; gap:8px;">
                            <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444; flex-shrink:0;"></i>
                            Pastikan nominal transfer sesuai total pesanan. Format file: JPG, PNG (maks. 1MB).
                        </div>

                        {{-- UPLOAD FORM --}}
                        @if ($order->payment_status === 'pending' && !$order->payment_proof)
                            <div id="upload-form">
                                <form method="POST" action="{{ route('customer.orders.upload', $order->id) }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div style="border:2px dashed rgba(99,102,241,0.3); border-radius:12px; padding:24px; text-align:center; background:rgba(99,102,241,0.05); transition:all 0.2s; cursor:pointer;"
                                        onclick="document.getElementById('paymentProof-{{ $order->id }}').click()"
                                        onmouseover="this.style.borderColor='rgba(99,102,241,0.6)'; this.style.background='rgba(99,102,241,0.1)'"
                                        onmouseout="this.style.borderColor='rgba(99,102,241,0.3)'; this.style.background='rgba(99,102,241,0.05)'">

                                        <i class="fa-solid fa-cloud-arrow-up fa-2x"
                                            style="color:#6366f1; margin-bottom:10px;"></i>
                                        <div style="font-size:14px; font-weight:600; color:#a5b4fc; margin-bottom:4px;">
                                            Klik untuk pilih file
                                        </div>
                                        <div style="font-size:12px; color:#64748b;"
                                            id="file-name-text-{{ $order->id }}">
                                            PNG, JPG hingga 1MB
                                        </div>

                                        <input type="file" name="payment_proof" id="paymentProof-{{ $order->id }}"
                                            accept="image/png, image/jpeg" required style="display:none;"
                                            onchange="document.getElementById('file-name-text-{{ $order->id }}').textContent = this.files[0]?.name ?? 'PNG, JPG hingga 1MB'">
                                    </div>

                                    @error('payment_proof')
                                        <div
                                            style="font-size:12px; color:#f87171; margin-top:8px; display:flex; align-items:center; gap:6px;">
                                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                        </div>
                                    @enderror

                                    <button type="submit"
                                        style="width:100%; margin-top:14px; background:linear-gradient(135deg,#6366f1,#4f46e5); color:#fff; border:none; border-radius:10px; padding:13px; font-size:14px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:opacity 0.2s;"
                                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        Upload Bukti Transfer
                                    </button>

                                </form>
                            </div>
                        @else
                            {{-- SUDAH UPLOAD --}}
                            <div id="upload-success"
                                style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); border-radius:12px; padding:18px 20px;">

                                <div style="display:flex; align-items:flex-start; gap:14px;">

                                    <div
                                        style="width:40px; height:40px; background:rgba(16,185,129,0.15); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="fa-solid fa-circle-check" style="color:#10b981; font-size:18px;"></i>
                                    </div>

                                    <div style="flex:1;">
                                        <div style="font-size:14px; font-weight:700; color:#e2e8f0; margin-bottom:4px;">
                                            Bukti transfer telah diupload
                                        </div>
                                        <div style="font-size:13px; color:#94a3b8;" id="upload-desc">
                                            @if ($order->payment_status === 'pending')
                                                Sedang diverifikasi oleh admin.
                                            @elseif ($order->payment_status === 'paid')
                                                Pembayaran berhasil dikonfirmasi. Pesanan sedang diproses.
                                            @elseif ($order->payment_status === 'rejected')
                                                Bukti transfer ditolak. Silakan upload ulang.
                                            @endif
                                        </div>

                                        @if ($order->payment_proof)
                                            <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank"
                                                style="display:inline-flex; align-items:center; gap:6px; margin-top:10px; background:rgba(16,185,129,0.15); color:#6ee7b7; border:1px solid rgba(16,185,129,0.3); border-radius:8px; padding:6px 14px; font-size:12px; text-decoration:none; transition:all 0.2s;"
                                                onmouseover="this.style.background='rgba(16,185,129,0.25)'"
                                                onmouseout="this.style.background='rgba(16,185,129,0.15)'">
                                                <i class="fa-solid fa-image"></i>
                                                Lihat Bukti Transfer
                                            </a>
                                        @endif
                                    </div>

                                    <div>
                                        @switch($order->payment_status)
                                            @case('pending')
                                                <span
                                                    style="display:inline-flex; align-items:center; gap:5px; background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:600; white-space:nowrap;">
                                                    <i class="fa-solid fa-clock"></i> Menunggu
                                                </span>
                                            @break

                                            @case('paid')
                                                <span
                                                    style="display:inline-flex; align-items:center; gap:5px; background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:600; white-space:nowrap;">
                                                    <i class="fa-solid fa-check"></i> Berhasil
                                                </span>
                                            @break

                                            @case('rejected')
                                                <span
                                                    style="display:inline-flex; align-items:center; gap:5px; background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:600; white-space:nowrap;">
                                                    <i class="fa-solid fa-xmark"></i> Ditolak
                                                </span>
                                            @break
                                        @endswitch
                                    </div>

                                </div>

                            </div>
                        @endif

                    </div>
                @endif

            </div>


            {{-- ITEMS --}}
            <div class="glass-card">
                <h6 class="section-title">
                    <i class="fa-solid fa-box"></i>
                    Item Pesanan
                </h6>

                @forelse ($order->items as $item)
                    @php
                        $product = $item->variant?->product;
                        $imgPath = $product?->mainImage?->path
                            ? asset('storage/' . $product->mainImage->path)
                            : asset('images/no-image.png');
                    @endphp

                    <div class="item-card">

                        {{-- GAMBAR --}}
                        <div class="item-img me-3" style="width:70px; height:70px; flex-shrink:0;">
                            <img src="{{ $imgPath }}" alt="{{ $item->product_name }}"
                                class="w-100 h-100 rounded-3 object-fit-cover"
                                onerror="this.src='{{ asset('images/no-image.png') }}'; this.onerror=null;">
                        </div>

                        <div class="item-left flex-grow-1">

                            {{-- UMKM & Branch --}}
                            <div class="d-flex align-items-center gap-2 mb-1">
                                @if ($product?->umkm)
                                    <span class="badge px-2 py-1"
                                        style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-size: 0.65rem; border: 1px solid rgba(99,102,241,0.4);">
                                        <i class="fa-solid fa-store me-1"></i>{{ $product->umkm->name }}
                                    </span>
                                @endif

                                @if ($product?->umkm && $item->variant?->branch)
                                    <span style="color: #475569;">|</span>
                                @endif

                                @if ($item->variant?->branch)
                                    <span class="badge px-2 py-1"
                                        style="background: rgba(16,185,129,0.15); color: #6ee7b7; font-size: 0.65rem; border: 1px solid rgba(16,185,129,0.3);">
                                        <i class="fa-solid fa-location-dot me-1"></i>{{ $item->variant->branch->name }}
                                    </span>
                                @endif
                            </div>

                            {{-- NAMA PRODUK --}}
                            <div class="item-name fw-semibold text-white mb-1">
                                {{ $item->product_name }}
                            </div>

                            {{-- VARIANT ATTRIBUTES --}}
                            @if ($item->variant_attributes)
                                <div class="item-variant text-secondary small mb-1">
                                    {{ collect($item->variant_attributes)->map(fn($v, $k) => ucfirst($k) . ': ' . $v)->implode(' • ') }}
                                </div>
                            @endif

                            {{-- SKU --}}
                            @if ($item->variant_sku)
                                <div class="small mb-2" style="color: #64748b;">
                                    SKU: {{ $item->variant_sku }}
                                </div>
                            @endif

                            {{-- HARGA × QTY --}}
                            <div class="item-meta">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                                × {{ $item->quantity }}
                            </div>

                        </div>

                        {{-- SUBTOTAL --}}
                        <div class="item-right fw-bold text-white text-nowrap ms-3">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>

                    </div>
                @empty
                    <div class="empty-state">
                        Tidak ada item
                    </div>
                @endforelse

                <hr>

                <div class="total-card">
                    <div class="total-label">Total Pembayaran</div>
                    <div class="total-price">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* ================= BRANCH CARD ================= */
        .branch-card {
            background: rgba(99, 102, 241, .08);
            border: 1px solid rgba(99, 102, 241, .2);
            padding: 18px;
            border-radius: 16px;
            margin-top: 15px;
        }

        .branch-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #a5b4fc;
            margin-bottom: 8px;
        }

        .branch-name {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }

        .branch-address {
            font-size: 13px;
            color: #9ca3af;
            margin-bottom: 12px;
        }

        .btn-map {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            transition: .25s ease;
        }

        .btn-map:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, .4);
            color: #fff;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-title {
            color: #fff;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .order-date {
            font-size: 13px;
            color: #9ca3af;
        }

        .order-meta {
            font-size: 13px;
            color: #9ca3af;
        }

        .order-meta strong {
            color: #fff;
        }

        .rekening-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .rekening-item {
            background: rgba(255, 255, 255, .05);
            padding: 12px;
            border-radius: 12px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, .05);
        }

        .item-info {
            max-width: 70%;
        }

        .item-name {
            color: #fff;
            font-weight: 600;
        }

        .item-variant,
        .item-price {
            font-size: 13px;
            color: #9ca3af;
        }

        .item-subtotal {
            font-weight: 600;
            color: #fff;
        }

        @media(max-width:768px) {
            .item-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .item-info {
                max-width: 100%;
            }
        }

        /* Glass Card */
        .glass-card {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 20px;
            padding: 25px;
            backdrop-filter: blur(10px);
            transition: .3s;
        }

        .glass-card:hover {
            border-color: rgba(255, 255, 255, .15);
            box-shadow: 0 15px 40px rgba(0, 0, 0, .4);
        }

        /* Badge */
        .order-badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending {
            background: rgba(251, 191, 36, .2);
            color: #fbbf24;
        }

        .badge-process {
            background: rgba(59, 130, 246, .2);
            color: #3b82f6;
        }

        .badge-done {
            background: rgba(34, 197, 94, .2);
            color: #22c55e;
        }

        .badge-cancel {
            background: rgba(239, 68, 68, .2);
            color: #ef4444;
        }

        /* Section */
        .section-title {
            color: #fff;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .section-title i {
            margin-right: 8px;
        }

        /* Rekening */
        .rekening-box {
            background: rgba(255, 255, 255, .05);
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 15px;
        }

        .label {
            font-size: 12px;
            color: #9ca3af;
        }

        .value {
            color: #fff;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .btn-copy {
            background: rgba(99, 102, 241, .2);
            border: none;
            color: #6366f1;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            transition: .2s;
        }

        .btn-copy:hover {
            background: #6366f1;
            color: #fff;
        }

        /* Upload */
        .upload-box input {
            display: block;
            margin-bottom: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            padding: 10px;
            border-radius: 12px;
            color: #fff;
            width: 100%;
        }

        /* Item */
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, .05);
        }

        .item-name {
            color: #fff;
            font-weight: 600;
        }

        .item-variant,
        .item-price {
            font-size: 13px;
            color: #9ca3af;
        }

        .item-subtotal {
            color: #fff;
            font-weight: 600;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            color: #fff;
            font-size: 16px;
        }

        .payment-proof-img {
            max-width: 300px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .5);
        }

        /* Back Button */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, .08);
            padding: 8px 14px;
            border-radius: 10px;
            color: #fff;
            text-decoration: none;
            transition: .2s;
        }

        .btn-back:hover {
            background: #6366f1;
            color: #fff;
        }

        .warning-note {
            font-size: 13px;
            color: #fbbf24;
            margin-bottom: 10px;
        }

        @media(max-width:768px) {
            .item-row {
                flex-direction: column;
                gap: 5px;
            }
        }

        /* Hide default file input */
        /* HILANGKAN DEFAULT INPUT FILE */
        .file-input {
            position: absolute;
            left: -9999px;
            visibility: hidden;
        }

        /* STYLE LABEL CUSTOM */
        .file-label {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(99, 102, 241, .15);
            color: #6366f1;
            padding: 12px 16px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            transition: .2s ease;
        }

        .file-label:hover {
            background: #6366f1;
            color: #fff;
        }

        /* Upload button improve */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            padding: 12px;
            border-radius: 14px;
            color: #fff;
            font-weight: 600;
            transition: .3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, .4);
        }

        .order-info-box {
            margin-top: 15px;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
        }

        .info-process {
            background: rgba(59, 130, 246, .15);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, .3);
        }

        .info-done {
            background: rgba(34, 197, 94, .15);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, .3);
        }

        .order-status-description {
            margin-top: 6px;
            font-size: 13px;
            color: #9ca3af;
            max-width: 420px;
        }

        .order-badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .order-header-card {
            padding: 28px;
        }

        .order-id {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
        }

        .order-date {
            font-size: 13px;
            color: #94a3b8;
        }

        .order-description {
            margin-top: 8px;
            color: #94a3b8;
            font-size: 13px;
        }

        .badge-modern {

            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;

            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .branch-card {

            background: rgba(255, 255, 255, .05);
            padding: 18px;
            border-radius: 14px;
            min-width: 240px;
        }

        .branch-label {

            font-size: 12px;
            color: #94a3b8;
        }

        .branch-name {

            font-weight: 700;
            color: #fff;
        }

        .branch-address {

            font-size: 13px;
            color: #94a3b8;
        }

        .btn-map-modern {

            margin-top: 10px;
            display: inline-flex;
            gap: 6px;

            background: #6366f1;
            color: white;

            padding: 6px 12px;
            border-radius: 8px;

            font-size: 12px;
        }

        .item-card {

            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 18px;

            border-radius: 14px;

            background: rgba(255, 255, 255, .03);

            margin-bottom: 10px;
        }

        .item-name {

            font-weight: 600;
            color: #fff;
        }

        .item-variant {

            font-size: 12px;
            color: #94a3b8;
        }

        .item-meta {

            font-size: 13px;
            color: #94a3b8;
        }

        .item-right {

            font-weight: 700;
            font-size: 16px;
            color: #fff;
        }

        .total-card {

            margin-top: 20px;

            padding: 20px;

            border-radius: 14px;

            background: rgba(99, 102, 241, .15);

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-price {

            font-size: 20px;
            font-weight: 700;
        }

        /* logika jika upload bukti terisi  */
        .upload-success-box {

            display: flex;
            align-items: center;
            gap: 16px;

            padding: 18px;

            border-radius: 14px;

            background: rgba(34, 197, 94, .08);
            border: 1px solid rgba(34, 197, 94, .25);
        }

        .upload-success-icon {

            font-size: 28px;
            color: #22c55e;
        }

        .upload-success-title {

            font-weight: 600;
            color: #fff;
        }

        .upload-success-desc {

            font-size: 13px;
            color: #94a3b8;
        }

        .upload-success-badge {

            margin-left: auto;
        }

        .badge-warning {

            background: rgba(251, 191, 36, .15);
            color: #fbbf24;
            padding: 4px 10px;
            border-radius: 50px;
        }

        .badge-success {

            background: rgba(34, 197, 94, .15);
            color: #22c55e;
            padding: 4px 10px;
            border-radius: 50px;
        }

        .badge-info {

            background: rgba(59, 130, 246, .15);
            color: #60a5fa;
            padding: 4px 10px;
            border-radius: 50px;
        }

        .upload-success-box {

            display: flex;
            gap: 16px;
            align-items: center;

            padding: 20px;

            border-radius: 16px;

            background: rgba(34, 197, 94, .06);
            border: 1px solid rgba(34, 197, 94, .2);

        }

        .upload-success-icon {

            font-size: 32px;
            color: #22c55e;

        }

        .upload-success-title {

            font-weight: 600;
            color: #fff;
        }

        .upload-success-desc {

            font-size: 13px;
            color: #94a3b8;
        }

        .upload-success-badge {

            margin-left: auto;
        }

        .btn-preview-proof {

            display: inline-flex;
            align-items: center;
            gap: 6px;

            font-size: 13px;

            color: #6366f1;

            text-decoration: none;
        }

        .btn-preview-proof:hover {

            color: #8b5cf6;
        }

        .badge {

            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
        }

        .badge-warning {
            background: rgba(251, 191, 36, .15);
            color: #fbbf24;
        }

        .badge-success {
            background: rgba(34, 197, 94, .15);
            color: #22c55e;
        }

        .badge-danger {
            background: rgba(239, 68, 68, .15);
            color: #ef4444;
        }

        .badge-info {
            background: rgba(59, 130, 246, .15);
            color: #60a5fa;
        }

        .total-card {

            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 20px 24px;
            margin-top: 24px;

            border-radius: 18px;

            background: linear-gradient(135deg,
                    rgba(99, 102, 241, .15),
                    rgba(139, 92, 246, .10));

            border: 1px solid rgba(99, 102, 241, .25);

            backdrop-filter: blur(10px);

            transition: all .3s ease;

        }

        /* Hover effect */
        .total-card:hover {

            transform: translateY(-3px);

            border-color: rgba(139, 92, 246, .45);

            box-shadow:
                0 10px 30px rgba(99, 102, 241, .25),
                inset 0 0 0 1px rgba(255, 255, 255, .05);

        }


        /* Label */
        .total-label {

            font-size: 14px;
            color: #94a3b8;

            letter-spacing: .3px;

        }


        /* Price */
        .total-price {

            font-size: 24px;
            font-weight: 700;

            background: linear-gradient(135deg,
                    #6366f1,
                    #8b5cf6);

            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;

            letter-spacing: .5px;

        }


        /* Optional subtle glow */
        .total-price::after {

            content: "";
            display: block;

            height: 2px;
            width: 100%;

            margin-top: 4px;

            background: linear-gradient(90deg,
                    transparent,
                    rgba(139, 92, 246, .6),
                    transparent);

            opacity: .6;

        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =========================
            // COPY REKENING
            // =========================
            window.copyRekening = function(rekening, btn) {
                navigator.clipboard.writeText(rekening).then(() => {

                    const original = btn.innerHTML;

                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Berhasil Disalin';
                    btn.style.background = '#22c55e';
                    btn.style.color = '#fff';

                    setTimeout(() => {
                        btn.innerHTML = original;
                        btn.style.background = 'rgba(99,102,241,.2)';
                        btn.style.color = '#6366f1';
                    }, 2000);
                });
            };

            // =========================
            // FILE INPUT LABEL
            // =========================
            document.querySelectorAll('.file-input').forEach(input => {
                input.addEventListener('change', function() {
                    const label = this.nextElementSibling?.querySelector('.file-name-text');
                    if (this.files.length > 0 && label) {
                        label.textContent = this.files[0].name;
                    }
                });
            });

            // =========================
            // AMBIL ORDER ID
            // =========================
            const orderContainer = document.getElementById("order-container");
            if (!orderContainer) return;

            const orderId = orderContainer.dataset.orderId;
            if (!orderId) return;

            // =========================
            // STATUS HELPER
            // =========================
            function getOrderStatusClass(status) {
                switch (status) {
                    case "pending":
                        return "badge-pending";
                    case "process":
                        return "badge-process";
                    case "done":
                        return "badge-done";
                    case "cancel":
                        return "badge-cancel";
                    default:
                        return "badge-default";
                }
            }

            function getOrderStatusText(status) {
                switch (status) {
                    case "pending":
                        return "Pesanan Menunggu Konfirmasi";
                    case "process":
                        return "Pesanan Sedang Diproses";
                    case "done":
                        return "Pesanan Selesai";
                    case "cancel":
                        return "Pesanan Dibatalkan";
                    default:
                        return "Pesanan Menunggu Konfirmasi";
                }
            }

            function getPaymentStatusClass(status) {
                switch (status) {
                    case "pending":
                        return "badge-pending";
                    case "paid":
                        return "badge-done";
                    case "rejected":
                        return "badge-cancel";
                    default:
                        return "badge-default";
                }
            }

            function getPaymentStatusText(status) {
                switch (status) {
                    case "pending":
                        return "Pembayaran Menunggu Verifikasi";
                    case "paid":
                        return "Pembayaran Berhasil";
                    case "rejected":
                        return "Pembayaran Ditolak";
                    default:
                        return "Pembayaran Menunggu Verifikasi";
                }
            }

            // =========================
            // UPDATE BADGE
            // =========================
            function updateBadge(badgeId, textId, status, type = 'order') {
                const badge = document.getElementById(badgeId);
                const text = document.getElementById(textId);

                if (!badge || !text) return;

                badge.classList.remove(
                    "badge-pending",
                    "badge-process",
                    "badge-done",
                    "badge-cancel",
                    "badge-default"
                );

                if (type === 'order') {
                    badge.classList.add(getOrderStatusClass(status));
                    text.innerText = getOrderStatusText(status);
                } else {
                    badge.classList.add(getPaymentStatusClass(status));
                    text.innerText = getPaymentStatusText(status);
                }
            }

            // =========================
            // 🔥 UPDATE UI TAMBAHAN (REKENING & UPLOAD)
            // =========================
            function updatePaymentUI(paymentStatus) {

                const paymentSection = document.getElementById('payment-section');
                const uploadForm = document.getElementById('upload-form');
                const uploadSuccess = document.getElementById('upload-success');
                const uploadDesc = document.getElementById('upload-desc');

                if (!paymentSection) return;

                // ❌ sembunyikan rekening kalau sudah bukan pending
                if (paymentStatus !== 'pending') {
                    paymentSection.style.display = 'none';
                } else {
                    paymentSection.style.display = 'block';
                }

                // 🔥 kontrol upload / success
                if (paymentStatus === 'pending') {
                    if (uploadForm) uploadForm.style.display = 'block';
                    if (uploadSuccess) uploadSuccess.style.display = 'none';
                } else {
                    if (uploadForm) uploadForm.style.display = 'none';
                    if (uploadSuccess) uploadSuccess.style.display = 'block';

                    if (uploadDesc) {
                        if (paymentStatus === 'paid') {
                            uploadDesc.innerText = 'Pembayaran berhasil dikonfirmasi. Pesanan sedang diproses.';
                        } else if (paymentStatus === 'rejected') {
                            uploadDesc.innerText = 'Bukti transfer ditolak. Silakan upload ulang.';
                        }
                    }
                }
            }

            // =========================
            // FULL UI UPDATE
            // =========================
            function updateOrderUI(e) {

                // badge
                updateBadge("order-status-badge", "order-status-text", e.status, 'order');
                updateBadge("payment-status-badge", "payment-status-text", e.payment_status, 'payment');

                // 🔥 tambahan UI
                updatePaymentUI(e.payment_status);

                // =========================
                // DESCRIPTION
                // =========================
                const desc = document.querySelector('.order-description');

                if (desc) {
                    switch (e.status) {
                        case "pending":
                            desc.innerText = "Pesanan kamu sudah dibuat dan sedang menunggu konfirmasi dari toko.";
                            break;
                        case "process":
                            desc.innerText = "Pesanan kamu sedang disiapkan oleh toko.";
                            break;
                        case "done":
                            desc.innerText = "Pesanan sudah selesai dan berhasil diterima.";
                            break;
                        case "cancel":
                            desc.innerText = "Pesanan dibatalkan oleh sistem atau toko.";
                            break;
                    }
                }

                // =========================
                // INFO BOX
                // =========================
                const oldBox = document.querySelector('.order-info-box');
                if (oldBox) oldBox.remove();

                const container = document.querySelector('.order-header-card');
                if (!container) return;

                if (e.status === "process") {
                    container.insertAdjacentHTML('beforeend', `
                <div class="order-info-box info-process">
                    Silahkan datangi toko terdekat, tunjukan bukti, dan ambil pesanan anda.
                </div>
            `);
                }

                if (e.status === "done") {
                    container.insertAdjacentHTML('beforeend', `
                <div class="order-info-box info-done">
                    Terimakasih sudah belanja ditoko kami 🎉
                </div>
            `);
                }
            }

            // =========================
            // PUSHER
            // =========================
            Pusher.logToConsole = true;

            const echo = new Echo({
                broadcaster: 'pusher',
                key: "{{ env('PUSHER_APP_KEY') }}",
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                forceTLS: true
            });

            // =========================
            // REALTIME LISTENER
            // =========================
            echo.channel('order.' + orderId)
                .listen('.OrderStatusUpdated', (e) => {

                    if (!e.status || !e.payment_status) return;

                    e.status = e.status.toLowerCase();
                    e.payment_status = e.payment_status.toLowerCase();

                    requestAnimationFrame(() => {
                        updateOrderUI(e);
                    });

                    Toast.fire({
                        icon: 'info',
                        title: 'Status pesanan diperbarui'
                    });
                });

        });
    </script>
@endpush
