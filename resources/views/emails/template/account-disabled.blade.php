@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img src="{{ asset('assets/images/logo/Logo-Email.png') }}" alt="{{ config('app.name') }}">
@endcomponent
@endslot

# Hallo, {{ $user->name }}

Untuk sementara waktu, akun anda kami nonaktifkan dikarenakan
suatu hal semisal melanggar kebijakan privasi atau term of service
dari kami.

Anda dapat bertanya secara detail kepada kami kenapa akun ini
dinonaktifkan. Segera hubungi kami dinomor 62 {{ $setting->phone_number }} 🙏🙏

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