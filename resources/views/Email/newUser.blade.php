@component('mail::message')
Beste {{ $user->firstname }},
<br><br>
Wij hebben u toegang gegeven tot het extranet van {{ $user->delivers_to }}. U kan inloggen met volgende gegevens
<br><br>
<strong>Login: </strong>{{ $user->email }}<br>
<strong>Wachtwoord: </strong>{{ $user->password_plain }}
<br><br>

@component('mail::button', ['url' => 'https://extranet.rotom-tomatoes.com/?delivers_at' . $user->delivers_to])
    Ga naar het extranet
@endcomponent

Met vriendelijke groeten,
<br>
{{ $user->delivers_to }}

@endcomponent
