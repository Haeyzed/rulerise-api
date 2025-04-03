<x-mail::message>
# Account Deactivated

Hello {{ $user->first_name }},

Your account on {{ config('app.name') }} has been deactivated as per your request.

If you wish to reactivate your account, please log in to the system and follow the reactivation process.

If you did not request this action, please contact our support team immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
