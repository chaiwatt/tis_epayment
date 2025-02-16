

 <?php if(!is_null($history->details)): ?>
<?php 
       $details = json_decode($history->details);
       $notice =  App\Models\Certify\Applicant\Notice::find($history->ref_id);
       $app =     App\Models\Certify\Applicant\CertiLab::where('app_no',@$history->app_no)->first();
 ?>   
 <div class="row">
  <div class="col-md-2 text-right">
     <p class="text-nowrap">เลขคำขอ :</p>
  </div>
  <div class="col-md-4">
       <?php echo e($app->app_no ??  null); ?>

  </div>
  <div class="col-md-2 text-right">
     <p class="text-nowrap">หน่วยงาน :</p>
  </div>
  <div class="col-md-4">
    <?php echo e(!empty($app->BelongsInformation->name) ? $app->BelongsInformation->name: null); ?>

  </div>
</div>

 <div class="row">
  <div class="col-md-2 text-right">
     <p class="text-nowrap">ชื่อห้องปฏิบัติการ :</p>
  </div>
  <div class="col-md-4">
    <?php echo e(!empty($app->lab_name) ? $app->lab_name : null); ?>

  </div>
  <div class="col-md-2 text-right">
     <p class="text-nowrap">วันที่ทำรายงาน :</p>
  </div>
  <div class="col-md-4">
       <?php echo e(!empty($details->assessment_date) ? @HP::DateThai(date("Y-m-d",strtotime($details->assessment_date))) : null); ?>

  </div>
</div>

 <div class="row">
  <div class="col-md-3 text-right">
     <p class="text-nowrap">รายงานการตรวจประเมิน :</p>
  </div>
  <div class="col-md-2">
    <?php if(!is_null($history->file) ): ?> 
        <p> 
            <a href="<?php echo e(url('certify/check/file_client/'.$history->file.'/'.( !empty($history->file_client_name) ? $history->file_client_name : basename($history->file)))); ?>" 
                title=" <?php echo e(!empty($history->file_client_name) ? $history->file_client_name : basename($history->file)); ?>"   target="_blank">
                <?php echo HP::FileExtension($history->file)  ?? ''; ?>

            </a>
         </p>
      <?php endif; ?>
  </div>

  <?php if(!is_null($history->attachs)): ?>
  <?php 
       $attachs = json_decode($history->attachs);
  ?>  
    <div class="col-md-2 text-right">
        <p class="text-nowrap">ไฟล์แนบ :</p>
    </div>
    <div class="col-md-4">
        <?php $__currentLoopData = $attachs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key1 => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>   
            <p> 
                <a href="<?php echo e(url('certify/check/file_client/'.$item->attachs.'/'.( !empty($item->attachs_client_name) ? $item->attachs_client_name : basename($history->attachs) ))); ?>" 
                    title=" <?php echo e(!empty($item->attachs_client_name) ? $item->attachs_client_name :  basename($item->attachs)); ?>"  target="_blank">
                    <?php echo HP::FileExtension($item->attachs)  ?? ''; ?>

                </a>
            </p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  <?php endif; ?>
  
</div>

<div class="row">
    <div class="col-md-2 text-right">
       <p class="text-nowrap">รายงานข้อบกพร่อง :</p>
    </div>
    <div class="col-md-10">
      <?php if($details->report_status == 1): ?>
          <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" checked>  &nbsp; มี &nbsp;</label>
          <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-red">  &nbsp;ไม่มี &nbsp;</label>
      <?php else: ?> 
          <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-green">  &nbsp; มี &nbsp;</label>
          <label>   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" checked>  &nbsp;ไม่มี &nbsp;</label>
      <?php endif; ?>

    </div>
</div>
<?php endif; ?>



