<!-- Modal เลข 3 -->
<div class="modal fade text-left" id="actionFour<?php echo e($id); ?>" tabindex="-1" role="dialog" aria-labelledby="addBrand">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">ยกเลิกคำขอ</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <h6>รายละเอียด : <span style="color:black;"><?php echo e($desc ?? null); ?> </span> </h6>
                    <?php $__currentLoopData = $file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dataFile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p>ไฟล์แนบ : <a href="<?php echo e(url('certify/check/files/'.$dataFile->file)); ?>">
                            <?php echo HP::FileExtension($dataFile->file)  ?? ''; ?>

                            <?php echo e(basename($dataFile->file)); ?></a>
                        </p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if(count($delete_file) > 0): ?>
                        <?php $__currentLoopData = $delete_file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p>ไฟล์แนบ : 
                                <?php echo e($item->name ?? ' '); ?>

                                <a href="<?php echo e(url('certify/check/files/'.$item->path)); ?>">
                                <?php echo HP::FileExtension($item->path)  ?? ''; ?>

                                 <?php echo e(basename($item->path)); ?></a>
                            </p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

