<x-mail::message>
# New Jobs Matching Your Alert

Dear {{ $user->name }},

We found {{ $matchingJobs->count() }} new jobs matching your "**{{ $jobAlert->name }}**" alert.

<x-mail::table>
| Job Title | Company | Location | Posted |
| --------- | ------- | -------- | ------ |
@foreach($matchingJobs as $job)
| {{ $job->title }} | {{ $job->company->name }} | {{ $job->location }} | {{ $job->created_at->diffForHumans() }} |
@endforeach
</x-mail::table>

<x-mail::button :url="config('app.url') . '/jobs?alert=' . $jobAlert->id">
View All Matching Jobs
</x-mail::button>

You are receiving this email because you set up a job alert on our platform. You can manage your job alerts in your account settings.

Thank you for using our platform.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>