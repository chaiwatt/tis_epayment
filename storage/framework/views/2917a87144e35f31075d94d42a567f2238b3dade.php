<?php 
$details_one = json_decode($history->details_one);
?>
<?php if(isset($details_one->auditor)): ?>
<div class="row">
  <div class="col-md-5 text-right">
      <p class="text-nowrap">ชื่อคณะผู้ตรวจประเมิน</p>
  </div>
  <div class="col-md-7 text-left">
    <?php echo e($details_one->auditor ?? null); ?>

  </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-md-5 text-right">
       <p class="text-nowrap">วันที่ตรวจประเมิน</p>
    </div>
    <div class="col-md-7 text-left">
        <span><?php echo $history->DataBoardAuditorDateTitle ?? '-'; ?></span>
    </div>
</div>

<?php if(!is_null($history->file)): ?>
<div class="row">
  <div class="col-md-5 text-right">
      <p class="text-nowrap">บันทึก ลมอ.  แต่งตั้งคณะผู้ตรวจประเมิน</p>
  </div>
  <div class="col-md-7 text-left">
        
            <a href="<?php echo e(url('certify/check/file_cb_client/'.$item->file.'/'.( !empty($history->file_client_name) ? $history->file_client_name : basename($history->file) ))); ?>" target="_blank">
                <?php echo HP::FileExtension($history->file)  ?? ''; ?>

            </a>  
         
  </div>
</div>
<?php endif; ?>

<?php if(!is_null($history->attachs)): ?>
<div class="row">
<div class="col-md-5 text-right">
   <p class="text-nowrap">กำหนดการตรวจประเมิน</p>
</div>
<div class="col-md-7 text-left">
    
     <a href="<?php echo e(url('certify/check/file_cb_client/'.$history->attachs.'/'.( !empty($history->attach_client_name) ? $history->attach_client_name : basename($history->attachs) ))); ?>" target="_blank">
        <?php echo HP::FileExtension($history->attachs)  ?? ''; ?>

    </a>     
    
</div>
</div>
<?php endif; ?>

<?php if(!is_null($history->details_two)): ?>
 <label class="col-md-12 text-left">โดยคณะผู้ตรวจประเมิน มีรายนามดังต่อไปนี้</label>
<div class="col-md-12">
<table class="table table-bordered">
    <thead class="bg-primary">
    <tr>
        <th class="text-center bg-info  text-white" width="2%">ลำดับ</th>
        <th class="text-center bg-info  text-white" width="30%">สถานะผู้ตรวจประเมิน</th>
        <th class="text-center bg-info  text-white" width="40%">ชื่อผู้ตรวจประเมิน</th>
        <th class="text-center bg-info  text-white" width="26%">หน่วยงาน</th>
    </tr>
    </thead>
    <tbody>
     <?php
     $details_three = json_decode($history->details_three);
     ?>
     <?php $__currentLoopData = $details_three; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key3 => $three): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <?php
             $status = App\Models\Bcertify\StatusAuditor::where('id',$three->status)->first();
         ?>
    <tr>
        <td  class="text-center"><?php echo e($key3 +1); ?></td>
        <td> <?php echo e($status->title ?? '-'); ?></td>
        <td>
             <?php echo e($three->temp_users ?? '-'); ?>

        </td>
        <td>
              <?php echo e($three->temp_departments ?? '-'); ?>

        </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
</div>
<?php endif; ?>

               
<?php if(!is_null($history->details_four)): ?>
<?php
  $details_four = json_decode($history->details_four);
?>
 
<label class="col-md-12 text-left">ค่าใช้จ่าย</label>
<div class="col-md-12">
<table class="table table-bordered">
  <thead class="bg-primary">
  <tr>
      <th class="text-center bg-info  text-white" width="2%">ลำดับ</th>
      <th class="text-center bg-info  text-white" width="38%">รายละเอียด</th>
      <th class="text-center bg-info  text-white" width="20%">จำนวนเงิน (บาท)</th>
      <th class="text-center bg-info  text-white" width="20%">จำนวนวัน (วัน)</th>
      <th class="text-center bg-info  text-white" width="20%">รวม (บาท)</th>
  </tr>
  </thead>
  <tbody>
         <?php    
        $SumAmount = 0;
        ?>
      <?php $__currentLoopData = $details_four; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key4 => $four): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php     
          $amount_date = !empty($four->amount_date) ? $four->amount_date : 0 ;
          $amount = !empty($four->amount) ? $four->amount : 0 ;
          $sum =   $amount*$amount_date;
          $SumAmount  +=  $sum;
          $details =  App\Models\Bcertify\StatusAuditor::where('id',$four->detail)->first();
          ?>
          <tr>
              <td class="text-center"><?php echo e($key4+1); ?></td>
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
               <?php echo e(!empty($SumAmount) ?  number_format($SumAmount, 2) : '-'); ?> 
          </td>
      </tr>
  </footer>
