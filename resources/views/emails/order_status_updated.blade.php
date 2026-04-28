<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Pesanan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: linear-gradient(135deg, #0ea5e9, #6366f1);
            padding: 40px 32px;
            text-align: center;
        }

        .header h1 {
            color: #fff;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .header p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
        }

        .badge-code {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 12px;
            margin-top: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .body {
            padding: 32px;
        }

        .greeting {
            font-size: 16px;
            color: #475569;
            margin-bottom: 24px;
        }

        .status-update-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .status-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .status-row:last-child {
            border-bottom: none;
        }

        .status-row .label {
            font-size: 13px;
            color: #64748b;
            min-width: 130px;
        }

        .status-arrow {
            color: #94a3b8;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-process {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-done {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancel {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        .item-card {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .item-card:last-child {
            border-bottom: none;
        }

        .item-name {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .item-meta {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .item-badge {
            display: inline-block;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 999px;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        .badge-umkm {
            background: #ede9fe;
            color: #6d28d9;
        }

        .badge-branch {
            background: #d1fae5;
            color: #065f46;
        }

        .item-subtotal {
            font-size: 14px;
            font-weight: 700;
            color: #4f46e5;
            white-space: nowrap;
            margin-left: 16px;
        }

        .total-box {
            background: linear-gradient(135deg, #0ea5e9, #6366f1);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
        }

        .total-box .label {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
        }

        .total-box .amount {
            color: #fff;
            font-size: 22px;
            font-weight: 800;
        }

        .footer {
            background: #f8fafc;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer p {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }

        .footer .brand {
            font-size: 18px;
            font-weight: 800;
            color: #4f46e5;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        {{-- HEADER --}}
        <div class="header">
            <h1>🔔 Update Status Pesanan</h1>
            <p>Ada perubahan pada pesanan kamu</p>
            <span class="badge-code">{{ $order->order_code }}</span>
        </div>

        {{-- BODY --}}
        <div class="body">

            <p class="greeting">
                Halo <strong>{{ $order->user->name ?? 'Pelanggan' }}</strong>, terdapat perubahan status pada pesananmu.
                Berikut detailnya:
            </p>

            {{-- STATUS UPDATE --}}
            <div class="status-update-box">
                <div class="status-row">
                    <span class="label">Status Pesanan</span>
                    <span class="status-badge status-{{ $oldStatus }}">{{ ucfirst($oldStatus) }}</span>
                    <span class="status-arrow">→</span>
                    <span class="status-badge status-{{ $newStatus }}">{{ ucfirst($newStatus) }}</span>
                </div>
                <div class="status-row">
                    <span class="label">Status Pembayaran</span>
                    <span class="status-badge status-{{ $oldPayment }}">{{ ucfirst($oldPayment) }}</span>
                    <span class="status-arrow">→</span>
                    <span class="status-badge status-{{ $newPayment }}">{{ ucfirst($newPayment) }}</span>
                </div>
                <div class="status-row">
                    <span class="label">Cabang</span>
                    <span style="font-size:13px; font-weight:600;">{{ $order->branch->name ?? '-' }}</span>
                </div>
                <div class="status-row">
                    <span class="label">Diperbarui</span>
                    <span style="font-size:13px;">{{ $order->updated_at->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="section-title">🛍️ Detail Item Pesanan</div>

            @foreach ($order->items as $item)
                @php
                    $umkm = $item->variant?->product?->umkm;
                    $branch = $item->variant?->branch;
                @endphp
                <div class="item-card">
                    <div>
                        <div class="item-name">{{ $item->product_name }}</div>

                        <div style="margin-bottom: 6px;">
                            @if ($umkm)
                                <span class="item-badge badge-umkm">🏪 {{ $umkm->name }}</span>
                            @endif
                            @if ($branch)
                                <span class="item-badge badge-branch">📍 {{ $branch->name }}</span>
                            @endif
                        </div>

                        @if ($item->variant_attributes)
                            <div class="item-meta">
                                {{ collect($item->variant_attributes)->map(fn($v, $k) => ucfirst($k) . ': ' . $v)->implode(' • ') }}
                            </div>
                        @endif

                        @if ($item->variant_sku)
                            <div class="item-meta">SKU: {{ $item->variant_sku }}</div>
                        @endif

                        <div class="item-meta" style="margin-top: 4px;">
                            Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}
                        </div>
                    </div>
                    <div class="item-subtotal">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach

            {{-- TOTAL --}}
            <div class="total-box">
                <span class="label">Total Pembayaran</span>
                <span class="amount">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="footer">
            <div class="brand">Trendora</div>
            <p>
                Email ini dikirim secara otomatis oleh sistem.<br>
                Mohon untuk tidak membalas pesan ini.<br>
                © {{ date('Y') }} Trendora. All rights reserved.
            </p>
        </div>

    </div>
</body>

</html>
