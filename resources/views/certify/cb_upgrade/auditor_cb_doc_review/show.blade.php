@extends('layouts.master')

@push('css')
    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
    <!-- Data Table CSS -->
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <style type="text/css">
        .form_group {
            margin-bottom: 10px;
        }
    </style>
@endpush
@section('content')

<div class="container-fluid">
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">

                <h3 class="box-title pull-left">การบันทึกผลการตรวจสอบเอกสาร {{ $certi_cb->app_no ?? null }} </h3>
                @can('view-'.str_slug('checkcertificatecb'))
                    <a class="btn btn-success pull-right" href="{{ url('/certify/check_certificate-cb') }}">
                        <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                    </a>
                @endcan
                <div class="clearfix"></div>
                <hr>


            <div class="row">
                <div class="col-sm-12 ">
                    <a class="form_group btn {{ ($certi_cb->status >= 6) ? 'btn-info' : 'btn-warning'  }} "   href="{{ url('certify/check_certificate-cb/show/'.$certi_cb->app_no) }}" >
                        <i class="fa fa-search" aria-hidden="true"></i> รายละเอียดคำขอ
                    </a>
                </div>
            </div>



            <div class="clearfix"></div>
            <br>

            <div class="white-box">
                <div class="row ">
                    <div class="col-sm-12"> 
                        <h3 class="box-title">การบันทึกผลการตรวจสอบเอกสาร</h3>
                        <hr>
                        <div class="row text-center">

                            {!! Form::model($certi_cb, [
                                // 'method' => 'PUT',
                                'url' => ['/certify/auditor_cb_doc_review/auditor_cb_doc_review_result_update', $certi_cb->id],
                                'class' => 'form-horizontal',
                                'id' => 'form_operating',
                                'files' => true
                            ]) !!}

                                {{ csrf_field() }}
                                {{ method_field('PUT') }}

                                @php
                                    $CertiCB_Status = App\Models\Certify\ApplicantCB\CertiCBStatus::whereNotIn('id',[0])->whereIN('id',[1,2,3,4,5,6])->pluck('title', 'id');
                                @endphp
                               
                                {{-- <div class="col-sm-8">
                                    <div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
                                        {!! HTML::decode(Form::label('status', '<span class="text-danger">*</span> ผลการตรวจสอบเอกสาร', ['class' => 'col-md-4 control-label label-filter text-right'])) !!}
                                        <div class="col-md-7">
                                            @if($certi_cb->status >= 9)
                                                {!! Form::select('status',$CertiCB_Status ,   $certi_cb->status ?? null,  ['class' => 'form-control',  'placeholder'=>'-เลือกผู้ที่ต้องการมอบหมายงาน-', 'id'=>'status', 'required' => true]); !!}
                                            @else 
                                                {!! Form::text('status',  $certi_cb->TitleStatus->title ?? null ,['class' => 'form-control', 'placeholder'=>'', 'disabled']) !!}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                 --}}


                                 <div class="col-sm-8">
                                    <div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
                                        {!! HTML::decode(Form::label('status', '<span class="text-danger">*</span> ผลการตรวจสอบเอกสาร', ['class' => 'col-md-4 control-label label-filter text-right'])) !!}
                                        <div class="col-md-7">
                                            {{-- {{$certi_cb->more_doc_require}} --}}
                                            @if($certi_cb->status >= 9)
                                                <?php
                                                    // แปลง object เป็น array ถ้าเป็น Eloquent Collection หรือ Object ที่มี toArray()
                                                    $statusArray = $CertiCB_Status->toArray();
                                        
                                                    // เลือกเฉพาะ index 3 และ 6
                                                    $filteredStatus = \Illuminate\Support\Arr::only($statusArray, [3, 6]);
                                        
                                                    // ตรวจสอบเงื่อนไข $certi_cb->more_doc_require เพื่อตั้งค่าที่เลือกใน select
                                                    $selectedStatus = null;
                                                    if ($certi_cb->more_doc_require == 1) {
                                                        $selectedStatus = 3;  // ถ้า more_doc_require = 1 ให้เลือก index 3
                                                    } elseif ($certi_cb->more_doc_require == 2) {
                                                        $selectedStatus = 6;  // ถ้า more_doc_require = 2 ให้เลือก index 6
                                                    }
                                                ?>
                                                {!! Form::select('status', $filteredStatus, $selectedStatus, ['class' => 'form-control', 'placeholder' => '-เลือกผู้ที่ต้องการมอบหมายงาน-', 'id' => 'status', 'required' => true]) !!}
                                            @else
                                                {!! Form::text('status',  $certi_cb->TitleStatus->title ?? null , ['class' => 'form-control', 'placeholder' => '', 'disabled']) !!}
                                            @endif
                                        </div>
                                        
                                    </div>
                                </div>
                                
                                
                                

                                @if(!in_array($certi_cb->status,['3','4','5']))

                                    <!-- 3.ขอเอกสารเพิ่มเติม 5.ไม่ผ่านการตรวจสอบ  -->
                                    <div class="col-sm-8 m-t-15 isShowDesc">
                                        <div class="form-group {{ $errors->has('desc') ? 'has-error' : ''}}">
                                            {!! HTML::decode(Form::label('desc', '<span class="text-danger">*</span> ระบุรายละเอียด', ['class' => 'col-md-4 control-label label-filter text-right'])) !!}
                                            <div class="col-md-7">
                                                {!! Form::textarea('desc', null, ['class' => 'form-control requiredDesc', 'placeholder'=>'ระบุรายละเอียดที่นี่(ถ้ามี)', 'rows'=>'5']); !!}
                                            </div>
                                        </div>
                                    </div>


                                    <div  class="col-sm-8 m-t-15 isShowDesc">
                                        <div id="attach_files-box">
                                            <div class="form-group attach_files">
                                                <div class="col-md-4  text-light">
                                                    {!! Form::label('attach_files', 'ไฟล์แนบ', ['class' => 'col-md-12 label_attach text-light  control-label ']) !!}
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

                                    {{-- <div class="col-sm-8 m-t-15">
                                        <div class="form-group {{ $errors->has('no') ? 'has-error' : ''}}">
                                            {!! Form::label('employ_name', 'เจ้าหน้าที่ตรวจสอบคำขอ', ['class' => 'col-md-4 control-label label-filter text-right']) !!}
                                            <div class="col-md-7 text-left">
                                                {!! Form::text('employ_name',  $certi_cb->FullRegName ?? null   , ['class' => 'form-control', 'placeholder'=>'', 'disabled']); !!}
                                            </div>
                                        </div>
                                    </div> --}}

                                    {{-- <div class="col-sm-8 m-t-15">
                                        <div class="form-group {{ $errors->has('no') ? 'has-error' : ''}}">
                                            {!! HTML::decode(Form::label('save_date', '<span class="text-danger">*</span> วันที่บันทึก', ['class' => 'col-md-4 control-label label-filter text-right'])) !!}
                                            <div class="col-md-7 text-left">
                                                {!! Form::text('save_date',
                                                    $certi_cb->save_date ? HP::revertDate($certi_cb->save_date,true): null,
                                                    ['class' => 'form-control mydatepicker', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off', 'required' => 'required'
                                                    ,'disabled' => ($certi_cb->status >= 6) ?   true :  false   ]) !!}
                                                {!! $errors->first('save_date', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="col-sm-8 m-t-15">
                                        <div class="form-group {{ $errors->has('no') ? 'has-error' : ''}}">
                                            {!! HTML::decode(Form::label('save_date', '<span class="text-danger">*</span> วันที่บันทึก', ['class' => 'col-md-4 control-label label-filter text-right'])) !!}
                                            <div class="col-md-7 text-left">
                                                {!! Form::text('save_date',
                                                    $certi_cb->save_date ? HP::revertDate($certi_cb->save_date,true): null,
                                                    ['class' => 'form-control mydatepicker', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off', 'required' => 'required'
                                                    ,'disabled' => false   ]) !!}
                                                {!! $errors->first('save_date', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($certi_cb->status == 9)
                                        <div class="col-sm-8 m-t-15  {{ ($certi_cb->status == 9 && $certi_cb->more_doc_require == 2) ? 'hide' : ''  }}">
                                        {{-- <div class="col-sm-8 m-t-15"> --}}
                                            <div class="form-group">
                                                <div class="col-md-offset-4 col-md-6 m-t-15">
                                                    <button class="btn btn-primary" type="submit" id="form-save" onclick="submit_form('1');return false">
                                                        <i class="fa fa-paper-plane"></i> บันทึก
                                                    </button>
            
                                                    <a class="btn btn-default" href="{{url('/certify/check_certificate-cb')}}">
                                                        <i class="fa fa-rotate-left"></i> ยกเลิก
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    

                                @endif

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>

            @if($history->count() > 0 )

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
                                                @foreach($history as $key => $item)
                                                    <tr >
                                                        <td class="text-center">{{$key +1 }}</td>
                                                        <td> {{HP::DateTimeThai($item->created_at) ?? '-'}} </td>
                                                        <td>
                                                            @if (in_array($item->system,[6,10])  && is_null($item->created_by))
                                                                {{   'ระบบบันทึก' }}
                                                            @else
                                                                {{ $item->user_created->FullName ?? '-'}}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($item->DataSystem != '-')
                                                                <button type="button" class="btn btn-link {{!is_null($item->details_auditors_cancel) ? 'text-danger' : ''}}" style="line-height: 16px;text-align: left;" data-toggle="modal" data-target="#HistoryModal{{$item->id}}">
                                                                    {{ @$item->DataSystem }}
                                                                    <br>
                                                                    <!-- แต่งตั้งคณะผู้ตรวจประเมิน  -->
                                                                    @if(!is_null($item->auditors_id))
                                                                        <span class="text-danger" style="font-size: 10px">
                                                                            {{ isset($item->CertiCBAuditorsTo->auditor) ? '( '.$item->CertiCBAuditorsTo->auditor.' )' : null }}
                                                                        </span>
                                                                    @endif  
                                                                </button>

                                                                @include ('certify/cb/check_certificate_cb.history_detail',['history' => $item])
                                                            @else 
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')

    <script src="{{ asset('plugins/components/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('plugins/components/icheck/icheck.init.js') }}"></script>
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <!-- input calendar thai -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js') }}"></script>
    <!-- thai extension -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js') }}"></script>
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js') }}"></script>
    <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script>
    <!-- Data Table -->
    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/jasny-bootstrap.js')}}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script> 

        jQuery(document).ready(function() {

            @if($certi_cb->status == 1 && HP_API_PID::check_api('check_api_certify_check_certificate_cb') && HP_API_PID::CheckDataApiPid($certi_cb,(new App\Models\Certify\ApplicantCB\CertiCb)->getTable()) != '')
                var id    =   '{!! $certi_cb->id !!}';
                var table =   '{!! (new App\Models\Certify\ApplicantCB\CertiCb)->getTable()  !!}';

                $.ajax({
                    type: 'get',
                    url: "{!! url('certify/function/check_api_pid') !!}" ,
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
            @endif

            @if(\Session::has('flash_message'))
                $.toast({
                    heading: 'Success!',
                    position: 'top-center',
                    text: '{{session()->get('flash_message')}}',
                    loaderBg: '#70b7d6',
                    icon: 'success',
                    hideAfter: 3000,
                    stack: 6
                });
            @endif

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
         
    </script>
@endpush