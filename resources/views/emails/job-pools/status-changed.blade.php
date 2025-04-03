<x-mail::message>
# Your Job Pool Status Has Changed

Dear {{ $user->name }},

Your status in the **{{ $jobPool->name }}** talent pool at **{{ $company->name }}** has been updated.

**Previous Status:** {{ $previousStatus }}  
**Current Status:** {{ $currentStatus }}

@if($currentStatus == 'active')
Congratulations! You are now an active member of this talent pool. You will be considered for relevant job opportunities at {{ $company->name }}.
@elseif($currentStatus == 'selected')
Congratulations! You have been selected from the talent pool for consideration for a specific role. You will be contacted shortly with more details.
@elseif($currentStatus == 'inactive')
Your status in this talent pool has been set to inactive. This could be temporary and you may be reactivated in the future.
@elseif($currentStatus == 'removed')
You have been removed from this talent pool. We appreciate your interest in {{ $company->name }} and encourage you to apply for specific positions that match your skills and experience.
@endif

<x-mail::button :url="config('app.url') . '/job-pools/' . $jobPool->id">
    View Job Pool
</x-mail::button>

Thank you for using our platform.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>