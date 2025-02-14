<div class="row">
    <div class="col-sm-12 m-t-15" v-if="isTable">
        <table class="table color-bordered-table primary-bordered-table">
            <thead>
            <tr>
                <th class="text-center" width="2%">ลำดับ</th>
                <th class="text-center" width="10%">รายงานที่</th>
                <th class="text-center" width="18%">ผลการประเมินที่พบ</th>
                <th class="text-center" width="20%" >แนวทางการแก้ไข</th>
                <th class="text-center" width="12%" >ผลการประเมิน</th>
                <th class="text-center" width="13%" >หลักฐาน</th>
            </tr>
            </thead>
            <tbody  id="table_body">
                @if(count($assessment->CertiCBBugMany) > 0)
                @foreach($assessment->CertiCBBugMany as $key => $item)
                @php
                    $type =   ['1'=>'ข้อบกพร่อง','2'=>'ข้อสังเกต'];
                    $status = '';
                    if($item->status == 1){
                       $status = 'check_readonly';
                    }
                    $file_status = '';
                    if($item->file_status == 1){
                       $file_status = 'check_readonly';
                    }
                @endphp
                <tr>
                    <td class="text-center">
                        {{$key+1}}
                    </td>
                    <td>
                        {!! Form::hidden('id[]',!empty($item->id)?$item->id:null, ['class' => 'form-control'])  !!}
                       {!! Form::text('report[]', $item->report ?? null,  ['class' => 'form-control ','disabled'=>true])!!}
                    </td>
                    <td>
                  
                        {!! Form::text('notice[]', $item->remark ?? null,  ['class' => 'form-control notice','disabled'=>true])!!}
                    </td>
                    <td>
                         {!! Form::textarea('details', $item->details ?? null, [ 'class' => 'form-control','rows' => 3,'disabled'=>true]) !!} 
                    </td>
                    <td  class="text-center">
                          <label>
                              {!! Form::checkbox('status['.$item->id.']', '1', !empty($item->status == 1 ) ? true : false, 
                            ['class'=>"check checkbox_status $status assessment_results",'data-checkbox'=>"icheckbox_flat-green", "data-key"=>($key+1)]) !!}
                              &nbsp;ผ่าน &nbsp;
                        </label>
                   </td>
                   <td  class="text-center">

                       @if(!is_null($item->attachs))
                              <a href="{{url('certify/check/file_cb_client/'.$item->attachs.'/'.( !empty($item->attach_client_name) ? $item->attach_client_name :   basename($item->attachs) ))}}" 
                                  title="{{ !empty($item->attach_client_name) ? $item->attach_client_name :  basename($item->attachs) }}" target="_blank">
                                  {!! HP::FileExtension($item->attachs)  ?? '' !!}
                             </a>
                             &nbsp;&nbsp;&nbsp; 
                            <label>
                                {!! Form::checkbox('file_status['.$item->id.']', '1', !empty($item->file_status == 1 ) ? true : false, 
                                ['class'=>"check $file_status file_status",'data-checkbox'=>"icheckbox_flat-green", "data-key"=>($key+1)]) 
                                !!} &nbsp;ผ่าน &nbsp;
                           </label>
                        @endif

                   </td>
                 </tr>
                   @endforeach
                  @endif
            </tbody>
        </table>
    </div>
</div>
<div class="row" id="div_comment">
    <div class="col-sm-3 text-right">ระบุข้อคิดเห็น (ผลการประเมิน) :</div>
    <div class="col-sm-9">
        <table class="table color-bordered-table primary-bordered-table">
            <thead>
                <tr>
                    <th class="text-center" width="2%">ลำดับ</th>
                    <th class="text-center" width="40%">ผลการประเมินที่พบ</th>
                    <th class="text-center" width="58%">ข้อคิดเห็นของคณะผู้ตรวจประเมิน</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @if(count($assessment->CertiCBBugMany) > 0)
                @foreach($assessment->CertiCBBugMany as $key => $item)
                        @if($item->status != 1)
                            <tr>
                                <td class="text-center">
                                    {{$key+1}}
                                </td>
                                <td>
                                    {{ $item->remark ?? null }}
                                </td>
                                <td>
                                    <input type="hidden" class="type_itme" value="{{$item->id}}">
                                    {!! Form::textarea('comment['.$item->id.']',null, [ 'class' => 'form-control','rows' => 3,'required'=>true]) !!} 
                                </td>
                            </tr>
                        @endif
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>


