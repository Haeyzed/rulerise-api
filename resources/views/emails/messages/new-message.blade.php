<x-mail::message>
# New Message Received

Dear {{ $user->name }},

You have received a new message from **{{ $sender->name }}**.

**Message:**  
{{ Str::limit($message->content, 300) }}

<x-mail::button :url="config('app.url') . '/messages/' . $message->conversation_id">
View Conversation
</x-mail::button>

Thank you for using our platform.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>