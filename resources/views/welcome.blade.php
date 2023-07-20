<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>{{ config('app.name') }}</title>
    </head>
    <body class="antialiased">
        <div id="app">
            <header-component v-if="$store.state.authStore.user"></header-component>

            <div class="content-wrapper" :class="{'nopadding': !$store.state.authStore.user}">
                <Sidebar v-if="$store.state.authStore.user"></Sidebar>

                <div class="container app-container">
                    <router-view></router-view>
                </div>
            </div>
        </div>

        <script>
            var Laravel = {
                'csrfToken' : '{{csrf_token()}}'
            };
        </script>

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
