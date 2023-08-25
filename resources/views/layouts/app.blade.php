<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script>
          window.CSRF =  <?php echo json_encode([
              'csrfToken' => csrf_token(),
          ]); ?>
    </script>
</head>
<body>
    @auth
    <div id="app">
      <div id="loader">
        <style>
          .lds-ripple {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
            }
            .lds-ripple div {
            position: absolute;
            border: 4px solid #fff;
            opacity: 1;
            border-radius: 50%;
            animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
            }
            .lds-ripple div:nth-child(2) {
            animation-delay: -0.5s;
            }
            @keyframes lds-ripple {
            0% {
              top: 36px;
              left: 36px;
              width: 0;
              height: 0;
              opacity: 1;
            }
            100% {
              top: 0px;
              left: 0px;
              width: 72px;
              height: 72px;
              opacity: 0;
            }
            }

        </style>
        <div>
          <div class="lds-ripple"><div></div><div></div></div>
        </div>
      </div>

      @yield('content')
      <div id="sideNavigation" :class="{side: dark}">
                <div class="items">
                   <router-link to="/" class="item" data-toggle="tooltip" title="Main">
                     <span class="icon" data-feather="home"></span>
                     <span class="ml-3">Pagrindinis</span>
                   </router-link>
                  <router-link to="/transport" class="item" data-toggle="tooltip" title="Main">
                     <span class="icon" data-feather="truck"></span>
                     <span class="ml-3">Transportas</span>
                   </router-link>
                   <router-link to="/drivers" class="item" data-toggle="tooltip" title="Main">
                     <span class="icon" data-feather="user"></span>
                     <span class="ml-3">Vairuotojai</span>
                   </router-link>
                </div>
                <div class="items">
                  {{-- <span class="text-muted ml-4 mb-4">Ataskaitos</span> --}}
                  <router-link to="/reports/transport/status" class="item" data-toggle="tooltip" title="Main">
                    <span class="icon" data-feather="book"></span>
                    <span class="ml-3">Transporto būklė</span>
                  </router-link>

                  <router-link to="/system/errors" class="item" data-toggle="tooltip" title="Main">
                    <span class="icon" data-feather="alert-triangle"></span>
                    <span class="ml-3">Sistemos klaidos</span>
                  </router-link>

                </div>
                <div class="items">
                  <router-link to="/settings/users" class="item" data-toggle="tooltip" title="Main">
                    <span class="icon" data-feather="settings"></span>
                    <span class="ml-3">Nustatymai</span>
                  </router-link>
                  <form action="{{route('logout')}}" method="POST" id="logout-form">
                  <a style="cursor:pointer;" onclick="document.getElementById('logout-form').submit()" class="item item-red">
                    <span class="icon" data-feather="log-out"></span>
                    <span class="ml-3">Atsijungti</span>
                    {{csrf_field()}}
                  </a>
                </form>
                </div>
      </div>

    <main class="content" :class="{change: dark }" @click="checkInternetConnectivity">
        <router-view v-if="connectionStatus"></router-view>
        <div class="justify-content-center">
          <div class="col-md-6">
            <div class="alert alert-danger" v-if="connectionStatus == false">
              Nutruko interneto rysys. Noredami toliau naudotis sistema isitikinkite, kad sis irenginys yra prijungtas prie tinklo
            </div>
          </div>
        </div>
    </main>
    </div>
    @else
      <div class="justify-content-center flex-column" style="height: 100vh;">
        @yield('content')
      </div>
    @endauth
</body>
</html>
