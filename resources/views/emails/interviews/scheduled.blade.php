<x-mail::message>
# Interview Scheduled

Dear {{ $user->name }},

An interview has been scheduled for the position of **{{ $job->title }}** at **{{ $company->name }}**.

**Date & Time:** {{ $interview->scheduled_at->format('F j, Y \a\t g:i A') }}  
**Type:** {{ ucfirst($interview->type) }}  
@if($interview->location)
**Location:** {{ $interview->location }}
@endif
@if($interview->meeting_url)
**Meeting URL:** [Join Meeting]({{ $interview->meeting_url }})
@endif

@if($interview->notes)
**Additional Notes:**  
{{ $interview->notes }}
@endif

<x-mail::button :url="config('app.url') . '/interviews/' . $interview->id">
View Interview Details
</x-mail::button>

Please confirm your attendance by clicking the button above and selecting "Confirm Attendance".

Thank you for using our platform.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
