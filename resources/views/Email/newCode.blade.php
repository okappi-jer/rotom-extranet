@component('mail::message')
    Beste {{ $name }},
    <br><br>
    Hierbij bezorgen wij u de nieuwe toegangscode van onze poort.
    <br>
    Gelieve vanaf heden deze code te gebruiken, de oude gaat niet meer werken.
    <br><br>
    <strong>Code: </strong>{{ $code }}<br>
    <strong>Locatie: </strong>{{ $location }}<br>
    <strong>Geldig tot: </strong>{{ $validtill }}
    <br><br>
    Met vriendelijke groeten,
    <br>
    Rotom Tomatoes
@endcomponent
