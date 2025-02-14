
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <style>
       #style{

            padding: 5px;
            border: 5px solid gray;
            margin: 0;
            
       }    
       #customers td, #customers th {
            border: 1px solid #ddd;
            padding: 8px;
            }

        #customers th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #66ccff;
        color: #000000;
        }   
        .indent50 {
        text-indent: 50px;
        } 
        .indent100 {
        text-indent: 100px;
        } 
   </style>
</head>
<body>
   <div id="style">
    @if ($status == 3)  <!-- ขอเอกสารเพิ่มเติม -->
            <p>
                <b>เรียน  {{  !empty($certi_cb->name) ?  $certi_cb->name   :  ''  }} </b>
            </p>
            <p>
                <b>เรื่อง  ขอเอกสารเพิ่มเติม  </b>
            </p>
            <p class="indent50"> 
                ตามที่   {{  !empty($certi_cb->name) ?   $certi_cb->name   :  ''  }} 
                ได้ยื่นคำขอรับบริการยืนยันความสามารถหน่วยรับรอง
                ผ่านระบบการรับรองระบบงาน  
                คำขอเลขที่  {{  !empty($certi_cb->name) ?   $certi_cb->app_no  :  ''  }} 
                เมื่อวันที่  {{  !empty($certi_cb->start_date) ?  HP::formatDateThaiFull($certi_cb->start_date) :  ''  }} 
                นั้น
            </p>  
            <p class="indent50"> 
                สำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรมพิจารณาแล้ว  
                เห็นควรให้   {{  !empty($certi_cb->name) ?   $certi_cb->name  :  ''  }} 
            </p>  
    @endif

        <p class="indent50">   
            ดำเนินการแนบเอกสารเพิ่มเติม ภายใน 5 วัน นับจากวันที่สำนักงานฯแจ้ง เมื่อครบกำหนดแล้วหากท่านยันไม่ดำเนินการ สำนักงานฯ จะถือว่าท่านไม่สงค์จะยื่นคำขอและแจ้งยกเลิกคำขอต่อไป 
        </p>
        <p class="indent100">   
           เหตุผล :  {{$desc ?? null}}
        </p>
        @if($attachs != '-')
        <p class="indent50">  
            ไฟล์แนบ
        </p>
           @foreach($attachs as $item)
              <p class="indent100">
                @if($item->file !='' && HP::checkFileStorage($item->attach_path.$item->file))
                <a href="{{url('certify/check/file_cb_client/'.$item->file.'/'.( !empty($item->file_client_name) ? $item->file_client_name :   basename($item->file) ))}}" target="_blank">
                        {{  !empty($item->file_client_name) ? $item->file_client_name :  $item->file  }}
                </a>
                @endif
             </p>
            @endforeach
        @endif
        <p>
            จึงเรียนมาเพื่อพิจารณาดำเนินการต่อไป
            <a href="{{ $url ?? '/' }}"class="btn btn-link" target="_blank"> เข้าสู่ระบบ </a>
            <br>
            --------------------------
        </p>
        <img src="{!! asset('plugins/images/anchor_sm200.jpg') !!}"  height="200px" width="200px"/>
        <p>
            {!!auth()->user()->UserContact!!}
        </p>
    </div> 
</body>
</html>
