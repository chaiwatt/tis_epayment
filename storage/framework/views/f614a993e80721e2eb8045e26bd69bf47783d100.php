
 
<?php if(count($assessment->CertiCBHistorys) >= 2 ): ?>

<div class="row form-group">
    <div class="col-md-12">
     <div class="white-box" style="border: 2px solid #e5ebec;">
         <legend><h3>ประวัติบันทึกแก้ไขข้อบกพร่อง/ข้อสังเกต</h3></legend>  
<div class="row">
    <div class="col-md-12">
         <div class="panel block4">
            <div class="panel-group" id="accordion">
               <div class="panel panel-info">
                   <div class="panel-heading">
                    <h4 class="panel-title">
                         <a data-toggle="collapse" data-parent="#accordion" href="#collapse"> <dd>ประวัติบันทึกแก้ไขข้อบกพร่อง/ข้อสังเกต</dd>  </a>
                    </h4>
                  </div>
  
<div id="collapse" class="panel-collapse collapse ">
    <br>
 <?php $__currentLoopData = $assessment->CertiCBHistorys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key1 => $item1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

 <div class="row form-group">
     <div class="col-md-12">
        <div class="white-box" style="border: 2px solid #e5ebec;">
   <legend><h3> ครั้งที่ <?php echo e($key1 +1); ?> </h3></legend>

   <div class="container-fluid">
    <?php if(!is_null($item1->details_two)): ?>
    <?php 
        $details_two = json_decode($item1->details_two);
    ?> 
    <table class="table color-bordered-table primary-bordered-table table-bordered">
        <thead>
            <tr>
                <th class="text-center" width="2%">ลำดับ</th>
                <th class="text-center" width="15%">รายงานที่</th>
                <th class="text-center" width="15%">ผลการประเมินที่พบ</th>
                <th class="text-center" width="15%">
                    <?php echo e(!empty($assessment->CertiCBCostTo->FormulaTo->title) ?   
                                $assessment->CertiCBCostTo->FormulaTo->title :''); ?>

                </th>
                <th class="text-center" width="10%">ประเภท</th>
                <th class="text-center" width="20%">แนวทางการแก้ไข</th>

                <?php if($key1 > 0): ?> 
                <th class="text-center" width="25%" >หลักฐาน</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
          <?php if(!is_null($details_two)): ?>
            <?php $__currentLoopData = $details_two; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key2 => $item2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
             $type =   ['1'=>'ข้อบกพร่อง','2'=>'ข้อสังเกต'];
            ?>
            <tr>
                <td class="text-center"><?php echo e($key2+ 1); ?></td>
                <td>
                    <?php echo e($item2->report ?? null); ?>

                </td>
                <td>
                     <?php echo e($item2->remark ?? null); ?>

                </td>
                <td>
                    <?php echo e($item2->no ?? null); ?>

                </td>
                <td>
                    <?php echo e(array_key_exists($item2->type,$type) ? $type[$item2->type] : '-'); ?>  
                </td>
              
                <td>
                    <?php echo e(@$item2->details ?? null); ?>

                    <br>
                    <?php if($item2->status == 1): ?> 
                      <label for="app_name"> <span> <i class="fa fa-check-square" style="font-size:20px;color:rgb(0, 255, 42)"></i></span> ผ่าน </label> 
                    <?php elseif(!is_null($item2->comment)): ?> 
                    <label for="app_name"><span>  <i class="fa  fa-close" style="font-size:20px;color:rgb(255, 0, 0)"></i> <?php echo e('ไม่ผ่าน:'.$item2->comment ?? null); ?></span> </label> 
                   <?php endif; ?>
                </td>
                <?php if($key1 > 0): ?> 
                  <td>
                         <?php if($item2->status == 1): ?> 
                                     <?php if($item2->file_status == 1): ?>
                                              <span> <i class="fa fa-check-square" style="font-size:20px;color:rgb(0, 255, 42)"></i> ผ่าน</span>  
                                     <?php elseif(isset($item2->file_comment)): ?>
                                            <?php if(!is_null($item2->file_comment)): ?>
                                              <span> <i class="fa  fa-close" style="font-size:20px;color:rgb(255, 0, 0)"></i> ไม่ผ่าน </span> 
                                              <?php echo " : ".$item2->file_comment ?? null; ?>

                                            <?php endif; ?>
                                    <?php endif; ?>
                                <label for="app_name">
                                    <span>
                                         <?php if($item2->attachs !='' && HP::checkFileStorage($attach_path.$item2->attachs)): ?>
                                                <a href="<?php echo e(url('certify/check/file_cb_client/'.$item2->attachs.'/'.( !empty($item2->attach_client_name) ? $item2->attach_client_name :   basename($item2->attachs) ))); ?>" 
                                                     title="<?php echo e(!empty($item2->attach_client_name) ? $item2->attach_client_name :  basename($item2->attachs)); ?>" target="_blank">
                                                    <?php echo HP::FileExtension($item2->attachs)  ?? ''; ?>

                                                </a>
                                        <?php endif; ?>
                                    </span> 
                                </label> 
                        <?php endif; ?>
                 </td>
                <?php endif; ?>
              
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
          <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if(!is_null($item1->details_three)): ?> 
    <div class="row">
    <div class="col-md-3 text-right">
        <p class="text-nowrap">รายงานการตรวจประเมิน :</p>
    </div>
    <div class="col-md-9">
        <p>
            
              <a href="<?php echo e(url('certify/check/file_cb_client/'.$item1->details_three.'/'.( !empty($item1->file_client_name) ? $item1->file_client_name :  basename($item1->details_three) ))); ?>" 
                  title="<?php echo e(!empty($item1->file_client_name) ? $item1->file_client_name :  basename($item1->details_three)); ?>" target="_blank">
                <?php echo HP::FileExtension($item1->details_three)  ?? ''; ?>

              </a> 
            
        </p>
    </div>
    </div>
    <?php endif; ?>

    <?php if(!is_null($item1->file)): ?> 
    <div class="row">
    <div class="col-md-3 text-right">
        <p class="text-nowrap">ไฟล์แนบ :</p>
    </div>
    <div class="col-md-9">
            <?php 
                $files = json_decode($item1->file);
            ?>  
            <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 
                    <a href="<?php echo e(url('certify/check/file_cb_client/'.$item2->file.'/'.( !empty($item2->file_client_name) ? $item2->file_client_name :  basename($item2->file) ))); ?>" 
                            title="<?php echo e(!empty($item2->file_client_name) ? $item2->file_client_name :  basename($item2->file)); ?>" target="_blank">
                        <?php echo HP::FileExtension($item2->file)  ?? ''; ?>

                    </a> 
                 
 
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    </div> 
    <?php endif; ?>
    <?php if(!is_null($item1->attachs_car)): ?> 
    <div class="row">
    <div class="col-md-3 text-right">
        <p class="text-nowrap"> รายงานปิด Car :</p>
    </div>
    <div class="col-md-9">
        <p>
            
                 <a href="<?php echo e(url('certify/check/file_cb_client/'.$item1->attachs_car.'/'.( !empty($item1->attach_client_name) ? $item1->attach_client_name : basename($item1->attachs_car) ))); ?>" 
                    title="<?php echo e(!empty($item1->attach_client_name) ? $item1->attach_client_name :  basename($item1->attachs_car)); ?>" target="_blank">
                    <?php echo HP::FileExtension($item1->attachs_car)  ?? ''; ?>

                </a>
            
        </p>
    </div>
    </div>
    <?php endif; ?>

    <?php if(!is_null($item1->created_at)): ?> 
    <div class="row">
    <div class="col-md-3 text-right">
        <p class="text-nowrap">วันที่เจ้าหน้าที่บันทึก :</p>
    </div>
    <div class="col-md-9">
        <?php echo e(@HP::DateThai($item1->created_at) ?? '-'); ?>

    </div>
    </div>
    <?php endif; ?>

    <?php if(!is_null($item1->date)): ?> 
    <div class="row">
    <div class="col-md-3 text-right">
        <p class="text-nowrap">วันที่ผู้ประกอบการบันทึก :</p>
    </div>
    <div class="col-md-9">
        <?php echo e(@HP::DateThai($item1->date) ?? '-'); ?>

    </div>
    </div>
    <?php endif; ?>


  </div>


        </div>
    </div>
</div>


<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
 </div>
            </div>
        </div>
    </div>
</div>


        </div>
    </div>
</div>
</div>
<?php endif; ?>