<h2>Terima kasih telah berbelanja 🙌</h2>

<p>Halo, pesanan kamu berhasil dibuat.</p>

<p><b>Status:</b> {{ $order->status }}</p>
<p><b>Pembayaran:</b> {{ $order->payment_status }}</p>

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

<p>Silakan lakukan pembayaran sesuai instruksi.</p>

<hr>

<p style="font-size:12px; color:gray;">
    Email ini dikirim secara otomatis oleh sistem.<br>
    Mohon untuk tidak membalas pesan ini.
</p>
