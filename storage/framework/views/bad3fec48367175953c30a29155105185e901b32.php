<?php
    $theme_name = 'default';
    $fix_header = false;
    $fix_sidebar = false;
    $theme_layout = '';

    if(auth()->user()){

        $user = auth()->user();

        $params = (object)json_decode($user->params);

        if(!empty($params->theme_name)){
            if(is_file('css/colors/'.$params->theme_name.'.css')){
                $theme_name = $params->theme_name;
            }
        }

        if(!empty($params->fix_header) && $params->fix_header=="true"){
            $fix_header = true;
        }

        if(!empty($params->fix_sidebar) && $params->fix_sidebar=="true"){
            $fix_sidebar = true;
        }

        if(!empty($params->theme_layout)){
            $theme_layout = $params->theme_layout;;
        }

    }

?>

<aside class="sidebar">
    <div class="scroll-sidebar">

        <?php if(auth()->check()): ?>
            <?php if($theme_layout != 'fix-header'): ?>
                <div class="user-profile">
                    <div class="dropdown user-pro-body ">
                        <div class="profile-image" id="profile-image">
                            <?php if($user->profile == null || $user->profile->pic == null): ?>
                                <img src="<?php echo e(asset('storage/uploads/users/no_avatar.png')); ?>" alt="user-img"
                                     class="img-circle">
                            <?php else: ?>
                                <img src="<?php echo e(HP::getFileStorage('users/'.$user->profile->pic)); ?>"
                                     alt="user-img" class="img-circle">
                            <?php endif; ?>

                            <a href="javascript:void(0);" class="dropdown-toggle u-dropdown text-blue"
                               data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <span class="badge badge-danger">
                                    <i class="fa fa-angle-down"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu animated flipInY">
                                <li class="font-15"><a href="<?php echo e(url('profile')); ?>"><i class="fa fa-user"></i> โปรไฟล์ </a></li>
                                <li class="font-15"><a href="<?php echo e(url('image-crop')); ?>"><i class="fa fa-camera"></i> เปลี่ยนภาพโปรไฟล์</a></li>
                                <li class="font-15"><a href="<?php echo e(url('account-settings')); ?>"><i class="fa fa-cog"></i> ตั้งค่าบัญชีผู้ใช้</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="font-15"><a href="<?php echo e(url('logout')); ?>"><i class="fa fa-power-off"></i> ออกจากระบบ</a></li>
                            </ul>
                        </div>
                        <p class="profile-text m-t-15 font-16">
                           <a href="javascript:void(0);">
                               <?php echo e($user->reg_fname); ?> <?php echo e($user->reg_lname); ?>

                           </a><br>
                           <span style="margin-top:-10px;">(Back office)</span>
                       </p>
                        <a href="<?php echo e(url('profile')); ?>"> โปรไฟล์ </a>

                    </div>
                </div>
            <?php endif; ?>
            <nav class="sidebar-nav">
                <ul id="side-menu">

                    <li>
                        <a class="waves-effect" href="<?php echo e(url('/page/manuals')); ?>">
                            <i class="mdi mdi-library-books pre-icon"></i>
                            <span class="hide-menu">คู่มือการใช้งาน</span>
                        </a>
                    </li>

                    <?php $__currentLoopData = HP::MenuSidebar(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php if( isset($section->hr) ): ?>
                            <li>
                                <hr class="m-t-0 m-b-0"/>
                            </li>
                        <?php else: ?>
                            <li>
                                <a class="waves-effect sidebar-item" href="#" aria-expanded="false" data-mianulr="<?php echo !empty($section->url)? url($section->url):'-'; ?>">
                                    <i class="<?php echo isset($section->icon)?$section->icon:''; ?> pre-icon"></i>
                                    <span class="hide-menu"><?php echo $section->_comment; ?></span>
                                </a>
                                <ul aria-expanded="false" class="collapse">
                                    <?php $__currentLoopData = $section->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <?php if(isset($menu->sub_menus) && HP::CheckMenuItem([$menu])): ?>
                                            <li>
                                                <a class="waves-effect sidebar-item" href="#" aria-expanded="false">
                                                    <span><i class="fa fa-caret-down pre-icon"></i> <?php echo $menu->display; ?> </span>
                                                </a>
                                                <ul aria-expanded="false" class="collapse">
                                                    <?php echo $__env->make('layouts.partials.sub-menu',[ 'submenu' => $menu->sub_menus], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                                </ul>
                                            </li>
                                        <?php else: ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-'.str_slug($menu->title))): ?>
                                                <li>
                                                    <a class="waves-effect" href="<?php echo e(url($menu->url)); ?>">
                                                        <i class="<?php echo e($menu->icon); ?> pre-icon"></i>
                                                        <?php echo e($menu->display); ?>


                                                        <?php if( array_key_exists($menu->title, ['law-notifys'=>'law-notifys'] ) && ( HP_Law::CategoryNotify()->sum('law_notify_count') >= 1 ) ): ?>
                                                            <span class="badge badge-danger pull-right m-t-10"> 
                                                                <?php echo HP_Law::CategoryNotify()->sum('law_notify_count'); ?>

                                                            </span>
                                                        <?php endif; ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>
            </nav>
        <?php else: ?>
            <?php
                $categories = App\BlogCategory::all();
                $tags = App\Tag::all();
            ?>

            <div class="list-group m-b-0">
            <h4 align="center">ยินดีต้อนรับ</h4>
                <span class="list-group-item bg-primary no-border text-center">หมวดหมู่</span>
                <?php if(count($categories) > 0): ?>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a class="list-group-item"
                        href="<?php echo e(url('blogs/category/'.$category->slug)); ?>"><?php echo e($category->title); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    No Categories Yet
                <?php endif; ?>
            </div>
            <div class="list-group">
                <span class="list-group-item bg-primary no-border text-center">แท็ก</span>
                <?php if(count($tags) > 0): ?>
                    <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a class="list-group-item" href="<?php echo e(url('blogs/tag/'.$tag->slug)); ?>"><?php echo e($tag->name); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    No Categories Yet
                <?php endif; ?>
            </div>
            
        <?php endif; ?>
    </div>
</aside>
