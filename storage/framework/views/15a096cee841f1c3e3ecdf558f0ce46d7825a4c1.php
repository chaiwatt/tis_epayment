<?php if(!is_null($history->details)): ?> 
<div class="row">
  <div class="col-md-4 text-right">
     <p class="text-nowrap">ชื่อคณะผู้ตรวจประเมิน :</p>
  </div>
  <div class="col-md-7">
    <span><?php echo e($history->details ?? '-'); ?></span> 
  </div>
 </div>
 <?php endif; ?>  

 <?php if(!is_null($history->DataBoardAuditorDateTitle)): ?> 
 <div class="row">
   <div class="col-md-4 text-right">
      <p class="text-nowrap">วันที่ตรวจประเมิน :</p>
   </div>
   <div class="col-md-7">
     <span>   <?php echo @$history->DataBoardAuditorDateTitle  ?? '-'; ?>  </span> 
   </div>
  </div>
<?php endif; ?>  


<?php if(!is_null($history->file)): ?> 
<div class="row">
  <div class="col-md-4 text-right">
     <p class="text-nowrap">บันทึก ลมอ.  แต่งตั้งคณะผู้ตรวจประเมิน :</p>
  </div>
  <div class="col-md-7">
    <span>  
        <a href="<?php echo e(url('certify/check/file_client/'.$history->file.'/'.( !empty($history->file_client_name) ? $history->file_client_name :   basename($history->file) ))); ?>" target="_blank">
           <?php echo HP::FileExtension($history->file)  ?? ''; ?>

       </a>
   </span> 
  </div>
 </div>
<?php endif; ?>  

<?php if(!is_null($history->attachs)): ?> 
<div class="row">
 <div class="col-md-4 text-right">
    <p class="text-nowrap">กำหนดการตรวจประเมิน :</p>
 </div>
 <div class="col-md-7">
    <span>  
       <a href="<?php echo e(url('certify/check/file_client/'.$history->attachs.'/'.( !empty($history->attach_client_name) ? $history->attach_client_name : basename($history->attachs) ))); ?>" target="_blank">
          <?php echo HP::FileExtension($history->attachs)  ?? ''; ?>

      </a>
   </span> 
 </div>
</div>
<?php endif; ?>  

<div class="col-md-12">
  <label>โดยคณะผู้ตรวจประเมิน มีรายนามดังต่อไปนี้</label>
</div>

<?php if(!is_null($history->details_table)): ?>
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
           $groups = json_decode($history->details_table);
         ?>
          <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key2 => $item2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                 $status = App\Models\Bcertify\StatusAuditor::where('id',$item2->status)->first();
            ?>
          <tr>
              <td  class="text-center"><?php echo e($key2 +1); ?></td>
              <td> <?php echo e($status->title ?? '-'); ?></td>
              <td>
                <?php if(count($item2->temp_users) > 0): ?> 
                    <?php $__currentLoopData = $item2->temp_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key3 => $item3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($item3 ?? '-'); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
              </td>
              <td>
                <?php if(count($item2->temp_departments) > 0): ?> 
                    <?php $__currentLoopData = $item2->temp_departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key4 => $item4): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($item4 ?? '-'); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
              </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<?php if(!is_null($history->details_cost_confirm)): ?>
<?php
  $details_cost_confirm = json_decode($history->details_cost_confirm);
?>
<div class="col-md-12">
  <label>ประมาณค่าใช้จ่าย</label>
</div>
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
      <?php $__currentLoopData = $details_cost_confirm; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php     
          $amount_date = !empty($item3->amount_date) ? $item3->amount_date : 0 ;
          $amount = !empty($item3->amount) ? $item3->amount : 0 ;
          $sum =   $amount*$amount_date;
          $SumAmount  +=  $sum;
          $details =   App\Models\Bcertify\StatusAuditor::where('id',$item3->desc)->first();
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
    <p class="text-nowrap">กำหนดการตรวจประเมิน :</p>
 </div>
 <div class="col-md-7">
    <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" <?php echo e(($history->status == 1 ) ? 'checked' : ' '); ?>>  &nbsp;เห็นชอบดำเนินการแต่งตั้งคณะผู้ตรวจประเมินต่อไป &nbsp;</label>
    <br>
    <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" <?php echo e(($history->status == 2 ) ? 'checked' : ' '); ?>>  &nbsp;ไม่เห็นชอบ เพราะ  &nbsp;</label>
 </div>
</div>
<?php endif; ?>  

<?php if(!is_null($history->remark)): ?> 
<div class="row">
<div class="col-md-4 text-right">
   <p class="text-nowrap">หมายเหตุ :</p>
</div>
<div class="col-md-7">
    <?php echo e(@$history->remark  ?? '-'); ?>

</div>
</div>
<?php endif; ?>  

<?php if(!is_null($history->attachs_file)): ?> 
<?php 
$attachs_file = json_decode($history->attachs_file);
?> 
<div class="row">
<div class="col-md-4 text-right">
  <p class="text-nowrap">หลักฐาน :</p>
</div>
<div class="col-md-7">
  <?php $__currentLoopData = $attachs_file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $files): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <p> 
        <?php echo e(@$files->file_desc); ?>

        <a href="<?php echo e(url('certify/check/files/'.$files->file)); ?>"  target="_blank"> 
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
           <p class="text-nowrap">วันที่บันทึก :</p>
     </div>
      <div class="col-md-7">
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