<div class="row" id="div_file_comment">
    <div class="col-sm-3 text-right">ระบุข้อคิดเห็น (หลักฐาน) :</div>
    <div class="col-sm-9">
        <table class="table color-bordered-table primary-bordered-table">
            <thead>
                <tr>
                    <th class="text-center" width="2%">ลำดับ</th>
                    <th class="text-center" width="40%">ผลการประเมินที่พบ</th>
                    <th class="text-center" width="58%">ข้อคิดเห็นของคณะผู้ตรวจประเมิน</th>
                </tr>
            </thead>
            <tbody id="table_body_file">
                @if(count($assessment->CertiCBBugMany) > 0)
                @foreach($assessment->CertiCBBugMany as $key => $item)
                        @if($item->status == 1 &&   $item->file_status != 1)
                            <tr>
                                <td class="text-center">
                                    {{$key+1}}
                                </td>
                                <td>
                                    {{ $item->remark ?? null }}
                                </td>
                                <td>
                                     <input type="hidden" class="type_itme" value="{{$item->id}}">
                                    {!! Form::textarea('file_comment['.$item->id.']', null ,  ['class' => 'form-control file_comment','rows' => 3,'required'=>true])!!}
                                </td>
                            </tr>
                        @endif
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="row div_hide_show_scope"  id="div_details">
    <div class="col-md-12">
         <div class="white-box">

            <div class="row ">
                <div class="col-sm-4 text-right"><span class="text-danger">*</span>รายงานปิด Car  :</div>
                <div class="col-sm-6">
                    @if(isset($assessment)  && !is_null($assessment->FileAttachAssessment4To)) 
                    <p id="RemoveFlieScope">
                      {{-- @if($assessment->FileAttachAssessment4To->file !='' && HP::checkFileStorage($attach_path.$assessment->FileAttachAssessment4To->file)) --}}
                         <a href="{{url('certify/check/file_cb_client/'.$assessment->FileAttachAssessment4To->file.'/'.( !empty($assessment->FileAttachAssessment4To->file_client_name) ? $assessment->FileAttachAssessment4To->file_client_name : 'null' ))}}" 
                                title="{{ !empty($assessment->FileAttachAssessment4To->file_client_name) ? $assessment->FileAttachAssessment4To->file_client_name :  basename($assessment->FileAttachAssessment4To->file) }}" target="_blank">
                            {!! HP::FileExtension($assessment->FileAttachAssessment4To->file)  ?? '' !!}
                       </a>
                      {{-- @endif --}}
                    </p>
                    @else 
                       <div class="fileinput fileinput-new input-group" data-provides="fileinput" >
                        <div class="form-control" data-trigger="fileinput">
                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                        <span class="fileinput-new">เลือกไฟล์</span>
                        <span class="fileinput-exists">เปลี่ยน</span>
                            <input type="file" name="file_car" class="report_scope check_max_size_file" >
                            </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                        </div>
                    @endif
                </div>
            </div>
            
   

     <div class="row form-group">
         <div class="col-md-12">
             <div class="white-box" style="border: 2px solid #e5ebec;">
             <legend><h3>ขอบข่ายที่ขอรับการรับรอง (Scope)</h3></legend>   
                   
                <div class="row">
                    <div class="col-md-12 ">
                        <div id="other_attach-box">
                        @if(!is_null($assessment) && (count($assessment->FileAttachAssessment2Many) > 0 ) )
                            @foreach($assessment->FileAttachAssessment2Many as  $key => $item)
                              <p id="remove_attach_all{{$item->id}}">
                           
                                {{-- @if($item->file !='' && HP::checkFileStorage($attach_path.$item->file)) --}}
                                <a href="{{url('certify/check/file_cb_client/'.$item->file.'/'.( !empty($item->file_client_name) ? $item->file_client_name : 'null' ))}}" 
                                    title="{{ !empty($item->file_client_name) ? $item->file_client_name :  basename($item->file) }}" target="_blank">
                                        {!! HP::FileExtension($item->file)  ?? '' !!}
                                    </a>  
                                 {{-- @endif --}}
                              </p>
                            @endforeach
                        @else 
                            <div class="form-group other_attach_scope">
                                <div class="col-md-4 text-right">
                                    <label class="attach_remove"><span class="text-danger">*</span>รายงาน Scope  </label>
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
                                            <input type="file"  name="file_scope[]" class="file_scope_required   check_max_size_file">
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
                        @endif
                          
                           </div>
                     </div>
                </div>
                <div class="row">
                    <div class="col-md-12 ">
                        <div id="other_attach_report">
                            @if(!is_null($assessment) && (count($assessment->FileAttachAssessment3Many) > 0 ) )
                            @foreach($assessment->FileAttachAssessment3Many as  $key => $item)
                              <p id="remove_attach_all{{$item->id}}">
                 
                                {{-- @if($item->file !='' && HP::checkFileStorage($attach_path.$item->file)) --}}
                                    <a href="{{url('certify/check/file_cb_client/'.$item->file.'/'.( !empty($item->file_client_name) ? $item->file_client_name : 'null' ))}}" 
                                        title="{{ !empty($item->file_client_name) ? $item->file_client_name :  basename($item->file) }}" target="_blank">
                                        {!! HP::FileExtension($item->file)  ?? '' !!}
                                    </a>  
                                {{-- @endif --}}
                              </p>
                            @endforeach
                          @else 
                          <div class="form-group other_attach_report">
                            <div class="col-md-4 text-right">
                                <label class="attach_remove"><span class="text-danger">*</span> สรุปรายงานการตรวจทุกครั้ง </label>
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
                                        <input type="file"  name="file_report[]" class="file_scope_required check_max_size_file">
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
                          @endif

                           </div>
                     </div>
                </div>
    
            </div>
        </div>
    </div>         
          


        </div>
    </div>     
 </div> 

