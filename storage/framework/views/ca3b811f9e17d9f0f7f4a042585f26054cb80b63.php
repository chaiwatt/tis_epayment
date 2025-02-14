<?php $__env->startSection('content'); ?>
<style>
    .input-login{
        border-bottom: 1px solid black !important;
    }
</style>
<section id="wrapper" class="login-register">
    <div class="login-box">
        <div class="white-box">

            
            <?php if(!empty(config('app.login_message_notice'))): ?>
                <div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <b class="text-dark"><?php echo e(config('app.login_message_notice')); ?></b>
                </div>
            <?php endif; ?>

            <form class="form-horizontal form-material" id="loginform" method="post" action="<?php echo e(route('login')); ?>">
                <?php echo e(csrf_field()); ?>

                <h4 class="box-title font-20 m-b-20">&nbsp;เข้าสู่ระบบ สำหรับเจ้าหน้าที่ สมอ. (SSO)</h4>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input id="email" placeholder="อีเมล์" class="form-control input-login <?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required autofocus>
                        <?php if($errors->first()): ?>
                            <span class="text-danger">
                                <?php echo e($errors->first()); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input id="password" type="password" class="form-control input-login <?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" required placeholder="รหัสผ่าน">
                        <?php if($errors->has('password')): ?>
                            <span class="text-danger">
                                <?php echo e($errors->first('password')); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="checkbox checkbox-primary pull-left p-t-0">
                            <input type="checkbox" id="checkbox-signup" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                            <label for="checkbox-signup"> จำการเข้าระบบ </label>
                        </div>
                        <a href="<?php echo e(route('password.request')); ?>" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> ลืมรหัสผ่าน?</a> </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit"> ลงชื่อ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>