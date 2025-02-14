<?php $__env->startPush('css'); ?>
    <link href="<?php echo e(asset('plugins/components/icheck/skins/all.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')); ?>" rel="stylesheet" type="text/css" />
    <!-- Data Table CSS -->
    <link href="<?php echo e(asset('plugins/components/datatables/jquery.dataTables.min.css')); ?>" rel="stylesheet" type="text/css"/>
    <style type="text/css">
        .form_group {
            margin-bottom: 10px;
        }

    .swal-btn {
        font-size: 16px !important;
        padding: 10px 18px !important;
    }
</style>

<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">

                <h3 class="box-title pull-left">ระบบตรวจสอบคำขอหน่วยรับรอง landing <?php echo e($certi_cb->app_no ?? null); ?> </h3>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-'.str_slug('checkcertificatecb'))): ?>
                    <a class="btn btn-success pull-right" href="<?php echo e(url('/certify/check_certificate-cb')); ?>">
                        <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                    </a>
                <?php endif; ?>
                <div class="clearfix"></div>
                <hr>


<div class="row">
    <div class="col-sm-12 ">


                <a class="form_group btn <?php echo e(($certi_cb->status >= 6) ? 'btn-info' : 'btn-warning'); ?> "   href="<?php echo e(url('certify/check_certificate-cb/show/'.$certi_cb->app_no)); ?>" >
                    <i class="fa fa-search" aria-hidden="true"></i> คำขอ
                </a>

                <!-- START  admin , ผอ , ผก , เจ้าหน้าที่ CB -->
                <?php if(auth()->user()->SetRolesLicenseCertify() == "true" ||  in_array("29",auth()->user()->RoleListId)): ?>  

                    <?php if($certi_cb->status >= 6 && !is_null($certi_cb->CertiCBCostTo)): ?>
                        <?php 
                            $Cost = $certi_cb->CertiCBCostTo;
                            $cost_btn =  '';
                            $cost_icon =  '';
                    
                            if($Cost->check_status == 1   &&  $Cost->status_scope  == 1 ){//ผ่านการประมาณค่าใช้จ่ายแล้ว
                                $cost_btn = 'btn-info';
                                $cost_icon =  '<i class="fa fa-check-square-o"></i>';
                            }elseif($Cost->draft == 1  &&  $Cost->vehicle  == 1 ){  // ส่งให้ ผปก. แล้ว 
                                $cost_btn = 'btn-success';
                                $cost_icon =  '<i class="fa fa-file-text"></i>';
                            }elseif($Cost->check_status == 2   || $Cost->status_scope  == 2 ){    // ผปก. ส่งมา
                                $cost_btn = 'btn-danger';
                                $cost_icon =  '<i class="fa fa-arrow-circle-right"></i>';
                            }else{
                                $cost_btn = 'btn-warning'; 
                            }
                        ?>
                        <a  class="form_group btn <?php echo e($cost_btn); ?>" href="<?php echo e(url('certify/estimated_cost-cb/'.$Cost->id.'/edit')); ?>">
                            <?php echo $cost_icon; ?>  ค่าใช้จ่าย
                        </a>
                    <?php endif; ?> 

                    <?php
                        $doneDocAuditorAssigment = $certi_cb->doc_auditor_assignment;
                    ?>
                    
                    <?php if($certi_cb->status >= 9 && $doneDocAuditorAssigment != null): ?>
                        <div class="form_group btn-group">
                            <div class="btn-group">
                                <?php if($certi_cb->cbDocReviewAuditor == null): ?>
                                        <button type="button" id="btn_doc_auditor" 
                                            class="btn <?php echo e($doneDocAuditorAssigment == 1 ? 'btn-warning' : 'btn-info'); ?>">
                                            แต่งตั้งคณะผู้ตรวจเอกสาร
                                        </button>
                                    <?php else: ?>
                                    <a href="<?php echo e(route("auditor_cb_doc_review_edit",['id' => $certi_cb->id])); ?>"
                                        class="btn 
                                            <?php if($certi_cb->cbDocReviewAuditor->status == '0'): ?> btn-warning 
                                            <?php elseif($certi_cb->cbDocReviewAuditor->status == '2'): ?> btn-danger 
                                            <?php elseif($certi_cb->cbDocReviewAuditor->status == '1'): ?> btn-info 
                                            <?php else: ?> btn-secondary <?php endif; ?>">
                                        คณะผู้ตรวจเอกสาร
                                    </a>
                                
                                <?php endif; ?>
                                
                            
                            </div>
                        </div>
                    <?php endif; ?>
     
 
                    <!-- START  status 9 -->  
                    <?php if($certi_cb->status >= 9 && ( $doneDocAuditorAssigment == null || $doneDocAuditorAssigment == 2 )): ?>
                        <?php if(count($certi_cb->CertiCBAuditorsManyBy) > 0): ?> 
                            <?php 
                                $auditors_btn =  '';
                                $auditors_icon =  '';
                                if($certi_cb->CertiCBAuditorsStatus == "StatusSent"){
                                    $auditors_btn = 'btn-success';
                                    $auditors_icon =  '<i class="fa fa-file-text"></i>';
                                }elseif($certi_cb->CertiCBAuditorsStatus == "StatusNotView"){
                                    $auditors_btn =  'btn-danger';
                                    $auditors_icon =  '<i class="fa fa-arrow-circle-right"></i>';
                                }elseif($certi_cb->CertiCBAuditorsStatus == "StatusView"){
                                    $auditors_btn = 'btn-info';
                                    $auditors_icon =  '<i class="fa fa-check-square-o"></i>';
                                }else{
                                    $auditors_btn =  'btn-warning';
                                    $auditors_icon =  '';
                                }
                            ?>
                            <div class="form_group btn-group">
                                <div class="btn-group">
                                    <button type="button" class="btn <?php echo e($auditors_btn); ?> dropdown-toggle" data-toggle="dropdown">
                                        แต่งตั้งคณะฯ   <span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu" role="menu" >
                                        <?php if($certi_cb->status == 10): ?>   <!-- อยู่ระหว่างดำเนินการ -->
                                            <form action="<?php echo e(url('/certify/auditor-cb/create')); ?>" method="POST" style="display:inline" > 
                                                <?php echo e(csrf_field()); ?>

                                                <?php echo Form::hidden('certicb_id', (!empty($certi_cb->id) ? $certi_cb->id  : null) , [ 'class' => 'form-control' ]);; ?>

                                                <button class="btn btn-warning" type="submit"   style="width:750px;text-align: left"> 
                                                    <i class="fa fa-plus"></i>    แต่งตั้งคณะฯ
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php 
                                            $i_key = 0;   
                                        ?>
                                        <?php $__currentLoopData = $certi_cb->CertiCBAuditorsManyBy; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php 
                                                $auditors_btn =  '';
                                                if(is_null($item->status)){
                                                    $auditors_btn = 'btn-success';  
                                                }elseif($item->status_cancel == 1){
                                                    $auditors_btn =  '#ffff80';
                                                }elseif($item->status == 1){
                                                    $auditors_btn = 'btn-info';  
                                                }elseif($item->status == 2){
                                                    $auditors_btn = 'btn-danger';  
                                                }
                                            ?>
                                            <?php if($item->status_cancel != 1): ?>
                                                <a  class="btn <?php echo e($auditors_btn); ?> " href="<?php echo e(url("certify/auditor-cb/".$item->id."/edit")); ?>" style="background-color:<?php echo e($auditors_btn); ?>;width:750px;text-align: left">
                                                    ครั้งที่ <?php echo e(($i_key + 1 )); ?> :  
                                                    <?php echo e($item->auditor ?? '-'); ?>

                                                </a> 
                                                <br>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?> 
                            <div class="btn-group form_group">
                                <form action="<?php echo e(url('/certify/auditor-cb/create')); ?>" method="POST" style="display:inline" > 
                                    <?php echo e(csrf_field()); ?>

                                    <?php echo Form::hidden('certicb_id', (!empty($certi_cb->id) ? $certi_cb->id  : null) , [ 'class' => 'form-control' ]);; ?>

                                    <button class="btn btn-warning" type="submit" >
                                        <i class="fa fa-plus"></i>    แต่งตั้งคณะฯ
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <!-- END  status 9 -->  
                <?php endif; ?>

                <!-- START  admin , ผอ , ผก , เจ้าหน้าที่ ลท. -->
                <?php if(auth()->user()->SetRolesLicenseCertify() == "true" ||  in_array("26",auth()->user()->RoleListId)): ?>  
                
                    <!-- Button trigger modal     แนบใบ Pay-in ครั้งที่ 1 -->
                    <?php if(count($certi_cb->CertiCBPayInOneMany) > 0): ?>
                        <?php 
                            $payin1_btn =  '';
                            $payin1_icon =  '';
                        //  dd($certi_cb->CertiCBPayInOneStatus );
                            if($certi_cb->CertiCBPayInOneStatus == "StatePayInOne"){
                                $payin1_btn = 'btn-success';
                                $payin1_icon =  '<i class="fa fa-file-text"></i>';
                            }elseif($certi_cb->CertiCBPayInOneStatus == "StatusPayInOneNotNeat"){
                                $payin1_btn =  'btn-danger';
                                $payin1_icon =  '<i class="fa fa-arrow-circle-right"></i>';
                            }elseif($certi_cb->CertiCBPayInOneStatus == "StatusPayInOneNeat"){
                                $payin1_btn = 'btn-info';
                                $payin1_icon =  '<i class="fa fa-check-square-o"></i>';
                            }else{
                                $payin1_btn =  'btn-warning';
                                $payin1_icon =  '';
                            }
                            
                        ?>

                        <div class="form_group btn-group">
                            <div class="btn-group">
                                <button type="button" class="btn <?php echo e($payin1_btn); ?> dropdown-toggle" data-toggle="dropdown">
                                    <?php echo $payin1_icon; ?>  Pay-in ครั้งที่ 1 <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu" role="menu" >
                                    <?php $key_payin_one = 0;   ?>
                                    <?php $__currentLoopData = $certi_cb->CertiCBPayInOneMany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php 
                                            $payin1_btn =  '';
                                            if(is_null($item->state)){
                                                $payin1_btn = 'btn-warning';  
                                            }elseif($item->status == 1){ // ผ่าน
                                                $payin1_btn = 'btn-info';  
                                            }elseif($item->state == 1){  //จนท. ส่งให้ ผปก.
                                                $payin1_btn = 'btn-success';  
                                            }elseif($item->state == 2){   //ผปก. ส่งให้ จนท.
                                                $payin1_btn = 'btn-danger';  
                                            }
                                        ?>
                                        <?php if($item->status   != 3): ?> 
                                            <a  class="btn <?php echo e($payin1_btn); ?> " href="<?php echo e(url("certify/check_certificate-cb/Pay_In1/".$item->id."/".$certi_cb->token)); ?>" style="width:750px;text-align: left">
                                                ครั้งที่ <?php echo e(($key_payin_one +1)); ?> :  
                                                <?php echo e($item->CertiCBAuditorsTo->auditor ?? '-'); ?>

                                            </a> 
                                            <br>
                                        <?php endif; ?>
                                                                            
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                    <!-- Button trigger modal     แนบใบ Pay-in ครั้งที่ 1 -->
                
                <?php endif; ?>

                <!-- START  admin , ผอ , ผก ,เจ้าหน้าที่ CB  -->
                <?php if((auth()->user()->SetRolesLicenseCertify() == "true" ||  in_array("29",auth()->user()->RoleListId)) && count($certi_cb->CertiCBPayInOneStatusMany)  > 0 ): ?>  

                    <?php if(count($certi_cb->CertiCBSaveAssessmentMany) > 0 ): ?> 
                        <?php 
                            $assessment_btn =  '';
                            $assessment_icon =  '';
                            if($certi_cb->CertiCBSaveAssessmentStatus == "statusInfo"){ 
                                $assessment_btn = 'btn-info';
                                $assessment_icon =  '<i class="fa fa-check-square-o"></i>';
                            }elseif($certi_cb->CertiCBSaveAssessmentStatus == "statusSuccess"){
                                $assessment_btn = 'btn-success';
                                $assessment_icon =  '<i class="fa fa-file-text"></i>';
                            }elseif($certi_cb->CertiCBSaveAssessmentStatus == "statusDanger"){
                                $assessment_btn =  'btn-danger';
                                $assessment_icon =  '<i class="fa fa-arrow-circle-right"></i>';
                            }elseif($certi_cb->CertiCBSaveAssessmentStatus == "statusPrimary"){
                                $assessment_btn =  'btn-primary';
                            }else{
                                $assessment_btn =  'btn-warning';
                                $assessment_icon =  '';
                            }
                        ?>

                        <div class="form_group btn-group">
                            <div class="btn-group">
                                <a  class="btn <?php echo e($assessment_btn); ?>" href="<?php echo e(url("certify/save_assessment-cb")); ?>" >
                                    <?php echo $assessment_icon; ?>    ผลการตรวจประเมิน
                                </a>
                                <button type="button" class="btn  <?php echo e($assessment_btn); ?> dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>

                                <div class="dropdown-menu" role="menu" >
                                    <?php $__currentLoopData = $certi_cb->CertiCBSaveAssessmentMany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $assessment_url =  '';
                                            $assessment_btn =  '';
                                            if ($assessment->degree == 7) { // ผ่านการการประเมิน
                                                $assessment_btn =  'btn-info';
                                                $assessment_url =  'certify/save_assessment-cb/assessment/'.$assessment->id.'/edit';
                                            }elseif ($assessment->degree == 0) {  //ฉบับร่าง
                                                $assessment_btn =  'btn-primary';
                                                $assessment_url =  'certify/save_assessment-cb/'.$assessment->id.'/edit'; 
                                            }elseif (in_array($assessment->degree,[1,3,4,6])) {  //จนท. ส่งให้ ผปก.
                                                $assessment_btn =  'btn-success';
                                                $assessment_url =  'certify/save_assessment-cb/assessment/'.$assessment->id.'/edit';
                                            }elseif ($assessment->degree == 8) {  //จนท. ส่งให้ ผปก.
                                                $assessment_btn =  '#ffff80';
                                                $assessment_url =  'certify/save_assessment-cb/assessment/'.$assessment->id.'/edit';
                                            }else {    //ผปก. ส่งให้ จนท.
                                                $assessment_btn =  'btn-danger';
                                                $assessment_url =  'certify/save_assessment-cb/assessment/'.$assessment->id.'/edit';
                                            }

                                        ?>
                                        <a  class="btn <?php echo e($assessment_btn); ?>  " href="<?php echo e(url("$assessment_url")); ?>"  style="background-color:<?php echo e($assessment_btn); ?>;width:750px;text-align: left">
                                            ครั้งที่ <?php echo e(count($certi_cb->CertiCBSaveAssessmentMany) - ($key)); ?> :  
                                            <?php echo e($assessment->CertiCBAuditorsTo->auditor ?? '-'); ?>

                                        </a> 
                                        <br>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?> 
                        <a  class="form_group btn btn-warning" href="<?php echo e(url("certify/save_assessment-cb")); ?>" >
                            ผลการตรวจประเมิน
                        </a>
                    <?php endif; ?>

                    
                    <?php if( $certi_cb->status >= 11  && count($certi_cb->CertiCBSaveAssessmentMany) > 0   && $certi_cb->CertiCBSaveAssessmentStatus == "statusInfo"): ?>
                        <?php 
                            $review =  $certi_cb->CertiCBReviewTo;
                            $review_btn =  '';
                            $review_icon =  '';       
                            if($certi_cb->review == 1){
                                $review_btn =  'btn-warning';
                                $review_icon =  '';
                            }else{
                                $review_btn =  'btn-info';
                                $review_icon =  '<i class="fa fa-check-square-o"></i>';
                            }
                        ?>

                        <button type="button" class="form_group btn <?php echo e($review_btn); ?>"  data-toggle="modal" data-target="#ReviewModal">
                            <?php echo $review_icon; ?> ทบทวนฯ
                        </button>
                        <?php echo $__env->make('certify/cb/check_certificate_cb/modal.modalreview',['review' => $review,'certi_cb'=> $certi_cb], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php endif; ?>

                    <?php if($certi_cb->status >= 12): ?>

                        <?php 
                            $report = $certi_cb->CertiCBReportTo;
                            $report_btn =  '';
                            $report_icon =  '';
                            if(isset($report->report_status) && is_null($report->report_status)){
                                $report_btn =  'btn-warning';
                            }elseif ( isset($report->report_status) && $report->report_status == 1 && is_null($report->updated_by)) {
                                $report_btn =  'btn-success';
                                $report_icon = '<i class="fa fa-file-text"></i> ';
                            }elseif (isset($report->report_status) && $report->report_status == 1) {
                                $report_btn =  'btn-info';
                                $report_icon =  '<i class="fa fa-check-square-o"></i>';
                            }else{
                                $report_btn =  'btn-warning';
                            }
                        ?>

                        <!-- Button trigger modal     	สรุปรายงานและเสนออนุกรรมการฯ  -->
                        <?php if( !is_null($report) ): ?>
                            <button type="button" class="form_group btn <?php echo e($report_btn); ?>" data-toggle="modal" data-target="#exampleModalReport">
                                <?php echo $report_icon; ?> สรุปรายงาน
                            </button>
                            <?php echo $__env->make('certify/cb/check_certificate_cb/modal.modalstatus17',['report' => $report ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php endif; ?>
                        <!-- Button trigger modal    	สรุปรายงานและเสนออนุกรรมการฯ  -->

                    <?php endif; ?>
       
                <?php endif; ?>


                <!-- START  admin , ผอ , ผก , เจ้าหน้าที่ ลท. -->
                <?php if(auth()->user()->SetRolesLicenseCertify() == "true" ||  in_array("26",auth()->user()->RoleListId)): ?>   

                    <?php if($certi_cb->status >= 14): ?>

                        <?php 
                            $payin2 = $certi_cb->CertiCBPayInTwoTo;
                            $payin2_btn =  '';
                            $payin2_icon =  '';
                            if( is_null($payin2) || is_null($payin2->degree)){
                                $payin2_btn =  'btn-warning';
                            }elseif ($payin2->degree == 3) {
                                $payin2_btn =  'btn-info';
                                $payin2_icon =  '<i class="fa fa-check-square-o"></i>';
                            }elseif ($payin2->degree == 1) {
                                $payin2_btn =  'btn-success';
                                $payin2_icon = '<i class="fa fa-file-text"></i> ';
                            }elseif ($payin2->degree == 2) {
                                $payin2_btn =  'btn-danger';
                                $payin2_icon =  '<i class="fa fa-arrow-circle-right"></i>';
                            }
                        ?>

                        <?php if( isset($payin2->id) ): ?>
                            <!-- Button แนบใบ Pay-in ครั้งที่ 2  -->
                            <a  class="form_group btn <?php echo e($payin2_btn); ?> " href="<?php echo e(url("certify/check_certificate-cb/Pay_In2/".$payin2->id."/".$certi_cb->token)); ?>">
                                <?php echo $payin2_icon; ?>  Pay-in ครั้งที่ 2
                            </a> 
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php if($certi_cb->status >= 17): ?>
                        
                        <?php if(!empty($certi_cb->CertiCBExportTo) && !in_array($certi_cb->CertiCBExportTo->status,[99])): ?>
                            <?php 
                                $export =  $certi_cb->CertiCBExportTo;
                                $export_btn =  '';
                                $export_icon =  '';
                                if($export->status ==  4){
                                    $export_btn = 'btn-info';
                                    $export_icon =  '<i class="fa fa-check-square-o"></i>';
                                }elseif($export->status == 3){
                                    $export_btn = 'btn-success';
                                    $export_icon =  '<i class="fa fa-file-text"></i>';
                                }elseif($export->status == 5){
                                    $export_btn =  'btn-danger';
                                    $export_icon =  '<i class="fa fa-arrow-circle-right"></i>';
                                }else{
                                    $export_btn =  'btn-warning';
                                    $export_icon =  '';
                                }
                            ?>

                            <a href="<?php echo e(url('certify/certificate-export-cb/'.$export->id.'/edit')); ?>" class="form_group btn  <?php echo e($export_btn); ?>" >
                                    <?php echo $export_icon; ?>    ออกใบรับรอง
                            </a>
                        <?php elseif(!empty($certi_cb->certi_cb_export_mapreq_to)): ?>
                            <a  class="form_group btn  btn-info " href="<?php echo e(url("certify/certificate_detail-cb/".$certi_cb->token)); ?>" >
                                <i class="fa fa-paperclip"></i>  แนบท้าย
                            </a> 
                        <?php else: ?> 
                            <div class="btn-group form_group">
                                <form action="<?php echo e(url('/certify/certificate-export-cb/create')); ?>" method="POST" style="display:inline" > 
                                    <?php echo e(csrf_field()); ?>

                                    <?php echo Form::hidden('app_token', (!empty($certi_cb->token) ? $certi_cb->token  : null) , ['id' => 'app_token', 'class' => 'form-control' ]);; ?>

                                    <button class=" btn btn-warning" type="submit" >
                                        ออกใบรับรอง
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?> 
                  
                         
                    <?php endif; ?>


                <?php endif; ?>

    </div>
</div>



                <div class="clearfix"></div>
                <br>

                <div class="white-box">
                    <div class="row ">
                        <div class="col-sm-12"> 
                            <h3 class="box-title">ผลการตรวจสอบคำขอรับหน่วยรับรอง</h3>
                            <hr>
                            <div class="row text-center">

                                <?php echo Form::model($certi_cb, [
                                    'method' => 'PATCH',
                                    'url' => ['/certify/check_certificate-cb', $certi_cb->id],
                                    'class' => 'form-horizontal',
                                    'id' => 'form_operating',
                                    'files' => true
                                ]); ?>


                                    <?php echo e(csrf_field()); ?>

                                    <?php echo e(method_field('PUT')); ?>


                                    <?php
                                        $CertiCB_Status = App\Models\Certify\ApplicantCB\CertiCBStatus::whereNotIn('id',[0])->whereIN('id',[1,2,3,4,5,6])->pluck('title', 'id');
                                    ?>

                                    <div class="col-sm-8">
                                        <div class="form-group <?php echo e($errors->has('status') ? 'has-error' : ''); ?>">
                                            <?php echo HTML::decode(Form::label('status', '<span class="text-danger">*</span> ผลการตรวจสอบคำขอ', ['class' => 'col-md-4 control-label label-filter text-right'])); ?>

                                            <div class="col-md-7">
                                                <?php if($certi_cb->status < 6): ?>
                                                    <?php echo Form::select('status',$CertiCB_Status ,   $certi_cb->status ?? null,  ['class' => 'form-control',  'placeholder'=>'-เลือกผู้ที่ต้องการมอบหมายงาน-', 'id'=>'status', 'required' => true]);; ?>

                                                <?php else: ?> 
                                                    <?php echo Form::text('status',  $certi_cb->TitleStatus->title ?? null ,['class' => 'form-control', 'placeholder'=>'', 'disabled']); ?>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if(!in_array($certi_cb->status,['3','4','5'])): ?>

                                        <!-- 3.ขอเอกสารเพิ่มเติม 5.ไม่ผ่านการตรวจสอบ  -->
                                        <div class="col-sm-8 m-t-15 isShowDesc">
                                            <div class="form-group <?php echo e($errors->has('desc') ? 'has-error' : ''); ?>">
                                                <?php echo HTML::decode(Form::label('desc', '<span class="text-danger">*</span> ระบุรายละเอียด', ['class' => 'col-md-4 control-label label-filter text-right'])); ?>

                                                <div class="col-md-7">
                                                    <?php echo Form::textarea('desc', null, ['class' => 'form-control requiredDesc', 'placeholder'=>'ระบุรายละเอียดที่นี่(ถ้ามี)', 'rows'=>'5']);; ?>

                                                </div>
                                            </div>
                                        </div>


                                        <div  class="col-sm-8 m-t-15 isShowDesc">
                                            <div id="attach_files-box">
                                                <div class="form-group attach_files">
                                                    <div class="col-md-4  text-light">
                                                        <?php echo Form::label('attach_files', 'ไฟล์แนบ', ['class' => 'col-md-12 label_attach text-light  control-label ']); ?>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                            <div class="form-control" data-trigger="fileinput">
                                                                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                                <span class="fileinput-filename"></span>
                                                            </div>
                                                            <span class="input-group-addon btn btn-default btn-file">
                                                                <span class="fileinput-new">เลือกไฟล์</span>
                                                                <span class="fileinput-exists">เปลี่ยน</span>
                                                                <input type="file" name="file[]" class="check_max_size_file">
                                                            </span>
                                                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-sm btn-success attach-add" id="attach-add">
                                                            <i class="icon-plus"></i>&nbsp;เพิ่ม
                                                        </button>
                                                        <div class="button_remove"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-8 m-t-15">
                                            <div class="form-group <?php echo e($errors->has('no') ? 'has-error' : ''); ?>">
                                                <?php echo Form::label('employ_name', 'เจ้าหน้าที่ตรวจสอบคำขอ', ['class' => 'col-md-4 control-label label-filter text-right']); ?>

                                                <div class="col-md-7 text-left">
                                                    <?php echo Form::text('employ_name',  $certi_cb->FullRegName ?? null   , ['class' => 'form-control', 'placeholder'=>'', 'disabled']);; ?>

                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="col-sm-8 m-t-15">
                                            <div class="form-group <?php echo e($errors->has('no') ? 'has-error' : ''); ?>">
                                                <label for="save_date" class="col-md-4 control-label label-filter text-right">
                                                    <span class="text-danger">*</span> วันที่บันทึก
                                                </label>
                                                <div class="col-md-7 text-left">
                                                    <input type="text" name="save_date" 
                                                            value="<?php echo e($certi_cb->save_date ? \Carbon\Carbon::parse($certi_cb->save_date)->addYears(543)->format('d/m/Y') : \Carbon\Carbon::now()->addYears(543)->format('d/m/Y')); ?>"
                                                            class="form-control mydatepicker" 
                                                            placeholder="dd/mm/yyyy" 
                                                            autocomplete="off" 
                                                            required
                                                            <?php echo e($certi_cb->status >= 6 ? 'disabled' : ''); ?>>

                                                        
                                                    <?php if($errors->has('save_date')): ?>
                                                        <p class="help-block"><?php echo e($errors->first('save_date')); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                     
                                        <?php if($certi_cb->status < 6): ?>
                                            <div class="col-sm-8 m-t-15  <?php echo e(($certi_cb->status == 4 || $certi_cb->status == 5) ? 'hide' : ''); ?>">
                                                <div class="form-group">
                                                    <div class="col-md-offset-4 col-md-6 m-t-15">
                                                        <button class="btn btn-primary" type="submit" id="form-save" onclick="submit_form('1');return false">
                                                            <i class="fa fa-paper-plane"></i> บันทึก
                                                        </button>
                
                                                        <a class="btn btn-default" href="<?php echo e(url('/certify/check_certificate-cb')); ?>">
                                                            <i class="fa fa-rotate-left"></i> ยกเลิก
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        

                                    <?php endif; ?>

                                <?php echo Form::close(); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <?php if($history->count() > 0 ): ?>

                    <div class="white-box">
                        <div class="row">
                            <div class="col-sm-12">
                                <legend><h3 class="box-title">ประวัติคำขอหน่วยรับรอง</h3></legend>
                                <div class="row">

                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table zero-configuration  table-hover" id="myTable" width="100%">
                                                <thead>
                                                        <tr>
                                                            <th class="text-center bg-info  text-white" width="2%">ลำดับ</th>
                                                            <th class="text-center bg-info  text-white" width="30%">วันที่/เวลาบันทึก</th>
                                                            <th class="text-center bg-info  text-white" width="30%">เจ้าหน้าที่บันทึก</th>
                                                            <th class="text-center bg-info  text-white" width="38%">รายละเอียด</th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr >
                                                            <td class="text-center"><?php echo e($key +1); ?></td>
                                                            <td> <?php echo e(HP::DateTimeThai($item->created_at) ?? '-'); ?> </td>
                                                            <td>
                                                                <?php if(in_array($item->system,[6,10])  && is_null($item->created_by)): ?>
                                                                    <?php echo e('ระบบบันทึก'); ?>

                                                                <?php else: ?>
                                                                    <?php echo e($item->user_created->FullName ?? '-'); ?>

                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if($item->DataSystem != '-'): ?>
                                                                    <button type="button" class="btn btn-link <?php echo e(!is_null($item->details_auditors_cancel) ? 'text-danger' : ''); ?>" style="line-height: 16px;text-align: left;" data-toggle="modal" data-target="#HistoryModal<?php echo e($item->id); ?>">
                                                                        <?php echo e(@$item->DataSystem); ?>

                                                                        <br>
                                                                        <!-- แต่งตั้งคณะผู้ตรวจประเมิน  -->
                                                                        <?php if(!is_null($item->auditors_id)): ?>
                                                                            <span class="text-danger" style="font-size: 10px">
                                                                                <?php echo e(isset($item->CertiCBAuditorsTo->auditor) ? '( '.$item->CertiCBAuditorsTo->auditor.' )' : null); ?>

                                                                            </span>
                                                                        <?php endif; ?>  
                                                                    </button>
    
                                                                    <?php echo $__env->make('certify/cb/check_certificate_cb.history_detail',['history' => $item], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                                                <?php else: ?> 
                                                                    -
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="pull-right">
                            
                        </div> 
                    </div>

                <?php endif; ?>

                <?php if(count($certi_cb->CertiCBAuditorsMany) > 0 ): ?>

                    <div class="white-box">
                        <div class="row">
                            <div class="col-sm-12">
                                <legend><h3 class="box-title">คณะกรรมการผู้ตรวจประเมิน</h3></legend>
                                <div class="row">

                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table color-bordered-table info-bordered-table table-bordered" >
                                                <thead>
                                                    <tr>
                                                        <th class="text-center text-white" width="2%">ลำดับ</th>
                                                        <th class="text-center text-white" width="20%">วันที่/เวลาบันทึก</th>
                                                        <th class="text-center text-white" width="40%">คณะผู้ตรวจประเมิน</th>
                                                        <th class="text-center text-white" width="38%">สถานะ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $certi_cb->CertiCBAuditorsMany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo e($key+1); ?></td>
                                                            <td> <?php echo e(HP::DateTimeThai($item->created_at) ?? '-'); ?> </td>
                                                            <td><?php echo e($item->auditor ?? null); ?></td>
                                                            <td>
                                                                <span style="color: <?php echo e(($item->step_id == 9) ? 'red' : ''); ?> ">
                                                                    <?php echo e($item->CertiCBAuditorsStepTo->title ?? null); ?>

                                                                </span>
                                                                <?php if(!is_null($item->reason_cancel)): ?>
                                                                    <br>
                                                                    <span class="text-danger" style="font-size: 10px">
                                                                        ผู้ยกเลิก :   <?php echo e(isset($item->reason_cancel) ? @$item->UserCancelTo->FullName  : null); ?> <br>
                                                                        วันที่ยกเลิก :   <?php echo e(isset($item->date_cancel) ? HP::DateThai($item->date_cancel)   : null); ?> <br>
                                                                        เหตุผลที่ยกเลิก :   <?php echo e(isset($item->reason_cancel) ? $item->reason_cancel  : null); ?>

                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>

    <script src="<?php echo e(asset('plugins/components/icheck/icheck.min.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/components/icheck/icheck.init.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/components/toast-master/js/jquery.toast.js')); ?>"></script>
    <!-- input calendar thai -->
    <script src="<?php echo e(asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js')); ?>"></script>
    <!-- thai extension -->
    <script src="<?php echo e(asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')); ?>"></script>
    <!-- Data Table -->
    <script src="<?php echo e(asset('plugins/components/datatables/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jasny-bootstrap.js')); ?>"></script>
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    <script> 
        let certi_cb;
        jQuery(document).ready(function() {
            certi_cb = <?php echo json_encode($certi_cb ?? [], 15, 512) ?>;

            <?php if($certi_cb->status == 1 && HP_API_PID::check_api('check_api_certify_check_certificate_cb') && HP_API_PID::CheckDataApiPid($certi_cb,(new App\Models\Certify\ApplicantCB\CertiCb)->getTable()) != ''): ?>
                var id    =   '<?php echo $certi_cb->id; ?>';
                var table =   '<?php echo (new App\Models\Certify\ApplicantCB\CertiCb)->getTable(); ?>';

                $.ajax({
                    type: 'get',
                    url: "<?php echo url('certify/function/check_api_pid'); ?>" ,
                    data: {
                        id:id,
                        table:table,
                        type:'false'
                    },
                }).done(function( object ) {
                    Swal.fire({
                        position: 'center',
                        html: object.message,
                        showConfirmButton: true,
                        width: 800
                    }).then((result) => {
                        if (result.value) {
                                
                        }
                    });
                });
            <?php endif; ?>

            <?php if(\Session::has('flash_message')): ?>
                $.toast({
                    heading: 'Success!',
                    position: 'top-center',
                    text: '<?php echo e(session()->get('flash_message')); ?>',
                    loaderBg: '#33ff33',
                    icon: 'success',
                    hideAfter: 3000,
                    stack: 6
                });
            <?php endif; ?>

            $('#form_operating').parsley().on('field:validated', function() {
                var ok = $('.parsley-error').length === 0;
                $('.bs-callout-info').toggleClass('hidden', !ok);
                $('.bs-callout-warning').toggleClass('hidden', ok);
            })  .on('form:submit', function() {
                // Text
                $.LoadingOverlay("show", {
                    image       : "",
                    text  : "กำลังบันทึก กรุณารอสักครู่..."
                });
                return true; // Don't submit form for this demo
            });

            $("input[name=report_status]").on("ifChanged", function(event) {;
                status_show_report_status();
            });
            status_show_report_status();

            $('.check-readonly').prop('disabled', true);
            $('.check-readonly').parent().removeClass('disabled');
            $('.check-readonly').parent().css({"background-color": "rgb(238, 238, 238);","border-radius":"50%"});

            $('#myTable').DataTable( {
                dom: 'Brtip',
                pageLength:5,
                processing: true,
                lengthChange: false,
                ordering: false,
                order: [[ 0, "desc" ]]
            });

            $('#myTable1').DataTable( {
                dom: 'Brtip',
                pageLength:5,
                processing: true,
                lengthChange: false,
                ordering: false,
                order: [[ 0, "desc" ]]
            });

            IsInputNumber();
            AttachFileLoa();

            // <!-- 3.ขอเอกสารเพิ่มเติม 5.ไม่ผ่านการตรวจสอบ  -->
            $('#status').change(function(){ 
                $('.isShowDesc').hide();
                $('.requiredDesc').prop('required', false);

                if($(this).val() == 3 || $(this).val() == 4 ||$(this).val() == 5){
                    $('.isShowDesc').show();
                    $('.requiredDesc').prop('required', true);
                }

            });

            $('#status').change();
            check_max_size_file();

            //เพิ่มไฟล์แนบ
            $('#attach-add').click(function(event) {
                $('.attach_files:first').clone().appendTo('#attach_files-box');
                $('.attach_files:last').find('input').val('');
                $('.attach_files:last').find('a.fileinput-exists').click();
                $('.attach_files:last').find('a.view-attach').remove();
                $('.attach_files:last').find('.label_attach').remove();
                $('.attach_files:last').find('button.attach-add').remove();
                $('.attach_files:last').find('.button_remove').html('<button class="btn btn-danger btn-sm attach_remove" type="button"> <i class="icon-close"></i>  </button>');
                check_max_size_file();
            });

            //ลบไฟล์แนบ
            $('body').on('click', '.attach_remove', function(event) {
                $(this).parent().parent().parent().remove();
            });

            //ปฎิทิน
            $('.mydatepicker').datepicker({
                toggleActive: true,
                language:'th-th',
                format: 'dd/mm/yyyy',
            });

        });

        function status_show_report_status(){
            var row = $("input[name=report_status]:checked").val();
            if(row == "1"){ 
                $('#div_file_loa').show();
                $('#file_loa').prop('required' ,true);
            } else{
                $('#div_file_loa').hide();
                $('#file_loa').prop('required' ,false);
            }
        }

        function submit_form(status) {
            Swal.fire({
                title: 'ยืนยันทำรายการ !',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.value) {
                    $('#form_operating').submit();
                }
            });
            
        }

        function IsInputNumber() {
            // ฟังก์ชั่นสำหรับค้นและแทนที่ทั้งหมด
            String.prototype.replaceAll = function(search, replacement) {
                var target = this;
                return target.replace(new RegExp(search, 'g'), replacement);
            }; 
                    
            var formatMoney = function(inum){ // ฟังก์ชันสำหรับแปลงค่าตัวเลขให้อยู่ในรูปแบบ เงิน 
                    var s_inum=new String(inum); 
                    var num2=s_inum.split("."); 
                    var n_inum=""; 
                    if(num2[0]!=undefined){
                        var l_inum=num2[0].length; 
                        for(i=0;i<l_inum;i++){ 
                            if(parseInt(l_inum-i)%3==0){ 
                                if(i==0){ 
                                    n_inum+=s_inum.charAt(i); 
                                }else{ 
                                    n_inum+=","+s_inum.charAt(i); 
                                } 
                            }else{ 
                                n_inum+=s_inum.charAt(i); 
                            } 
                        } 
                    }else{
                        n_inum=inum;
                    }
                    if(num2[1]!=undefined){ 
                        n_inum+="."+num2[1]; 
                    }
                    return n_inum; 
                } 
                // อนุญาติให้กรอกได้เฉพาะตัวเลข 0-9 จุด และคอมม่า 
            $(".input_number").on("keypress",function(e){
                var eKey = e.which || e.keyCode;
                if((eKey<48 || eKey>57) && eKey!=46 && eKey!=44){
                   return false;
                }
            }); 
                   
            // ถ้ามีการเปลี่ยนแปลง textbox ที่มี css class ชื่อ css_input1 ใดๆ 
            $(".input_number").on("change",function(){
                var thisVal=$(this).val(); // เก็บค่าที่เปลี่ยนแปลงไว้ในตัวแปร
                if(thisVal != ''){
                    if(thisVal.replace(",","")){ // ถ้ามีคอมม่า (,)
                        thisVal=thisVal.replaceAll(",",""); // แทนค่าคอมม่าเป้นค่าว่างหรือก็คือลบคอมม่า
                        thisVal = parseFloat(thisVal); // แปลงเป็นรูปแบบตัวเลข 
                    }else{ // ถ้าไม่มีคอมม่า
                        thisVal = parseFloat(thisVal); // แปลงเป็นรูปแบบตัวเลข 
                    } 
                    thisVal=thisVal.toFixed(2);// แปลงค่าที่กรอกเป้นทศนิยม 2 ตำแหน่ง
                    $(this).data("number",thisVal); // นำค่าที่จัดรูปแบบไม่มีคอมม่าเก็บใน data-number
                    $(this).val(formatMoney(thisVal));// จัดรูปแบบกลับมีคอมม่าแล้วแสดงใน textbox นั้น
                }else{
                    $(this).val('');
                }
            });
        }

        //  Attach File
        function  AttachFileLoa(){
            $('.file_loa').change( function () {
                var fileExtension = ['pdf'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1 && $(this).val() != '') {
                    Swal.fire(
                        'ไม่ใช่หลักฐานประเภทไฟล์ที่อนุญาต .pdf ',
                        '',
                        'info'
                    );
                    // this.value = '';
                    $(this).parent().parent().find('.fileinput-exists').click();
                    return false;
                }
            });
        }
         
       


        $("#btn_doc_auditor").on("click", function() {
            const _token = $('input[name="_token"]').val();
            let certiCbId = certi_cb.id;
            console.log(certiCbId);

            Swal.fire({
                title: "ต้องการแต่งตั้งทีมตรวจประเมินหรือไม่?",
                icon: "question",
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: "แต่งตั้ง",
                denyButtonText: "ไม่แต่งตั้ง",
                cancelButtonText: "ยกเลิก",
                customClass: {
                    confirmButton: 'swal-btn', 
                    denyButton: 'swal-btn', 
                    cancelButton: 'swal-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Swal.fire("แต่งตั้งเรียบร้อย!", "", "success");
                    window.location.href = "/certify/auditor_cb_doc_review/auditor_cb_doc_review/" + certiCbId;
                } else if (result.isDenied) {
                    $.ajax({
                        
                        url: "<?php echo e(route('bypass_doc_auditor_assignment')); ?>",
                        method: "POST",
                        data: {
                            certiCbId: certiCbId,
                            _token: _token
                        },
                        success: function(result) {
                            location.reload(); // รีโหลดหน้า
                        }
                    });
                }
            });
        });


      
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>