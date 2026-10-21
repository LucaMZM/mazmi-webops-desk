<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Mazmi WebOps Desk: panel para gestionar mantenimiento web, clientes, tickets, tareas y reportes.">
        <meta name="theme-color" content="#f4f6fa">

        <title inertia>{{ config('app.name', 'Mazmi WebOps Desk') }}</title>
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">

        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
