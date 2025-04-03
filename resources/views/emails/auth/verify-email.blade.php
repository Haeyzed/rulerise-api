<x-mail::message>
# Verify Your Email Address

Hello {{ $user->first_name }},

Thank you for registering with {{ config('app.name') }}. Please click the button below to verify your email address.

<x-mail::button :url="$url">
Verify Email Address
</x-mail::button>

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}

<x-mail::subcopy>
If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
</x-mail::subcopy>
</x-mail::message>
