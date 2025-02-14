
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
        text-align: center;
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
 
        <p>
            <b>เรียน    {{  !empty($data->name) ?  $data->name   :  ''  }}</b>
        </p>
         <p>
            <b>เรื่อง แจ้งผลการประเมินแ  </b>
         </p> 

          <p class="indent50"> 
              ตามที่   {{ !empty($data->name) ?   $data->name  :  '' }}  คำขอเลขที่  {{ !empty($export->reference_refno) ?   $export->reference_refno  :  ''  }} 
              สำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรมได้ดำเนินการตรวจสอบ ผ่านผลการตรวจประเมินคณะผู้ตรวจประเมิน  {{ !empty($assessment->auditors_to->auditor) ?   $assessment->auditors_to->auditor  :  ''  }}
          </p>
          
         <p>
            จึงเรียนมาเพื่อทราบและโปรดดำเนินการ
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

