@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img src="{{ asset('assets/images/logo/Logo-Email.png') }}" alt="{{ config('app.name') }}">
@endcomponent
@endslot

# Hallo, {{ $pam->name }}

@if ($pam->is_prepaid)
Terima kasih sudah bergabung dengan kami.
@else
Terima kasih sudah memperpanjang masa berlangganan Anda.
@endif

<div class="tbr_email--summary-wrap">
<div class="tbr_summary--block">
    <div class="tbr_text--dark tbr_weight--bold">Informasi Berlangganan</div>
    <div>
        <span class="tbr_text--warning">Invoice ke {{ $pam->transactions->count() }} ({{ $lastTransaction->transaction_id }})</span>&nbsp;
        <span class="tbr_weight--bold tbr_text--success">Lunas</span>
    </div>
    <div>Untuk tanggal {{ formatDate($pam->date_start, 'd F Y') }} - {{ formatDate($pam->date_end, 'd F Y') }} (30 Hari)</div>
</div>

<div class="tbr_summary--block">
    <div class="tbr_text--dark tbr_weight--bold">Rincian Biaya</div>
    <table class="tbr_table--simple">
        <tr>
            @if ($pam->is_prepaid)
                <td>Harga</td>
                <td> : </td>
                <td> {{ $setting->rupiah_trial_price }} </td>
            @else
                <td>Harga per pelanggan</td>
                <td> : </td>
                <td>{{ rupiah($activeCustomerPrice) }}</td>
            @endif
        </tr>
        <tr>
            @if ($pam->is_prepaid)
                <td>Jumlah pelanggan</td>
                <td> : </td>
                <td> - </td>
            @else
                <td>Jumlah pelanggan</td>
                <td> : </td>
                <td>{{ $lastTransaction->total_amount / $activeCustomerPrice }} pelanggan</td>
            @endif
        </tr>
        <tr>
            <td>Total Biaya</td>
            <td> : </td>
            <td>{{ $lastTransaction->rupiah_total_amount }}</td>
        </tr>
    </table>
</div>
</div>

Semoga dengan aplikasi ini dapat mengatasi masalah ketersediaan
air bersih di desa Anda. Kami ucapkan sekali lagi terima kasih.

Bila ada pertanyaan terkait dengan hal ini, langsung hubungi kami
(62 {{ $setting->phone_number }}) 🤗😊

Salam Kami,<br>
<span class="tbr_signature">{{ config('app.name') }}</span>

@slot('footer')
@component('mail::footer')
&copy; Copyright {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.
<br>
{{ env('APP_VERSION') }} by TebarDigital.
@endcomponent
@endslot

@endcomponent