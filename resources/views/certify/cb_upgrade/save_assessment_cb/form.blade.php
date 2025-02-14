@push('css')
    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <div class="col-md-6">
                <label class="col-md-4 text-right"><span class="text-danger">*</span> เลขคำขอ : </label>
                <div class="col-md-8">
                    @if(isset($app_no))
                    {!! Form::select('auditors_id', 
                        $app_no, 
                        null,
                        ['class' => 'form-control',
                        'id' => 'auditors_id',
                        'placeholder'=>'- เลขคำขอ -', 
                        'required' => true]); !!}
                       {!! $errors->first('auditors_id', '<p class="help-block">:message</p>') !!}
                       <input type="hidden" class="form-control" value="{{ $assessment->app_certi_cb_id ?? null  }}" name="app_certi_cb_id"  id="app_certi_cb_id">   
                    @else 
                        <input type="text" class="form-control"    value="{{ $assessment->AuditorsTitle ?? null }}"   disabled >  
                    @endif
                
                </div>
            </div>
            <div class="col-md-6">
                <label class="col-md-4 text-right">ชื่อผู้ยื่นคำขอ : </label>
                <div class="col-md-8">
                    {!! Form::text('name', null,  ['class' => 'form-control', 'id'=>'applicant_name','readonly'=>true])!!}
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6">
                <label class="col-md-4 text-right">ชื่อหน่วยรับรอง/หน่วยตรวจสอบ : </label>
                <div class="col-md-8">
                    {!! Form::text('laboratory_name', null,  ['class' => 'form-control', 'id'=>'laboratory_name','readonly'=>true])!!}
                </div>
            </div>
            <div class="col-md-6">
                <label class="col-md-4 text-right"><span class="text-danger">*</span> วันที่ทำรายงาน : </label>
                <div class="col-md-8">
                     <div class="input-group">     
                        {!! Form::text('report_date', 
                        !empty($assessment->report_date) ? HP::revertDate($assessment->report_date,true) :  null,  
                        ['class' => 'form-control mydatepicker', 'id'=>'SaveDate',
                        'required'=>true,'placeholder'=>'dd/mm/yyyy'])!!}
                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6">
                <label class="col-md-5 text-right"><span class="text-danger">*</span> รายงานข้อบกพร่อง : </label>
                <div class="col-md-7">
                    <div class="row">
                        <label class="col-md-6">
                            {!! Form::radio('bug_report', '1', false , ['class'=>'check check-readonly', 'data-radio'=>'iradio_square-green','required'=>'required']) !!}  มี
                        </label>
                        <label class="col-md-6">
                            {!! Form::radio('bug_report', '2', true, ['class'=>'check check-readonly', 'data-radio'=>'iradio_square-red','required'=>'required']) !!} ไม่มี
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="col-md-4 text-right"><span class="text-danger">*</span>รายงานการตรวจประเมิน : </label>
                <div class="col-md-8">
                    @if(isset($assessment)  && !is_null($assessment->FileAttachAssessment1To)) 
                          <p id="RemoveFlie">
                            {{-- @if($assessment->FileAttachAssessment1To->file !='' && HP::checkFileStorage($attach_path. $assessment->FileAttachAssessment1To->file)) --}}
                                  <a href="{{url('certify/check/file_cb_client/'.$assessment->FileAttachAssessment1To->file.'/'.( !empty($assessment->FileAttachAssessment1To->file_client_name) ? $assessment->FileAttachAssessment1To->file_client_name : 'null' ))}}" 
                                    title="{{ !empty($assessment->FileAttachAssessment1To->file_client_name) ? $assessment->FileAttachAssessment1To->file_client_name :  basename($assessment->FileAttachAssessment1To->file) }}" target="_blank">
                                    {!! HP::FileExtension($assessment->FileAttachAssessment1To->file)  ?? '' !!}
                                </a>
                            {{-- @endif --}}
                            <button class="btn btn-danger btn-xs div_hide" type="button"
                             onclick="RemoveFlie({{$assessment->FileAttachAssessment1To->id}})">
                               <i class="icon-close"></i>
                           </button>   
                        </p>
                        <div id="AddFile"></div>      
                    @else 
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput" >
                        <div class="form-control" data-trigger="fileinput">
                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                        <span class="fileinput-new">เลือกไฟล์</span>
                        <span class="fileinput-exists">เปลี่ยน</span>
                            <input type="file" name="file" required class="check_max_size_file">
                            </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>      
 
 <div class="row form-group" id="div_file_scope">
     <div class="col-md-12">
         <div class="white-box" style="border: 2px solid #e5ebec;">
         <legend><h3>ขอบข่ายที่ขอรับการรับรอง (Scope)</h3></legend>   
               
            <div class="row">
                <div class="col-md-12 ">
                    <div id="other_attach-box">
                        <div class="form-group other_attach_scope">
                            <div class="col-md-4 text-right">
                                <label class="attach_remove"><!--<span class="text-danger">*</span> -->Scope  </label>
                            </div>
                            <div class="col-md-6">
                                <div class="fileinput fileinput-new input-group " data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">เลือกไฟล์</span>
                                        <span class="fileinput-exists">เปลี่ยน</span>
                                        <input type="file"  name="file_scope[]" class="check_max_size_file  ">  <!-- file_scope_required -->   
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                </div>
                                {!! $errors->first('attachs', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-2 text-left">
                                <button type="button" class="btn btn-sm btn-success attach_remove" id="attach_add_scope">
                                    <i class="icon-plus"></i>&nbsp;เพิ่ม
                                </button>
                                <div class="button_remove_scope"></div>
                            </div> 
                         </div>
                       </div>
                 </div>
            </div>
            <div class="row">
                <div class="col-md-12 ">
                    <div id="other_attach_report">
                        <div class="form-group other_attach_report">
                            <div class="col-md-4 text-right">
                                <label class="attach_remove"><!--<span class="text-danger">*</span> -->สรุปรายงานการตรวจทุกครั้ง </label>
                            </div>
                            <div class="col-md-6">
                                <div class="fileinput fileinput-new input-group " data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">เลือกไฟล์</span>
                                        <span class="fileinput-exists">เปลี่ยน</span>
                                        <input type="file"  name="file_report[]" class="check_max_size_file">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                </div>
                                {!! $errors->first('attachs', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-2 text-left">
                                <button type="button" class="btn btn-sm btn-success attach_remove" id="attach_add_report">
                                    <i class="icon-plus"></i>&nbsp;เพิ่ม
                                </button>
                                <div class="button_remove_report"></div>
                            </div> 
                         </div>
                       </div>
                 </div>
            </div>

        </div>
    </div>
</div>         
       
<div class="clearfix"></div>
<div class="row status_bug_report">

    <div class="row">
        <div class="col-md-12 text-right">
                <button type="button" class="   btn btn-success btn-sm div_hide" id="plus-row"><i class="icon-plus"></i> เพิ่ม</button>
        </div>
    </div>
   
    <div class="col-sm-12 m-t-15 "  id="box-required">
        <table class="table color-bordered-table primary-bordered-table">
            <thead>
            <tr>
                <th class="text-center" width="1%">ลำดับ</th>
                <th class="text-center" width="10%">รายงานที่</th>
                <th class="text-center" width="10%">ข้อบกพร่อง/ข้อสังเกต</th>
                <th class="text-center" width="10%">
                    มอก. <span id="Tis">
                        {{  !empty($assessment->CertiCBCostTo->FormulaTo->title) ?   str_replace("มอก.","",$assessment->CertiCBCostTo->FormulaTo->title) :''  }}
                    </span>
                </th>
                <th class="text-center" width="10%">ประเภท</th>
                {{-- <th class="text-center" width="10%">ผู้พบ</th> --}}
                <th class="text-center  div_hide " width="5%"> <i class="fa fa-pencil-square-o"></i></th>
            </tr>
            </thead>
            <tbody id="table-body">
             @foreach($bug as $key => $item)

                <tr>
                    <td class="text-center">
                        1
                    </td>
                    <td>
                        {!! Form::hidden('detail[id][]',!empty($item->id)?$item->id:null, ['class' => 'form-control '])  !!}
                        {!! Form::text('detail[report][]', $item->report ?? null,  ['class' => 'form-control input_required','required'=>true])!!}
                    </td>
                    <td>
                        {!! Form::text('detail[notice][]', $item->remark ?? null,  ['class' => 'form-control input_required','required'=>true])!!}
                    </td>
                    <td>
                        {!! Form::text('detail[no][]',  $item->no ?? null,  ['class' => 'form-control input_required','required'=>true])!!}
                    </td>
                    <td>
                        {!! Form::select('detail[type][]',
                          ['1'=>'ข้อบกพร่อง','2'=>'ข้อสังเกต'],
                            $item->type ?? null,
                            ['class' => 'form-control type input_required  select2',
                            'required'=>true,
                            'placeholder'=>'-เลือกประเภท-'])
                        !!}
                    </td>
                    {{-- <td>
                        {!! Form::select('detail[found][]',
                             $found ??  [],
                             $item->reporter_id ?? null,
                            ['class' => 'form-control found input_required select2',
                            'required'=>true,
                            'placeholder'=>'-เลือกผู้พบ-'])
                        !!}
                    </td> --}}
                    <td class="text-center   div_hide">
                        <button type="button" class="btn btn-danger btn-sm remove-row" ><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach 
            </tbody>
        </table>
    </div>
</div>
<div class="row status_bug_report">
    <div class="col-md-12   ">
        <div id="other_attach">
            <div class="form-group other_attach_item">
                <div class="col-md-2 text-right">
                    <label for="#" class="label_other_attach text-right ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ไฟล์แนบ : </label>

                </div>
                <div class="col-md-6">
                    <div class="fileinput fileinput-new input-group " data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">เลือกไฟล์</span>
                            <span class="fileinput-exists">เปลี่ยน</span>
                            {!! Form::file('attachs[]', null) !!}
                        </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2 text-left">
                    <button type="button" class="btn btn-sm btn-success attach-add div_hide" id="attach-add">
                        <i class="icon-plus"></i>&nbsp;เพิ่ม
                    </button>
                    <div class="button_remove"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 ">
        <div class="col-md-2 text-right"></div>
        <div class="col-md-6">
        @if(!is_null($assessment) && (count($assessment->FileAttachAssessment4Many) > 0 ) )
            @foreach($assessment->FileAttachAssessment4Many as  $key => $item)
              <p id="remove_attach_all{{$item->id}}">
                @if( $item->file  !='' && HP::checkFileStorage($attach_path. $item->file ))
                    <a href="{{url('certify/check/file_cb_client/'.$item->file.'/'.( !empty($item->file_client_name) ? $item->file_client_name : 'null' ))}}" 
                            title="{{ !empty($item->file_client_name) ? $item->file_client_name :  basename($item->file) }}" target="_blank">
                        {!! HP::FileExtension($item->file)  ?? '' !!}
                    </a>
                @endif
                <button class="btn btn-danger btn-xs deleteFlie div_hide"
                     type="button" onclick="deleteFlieAttachAll({{$item->id}})">
                     <i class="icon-close"></i>
                </button>   
            </p>
            @endforeach
        @endif
     </div>
   </div>
 </div>
 <br>
 <div class="clearfix"></div>

 @if($assessment->degree != 1 && $assessment->degree !=4  && $assessment->degree !=8)
 <div class="form-group">
     <div class="col-md-offset-4 col-md-4">
        <input type="hidden" name="previousUrl" id="previousUrl" value="{{ $previousUrl ?? null}}">
         <div  class="status_bug_report"> 
            <label>{!! Form::checkbox('main_state', '2', false, ['class'=>'check','data-checkbox'=>"icheckbox_flat-red"]) !!} 
                &nbsp;ปิดผลการตรวจประเมิน&nbsp;
             </label>
         </div> 
 
         <div id="degree_btn"></div>
{{--  
         <button type="submit" id="submit_draft"  class="btn btn-success "onclick="submit_form('0');return false;"> 
             <i class="fa fa-file-o"></i> ฉบับร่าง
         </button> --}}
 
         <button class="btn btn-primary" type="submit" id="form-save"  onclick="submit_form('1');return false;">
             <i class="fa fa-paper-plane"></i> บันทึก
         </button>
 
         @can('view-'.str_slug('saveassessmentcb'))
             <a class="btn btn-default" href="{{  url("$previousUrl") }}">
                 <i class="fa fa-rotate-left"></i> ยกเลิก
             </a>
         @endcan
     </div>
 </div>
 @else 
 <div class="clearfix"></div>
    <a  href="{{  url("$previousUrl")  }}"  class="btn btn-default btn-lg btn-block">
       <i class="fa fa-rotate-left"></i>
      <b>กลับ</b>
  </a>
 
 @endif
 

@push('js')
  <script src="{{ asset('plugins/components/icheck/icheck.min.js') }}"></script>
  <script src="{{ asset('plugins/components/icheck/icheck.init.js') }}"></script>
    <!-- input calendar thai -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js') }}"></script>
    <!-- thai extension -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js') }}"></script>
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js') }}"></script>
    <script src="{{asset('js/jasny-bootstrap.js')}}"></script>
    <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script>
    <script>
        $(document).ready(function () {
             check_max_size_file();
            //เพิ่มไฟล์แนบ
            $('#attach_add_scope').click(function(event) {
                $('.other_attach_scope:first').clone().appendTo('#other_attach-box');
                $('.other_attach_scope:last').find('input').val('');
                $('.other_attach_scope:last').find('a.fileinput-exists').click();
                $('.other_attach_scope:last').find('a.view-attach').remove();
                $('.other_attach_scope:last').find('.attach_remove').remove();
                $('.other_attach_scope:last').find('.button_remove_scope').html('<button class="btn btn-danger btn-sm attach_remove_scope" type="button"> <i class="icon-close"></i>  </button>');
                check_max_size_file();
            });

            //ลบไฟล์แนบ
            $('body').on('click', '.attach_remove_scope', function(event) {
                $(this).parent().parent().parent().remove();
            });

            //เพิ่มไฟล์แนบ
            $('#attach_add_report').click(function(event) {
                $('.other_attach_report:first').clone().appendTo('#other_attach_report');
                $('.other_attach_report:last').find('input').val('');
                $('.other_attach_report:last').find('a.fileinput-exists').click();
                $('.other_attach_report:last').find('a.view-attach').remove();
                $('.other_attach_report:last').find('.attach_remove').remove();
                $('.other_attach_report:last').find('.button_remove_report').html('<button class="btn btn-danger btn-sm attach_remove_report" type="button"> <i class="icon-close"></i>  </button>');
                check_max_size_file();
            });

            //ลบไฟล์แนบ
            $('body').on('click', '.attach_remove_report', function(event) {
                $(this).parent().parent().parent().remove();
            });
        });

    </script>
  <script>
    function  submit_form(degree){ 

var bug_report = $("input[name=bug_report]:checked").val(); 
var vehicle =  $("input[name=vehicle]:checked").val();
var main_state =  $("input[name=main_state]:checked").val();
    if(bug_report == 2){
        let i = 4;
        Swal.fire({
                title:"ยืนยันทำรายการ !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.value) {
                        $('#degree_btn').html('<input type="text" name="degree" value="'+i+'" hidden>');
                        $('#form_assessment').submit();
                    }
               })
       
     }else{
  
        if(degree == 0){  // ฉบับร่าง
            Swal.fire({
                title:'ยืนยันทำฉบับร่างรายงานข้อบกพร่อง !',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.value) {
                        $('#degree_btn').html('<input type="text" name="degree" value="' + degree + '" hidden>');
                        $('#form_assessment').submit();
                    }
            })
        }else{
            let title = '';
            let l = '';
            if(main_state == 2){
                title =  'ยืนยันปิดผลการตรวจประเมิน !';
                l = 8;
            }else{
          
                title =  'ยืนยันทำรายงานข้อบกพร่อง !';
                l = 1;
            }
         
            Swal.fire({
                title:title,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.value) {
                        $('#degree_btn').html('<input type="text" name="degree" value="' + l + '" hidden>');
                        $('#form_assessment').submit();
                    }
            })
        }   
  
       } 
    }
    jQuery(document).ready(function() {
               $('#form_assessment').parsley().on('field:validated', function() {
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
 

        let check_readonly = '{{ ($assessment->bug_report == 1)  ? 1 : 2 }}';
        if(check_readonly == 1){
            $('.check-readonly').prop('disabled', true);
            $('.check-readonly').parent().removeClass('disabled');
            $('.check-readonly').parent().css({"background-color": "rgb(238, 238, 238);","border-radius":"50%"});
        }
     
        //ปฎิทิน
        $('.mydatepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            language:'th-th',
            format: 'dd/mm/yyyy',
            orientation: 'bottom'
        });
        
        $("#auditors_id").change(function(){
            
            // $('select.found').html('<option value="">-เลือกผู้พบ-</option>').select2();
            if($(this).val()!=""){
                $.ajax({
                    url:'{{ url('certify/save_assessment-cb/certi_cb') }}/' + $(this).val()
                }).done(function( object ) {
                    
                        if(object.certi_cb != '-'){ 
                            let certi_cb = object.certi_cb;
                            $('#applicant_name').val(certi_cb.name); 
                            $('#laboratory_name').val(certi_cb.name_standard);
                            $('#Tis').html(certi_cb.tis); 
                            $('#app_certi_cb_id').val(certi_cb.app_certi_cb_id); 
                        }else{
                            $('#applicant_name').val(''); 
                            $('#laboratory_name').val(''); 
                            $('#Tis').html(''); 
                            $('#app_certi_cb_id').val(''); 
                        }

                        // if(object.auditors_list != '-'){ 
                        //     let auditors = object.auditors_list;
                        //     $.each(auditors, function( index, data ) {
                        //          $('select.found').append('<option value="'+data.user_id+'">'+ data.temp_users+'</option>');
                        //     });
                        // }
                });
            }else{
                $('#applicant_name').val(''); 
                $('#laboratory_name').val(''); 
                $('#app_certi_cb_id').val(''); 
            }

        });
          //  รายงานข้อบกพร่อง
         $("input[name=bug_report]").on("ifChanged",function(){
            bug_report();
         });
            bug_report();
            function bug_report(){
            var row = $("input[name=bug_report]:checked").val(); 
                if(row == "1"){ 
                    $('.status_bug_report').show(200); 
                    $('#submit_draft').show(200); 
                    $('#box-required').find('.input_required').prop('required', true);
                    $('#div_file_scope').hide(400); 
                    $('#checkbox_document').hide(400); 
                    $('.file_scope_required').prop('required', false);
                } else{
                    $('.status_bug_report').hide(400);
                    $('#submit_draft').hide(400); 
                    $('#box-required').find('.input_required').prop('required', false);
                    $('#div_file_scope').show(200);
               
                    $('#checkbox_document').show(200);  
                    $('.file_scope_required').prop('required', true);
                }
            }

        //เพิ่มแถว
        $('#plus-row').click(function(event) {
          //Clone
          $('#table-body').children('tr:first()').clone().appendTo('#table-body');
          //Clear value
            var row = $('#table-body').children('tr:last()');
            row.find('select.select2').val('');
            row.find('select.select2').prev().remove();
            row.find('select.select2').removeAttr('style');
            row.find('select.select2').select2();
            row.find('input[type="text"],textarea').val('');
            row.find('.file_attachs').html('');
            row.find('.parsley-required').html('');
            row.find('input[type="hidden"]').val('');
          //เลขรัน 
          ResetTableNumber();
   
        });
        //ลบแถว
        $('body').on('click', '.remove-row', function(){
          $(this).parent().parent().remove();
          ResetTableNumber();
        });
        ResetTableNumber();



    //เพิ่มไฟล์แนบ
    $('#attach-add').click(function(event) {
        $('.other_attach_item:first').clone().appendTo('#other_attach');
        $('.other_attach_item:last').find('input').val('');
        $('.other_attach_item:last').find('a.fileinput-exists').click();
        $('.other_attach_item:last').find('a.view-attach').remove();
        $('.other_attach_item:last').find('.label_other_attach').remove();
        $('.other_attach_item:last').find('button.attach-add').remove();
        $('.other_attach_item:last').find('.button_remove').html('<button class="btn btn-danger btn-sm attach-remove" type="button"> <i class="icon-close"></i>  </button>');
        check_max_size_file();
    });

    //ลบไฟล์แนบ
    $('body').on('click', '.attach-remove', function(event) {
        $(this).parent().parent().parent().remove();
    });

  });
          //รีเซตเลขลำดับ
     function ResetTableNumber(){
      var rows = $('#table-body').children(); //แถวทั้งหมด
      (rows.length==1)?$('.remove-row').hide():$('.remove-row').show();
        rows.each(function(index, el) {
        //เลขรัน
        $(el).children().first().html(index+1);
      });
    }

function  RemoveFlie(id){
        var html =[];
                html += '<div class="fileinput fileinput-new input-group" data-provides="fileinput" >';
                html += '<div class="form-control" data-trigger="fileinput">';
                html += '<i class="glyphicon glyphicon-file fileinput-exists"></i>';
                html += '<span class="fileinput-filename"></span>';
                html += '</div>';
                html += '<span class="input-group-addon btn btn-default btn-file">';
                html += '<span class="fileinput-new">เลือกไฟล์</span>';
                html += '<span class="fileinput-exists">เปลี่ยน</span>';
                html += '  <input type="file" name="file" required >';
                html += '</span>';
                html += '<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>';
                html += '</div>';
    Swal.fire({
            icon: 'error',
            title: 'ยื่นยันการลบไฟล์แนบ !',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.value) {
                   $.ajax({
                        url: "{!! url('/certify/check_certificate-cb/delete_file') !!}"  + "/" + id
                    }).done(function( object ) {
                        if(object == 'true'){
                            $('#RemoveFlie').remove();
                            $("#AddFile").append(html);
                            check_max_size_file();
                        }else{
                            Swal.fire('ข้อมูลผิดพลาด');
                        }
                    });

                }
            })
     }

     function  RemoveFlieScope(id){
        var html =[];
                html += '<div class="fileinput fileinput-new input-group" data-provides="fileinput" >';
                html += '<div class="form-control" data-trigger="fileinput">';
                html += '<i class="glyphicon glyphicon-file fileinput-exists"></i>';
                html += '<span class="fileinput-filename"></span>';
                html += '</div>';
                html += '<span class="input-group-addon btn btn-default btn-file">';
                html += '<span class="fileinput-new">เลือกไฟล์</span>';
                html += '<span class="fileinput-exists">เปลี่ยน</span>';
                html += '<input type="file" name="file_scope"  class="file_scope_required">';
                html += '</span>';
                html += '<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>';
                html += '</div>';
    Swal.fire({
            icon: 'error',
            title: 'ยื่นยันการลบไฟล์แนบ !',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.value) {
                   $.ajax({
                        url: "{!! url('/certify/check_certificate-cb/delete_file') !!}"  + "/" + id
                    }).done(function( object ) {
                        if(object == 'true'){
                            $('#RemoveFlieScope').remove();
                            $("#AddFileScope").append(html);
                            check_max_size_file();
                        }else{
                            Swal.fire('ข้อมูลผิดพลาด');
                        }
                    });

                }
            })
     }

     
    function  deleteFlieAttachAll(id){
      Swal.fire({
            icon: 'error',
            title: 'ยื่นยันการลบไฟล์แนบ !',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.value) {
                   $.ajax({
                        url: "{!! url('/certify/check_certificate-cb/delete_file') !!}"  + "/" + id
                    }).done(function( object ) {
                        if(object == 'true'){
                            $('#remove_attach_all'+id).remove();
                        }else{
                            Swal.fire('ข้อมูลผิดพลาด');
                        }
                    });

                }
            })
     }
</script>
@endpush

