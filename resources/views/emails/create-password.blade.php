@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => env('FRONT_END_URL')])
<img src="{{ asset('assets/images/logo/Logo-Email.png') }}" alt="{{ config('app.name') }}">
@endcomponent
@endslot

# Hallo, {{ $user->name }}

Anda telah didaftarkan sebagai admin pada sistem ERM SIMO. Agar dapat 
login untuk pertama kalinya, silakan buat password terlebih dahulu
melalui tombol di bawah ini.

@component('mail::button', ['color' => 'success', 'url' => env('FRONT_END_URL').'/reset-password?token='.$token.'&email='.$user->email.'&create_password=true'])
Buat Password
@endcomponent

Apabila tidak menginginkan menjadi admin pada sistem kami, tidak
ada tindakan lebih lanjut yang diperlukan.

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