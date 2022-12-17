@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => env('FRONT_END_URL')])
<img src="{{ asset('assets/images/logo/Logo-Email.png') }}" alt="{{ config('app.name') }}">
@endcomponent
@endslot

# Hallo, {{ $user->name }}

Kode two factor authentication anda adalah :

<div class="tbr_security--code">{{ $code }}</div>

@foreach ($introLines as $line)
{{ $line }}
@endforeach

Copy paste ke dalam form yang diminta oleh sistem. Bila ini dirasa cukup
mengganggu, anda dapat menonaktifkan fitur ini melalui menu pengaturan
di aplikasi.

Jika anda tidak berkeinginan atau membatalkan untuk masuk ke aplikasi,
tidak ada tindakan lebih lanjut yang diperlukan.

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