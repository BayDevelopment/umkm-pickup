<h2>Update Pesanan Anda 🔔</h2>

<p>Halo, ada perubahan pada pesanan Anda.</p>

<p>
    <b>Status Order:</b><br>
    {{ $oldStatus }} → <b>{{ $newStatus }}</b>
</p>

<p>
    <b>Status Pembayaran:</b><br>
    {{ $oldPayment }} → <b>{{ $newPayment }}</b>
</p>

<h3>Detail Pesanan:</h3>
<ul>
    @foreach ($order->items as $item)
        <li>
            {{ $item->product_name }}
            ({{ $item->quantity }} x Rp{{ number_format($item->price) }})
        </li>
    @endforeach
</ul>

<p><b>Total:</b> Rp{{ number_format($order->total_price) }}</p>

<hr>

<p style="font-size:12px; color:gray;">
    Email ini dikirim secara otomatis oleh sistem.<br>
    Mohon untuk tidak membalas email ini.
</p>
