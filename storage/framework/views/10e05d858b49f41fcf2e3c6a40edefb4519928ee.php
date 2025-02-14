<?php $__env->startPush('css'); ?>
   <link href="<?php echo e(asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')); ?>" rel="stylesheet" type="text/css" />
<style>

  .label-filter{
    margin-top: 7px;
  }
  /*
	Max width before this PARTICULAR table gets nasty. This query will take effect for any screen smaller than 760px and also iPads specifically.
	*/
	@media
	  only screen
    and (max-width: 760px), (min-device-width: 768px)
    and (max-device-width: 1024px)  {

		/* Force table to not be like tables anymore */
		table, thead, tbody, th, td, tr {
			display: block;
		}

		/* Hide table headers (but not display: none;, for accessibility) */
		thead tr {
			position: absolute;
			top: -9999px;
			left: -9999px;
		}

    tr {
      margin: 0 0 1rem 0;
    }

    tr:nth-child(odd) {
      background: #eee;
    }

		td {
			/* Behave  like a "row" */
			border: none;
			border-bottom: 1px solid #eee;
			position: relative;
			padding-left: 50%;
		}

		td:before {
			/* Now like a table header */
			/*position: absolute;*/
			/* Top/left values mimic padding */
			top: 0;
			left: 6px;
			width: 45%;
			padding-right: 10px;
			white-space: nowrap;
		}

		/*
		Label the data
    You could also use a data-* attribute and content for this. That way "bloats" the HTML, this way means you need to keep HTML and CSS in sync. Lea Verou has a clever way to handle with text-shadow.
		*/
		/*td:nth-of-type(1):before { content: "Column Name"; }*/

	}
  .check_api_pid {cursor: pointer;}
  .modalDelete {text-align: left;}
</style>

