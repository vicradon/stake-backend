@component('mail::message')
# A new project has been proposed

Dear supervisor {{$supervisor->name}},

{{$student->name}}, recently proposed a project on the topic, **{{ $proposal->name }}**.

Review this proposal [here]({{$url}}) or click the button below.

@component('mail::button', ['url' => $url])
Review proposal
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
