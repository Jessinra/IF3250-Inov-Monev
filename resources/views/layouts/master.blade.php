<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | Starter</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <script>window.laravel = { csrfToken: '{{ csrf_token}}'}</script> --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <!-- Header -->
    @include('layouts.header')

    <!-- Sidebar -->
    @yield('sidebar')

    <!-- Content -->
    @yield('content')

    <!-- Footer -->
    @include('layouts.footer')

    <!-- control -->
    @include('layouts.control')
    <!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>