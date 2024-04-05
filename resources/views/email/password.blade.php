<h1>Hello {{$user->first_name}}</h1>

<p>
    Plaese click the link to reset ypur password.

    <a href="{{env('APP_URL')}}/reset/{{$user->email}}/{{$code}}">
        click here
    </a> 
</p>