<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ระบบตรวจสอบคำขอหน่วยรับรอง (CB)</h3>


                    <div class="pull-right">
                      <?php if(isset($select_users) && count($select_users) > 0): ?>
                          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign_work-'.str_slug('checkcertificatecb'))): ?>
                          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"> มอบหมาย
                          </button>
                          <!--   popup ข้อมูลผู้ตรวจการประเมิน   -->
                          <div class="modal fade" id="exampleModal">
                              <div class="modal-dialog modal-xl"  role="document">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" tabindex="-1" aria-label="Close"><span aria-hidden="true">&times;</span>
                                          </button>
                                          <h4 class="modal-title" id="exampleModalLabel1">ระบบตรวจสอบคำขอใบรับรองหน่วยรับรอง (CB)</h4>
                                      </div>
                                      <div class="modal-body">
                                          <form id="form_assign" action="<?php echo e(route('check_certificate-cb.assign')); ?>" method="post" >
                                              <?php echo e(csrf_field()); ?>

                                              <div class="white-box">
                                                  <div class="row form-group">
                                                      <div class="col-md-12">
                                                              <div class="form-group <?php echo e($errors->has('no') ? 'has-error' : ''); ?>">
                                                                  <?php echo Form::label('checker', 'เลือกเจ้าหน้าที่ตรวจสอบคำขอ', ['class' => 'col-md-4 control-label label-filter text-right']); ?>

                                                                  <div class="col-md-6">
                                                                      <?php echo Form::select('',
                                                                        $select_users,
                                                                        null,
                                                                       ['class' => 'form-control',
                                                                       'id'=>"checker",
                                                                       'placeholder'=>'-เลือกผู้ที่ต้องการมอบหมายงาน-']);; ?>

                                                                  </div>
                                                                  <div class="col-md-2">
                                                                      <button type="button" class="btn btn-sm btn-primary pull-left m-l-5" id="add_items">&nbsp; เลือก</button>
                                                                  </div>
                                                              </div>
                                                      </div>
                                                  </div>
                                                  <div class="row " id="div_checker">
                                                      <div class="col-md-12">
                                                              <div class="form-group <?php echo e($errors->has('no') ? 'has-error' : ''); ?>">
                                                                  <div class="col-md-4"></div>
                                                                  <div class="col-md-8">
                                                                      <div class="table-responsive">
                                                                          <table class="table color-bordered-table info-bordered-table">
                                                                              <thead>
                                                                              <tr>
                                                                                  <th class="text-center" width="2%">#</th>
                                                                                  <th class="text-center" width="88%">เจ้าหน้าที่ตรวจสอบคำขอ</th>
                                                                                  <th class="text-center" width="10%">ลบ</th>
                                                                              </tr>
                                                                              </thead>
                                                                              <tbody id="table_tbody">

                                                                              </tbody>
                                                                          </table>
                                                                      </div>
                                                                  </div>
                                                           </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="text-center">
                                                  <button type="button"class="btn btn-primary"   onclick="submit_form('1');return false"><i class="icon-check"></i> บันทึก</button>
                                                  <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">
                                                      <?php echo __('ยกเลิก'); ?>

                                                  </button>
                                              </div>
                                          </form>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <?php endif; ?>
                      <?php endif; ?>
                  </div>
                    <div class="clearfix"></div>
                    <hr>

                    <?php echo Form::model($filter, ['url' => '/certify/check_certificate-cb', 'method' => 'get', 'id' => 'myFilter']); ?>


                    <div class="row">
                        <div class="col-md-3 form-group">
                              
                              <div class="form-group col-md-12">
                                  <?php echo Form::select('filter_status',
                                    $status,
                                    null,
                                   ['class' => 'form-control',
                                   'id'=>'filter_status',
                                   'placeholder'=>'-เลือกสถานะ-']); ?>

                             </div>
                        </div><!-- /form-group -->
                        <div class="col-md-5">
                               
                                 <div class="form-group col-md-7">
                                  <?php echo Form::text('filter_search', null, ['class' => 'form-control', 'placeholder'=>' เลขที่คำขอ ','id'=>'filter_search']);; ?>

                                </div>
                                <div class="form-group col-md-5">
                                    <?php echo Form::label('perPage', 'Show', ['class' => 'col-md-4 control-label label-filter text-right']); ?>

                                    <div class="col-md-8">
                                        <?php echo Form::select('perPage',
                                        ['10'=>'10', '20'=>'20', '50'=>'50', '100'=>'100','500'=>'500'],
                                          null,
                                         ['class' => 'form-control']); ?>

                                    </div>
                                </div>
                        </div><!-- /.col-lg-5 -->
                        <div class="col-md-2">
                          <div class="form-group">
                              <button type="button" class="btn btn-primary waves-effect waves-light" data-parent="#capital_detail" href="#search-btn" data-toggle="collapse" id="search_btn_all">
                                  <small>เครื่องมือค้นหา</small> <span class="glyphicon glyphicon-menu-up"></span>
                              </button>
                          </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group  pull-left">
                            <button type="submit" class="btn btn-info waves-effect waves-light" style="margin-bottom: -1px;">ค้นหา</button>
                        </div>
                        <div class="form-group  pull-left m-l-15">
                            <button type="button" class="btn btn-warning waves-effect waves-light" id="filter_clear">
                                ล้าง
                            </button>
                        </div>
                       </div><!-- /.col-lg-1 -->
                    </div><!-- /.row -->


                    <div id="search-btn" class="panel-collapse collapse">
                      <div class="white-box" style="display: flex; flex-direction: column;">

                            <div class="row">

                                <div class="form-group col-md-6">
                                    <?php echo Form::label('filter_inspector', 'เจ้าหน้าที่ตรวจสอบ:', ['class' => 'col-md-4 control-label label-filter']); ?>

                                    <div class="col-md-7">
                                        <?php echo Form::select('filter_inspector', $select_users, null, ['class' => 'form-control', 'placeholder'=>'-เลือกเจ้าหน้าที่-','id'=>'filter_inspector']);; ?>

                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <?php echo Form::label('filter_start_date', 'วันที่บันทึก:', ['class' => 'col-md-4 control-label label-filter']); ?>

                                    <div class="col-md-7">
                                    <div class="input-daterange input-group" id="date-range">
                                        <?php echo Form::text('filter_start_date', null, ['class' => 'form-control','id'=>'filter_start_date']); ?>

                                        <span class="input-group-addon bg-info b-0 text-white"> ถึง </span>
                                        <?php echo Form::text('filter_end_date', null, ['class' => 'form-control','id'=>'filter_end_date']); ?>

                                    </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-md-6">
                                    <?php echo Form::label('filter_name', 'หน่วยงาน:', ['class' => 'col-md-4 control-label label-filter']); ?>

                                    <div class="col-md-7">
                                        <?php echo Form::select('filter_name', App\Models\Certify\ApplicantCB\CertiCb::select('name')->whereNotNull('name')->groupBy('name')->pluck('name', 'name'), null, ['class' => 'form-control', 'placeholder'=>'-เลือกหน่วยงาน-','id'=>'filter_name']);; ?>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>


					    <input type="hidden" name="sort" value="<?php echo e(Request::get('sort')); ?>" />
						<input type="hidden" name="direction" value="<?php echo e(Request::get('direction')); ?>" />

					<?php echo Form::close(); ?>


                    <div class="clearfix"></div>

                        <table class="table table-borderless" id="myTable">
                            <thead>
                                <tr>
                                    <th  class="text-center text-top" width="2%">#</th>
                                    <th  class="text-center text-top" width="3%">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign_work-'.str_slug('checkcertificatecb'))): ?>
                                        <input type="checkbox" id="checkall">
                                        <?php endif; ?>
                                    </th>
                                    <th  class="text-center text-top" width="10%">เลขที่คำขอ</th>
                                    <th  class="text-center text-top" width="15%">หน่วยรับรอง</th>
                                    <th  class="text-center text-top" width="10%">เลขที่มาตรฐาน</th>
                                    <th  class="text-center text-top" width="10%">สาขา</th>
                                    <th  class="text-center text-top" width="10%">วันที่ยื่นคำขอ</th>
                                    <th  class="text-center text-top" width="15%">สถานะ</th>
                                    <th  class="text-center text-top" width="10%">เจ้าหน้าที่ตรวจสอบคำขอ</th>
                                    <th  class="text-center text-top" width="5%">รายละเอียด</th>
                                </tr>
                            </thead>
                            <tbody>
                             <?php if(count($certi_cbs) > 0): ?>
                               <?php $__currentLoopData = $certi_cbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="text-center text-top"><?php echo e($loop->iteration + ( ((request()->query('page') ?? 1) - 1) * $certi_cbs->perPage() )); ?></td>
                                        <td class="text-center text-top">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign_work-'.str_slug('checkcertificatecb'))): ?>
                                            <?php if(!in_array($item->status,['4'])): ?>
                                                <input type="checkbox" name="ib[]" class="ib" value="<?php echo e($item->id); ?>">
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-top">
                                            <?php echo @$item->app_no; ?>

                                        </td>
                                        <td class="text-top">
                                            <?php echo @$item->name_standard ?? @$item->EsurvTraderTitle; ?>

                                        </td>
                                        <td class="text-top"><?php echo e($item->FormulaTiTle); ?></td>
                                        <td class="text-top"><?php echo e($item->CertificationBranchName); ?></td>
                                        <td class="text-top"><?php echo e($item->StartDateShow); ?></td>
                                        <td class="text-top">

                                                <?php
                                                $TitleStatus =  $item->TitleStatus->title ?? '-' ;
                                                ?>
                                                <!-- icon  -->
                                                <?php if(in_array($item->status,["15"])): ?>
                                                    <img src="<?php echo e(asset('plugins/images/money01.png')); ?>" width="25px" height="25px">
                                                <?php endif; ?>
                                                <?php if(in_array($item->status,["16"])): ?>
                                                    <img src="<?php echo e(asset('plugins/images/money02.png')); ?>" width="25px" height="25px">
                                                <?php endif; ?>
                                                <?php if(in_array($item->status,["17"])): ?>
                                                    <img src="<?php echo e(asset('plugins/images/money03.png')); ?>"  width="25px" height="25px">
                                                <?php endif; ?>
                                            <!-- icon  -->
                                                <!-- status  -->
                                            <?php if($item->status == 4): ?>
                                            <button style="border: none;background-color: #ffffff;" data-toggle="modal"
                                                                            data-target="#actionFour<?php echo e($loop->iteration); ?>"
                                                                            data-id="<?php echo e($item->token); ?>"  >
                                                <i class="fa fa-close text-danger"></i> <?php echo e($TitleStatus); ?>

                                            </button>
                                                <?php echo $__env->make('certify/cb/check_certificate_cb/modal.modalstatus4', array('id' => $loop->iteration,
                                                                                                                        'desc' => $item->desc_delete ?? null ,
                                                                                                                        'token'=> $item->token,
                                                                                                                        'files' => $item->FileAttach7
                                                                                                                    ), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                                <?php elseif($item->status == 5): ?>
                                                <button style="border: none;background-color: #ffffff;" data-toggle="modal"
                                                                            data-target="#NotValidated<?php echo e($loop->iteration); ?>"
                                                                            data-id="<?php echo e($item->token); ?>"  >
                                                <i class="fa fa-close text-danger"></i> <?php echo e($TitleStatus); ?>

                                                </button>
                                                <?php echo $__env->make('certify/cb/check_certificate_cb/modal.modalstatus5', array('id' => $loop->iteration,
                                                                                                                        'desc' => $item->desc_delete ?? null ,
                                                                                                                        'token'=> $item->token,
                                                                                                                        'files' => $item->FileAttach8
                                                                                                                    ), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                            <?php elseif($item->status == 10 ): ?> <!-- อยู่ระหว่างดำเนินการ  -->
                                                        <button style="border: none" data-toggle="modal"
                                                                                        data-target="#TakeAction<?php echo e($loop->iteration); ?>"
                                                                                        data-id="<?php echo e($item->token); ?>"  >
                                                            <i class="mdi mdi-magnify"></i>     อยู่ระหว่างดำเนินการ
                                                        </button>

                                                        <?php echo $__env->make('certify/cb/check_certificate_cb/modal.modalstatus10',['id'=> $loop->iteration,
                                                                                                                        'auditors' => $item->CertiCBAuditorsMany,
                                                                                                                        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                                <?php else: ?>
                                                    <?php echo e($TitleStatus); ?>

                                                <?php endif; ?>
                                                <!-- status  -->

                                        </td>
                                        <td class="text-top">
                                            <?php echo $item->FullName ?? '-'; ?>

                                        </td>
                                        <td class="text-center text-top">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-'.str_slug('checkcertificatecb'))): ?>

                                                
                                                
                                                <a href="<?php echo e(url('/certify/check_certificate-cb/' . $item->token . '/show/'.$item->id)); ?>"
                                                    title="View Applicantib" class="btn btn-xs btn-info">
                                                    <i class="fa fa-search"></i>
                                                </a>
                                                

                                            <?php endif; ?>
                                            <?php
                                                $model = str_slug('check-checkcertificatecb','-');
                                            ?>
                                            <?php if(@auth()->user()->can('delete-'.$model) && (@$item->status >= 0 && @$item->status <= 22) && @$item->status != 4): ?>
                                                <button class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modalDelete<?php echo e($item->id); ?>" data-no="<?php echo e($item->app_no); ?>" data-id="<?php echo e($item->token); ?>">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </button>
                                                <?php echo $__env->make('certify/cb/check_certificate_cb/modal.modaldelete',['id'=>$item->id,
                                                                                                            'token'=>$item->token,
                                                                                                            'app_no'=>$item->app_no], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                            <?php endif; ?>
                                        </td>
                                </tr>
                           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                           <?php else: ?>
                           <tr>
                             <td colspan="8" class="text-center" >
                               <h3 style="color:red">
                                <b>
                                  <i class="fa fa-exclamation-circle" style="color:rgb(255, 230, 0)"></i>
                                  ไม่มีคำขอตรวจสอบหน่วยรับรอง (CB)
                                </b>
                               </h3>
                             </td>
                           </tr>
                           <?php endif; ?>

                            </tbody>
                        </table>

                        <div class="pagination-wrapper">
                          <?php echo $certi_cbs->appends([
                                                    'search' => Request::get('search'),
                                                    'sort' => Request::get('sort'),
                                                    'direction' => Request::get('direction'),
                                                    'perPage' => Request::get('perPage'),
                                                    'filter_status' => Request::get('filter_status'),
                                                    'filter_search' => Request::get('filter_search'),
                                                    'filter_inspector' => Request::get('filter_inspector'),
                                                    'filter_start_date' => Request::get('filter_start_date'),
                                                    'filter_end_date' => Request::get('filter_end_date'),
                                                    'filter_name' => Request::get('filter_name'),
                                                    'filter_state' => Request::get('filter_state')
                                                ])->render(); ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>



<?php $__env->startPush('js'); ?>
    <script src="<?php echo e(asset('plugins/components/toast-master/js/jquery.toast.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')); ?>"></script>
      <!-- input calendar thai -->
  <script src="<?php echo e(asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js')); ?>"></script>
  <!-- thai extension -->
  <script src="<?php echo e(asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js')); ?>"></script>
  <script src="<?php echo e(asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js')); ?>"></script>
    <script>
        $(document).ready(function () {
          $( "#filter_clear" ).click(function() {
                $('#filter_status').val('').select2();
                $('#filter_search').val('');

                $('#filter_state').val('').select2();
                $('#filter_start_date').val('');
                $('#filter_end_date').val('');
                $('#filter_branch').val('').select2();
                window.location.assign("<?php echo e(url('/certify/check_certificate-cb')); ?>");
            });

            if( checkNone($('#filter_state').val()) ||  checkNone($('#filter_start_date').val()) || checkNone($('#filter_end_date').val()) || checkNone($('#filter_branch').val())   ){
                // alert('มีค่า');
                $("#search_btn_all").click();
                $("#search_btn_all").removeClass('btn-primary').addClass('btn-success');
                $("#search_btn_all > span").removeClass('glyphicon-menu-up').addClass('glyphicon-menu-down');
            }

            $("#search_btn_all").click(function(){
                $("#search_btn_all").toggleClass('btn-primary btn-success', 'btn-success btn-primary');
                $("#search_btn_all > span").toggleClass('glyphicon-menu-up glyphicon-menu-down', 'glyphicon-menu-down glyphicon-menu-up');
            });

            function checkNone(value) {
            return value !== '' && value !== null && value !== undefined;
             }
             $(".check_api_pid").click(function() {
                var id =   $(this).data('id');
                var url =   $(this).data('url');
                var table =   $(this).data('table');

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
                            window.location = url;
                        }
                    });
                });

            });
            //ช่วงวันที่
            jQuery('#date-range').datepicker({
              toggleActive: true,
              language:'th-th',
              format: 'dd/mm/yyyy',
            });

             $('#add_items').on('click',function () {
                 let row =$('#checker').val();
                if(row != ''){
                    $('#div_checker').show();
                    let checker = $('#checker').find('option[value="'+row+'"]').text();
                    let table_tbody = $('#table_tbody');
                        // table_tbody.empty();
                        table_tbody.append('<tr>\n' +
                    '                    <td class="text-center">1</td>\n' +
                    '                    <td class="text-left">'+checker+'</td>\n' +
                    '                    <td class="text-center">' +
                    '                    <input type="hidden" name="checker[]"   class="data_checker" value="'+ row+'">\n' +
                    '                    <button type="button" class="btn btn-danger btn-xs inTypeDelete" data-types="'+row+'" ><i class="fa fa-remove"></i></button></td>\n' +
                    '                </tr>');
                    $("#checker option[value=" + row + "]").prop('disabled', true); //  เปิดรายการ
                    ResetTableNumber();
                    $('#checker').val('').select2();
                }else{
                    Swal.fire('กรุณาเลือกเจ้าหน้าที่ตรวจสอบคำขอ !!');
                }

            });
             ResetTableNumber();
            $(document).on('click','.inTypeDelete',function () {
                let types = $(this).attr('data-types');
                $("#checker option[value=" + types + "]").prop('disabled', false); //  เปิดรายการ
                $(this).parent().parent().remove();
                ResetTableNumber();
            });


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

            <?php if(\Session::has('message_error')): ?>
                $.toast({
                    heading: 'Error!',
                    position: 'top-center',
                    text: '<?php echo e(session()->get('message_error')); ?>',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 3000,
                    stack: 6
                });
            <?php endif; ?>

            //เลือกทั้งหมด
            $('#checkall').change(function (event) {
                if ($(this).prop('checked')) {//เลือกทั้งหมด
                    $('#myTable').find('input.ib').prop('checked', true);
                } else {
                    $('#myTable').find('input.ib').prop('checked', false);
                }
            });

            $('#form_assign').on('submit', function (e) {
                let ibs = $('input.ib:checked');
                if (ibs.length === 0) {
                    e.preventDefault();
                    return;
                }

                let form = $(this);
                form.children('input.apps').remove();
                ibs.each(function () {
                    let value = $(this).val();
                    let input = $('<input type="hidden" name="apps[]" class="apps" value="'+value+'" />');
                    input.appendTo(form);
                });
            })


        });

        function Delete(){

          if($('#myTable').find('input.cb:checked').length > 0){//ถ้าเลือกแล้ว
            if(confirm_delete()){
              $('#myTable').find('input.cb:checked').appendTo("#myForm");
              $('#myForm').submit();
            }
          }else{//ยังไม่ได้เลือก
            alert("กรุณาเลือกข้อมูลที่ต้องการลบ");
          }

        }

        function confirm_delete() {
            return confirm("ยืนยันการลบข้อมูล?");
        }

        function UpdateState(state){

          if($('#myTable').find('input.cb:checked').length > 0){//ถ้าเลือกแล้ว
              $('#myTable').find('input.cb:checked').appendTo("#myFormState");
              $('#state').val(state);
              $('#myFormState').submit();
          }else{//ยังไม่ได้เลือก
            if(state=='1'){
              alert("กรุณาเลือกข้อมูลที่ต้องการเปิด");
            }else{
              alert("กรุณาเลือกข้อมูลที่ต้องการปิด");
            }
          }

        }

         //รีเซตเลขลำดับ
         function ResetTableNumber(){
                var rows = $('#table_tbody').children(); //แถวทั้งหมด
                (rows.length==0)?$('#div_checker').hide():$('#div_checker').show();
                rows.each(function(index, el) {
                    $(el).children().first().html(index+1);
                });
          }
          function submit_form() {
                var data_checker = $(".data_checker").length;
                let ibs = $('input.ib:checked').length;

                if(data_checker > 0 && ibs > 0){
                         // Text
                          $.LoadingOverlay("show", {
                             image       : "",
                             text        : "กำลังบันทึก กรุณารอสักครู่..."
                        });
                     $('#form_assign').submit();
                }else if(ibs <= 0){
                    Swal.fire(
                        'กรุณาเลือกเลขที่คำขอ !!',
                        '',
                        'info'
                     )
                }else{
                    Swal.fire(
                        'กรุณาเลือกเจ้าหน้าที่ตรวจสอบคำขอ !!',
                        '',
                        'info'
                     )
                }
        }
    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>