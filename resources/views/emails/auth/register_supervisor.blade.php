@component('mail::message')
# Complete your Profile

Hi {{$user->name}},

{{$coordinator->name}} recently registered you as a supervisor on {{$app_name}}, the number 1 project management tool for final year projects.

You are required to complete your profile and change your password.

Your current password is

{{$password}}

Please, do not share your password with anyone. Ensure you change your password as soon as possible by [completing your profile]({{$profile_completion_url}}).

@component('mail::button', ['url' => $profile_completion_url])
Complete profile
@endcomponent

Thank you for being an awesome project supervisor. Your students look forward to working with you.

Onwards,<br>
{{ config('app.name') }}
@endcomponent
