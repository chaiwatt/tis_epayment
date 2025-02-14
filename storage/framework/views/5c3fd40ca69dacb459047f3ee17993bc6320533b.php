

<?php if(!is_null($history->details_five)): ?> 
<?php 
    $payin1 = json_decode($history->details_five);
?>
<div class="row">
    <div class="col-sm-4 text-right"> <b>เงื่อนไขการชำระเงิน :</b></div>
    <div class="col-sm-7">
        <p>  
         <?php if(!empty($payin1->conditional_type)): ?>
             <?php if($payin1->conditional_type == 1): ?> <!--  หลักฐานค่าธรรมเนียม -->
                หลักฐานค่าธรรมเนียม
            <?php elseif($payin1->conditional_type == 2): ?> <!--  ยกเว้นค่าธรรมเนียม -->  
                ยกเว้นค่าธรรมเนียม
            <?php elseif($payin1->conditional_type == 3): ?> <!--  ยกเว้นค่าชำระเงินนอกระบบ, ไม่เรียกชำระเงิน หรือ กรณีอื่นๆธรรมเนียม --> 
                ยกเว้นค่าชำระเงินนอกระบบ, ไม่เรียกชำระเงิน หรือ กรณีอื่นๆธรรมเนียม
            <?php endif; ?>
        <?php else: ?> 
             หลักฐานค่าธรรมเนียม   
        <?php endif; ?>
        </p>
    </div>
    </div>
<?php endif; ?>

<?php if(!is_null($history->details_three)): ?>
<div class="row">
  <div class="col-md-4 text-right">
      <p class="text-nowrap">ชื่อคณะผู้ตรวจประเมิน :</p>
  </div>
  <div class="col-md-8 text-left">
    <?php echo e($history->details_three ?? null); ?>

  </div>
</div>
<?php endif; ?>

<?php if(!empty($payin1->auditors_id)): ?> 
<div class="row">
<div class="col-md-4 text-right">
    <p class="text-nowrap">วันที่ตรวจประเมิน :</p>
</div>
<div class="col-md-8 text-left">
 <?php
     $auditors =  App\Models\Certify\ApplicantCB\CertiCBAuditors::where('id',$payin1->auditors_id)->first();
 ?>
 <?php echo e(!empty($auditors->CertiCBAuditorsDateTitle) ? $auditors->CertiCBAuditorsDateTitle  : null); ?>

</div>
</div>
<?php endif; ?>

 <?php if(!is_null($history->details_one)): ?> 
 <div class="row">
 <div class="col-md-4 text-right">
     <p class="text-nowrap">จำนวนเงิน :</p>
 </div>
 <div class="col-md-8 text-left">
     <?php echo e(number_format($history->details_one,2).' บาท' ?? '-'); ?>

 </div>
 </div>
 <?php endif; ?>

 <?php if(!is_null($history->details_five) && !empty($payin1)): ?> 
 
     <?php if($payin1->conditional_type == 1): ?> <!--  หลักฐานค่าธรรมเนียม -->
        <?php if(!is_null($history->details_two)): ?> 
            <div class="row">
                <div class="col-md-4 text-right">
                    <p class="text-nowrap"> วันที่แจ้งชำระ :</p>
                </div>
                <div class="col-md-8 text-left">
                    <?php echo e(@HP::DateThai($history->details_two) ?? '-'); ?>

                </div>
            </div>
        <?php endif; ?>
        <?php if(!is_null($history->attachs)): ?>
            <div class="row">
            <div class="col-md-4 text-right">
            <p class="text-nowrap">ค่าบริการในการตรวจประเมิน:</p>
            </div>
            <div class="col-md-8 text-left">
                <p> 
                    <?php if(isset($history->attachs)): ?>
                    <a href="<?php echo e(url('certify/check/file_cb_client/'.$history->attachs.'/'.( !empty($history->attach_client_name) ? $history->attach_client_name : basename($history->attachs) ))); ?>" target="_blank">
                    <?php echo HP::FileExtension($history->attachs)  ?? ''; ?>

                    <?php echo e(@basename($history->attachs)); ?> 
                    </a>
                    <?php endif; ?>
                </p>
            </div>
            </div>
        <?php endif; ?>
    <?php elseif($payin1->conditional_type == 2): ?> <!--  ยกเว้นค่าธรรมเนียม -->
        <div class="row">
            <div class="col-sm-4 text-right"> <b>วันที่แจ้งชำระ :</b></div>
            <div class="col-sm-6">
                <p>  
                    <?php echo e(!empty($payin1->start_date_feewaiver) && !empty($payin1->end_date_feewaiver) ? HP::DateFormatGroupTh($payin1->start_date_feewaiver,$payin1->end_date_feewaiver) :  '-'); ?>

                </p>
            </div>
            </div>   
            <?php if(!is_null($history->attachs)): ?> 
            <div class="row">
            <div class="col-sm-4 text-right"> <b>ใบแจ้งหนี้ค่าธรรมเนียม :</b></div>
            <div class="col-sm-6 text-left">
                <p>  
                    <a href="<?php echo e(url('funtions/get-view-file/'.base64_encode($history->attachs).'/'.( !empty($history->attach_client_name) ? $history->attach_client_name : basename($history->attachs) ))); ?>" target="_blank">
                        <?php echo HP::FileExtension($history->attachs)  ?? ''; ?>

                    </a>
                </p>
            </div>
            </div>
            <?php endif; ?>
    <?php elseif($payin1->conditional_type == 3): ?> <!--  ยกเว้นค่าชำระเงินนอกระบบ, ไม่เรียกชำระเงิน หรือ กรณีอื่นๆธรรมเนียม -->
         <div class="row">
            <div class="col-md-4 text-right">
                <p class="text-nowrap"> หมายเหตุ :</p>
            </div>
            <div class="col-md-8 text-left">
                 <?php echo e(!empty($payin1->detail)  ? $payin1->detail :  '-'); ?>

            </div>
        </div>
        <?php if(!is_null($history->attachs)): ?>
            <div class="row">
            <div class="col-md-4 text-right">
            <p class="text-nowrap">ไฟล์แนบ:</p>
            </div>
            <div class="col-md-8 text-left">
                <p> 
                    <?php if(isset($history->attachs)): ?>
                    <a href="<?php echo e(url('certify/check/file_cb_client/'.$history->attachs.'/'.( !empty($history->attach_client_name) ? $history->attach_client_name : basename($history->attachs) ))); ?>" target="_blank">
                    <?php echo HP::FileExtension($history->attachs)  ?? ''; ?>

                    <?php echo e(@basename($history->attachs)); ?> 
                    </a>
                    <?php endif; ?>
                </p>
            </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

