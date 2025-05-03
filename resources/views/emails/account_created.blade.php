@component('mail::message')
# Welcome to {{ config('app.name') }}

Hello {{ $user->name }},

An account has been created for you in **{{ config('app.name') }}**.

Here are your login credentials:

- **Email:** {{ $user->email }}
- **Password:** {{ $password }}

@component('mail::button', ['url' => route('login')])
    Login to {{ config('app.name') }}
@endcomponent

> For security reasons, please **log in and change your password immediately**.

If you have any questions, feel free to contact your administrator.

Thanks,

{{ config('app.name') }}
@endcomponent
