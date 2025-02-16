
<div class="form-group">
    <div class="col-md-6">
        <label class="col-md-8 text-right"> รายงานการตรวจประเมิน : </label>
        <div class="col-md-2">
            <?php if(!is_null($history->file)): ?>
               <p>
                <a href="<?php echo e(url('certify/check/file_client/'.$history->file.'/'.( !empty($history->file_client_name) ? $history->file_client_name : basename($history->file)  ))); ?>" 
                   title="<?php echo e(!empty($history->file_client_name) ? $history->file_client_name :  basename($history->file)); ?>"  target="_blank">
                    <?php echo HP::FileExtension($history->file)  ?? ''; ?>

                    
                </a>
             </p>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <?php if(!is_null($history->details_date)): ?>
        <label class="col-md-6 text-right"> รายงานปิด Car : </label>
        <div class="col-md-6">
            <p>
                <a href="<?php echo e(url('certify/check/file_client/'.$history->details_date.'/'.( !empty($history->attach_client_name) ? $history->attach_client_name :  basename($history->details_date)  ))); ?>" 
                        title="<?php echo e(!empty($history->attach_client_name) ? $history->attach_client_name :  basename($history->details_date)); ?>"  target="_blank">
                    <?php echo HP::FileExtension($history->details_date)  ?? ''; ?>

                    
                </a> 
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="form-group">
    <?php if(!is_null($history->details_table)): ?>
        <div class="col-md-6">
            <label class="col-md-6 text-right"> รายงาน Scope : </label>
            <div class="col-md-6">
                     <?php
                          $details_table = json_decode($history->details_table);
                    ?>
                    <?php if(!is_null($details_table)): ?>
                    <?php $__currentLoopData = $details_table; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p>
                           <a href="<?php echo e(url('certify/check/file_client/'.$item1->attachs.'/'.( !empty($item1->attachs_client_name) ? $item1->attachs_client_name : basename($item1->attachs)  ))); ?>" 
                                    title="<?php echo e(!empty($item1->attachs_client_name) ? $item1->attachs_client_name :  basename($item1->attachs)); ?>"  target="_blank">
                                <?php echo HP::FileExtension($item1->attachs)  ?? ''; ?>

                                
                            </a>
                        </p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
            </div>
        </div>
     <?php endif; ?>
     <?php if(!is_null($history->attachs)): ?>
     <div class="col-md-6">
         <label class="col-md-6 text-right"> ไฟล์แนบ : </label>
         <div class="col-md-6">
                  <?php
                       $attachs = json_decode($history->attachs);
                 ?>
                 <?php if(!is_null($attachs)): ?>
                 <?php $__currentLoopData = $attachs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                     <p>
                            <a href="<?php echo e(url('certify/check/file_client/'.$item3->attachs.'/'.( !empty($item3->attachs_client_name) ? $item3->attachs_client_name : basename($item3->attachs)  ))); ?>" 
                                title="<?php echo e(!empty($item3->attachs_client_name) ? $item3->attachs_client_name :  basename($item3->attachs)); ?>"  target="_blank">
                             <?php echo HP::FileExtension($item3->attachs)  ?? ''; ?>

                             
                            </a>
                     </p>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                 <?php endif; ?>
         </div>
     </div>
    <?php endif; ?>
</div>


<hr>
<?php if(!is_null($history->status_scope)): ?>
<div class="form-group">
    <div class="col-md-12">
        <label class="col-md-3 text-right">  เห็นชอบกับ Scope : </label>
        <div class="col-md-7">
            <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" <?php echo e(($history->status_scope == 1 ) ? 'checked' : ' '); ?>>  &nbsp;ยืนยัน Scope &nbsp;</label>
            <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" <?php echo e(($history->status_scope == 2 ) ? 'checked' : ' '); ?>>  &nbsp; แก้ไข Scope &nbsp;</label>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if(!is_null($history->remark)): ?>
<div class="form-group">
    <div class="col-md-12">
        <label class="col-md-4 text-right"> หมายเหตุ : </label>
        <div class="col-md-7">
                <?php echo e($history->remark ?? null); ?>

        </div>
    </div>
</div>
<?php endif; ?>

<div class="form-group">
    <div class="col-md-12">
        <?php if(!is_null($history->evidence)): ?>
        <label class="col-md-4 text-right"> ไฟล์แนบ : </label>
        <div class="col-md-7">
                <?php
                      $evidence = json_decode($history->evidence);
                ?>
                <?php if(!is_null($evidence)): ?>
                <?php $__currentLoopData = $evidence; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p>
                         <?php echo e(@$history->file_desc_text); ?>

                         <a href="<?php echo e(url('certify/check/files/'.$history3->attachs)); ?>" title="<?php echo e(basename($history3->attachs)); ?>"  target="_blank">
                            <?php echo HP::FileExtension($history3->attachs)  ?? ''; ?>

                            <?php echo e(basename($history3->attachs)); ?>

                        </a>
                    </p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
        </div>
         <?php endif; ?>
    </div>
</div>

<?php if(!is_null($history->date)): ?> 
<div class="row">
<div class="col-md-4 text-right">
    <p class="text-nowrap">วันที่บันทึก</p>
</div>
<div class="col-md-7">
    <?php echo e(@HP::DateThai($history->date) ?? '-'); ?>

</div>
</div>
<?php endif; ?>