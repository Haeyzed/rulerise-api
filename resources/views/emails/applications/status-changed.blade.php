<x-mail::message>
# Your Job Application Status Has Changed

Dear {{ $user->name }},

Your application for the position of **{{ $job->title }}** at **{{ $company->name }}** has been updated.

**Previous Status:** {{ $previousStatus }}  
**Current Status:** {{ $currentStatus }}

@switch($currentStatus)
@case('shortlisted')
Congratulations! Your application has been shortlisted. The hiring team was impressed with your qualifications and experience.
@break

@case('interview')
Congratulations! You have been selected for an interview. You will receive further details about the interview schedule soon.
@break

@case('offered')
Congratulations! We are pleased to inform you that you have been offered the position. Please check your account for more details about the offer.
@break

@case('hired')
Congratulations! Your hiring process is complete. Welcome to {{ $company->name }}! Please check your account for onboarding details.
@break

@case('rejected')
We regret to inform you that your application was not selected for further consideration at this time. We appreciate your interest in {{ $company->name }} and encourage you to apply for future opportunities that match your skills and experience.
@break
@endswitch

<x-mail::button :url="config('app.url') . '/applications/' . $jobApplication->id">
View Application
</x-mail::button>

Thank you for using our platform.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
