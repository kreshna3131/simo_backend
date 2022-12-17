@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img src="{{ asset('assets/images/logo/Logo-Email.png') }}" alt="{{ config('app.name') }}">
@endcomponent
@endslot

# Hallo, {{ $user->name }}

Akun anda berhasil kami aktifkan kembali.

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