
 
<?php if(!is_null($history->details)): ?>
<?php 
     $details =json_decode($history->details);
?> 
 
<?php if(isset($details->meet_date)): ?> 
    <div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">วันที่ประชุม :</p>
    </div>
    <div class="col-md-4">
        <?php echo e(!empty($details->meet_date) ? @HP::DateThai(date("Y-m-d",strtotime($details->meet_date))) : null); ?>

    </div>
    </div>
<?php endif; ?>

<?php if(isset($details->desc)): ?> 
    <div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">รายละเอียด :</p>
    </div>
    <div class="col-md-4">
        <?php echo e(@$details->desc ?? '-'); ?>

    </div>
    </div>
<?php endif; ?>

<?php if(isset($details->status)): ?> 
    <div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">มติคณะอนุกรรมการ :</p>
    </div>
    <div class="col-md-8">
        <label  >   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" <?php echo e(($details->status == 1 ) ? 'checked' : ' '); ?>>  &nbsp; เห็นชอบ  &nbsp;</label>
        <label >   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" <?php echo e(($details->status != 1 ) ? 'checked' : ' '); ?>>  &nbsp;ไม่เห็นชอบ  &nbsp;</label>
    </div>
    </div>
<?php endif; ?>

<?php endif; ?>

<?php if(!is_null($history->file)): ?> 
<div class="row">
    <div class="col-md-4 text-right">
       <p class="text-nowrap">ขอบข่ายที่ได้รับการเห็นชอบ :</p>
    </div>
    <div class="col-md-8">
          <p> 
              <a href="<?php echo e(url('certify/check/file_client/'.$history->file.'/'.( !empty($history->file_client_name) ? $history->file_client_name :  basename($history->file) ))); ?>" target="_blank">
                 <?php echo HP::FileExtension($history->file)  ?? ''; ?>

             </a>
           </p>
    </div>
  </div>
  <?php endif; ?>

  <?php if(!is_null($history->attachs)): ?> 
  <?php 
       $attachs = json_decode($history->attachs);
 ?>
  <div class="row">
      <div class="col-md-4 text-right">
         <p class="text-nowrap">หลักฐานอื่นๆ :</p>
      </div>
      <div class="col-md-8">
            <?php $__currentLoopData = $attachs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key1 => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>   
            <p>
                <?php echo e(@$item->file_desc); ?>

                    <a href="<?php echo e(url('certify/check/file_client/'.$item->file.'/'.( !empty($item->file_client_name) ? $item->file_client_name :   basename($item->file)  ))); ?>" 
                    title=" <?php echo e(!empty($item->file_client_name) ? $item->file_client_name : basename($item->file)); ?>"  target="_blank"> 
                 <?php echo HP::FileExtension($item->file)  ?? ''; ?>

            </a>
            </p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
</div>
<?php endif; ?>


<?php if(!is_null($history->date)): ?> 
<div class="row">
<div class="col-md-4 text-right">
    <p class="text-nowrap">วันที่บันทึก :</p>
</div>
<div class="col-md-8">
    <?php echo e(@HP::DateThai($history->date) ?? '-'); ?>

</div>
</div>
<?php endif; ?>