</table>
</div>
<?php endif; ?>

<hr>

<?php if(!is_null($history->status)): ?>
<div class="row">
 <div class="col-md-4 text-right">
 <p class="text-nowrap">กำหนดการตรวจประเมิน</p>
 </div>
 <div class="col-md-7 text-left">
 <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" <?php echo e(($history->status == 1 ) ? 'checked' : ' '); ?>>  &nbsp;เห็นชอบดำเนินการแต่งตั้งคณะผู้ตรวจประเมินต่อไป &nbsp;</label>
 <br>
 <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" <?php echo e(($history->status == 2 ) ? 'checked' : ' '); ?>>  &nbsp;ไม่เห็นชอบ เพราะ  &nbsp;</label>
 </div>
</div>
<?php endif; ?>

<?php if(isset($details_one->remark) &&  !is_null($details_one->remark)): ?>
<div class="row">
<div class="col-md-4 text-right">
  <p class="text-nowrap">หมายเหตุ</p>
</div>
<div class="col-md-7 text-left">
   <?php echo e(@$details_one->remark  ?? '-'); ?>

</div>
</div>
<?php endif; ?>

<?php if(!is_null($history->attachs_file)): ?>
<?php 
 $attachs_file = json_decode($history->attachs_file);
?> 
<div class="col-md-12">
 <?php echo Form::label('no', 'หลักฐาน :', ['class' => 'col-md-4 control-label text-right']); ?>

<div class="col-md-8 text-left">
 <?php $__currentLoopData = $attachs_file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $files): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <p> 
             <?php echo e(@$files->file_desc); ?>

             
                <a href="<?php echo e(url('certify/check/file_cb_client/'.$files->file.'/'.( !empty($files->file_client_name) ? $files->file_client_name : basename($files->file) ))); ?>" target="_blank">
                    <?php echo HP::FileExtension($files->file)  ?? ''; ?>

                </a>
            
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
 <div class="col-md-7 text-left">
     <?php echo e(HP::DateThai($history->date)  ?? '-'); ?>

 </div>
 </div>
<?php endif; ?>

<?php if(!is_null($history->details_auditors_cancel)): ?>
    <span class="text-danger">ยกเลิกแต่งตั้งคณะผู้ตรวจประเมิน</span>
    <hr>
    <?php
    $auditors_cancel = json_decode($history->details_auditors_cancel);
    ?>
    <?php if(!is_null(HP::UserTitle($auditors_cancel->created_cancel)) ): ?>
        <div class="row">
        <div class="col-md-4 text-right">
            <p class="text-nowrap">ผู้ยกเลิก</p>
        </div>
        <div class="col-md-7 text-left">
                <?php echo e(HP::UserTitle($auditors_cancel->created_cancel)->FullName); ?>

        </div>
        </div>
    <?php endif; ?>
    <?php if(isset($auditors_cancel->date_cancel)): ?>
        <div class="row">
        <div class="col-md-4 text-right">
            <p class="text-nowrap">วันที่ยกเลิก</p>
        </div>
        <div class="col-md-7 text-left">
            <?php echo e(HP::DateThai($auditors_cancel->date_cancel)  ?? '-'); ?>

        </div>
        </div>
    <?php endif; ?>
    <?php if(isset($auditors_cancel->reason_cancel)): ?>
        <div class="row">
        <div class="col-md-4 text-right">
            <p class="text-nowrap">เหตุผลที่ยกเลิก</p>
        </div>
        <div class="col-md-7 text-left">
            <?php echo e($auditors_cancel->reason_cancel   ?? '-'); ?>

        </div>
        </div>
    <?php endif; ?>
<?php endif; ?>