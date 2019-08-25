<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  </head>

  <body>
    <div id="app">
      @include('web.partials.header')
      @yield('content')
    </div>

    <script>
      var rainbowEmail = "{{ isset(auth()->user()->rainbowAccount->email) ? auth()->user()->rainbowAccount->email : "" }}";
      var rainbowPassword = "{{ isset(auth()->user()->rainbowAccount->password) ? auth()->user()->rainbowAccount->password : "" }}";
      var rainbowContact = "{{ isset(auth()->user()->rainbowAccount->contact_id) ? auth()->user()->rainbowAccount->contact_id : "" }}";
      var rainbowAppId = "{{ config('rainbow.application_id') }}";
      var rainbowAppSecret = "{{ config('rainbow.application_secret') }}"
    </script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/es5-shim/4.5.9/es5-shim.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/es6-promise/4.0.5/es6-promise.min.js"></script>
    {{--<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>--}}
    <script src="//cdn.jsdelivr.net/momentjs/2.15.1/moment-with-locales.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.5/angular.min.js"></script>
    <script src="{{ asset('js/rainbow/vendors-sdk.min.js') }}"></script>
    <script src="{{ asset('js/rainbow/rainbow-sdk.min.js') }}"></script>
    <script src="{{ asset('js/rainbow/rainbow.js') }}"></script>
  </body>
</html>
