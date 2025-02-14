<?php $__env->startPush('css'); ?>
    <style>
        .details-wrapper{
            display: inline-block;
            width: 100%;
        }
        .details-wrapper a{
            margin-bottom: 5px;
            display: inline-block;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <?php if(count($blogs) > 0): ?>
                        <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <h1><a href="<?php echo e(url('blogs/'.$blog->slug)); ?>"><?php echo e($blog->title); ?></a></h1>
                            <span class="text-muted">Posted on <?php echo e($blog->created_at->format('d-m-Y h:i a')); ?></span>
                            <p><?php echo ltrim(substr(strip_tags($blog->content), 0, 250)); ?> ...</p>
                            <div class="details-wrapper">
							
								<?php if(!is_null($blog->author)): ?>
                                    <a href="<?php echo e(('blogs/author/'.$blog->author->getKey())); ?>">
                                        <span class="label label-success">Author : <?php echo e($blog->author->FullName); ?></span>
                                    </a>
								<?php endif; ?>
								
								
                                <a href="<?php echo e(('blogs/category/'.$blog->category->slug)); ?>">
									<span class="label label-primary">Category : <?php echo e($blog->category->title); ?></span>
								</a>
								
								
                                <div class="pull-right">
                                    <?php if(count($blog->tags) > 0): ?>
                                        <?php $__currentLoopData = $blog->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(url('blogs/tag/'.$tag->slug)); ?>"><span
                                                        class="label label-warning"><?php echo e($tag->name); ?></span></a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="clearfix"></div>
                                <hr>
                            </div>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="text-center">
                            <?php echo $blogs->links(); ?>

                        </div>
                    <?php else: ?>
                        <h1 align="center">No Blogs Available</h1>
                    <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>