<?php else: ?>


    <?php if(!is_null($history->details_two)): ?> 
        <div class="row">
        <div class="col-md-4 text-right">
            <p class="text-nowrap"> วันที่แจ้งชำระ :</p>
        </div>
        <div class="col-md-8 text-left">
            <?php echo e(@HP::DateThai($history->details_two) ?? '-'); ?>

        </div>
        </div>
    <?php endif; ?>

    <?php if(!is_null($history->attachs)): ?>
        <div class="row">
        <div class="col-md-4 text-right">
        <p class="text-nowrap">ค่าบริการในการตรวจประเมิน:</p>
        </div>
        <div class="col-md-8 text-left">
            <p> 
                
                <a href="<?php echo e(url('certify/check/file_cb_client/'.$history->attachs.'/'.( !empty($history->attach_client_name) ? $history->attach_client_name : basename($history->attachs) ))); ?>" target="_blank">
                    <?php echo HP::FileExtension($history->attachs)  ?? ''; ?>

                    <?php echo e(@basename($history->attachs)); ?> 
                </a>
                
        
            </p>
        </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if(!is_null($history->attachs_file)): ?>
<div class="row">
<div class="col-md-4 text-right">
<p class="text-nowrap">หลักฐานการชำระเงินค่าตรวจประเมิน:</p>
</div>
<div class="col-md-8 text-left">
    <p> 
      
         <a href="<?php echo e(url('certify/check/file_cb_client/'.$history->attachs_file.'/'.( !empty($history->evidence) ? $history->evidence : basename($history->attachs_file) ))); ?>" target="_blank">
            <?php echo HP::FileExtension($history->attachs_file)  ?? ''; ?>

            <?php echo e(@basename($history->attachs_file)); ?>

        </a> 
     
    </p>
</div>
</div>
<?php endif; ?>
 
<?php if(!is_null($history->status)): ?>
<div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">ตรวจสอบการชำค่าตรวจประเมิน :</p> 
    </div>
    <div class="col-md-8 text-left">
        <label  >   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" <?php echo e(($history->status == 1 ) ? 'checked' : ' '); ?>>  &nbsp; ได้รับการชำระเงินค่าตรวจประเมินเรียบร้อยแล้ว  &nbsp;</label>
        <label >   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" <?php echo e(($history->status != 1 ) ? 'checked' : ' '); ?>>  &nbsp;ยังไม่ได้ชำระเงิน  &nbsp;</label>
    </div>
</div> 
<?php endif; ?>

<?php if(!is_null($history->details_four)): ?> 
<div class="row">
<div class="col-md-4 text-right">
    <p class="text-nowrap">หมายเหตุ :</p>
</div>
<div class="col-md-8 text-left">
    <?php echo e($history->details_four ?? '-'); ?>

</div>
</div>
<?php endif; ?>


 <?php if(!is_null($history->created_at)): ?> 
 <div class="row">
 <div class="col-md-4 text-right">
     <p class="text-nowrap">วันที่บันทึก :</p>
 </div>
 <div class="col-md-8 text-left">
     <?php echo e(@HP::DateThai($history->created_at) ?? '-'); ?>

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