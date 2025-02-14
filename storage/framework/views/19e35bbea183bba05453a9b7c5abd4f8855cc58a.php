<?php if(!is_null($history->details_two)): ?>
<?php 
    $details_one = json_decode($history->details_one);
    $details_two =json_decode($history->details_two);
?>              
   <h4 class="text-left">1. จำนวนวันที่ใช้ตรวจประเมินทั้งหมด <span><?php echo e($history->MaxAmountDate  ?? '-'); ?></span> วัน</h4>
   <h4 class="text-left">2. ค่าใช้จ่ายในการตรวจประเมินทั้งหมด <span><?php echo e($history->SumAmount ?? '-'); ?></span> บาท </h4>
    <table class="table table-bordered" id="myTable_labTest">
        <thead class="bg-primary">
        <tr>
            <th class="text-center bg-info  text-white" width="2%">ลำดับ</th>
            <th class="text-center bg-info  text-white" width="38%">รายละเอียด</th>
            <th class="text-center bg-info  text-white" width="20%">จำนวนเงิน (บาท)</th>
            <th class="text-center bg-info  text-white" width="20%">จำนวนวัน (วัน)</th>
            <th class="text-center bg-info  text-white" width="20%">รวม (บาท)</th>
        </tr>
        </thead>
        <tbody id="costItem">
            <?php $__currentLoopData = $details_two; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php     
                $amount_date = !empty($item2->amount_date) ? $item2->amount_date : 0 ;
                $amount = !empty($item2->amount) ? $item2->amount : 0 ;
                $sum =   $amount*$amount_date;
                $details =  App\Models\Bcertify\StatusAuditor::where('id',$item2->detail)->first();
                ?>
                <tr>
                    <td class="text-center"><?php echo e($key+1); ?></td>
                    <td><?php echo e(!is_null($details) ? $details->title : null); ?></td>
                    <td class="text-right"><?php echo e(number_format($amount, 2)); ?></td>
                    <td class="text-right"><?php echo e($amount_date); ?></td>
                    <td class="text-right"><?php echo e(number_format($sum, 2) ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <footer>
            <tr>
                <td colspan="4" class="text-right">รวม</td>
                <td class="text-right">
                     <?php echo e($history->SumAmount ?? '-'); ?> 
                </td>
            </tr>
        </footer>
    </table>
<?php endif; ?>

<?php if(!is_null($history->attachs)): ?> 
<?php 
$attachs = json_decode($history->attachs);
?>
<div class="row">
<div class="col-md-3 text-right">
<p class="text-nowrap">ขอบข่าย:</p>
</div>
<div class="col-md-9 text-left">
   <?php $__currentLoopData = $attachs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scope): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
   <p>      
      <a href="<?php echo e(url('certify/check/file_cb_client/'.$scope->file.'/'.( !empty($scope->file_client_name) ? $scope->file_client_name : @basename($scope->file) ))); ?>" target="_blank">
          <?php echo e(!empty($scope->file_client_name) ? $scope->file_client_name :  basename($scope->file)); ?>

      </a>
   </p>
   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
<?php endif; ?>

<?php if(isset($details_one->check_status) && !is_null($details_one->check_status)): ?> 
<legend class="text-left"><h3>เหตุผล / หมายเหตุ ขอแก้ไข</h3></legend>

<div class="row">
   <div class="col-md-3 text-right">
            <p class="text-nowrap">เห็นชอบกับค่าใช่จ่ายที่เสนอมา</p>
    </div> 
    <div class="col-md-9 text-left">
        <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" <?php echo e(($details_one->check_status == 1 ) ? 'checked' : ' '); ?>>  &nbsp;ยืนยัน &nbsp;</label>
        <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" <?php echo e(($details_one->check_status == 2 ) ? 'checked' : ' '); ?>>  &nbsp;แก้ไข &nbsp;</label>
    </div>
</div>
<?php endif; ?>

<?php if(isset($details_one->remark) && $details_one->check_status == 2): ?> 
    <div class="row">
    <div class="col-md-3 text-right">
    <p class="text-nowrap">หมายเหตุ</p>
    </div>
    <div class="col-md-9 text-left">
       <?php echo e(@$details_one->remark ?? ''); ?>

    </div>
    </div>
<?php endif; ?>

<?php if(!is_null($history->attachs_file)): ?>
        <?php 
        $attachs_file = json_decode($history->attachs_file);
        ?> 
        <div class="row">
        <div class="col-md-3 text-right">
        <p class="text-nowrap">หลักฐาน:</p>
        </div>
        <div class="col-md-9 text-left">
        <?php $__currentLoopData = $attachs_file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $files): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p> 
                <?php if(isset($files->file)): ?>
                <?php echo e(@$files->file_desc); ?>

                    <a href="<?php echo e(url('certify/check/file_cb_client/'.$files->file.'/'.( !empty($files->file_client_name) ? $files->file_client_name :  @basename($files->file) ))); ?>" target="_blank">
                        <?php echo e(!empty($files->file_client_name) ? $files->file_client_name :  basename($files->file)); ?>

                     </a>
                <?php endif; ?>
            </p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        </div>
 <?php endif; ?>
 
 <?php if(isset($details_one->status_scope) && !is_null($details_one->status_scope)): ?> 
<div class="row">
   <div class="col-md-3 text-right">
       <p class="text-nowrap">เห็นชอบกับ Scope</p>
    </div>
    <div class="col-md-9 text-left">
        <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" <?php echo e(($details_one->status_scope == 1 ) ? 'checked' : ' '); ?>>  &nbsp;ยืนยัน Scope &nbsp;</label>
        <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" <?php echo e(($details_one->status_scope == 2 ) ? 'checked' : ' '); ?>>  &nbsp; แก้ไข Scope &nbsp;</label>
    </div>
</div>
<?php endif; ?>

<?php if(isset($details_one->remark_scope) && $details_one->status_scope == 2): ?> 
    <div class="row">
    <div class="col-md-3 text-right">
    <p class="text-nowrap">หมายเหตุ</p>
    </div>
    <div class="col-md-9 text-left">
       <?php echo e(@$details_one->remark_scope ?? ''); ?>

    </div>
    </div>
<?php endif; ?>


<?php if(!is_null($history->evidence)): ?>
<?php 
$evidence = json_decode($history->evidence);
?> 
<div class="row">
<div class="col-md-3 text-right">
<p class="text-nowrap">หลักฐาน:</p>
</div>
<div class="col-md-9 text-left">
<?php $__currentLoopData = $evidence; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $files): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <p> 
        <?php if(isset($files->attach_files)): ?>
          <?php echo e(@$files->file_desc_text); ?>

              <a href="<?php echo e(url('certify/check/file_cb_client/'.$files->attach_files.'/'.( !empty($files->file_client_name) ? $files->file_client_name :  @basename($files->attach_files) ))); ?>" target="_blank">
                    <?php echo HP::FileExtension($files->attach_files)  ?? ''; ?>

                    <?php echo e(!empty($files->file_client_name) ? $files->file_client_name : basename($files->attach_files)); ?>

              </a> 
        <?php endif; ?>
    </p>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
<?php endif; ?>

<?php if(!is_null($history->date)): ?> 
   <div class="row">
       <div class="col-md-4 text-right">
           <p class="text-nowrap">วันที่บันทึก</p>
       </div>
       <div class="col-md-8 text-left">
           <?php echo e(@HP::DateThai($history->date) ?? '-'); ?>

       </div>
   </div>
<?php endif; ?>