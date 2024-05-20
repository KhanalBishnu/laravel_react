@component('mail::message')
# Welcome to Our Platform, {{ $user->name }}

Dear {{ $user->name }},<br>

We're excited to have you on board Here are your login credentials:<br>

Email: {{ $user->email }}<br>
Password: {{ $password }}<br>


@component('mail::button', ['url' => 'http://localhost:5173/login'])
Login
@endcomponent
Please log in to start exploring our platform. If you have any questions or need assistance, don't hesitate to reach out.<br><br>

Warm regards,<br>
<b>{{ config('app.name') }}</b>
@endcomponent