<?php

    $theme_name = 'default';
    $fix_header = false;
    $fix_sidebar = false;
    $theme_layout = 'normal';

    if(auth()->user()){

        $params = (object)json_decode(auth()->user()->params);

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
            $theme_layout = $params->theme_layout;
        }
    }

?>

<div class="right-sidebar">
    <div class="slimscrollright">
        <div class="rpanel-title"> <b>แผงควบคุม</b> <span><i class="icon-close right-side-toggler"></i></span></div>
        <?php if(auth()->check()): ?>
            <div class="text-center">
                <a class="btn btn-primary m-t-10" href="<?php echo e(route('logout')); ?>">ออกจากระบบ</a>
            </div>
        <?php endif; ?>
        <div class="r-panel-body">
            <?php if(auth()->check()): ?>
                <p><b>แบบหน้าจอ</b></p>
                <ul class="layouts">
                    <li class="<?php if($theme_layout == 'normal'): ?> active <?php endif; ?>"><a
                                href="<?php echo e(asset('?theme=normal')); ?>">ปกติ</a></li>
                    <li class="<?php if($theme_layout == 'fix-header'): ?> active <?php endif; ?>"><a
                                href="<?php echo e(asset('?theme=fix-header')); ?>">เมนูด้านบน</a></li>
                    <li class="<?php if($theme_layout == 'mini-sidebar'): ?> active <?php endif; ?>"><a
                                href="<?php echo e(asset('?theme=mini-sidebar')); ?>">แถบด้านข้างเล็ก</a></li>
                </ul>
                <br>
                <?php if($theme_layout != 'fix-header'): ?>
                    <ul class="hidden-xs">
                        <li><b>ตัวเลือกแบบหน้าจอ</b></li>
                        <li>
                            <div class="checkbox checkbox-danger">
                                <input id="headcheck" type="checkbox" class="fxhdr" <?php if($fix_header===true): ?> checked <?php endif; ?>>
                                <label for="headcheck"> ตรึงส่วนหัว </label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox checkbox-warning">
                                <input id="sidecheck" type="checkbox" class="fxsdr" <?php if($fix_sidebar===true): ?> checked <?php endif; ?>>
                                <label for="sidecheck"> ตรึงแถบด้านข้าง </label>
                            </div>
                        </li>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center">
                    <a class="btn btn-primary m-t-10 " href="<?php echo e(route('login')); ?>">LogIn</a> &nbsp;&nbsp;
                    <a class="btn btn-success m-t-10" href="<?php echo e(route('register')); ?>">Register</a>
                </div>
            <?php endif; ?>

            <ul id="themecolors" class="m-t-20">
                <li><b>แถบด้านข้างโปร่งใส</b></li>
                <li><a href="javascript:void(0)" data-theme="default" class="default-theme <?php if($theme_name=='default'): ?> working <?php endif; ?>">1</a></li>
                <li><a href="javascript:void(0)" data-theme="green" class="green-theme <?php if($theme_name=='green'): ?> working <?php endif; ?>">2</a></li>
                <li><a href="javascript:void(0)" data-theme="yellow" class="yellow-theme <?php if($theme_name=='yellow'): ?> working <?php endif; ?>">3</a></li>
                <li><a href="javascript:void(0)" data-theme="red" class="red-theme <?php if($theme_name=='red'): ?> working <?php endif; ?>">4</a></li>
                <li><a href="javascript:void(0)" data-theme="purple" class="purple-theme <?php if($theme_name=='purple'): ?> working <?php endif; ?>">5</a></li>
                <li><a href="javascript:void(0)" data-theme="black" class="black-theme <?php if($theme_name=='black'): ?> working <?php endif; ?>">6</a></li>
                <li class="db"><b>แถบด้านข้างมืด</b></li>
                <li><a href="javascript:void(0)" data-theme="default-dark" class="default-dark-theme <?php if($theme_name=='default-dark'): ?> working <?php endif; ?>">7</a></li>
                <li><a href="javascript:void(0)" data-theme="green-dark" class="green-dark-theme <?php if($theme_name=='green-dark'): ?> working <?php endif; ?>">8</a></li>
                <li><a href="javascript:void(0)" data-theme="yellow-dark" class="yellow-dark-theme <?php if($theme_name=='yellow-dark'): ?> working <?php endif; ?>">9</a></li>
                <li><a href="javascript:void(0)" data-theme="red-dark" class="red-dark-theme <?php if($theme_name=='red-dark'): ?> working <?php endif; ?>">10</a></li>
                <li><a href="javascript:void(0)" data-theme="purple-dark" class="purple-dark-theme <?php if($theme_name=='purple-dark'): ?> working <?php endif; ?>">11</a></li>
                <li><a href="javascript:void(0)" data-theme="black-dark" class="black-dark-theme <?php if($theme_name=='black-dark'): ?> working <?php endif; ?>">12</a></li>
            </ul>

        </div>
    </div>
</div>
