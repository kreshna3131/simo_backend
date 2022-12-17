@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => env('FRONT_END_URL')])
<img src="{{ asset('assets/images/logo/Logo-Email.png') }}" alt="{{ config('app.name') }}">
@endcomponent
@endslot

# Hallo, {{ $user->name }}

Anda menerima email ini karena kami menerima permintaan 
pengaturan ulang kata sandi untuk akun anda.

@component('mail::button', ['color' => 'success', 'url' => env('FRONT_END_URL').'/reset-password?token='.$token.'&email='.$user->email])
Reset Password
@endcomponent

Tautan pengaturan ulang kata sandi ini akan kedaluwarsa kurang dari
{{ config('auth.passwords.members.expire') }} menit. 

Jika Anda tidak meminta pengaturan ulang kata sandi, tidak ada 
tindakan lebih lanjut yang diperlukan.

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