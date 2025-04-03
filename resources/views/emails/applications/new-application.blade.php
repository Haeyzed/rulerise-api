<x-mail::message>
# New Job Application Received

Dear {{ $user->name }},

A new application has been received for the position of **{{ $job->title }}**.

**Applicant:** {{ $candidate->user->name }}  
**Applied On:** {{ $jobApplication->created_at->format('F j, Y') }}

@if($candidate->headline)
**Headline:** {{ $candidate->headline }}
@endif

@if($candidate->summary)
**Summary:**  
{{ Str::limit($candidate->summary, 200) }}
@endif

<x-mail::button :url="config('app.url') . '/applications/' . $jobApplication->id">
View Application
</x-mail::button>

Thank you for using our platform.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>