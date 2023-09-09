<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <!-- Scripts -->
    <script src="<?php echo e(asset('js/app.js')); ?>" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">

    <!-- Styles -->
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">

    <script>
          window.CSRF =  <?php echo json_encode([
              'csrfToken' => csrf_token(),
          ]); ?>
    </script>
</head>
<body>
    <?php if(auth()->guard()->check()): ?>
    <div id="app">
      <?php echo $__env->yieldContent('content'); ?>
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
                  <form action="<?php echo e(route('logout')); ?>" method="POST" id="logout-form">
                  <a style="cursor:pointer;" onclick="document.getElementById('logout-form').submit()" class="item item-red">
                    <span class="icon" data-feather="log-out"></span>
                    <span class="ml-3">Atsijungti</span>
                    <?php echo e(csrf_field()); ?>

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
    <?php else: ?>
      <div class="justify-content-center flex-column" style="height: 100vh;">
        <?php echo $__env->yieldContent('content'); ?>
      </div>
    <?php endif; ?>
</body>
</html>
<?php /**PATH /site/wwwroot/resources/views/layouts/app.blade.php ENDPATH**/ ?>