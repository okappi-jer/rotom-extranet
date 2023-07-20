@component('mail::message')
Beste,<br><br>We ontvingen een verzoek om uw wachtwoord opnieuw in te stellen.<br><br><strong>Let op:</strong> Heb je geen nieuw wachtwoord aangevraagd, dan mag je dit bericht negeren.<br><br>

@component('mail::button', ['url' => env('MIX_BASE_URL') . '/#/nieuw-wachtwoord?token=' . $token])
    Wijzig wachtwoord
@endcomponent

Met vriendelijke groeten,<br>
Packo-Reesink
@endcomponent
