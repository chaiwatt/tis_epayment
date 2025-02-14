<div class="row ">
    <div class="col-md-6">
        <label class="col-md-6 text-right"> รายงานการตรวจประเมิน : </label>
        <div class="col-md-6 text-left">
            <?php if(!is_null($history->details_three)): ?>
               <p>
                
                <a href="<?php echo e(url('certify/check/file_cb_client/'.$history->details_three.'/'.( !empty($history->file_client_name) ? $history->file_client_name : basename($history->details_three) ))); ?>" 
                    title="<?php echo e(!empty($history->file_client_name) ? $history->file_client_name :  basename($history->details_three)); ?>" target="_blank">
                    <?php echo HP::FileExtension($history->details_three)  ?? ''; ?>

                </a>
                
            
             </p>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <?php if(!is_null($history->attachs_car)): ?>
        <label class="col-md-6 text-right"> รายงานปิด Car : </label>
        <div class="col-md-6 text-left">
                    <p>
                        
                        <a href="<?php echo e(url('certify/check/file_cb_client/'.$history->attachs_car.'/'.( !empty($history->attach_client_name) ? $history->attach_client_name : basename($history->attachs_car) ))); ?>" 
                            title="<?php echo e(!empty($history->attach_client_name) ? $history->attach_client_name :  basename($history->attachs_car)); ?>" target="_blank">
                            <?php echo HP::FileExtension($history->attachs_car)  ?? ''; ?>

                        </a>
                        
                    </p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
<?php if(!is_null($history->details_four)): ?>
<div class="col-md-6">
    <label class="col-md-6 text-right"> รายงาน Scope : </label>
    <div class="col-md-6 text-left">
             <?php
                  $details_four = json_decode($history->details_four);
            ?>
            <?php if(!is_null($details_four)): ?>
            <?php $__currentLoopData = $details_four; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                    
                    <a href="<?php echo e(url('certify/check/file_cb_client/'.$item2->file.'/'.( !empty($item2->file_client_name) ? $item2->file_client_name :   basename($item2->file) ))); ?>" 
                        title="<?php echo e(!empty($item2->file_client_name) ? $item2->file_client_name :  basename($item2->file)); ?>" target="_blank">
                         <?php echo HP::FileExtension($item2->file)  ?? ''; ?>

                    </a>
                   
                
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php if(!is_null($history->attachs)): ?>
<div class="col-md-6">
    <label class="col-md-8 text-right"> สรุปรายงานการตรวจทุกครั้ง : </label>
    <div class="col-md-4 text-left">
             <?php
                  $attachs = json_decode($history->attachs);
            ?>
            <?php if(!is_null($attachs)): ?>
            <?php $__currentLoopData = $attachs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                    <?php if($item3->file !='' && HP::checkFileStorage($attach_path.$item3->file)): ?>
                    <a href="<?php echo e(url('certify/check/file_cb_client/'.$item3->file.'/'.( !empty($item3->file_client_name) ? $item3->file_client_name :  basename($item3->file) ))); ?>" 
                        title="<?php echo e(!empty($item3->file_client_name) ? $item3->file_client_name :  basename($item3->file)); ?>" target="_blank">
                        <?php echo HP::FileExtension($item3->file)  ?? ''; ?>

                    </a>
                   <?php endif; ?>
            
                
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
    </div>
</div>
<?php endif; ?>
</div>
<div class="row">
<?php if(!is_null($history->file)): ?>
<div class="col-md-6">
    <label class="col-md-6 text-right"> ไฟล์แนบ : </label>
    <div class="col-md-6 text-left">
             <?php
                  $files = json_decode($history->file);
            ?>
            <?php if(!is_null($files)): ?>
            <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item4): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                    <?php if($item4->file !='' && HP::checkFileStorage($attach_path.$item4->file)): ?>
                      <a href="<?php echo e(url('certify/check/file_cb_client/'.$item4->file.'/'.( !empty($item4->file_client_name) ? $item4->file_client_name :  basename($item4->file) ))); ?>" 
                        title="<?php echo e(!empty($item4->file_client_name) ? $item4->file_client_name :  basename($item4->file)); ?>" target="_blank">
                       <?php echo HP::FileExtension($item4->file)  ?? ''; ?>

                     </a>
                   <?php endif; ?>
              
                
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
    </div>
</div>
<?php endif; ?>
</div>


<hr><?php if(!is_null($history->status)): ?>
<div class="form-group">
    <div class="col-md-12">
        <label class="col-md-3 text-right">  เห็นชอบกับ Scope : </label>
        <div class="col-md-7">
            <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" <?php echo e(($history->status == 1 ) ? 'checked' : ' '); ?>>  &nbsp;ยืนยัน Scope &nbsp;</label>
            <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" <?php echo e(($history->status == 2 ) ? 'checked' : ' '); ?>>  &nbsp; แก้ไข Scope &nbsp;</label>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if(!is_null($history->remark)): ?>
<div class="form-group">
    <div class="col-md-12">
        <label class="col-md-3 text-right"> หมายเหตุ : </label>
        <div class="col-md-7">
                <?php echo e($history->remark ?? null); ?>

        </div>
    </div>
</div>
<?php endif; ?>

<div class="form-group">
    <div class="col-md-12">
        <?php if(!is_null($history->attachs_file)): ?>
        <label class="col-md-3 text-right"> ไฟล์แนบ : </label>
        <div class="col-md-7">
                <?php
                      $attachs_file = json_decode($history->attachs_file);
                ?>
                <?php $__currentLoopData = $attachs_file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item13): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p>
                        <?php echo e(@$item13->file_desc); ?>

                        
                           <a href="<?php echo e(url('certify/check/file_cb_client/'.$item13->file.'/'.( !empty($item13->file_client_name) ? $item13->file_client_name :  basename($item13->file) ))); ?>" 
                               title="<?php echo e(!empty($item13->file_client_name) ? $item13->file_client_name :  basename($item13->file)); ?>" target="_blank">
                               <?php echo HP::FileExtension($item13->file)  ?? ''; ?>

                           </a>
                        
                     
                    </p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
         
        </div>
         <?php endif; ?>
    </div>
</div>

<?php if(!is_null($history->date)): ?> 
<div class="row">
<label class="col-md-3 text-right">
  วันที่บันทึก :
</label>
<div class="col-md-8 text-left">
<?php echo e(@HP::DateThai($history->date) ?? '-'); ?>

</div>
</div>
<?php endif; ?>