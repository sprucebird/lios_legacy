<?php $__env->startSection('content'); ?>
  <div class="d-flex justify-content-center">

    <div class="login-card col-md-4">
      <div class="card-body">
        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group col-md-12 mb-5">
                <div class="h4 text-md-center">
                  LIOS
                </div>
                <div class="h6 text-md-center">
                  Sukūrė ir palaiko Sprucebird
                </div>
            </div>
            <div class="form-group mt-5">
                <label for="email" class="col-md-12 col-form-label text-md-left">Prisijungimo vardas</label>
                <div class="col-md">
                    <input id="email" type="email" class="form-control form-control-login<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="col-md-12 col-form-label text-md-left">Slaptažodis</label>

                <div class="col-md">
                    <input id="password" type="password" class="form-control form-control-login<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" required>

                    <?php if($errors->has('password')): ?>
                        <span class="invalid-feedback" role="alert">
                            <strong><?php echo e($errors->first('password')); ?></strong>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

           

            <div class="form-group mb-0">
                <div class="col-md offset-md">
                    <button type="submit" class="col-md-12 btn btn-primary btn-login">
                        Prisijungti
                    </button>
                </div>
            </div>
        </form>
      </div>
      </div>
  </div>
  
  </div> --}}

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/auth/login.blade.php ENDPATH**/ ?>