<x-mail::message>
# Reset Password

Hello {{ $user->first_name }},

You are receiving this email because we received a password reset request for your account.

<x-mail::button :url="$url">
Reset Password
</x-mail::button>

This password reset link will expire in {{ $count }} minutes.

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}

<x-mail::subcopy>
If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
</x-mail::subcopy>
</x-mail::message>
