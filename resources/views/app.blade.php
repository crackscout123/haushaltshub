<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'HaushaltsHub') }}</title>
        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="h-full bg-gray-50 dark:bg-gray-950 font-sans antialiased">
        @inertia
    </body>
</html>
