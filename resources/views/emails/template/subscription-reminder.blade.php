@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img src="{{ asset('assets/images/logo/Logo-Email.png') }}" alt="{{ config('app.name') }}">
@endcomponent
@endslot

# Hallo, {{ $pam->name }}

@if ($pam->is_active)
Masa aktif anda akan segera berakhir dalam {{ config('company.notification.reminder') }} hari lagi.
@endif
@if ($pam->is_billed)
{{ config('company.notification.reminder') }} hari sebelum Anda kami blokir. Segera lakukan pembayaran ya.
@endif

<div class="tbr_email--summary-wrap">
<div class="tbr_summary--block">
    <div class="tbr_text--dark tbr_weight--bold">Informasi Berlangganan</div>
    <div class="tbr_text--warning">Bulan ke {{ $pam->validTransactions->count() }}</div>
    <div>{{ formatDate($pam->date_start, 'd F Y') }} - {{ formatDate($pam->date_end, 'd F Y') }} (30 Hari)</div>
</div>
</div>

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