<?php if(!is_null($history->details_table)): ?> 
<?php 
$details_table = json_decode($history->details_table);
?>
<?php if(!is_null($details_table)): ?>
<div class="row">
<div class="col-sm-12 m-t-15" >
   <table class="table color-bordered-table primary-bordered-table">
       <thead>
       <tr>
          <th class="text-center bg-info  text-white" width="2%">ลำดับ</th>
          <th class="text-center bg-info  text-white" width="15%">รายงานที่</th>
          <th class="text-center bg-info  text-white" width="15%">ผลการประเมินที่พบ</th>
          <th class="text-center bg-info  text-white" width="15%">มอก. 17025 : ข้อ</th>
          <th class="text-center bg-info  text-white" width="10%">ประเภท</th>
          <th class="text-center bg-info  text-white" width="33%">แนวทางการแก้ไข</th>
          <th class="text-center bg-info  text-white" width="20%" >หลักฐาน</th>
       </tr>
       </thead>
       <tbody >
         <?php $__currentLoopData = $details_table; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key1 => $item1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>   
             <?php 
               $type =   ['1'=>'ข้อบกพร่อง','2'=>'ข้อสังเกต'];
           ?>
             <tr>
                <td class="text-center"><?php echo e($key1+1); ?></td>
                <td>
                    <?php echo e($item1->report ?? null); ?>

                </td>
                <td>
                    <?php echo e($item1->remark ?? null); ?>

                </td>
                <td>
                    <?php echo e($item1->no ?? null); ?>

                </td>
                <td>
                    <?php echo e(array_key_exists($item1->type,$type) ? $type[$item1->type] : '-'); ?>  
                </td>
              
                <td>
                   <?php echo e(@$item1->details ?? null); ?>

                    <br>
                    <?php if($item1->status == 1): ?> 
                      <label for="app_name"> <span> <i class="fa fa-check-square" style="font-size:20px;color:rgb(0, 255, 42)"></i></span> ผ่าน </label> 
                    <?php elseif(!is_null($item1->comment)): ?> 
                    <label for="app_name"><span>  <i class="fa  fa-close" style="font-size:20px;color:rgb(255, 0, 0)"></i> <?php echo e('ไม่ผ่าน:'.$item1->comment ?? null); ?></span> </label>
                    <?php endif; ?>
                </td>
                <td>
                       <?php if($item1->status == 1): ?> 
                       <?php if($item1->file_status == 1): ?>
                           <span> <i class="fa fa-check-square" style="font-size:20px;color:rgb(0, 255, 42)"></i> ผ่าน</span>  
                                <?php elseif(isset($item1->comment_file)): ?>
                                      <?php if(!is_null($item1->comment_file)): ?>
                                        <span> <i class="fa  fa-close" style="font-size:20px;color:rgb(255, 0, 0)"></i> ไม่ผ่าน </span> 
                                        <?php echo " : ".$item1->comment_file ?? null; ?>

                                      <?php endif; ?>
                              <?php endif; ?>
                          <label for="app_name">
                              <span>
                               
                                  <?php if(!is_null($item1->attachs) && isset($item1->attachs) ): ?>
                                     <a href="<?php echo e(url('certify/check/file_client/'.$item1->attachs.'/'.( !empty($item1->attachs_client_name) ? $item1->attachs_client_name :  basename($item1->attachs) ))); ?>" target="_blank">
                                        <?php echo HP::FileExtension($item1->attachs)  ?? ''; ?>

                                    </a>
                                    <?php endif; ?>
                              </span> 
                          </label> 
                       <?php endif; ?>
                  </td>
               </tr> 
         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </tbody>
   </table>
</div>
</div>
<?php endif; ?>   
<?php endif; ?> 





<?php if(!is_null($history->date)): ?> 
<div class="row">
<div class="col-md-2 text-right">
    <p class="text-nowrap">วันที่บันทึก :</p>
</div>
<div class="col-md-4">
    <?php echo e(@HP::DateThai($history->date) ?? '-'); ?>

</div>
</div>
<?php endif; ?>