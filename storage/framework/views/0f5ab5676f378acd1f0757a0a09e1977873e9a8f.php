

<?php if(!is_null($history->details)): ?>
    <?php 
       $details =json_decode($history->details);
    ?>
<?php endif; ?>
<?php if(isset($details->notification_date)): ?>
<div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">วันที่แจ้งชำระ :</p>
    </div>
    <div class="col-md-4">
        <p>  
            <?php echo e(!empty($details->notification_date) ? @HP::DateThai($details->notification_date) : null); ?>

       </p>
    </div>
 </div>
<?php endif; ?>
 
<?php if(!is_null($history->attachs)): ?>
<div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">ใบแจ้งหนี้ค่าธรรมเนียม :</p>
    </div>
    <div class="col-md-4">
        <p>  
            <?php echo e(isset($details->amount_fee) ? number_format($details->amount_fee,2).'บาท' : '0.00'); ?>

            <a href="<?php echo e(url('certify/check/file_client/'.$history->attachs.'/'.( !empty($history->attach_client_name) ? $history->attach_client_name :  basename($history->attachs) ))); ?>" 
                title=" <?php echo e(!empty($history->attach_client_name) ? $history->attach_client_name : basename($history->attachs)); ?>"  target="_blank"> 
                <?php echo HP::FileExtension($history->attachs)  ?? ''; ?>

           </a>
        </p> 
    </div>
 </div>
<?php endif; ?>


<?php if(!is_null($history->attachs_file)): ?>
<div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">หลักฐานการชำระเงินค่าธรรมเนียม :</p>
    </div>
    <div class="col-md-4">
        <a href="<?php echo e(url('certify/check/file_client/'.$history->attachs_file.'/'.( !empty($history->evidence) ? $history->evidence :  basename($history->attachs_file) ))); ?>" 
            title=" <?php echo e(!empty($history->attach_client_name) ? $history->evidence : basename($history->attachs_file)); ?>"  target="_blank"> 
            <?php echo HP::FileExtension($history->attachs_file)  ?? ''; ?>

        </a>
    </div>
 </div>
<?php endif; ?>


<?php if(isset($details->detail)): ?>
<div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">หมายเหตุ :</p>
    </div>
    <div class="col-md-4">
        <p>  
           <?php echo e($details->detail ?? '-'); ?>

        </p>
    </div>
 </div>
<?php endif; ?>

<?php if(!is_null($history->status)): ?> 
<div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">ตรวจสอบการชำระ :</p>
    </div>
    <div class="col-md-4">
        <label  >   <input type="radio" class="check check-readonly" data-radio="iradio_square-green" <?php echo e(($history->status == 1 ) ? 'checked' : ' '); ?>>  &nbsp; รับชำระเงินเรียบร้อยแล้ว  &nbsp;</label>
        <label >   <input type="radio" class="check check-readonly" data-radio="iradio_square-red" <?php echo e(($history->status != 1 ) ? 'checked' : ' '); ?>>  &nbsp;ยังไม่ชำระเงิน  &nbsp;</label>
    </div>
 </div>
 <?php endif; ?>

<?php if(!is_null($history->date)): ?> 
    <div class="row">
    <div class="col-md-4 text-right">
        <p class="text-nowrap">วันที่บันทึก :</p>
    </div>
    <div class="col-md-4">
        <?php echo e(@HP::DateThai($history->date) ?? '-'); ?>

    </div>
    </div>
<?php endif; ?>