@push('js')

<script>
    $(document).ready(function(){
        // ResetTableFileNumber();
        check_max_size_file();
        $('.div_hide_show_scope').hide();
        $(".file_status").on("ifChanged",function(){
            var itme =   $(this).parent().parent().parent().parent().find('input[type="hidden"]').val();
            if($(this).prop('checked')){
                $('#table_body_file').find('.type_itme[value="'+itme+'"]').parent().parent().remove();
            }else{
             var notice_id =   $(this).parent().parent().parent().parent().find('.notice').val();
             let key = $(this).data('key');
             var table = $('#table_body_file');
             var  html = [];
                  html += '<tr>';
                  html += '<td class="text-center">'+key+'</td>';
                  html += '<td>'+notice_id+'</td>';
                  html += '<td> <input type="hidden" class="type_itme" value="'+itme+'"> <textarea  name="file_comment['+itme+']" rows="3" cols="50" required  class="form-control"> </textarea>  </td>';
                  html += '</tr>';
                  table.append(html);
            }
           
            // ResetTableFileNumber();
            // 
            let file_status =  $(".file_status:checked").length;
            let notice = '{{ !empty($assessment->CertiCBBugMany) ? count($assessment->CertiCBBugMany) : 0 }}';
            if(file_status == notice){ 
                $('.div_hide_show_scope').show();
                $('.status_bug_report').hide();
                $('.report_scope').prop('required', true);
                $('.file_scope_required').prop('required', true);
            }else{
                $('.div_hide_show_scope').hide();
                $('.status_bug_report').show();
                $('.report_scope').prop('required', false);
                $('.file_scope_required').prop('required', false);
            } 

         });

        let file_status =    $('#table_body').find('.file_status:not(:checked)').length;
        if(file_status > 0){
            $('#div_file_comment').show();   
            $('.file_comment').prop('required', true);
        }else{
            $('#div_file_comment').hide();   
            $('.file_comment').prop('required', false);
        }

        let results =  $(".assessment_results:checked").length;
            let notice = '{{ !empty($assessment->CertiCBBugMany) ? count($assessment->CertiCBBugMany) : 0 }}';
            if(results == notice){
                $('#div_comment').hide();
            }

           //รีเซตเลขลำดับ
        // function ResetTableFileNumber(){
        //     var rows = $('#table_body_file').children(); //แถวทั้งหมด
        //     rows.each(function(index, el) {
        //         //เลขรัน
        //         $(el).children().first().html(index+1);
        //     });
        //     }

       

      $("#button_audit_report").click(function(){
          let row =  $(this).parent();
          var  html = [];
          let id = '{{ !empty($find_notice->id) ?  $find_notice->id : null }}';
          if(id != null  && confirm("ยืนยันการลบหลักฐาน !")){
               $("#audit_report").show(); 
                $.ajax({
                url: "{!! url('certify/save_assessment/remove_file') !!}" + "/" + id
                 }).done(function( object ) {
                    if(object.status = true){
                        row.remove(); 
                        html += '<div class="fileinput fileinput-new input-group " data-provides="fileinput">';
                        html += '<div class="form-control" data-trigger="fileinput">';
                        html += '<i class="glyphicon glyphicon-file fileinput-exists"></i>';
                        html += '<span class="fileinput-filename"></span>';
                        html += '</div>';
                        html += '<span class="input-group-addon btn btn-default btn-file">';
                        html += ' <span class="fileinput-new">เลือกไฟล์</span>';
                        html += ' <span class="fileinput-exists">เปลี่ยน</span>';
                        html += ' <input type="file" name="file" required class="input_required"></span>';
                        html += ' <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a></div>';
                        $("#audit_report").html(html); 
                        check_max_size_file();
                    }else{
                        alert('ไม่สามารถลบหลักฐานได้ !');  
                    }
               });
           }
      });





    });

    function remove_attachs(keys){
          let id = '{{ !empty($find_notice->id) ?  $find_notice->id : null }}';
          if(id != null  && confirm("ยืนยันการลบหลักฐาน !")){
               $("#audit_report").show(); 
                $.ajax({
                url: "{!! url('certify/save_assessment/remove_attachs') !!}" + "/" + id  + "/" +  keys
                 }).done(function( object ) {
                    if(object.status = true){
                        $('#remove_attachs').find('.attachs'+keys).remove();
                    }else{
                        alert('ไม่สามารถลบหลักฐานได้ !');  
                    }
               });
           }
      }

      function remove_file_car(keys){
          let id = '{{ !empty($find_notice->id) ?  $find_notice->id : null }}';
          if(id != null  && confirm("ยืนยันการลบหลักฐาน !")){
               $("#audit_report").show(); 
                $.ajax({
                url: "{!! url('certify/save_assessment/remove_file_car') !!}" + "/" + id  + "/" +  keys
                 }).done(function( object ) {
                    if(object.status = true){
                        $('#remove_file_car').find('.attachs'+keys).remove();
                    }else{
                        alert('ไม่สามารถลบหลักฐานได้ !');  
                    }
               });
           }
      }
    </script>
 <script>

   jQuery(document).ready(function() {
         ResetTableNumber();
        //  รายงานข้อบกพร่อง
        $(".checkbox_status").on("ifChanged",function(){
              var itme =   $(this).parent().parent().parent().parent().find('input[type="hidden"]').val();
              var notice =   $(this).parent().parent().parent().parent().find('.notice').val();
              let key = $(this).data('key');
                if($(this).prop('checked')){
                    $('#table-body').find('.type_itme[value="'+itme+'"]').parent().parent().remove();
                }else{
                    radio_status(itme,notice,key);
                }
             });
  
         function radio_status(itme,notice,key){
            var table = $('#table-body');
                 var  html = [];
                      html += '<tr>';
                      html += '<td class="text-center">'+key+'</td>';
                      html += '<td>'+notice+'</td>';
                      html += '<td> <input type="hidden" class="type_itme" value="'+itme+'">  <textarea  name="comment['+itme+']" rows="3" cols="50" required  class="form-control"> </textarea>  </td>';
                      html += '</tr>';
                      table.append(html);
                ResetTableNumber();
        }
 
        //รีเซตเลขลำดับ
        function ResetTableNumber(){
          var rows = $('#table-body').children(); //แถวทั้งหมด
          rows.each(function(index, el) {
            //เลขรัน
            $(el).children().first().html(index+1);
          });
        }
 
   });
  </script>
@endpush
