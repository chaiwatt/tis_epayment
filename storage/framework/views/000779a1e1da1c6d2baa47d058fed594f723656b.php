<?php
    $config = HP::getConfig(false);
?>

<?php $__currentLoopData = $submenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <?php if( isset($menu->title) ): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-'.str_slug($menu->title))): ?>
            <?php if($menu->title=='report-std-certifies'): ?>
            <li>
                <a class="waves-effect" href="<?php echo e(url($config->url_acc.$menu->url)); ?>">
                    <i class="fa fa-play-circle pre-icon"></i>
                    <?php echo e($menu->display); ?>

                </a>
            </li>
            <?php else: ?>
            <li>
                <a class="waves-effect" href="<?php echo e(url($menu->url)); ?>">
                    <i class="fa fa-play-circle pre-icon"></i>
                    <?php echo e($menu->display); ?>

                </a>
            </li>
            <?php endif; ?>
        <?php endif; ?>
    <?php else: ?>
        <li>
            <a class="waves-effect" href="<?php echo e(url($menu->url)); ?>">
                <i class="fa fa-play-circle pre-icon"></i>
                <?php echo e($menu->display); ?>

            </a>
        </li>
    <?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
