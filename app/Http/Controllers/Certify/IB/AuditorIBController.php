<?php

namespace App\Http\Controllers\Certify\IB;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use stdClass;
use Storage;
use HP;
use DB;
use Carbon\Carbon;
use App\User;

use App\Models\Certify\Applicant\CostDetails;
use  App\Models\Bcertify\StatusAuditor;
use App\Models\Certify\ApplicantIB\CertiIBAuditors;
use App\Models\Certify\ApplicantIB\CertiIBAuditorsDate;
use App\Models\Certify\ApplicantIB\CertiIBAuditorsCost;
use App\Models\Certify\ApplicantIB\CertiIBAuditorsStatus;
use App\Models\Certify\ApplicantIB\CertiIBAuditorsList;
use App\Models\Certify\ApplicantIB\CertiIBCheck;
use App\Models\Certify\ApplicantIB\CertiIBPayInOne; 

use App\Models\Certify\ApplicantIB\CertiIb;
use App\Models\Certify\ApplicantIB\CertiIBAttachAll;
use App\Models\Certify\ApplicantIB\CertiIbHistory;
use App\Models\Certify\ApplicantIB\CertiIBCost; // ประมาณการค่าใช้จ่าย
use App\Models\Certify\ApplicantIB\CertiIBSaveAssessment;
use App\Models\Certify\ApplicantIB\CertiIBReview;
use App\Models\Bcertify\AuditorExpertise;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\IB\IBAuditorsMail;
class AuditorIBController extends Controller
{

    private $attach_path;//ที่เก็บไฟล์แนบ
    public function __construct()
    {
        $this->middleware('auth');
        $this->attach_path = 'files/applicants/check_files_ib/';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function index(Request $request)
    {
        $model = str_slug('auditorib','-');
        if(auth()->user()->can('view-'.$model)) {

            $keyword = $request->get('search');
            $filter = [];

            $filter['filter_status'] = $request->get('filter_status', '');
            $filter['filter_search'] = $request->get('filter_search', '');
            $filter['perPage'] = $request->get('perPage', 10);


            $Query = new CertiIBAuditors;
            $Query = $Query->select('app_certi_ib_auditors.*');
            if ($filter['filter_status']!='') {
                if($filter['filter_status'] == 0){
                    $Query = $Query->whereNull('status');
                }else{
                    $Query = $Query->where('status', $filter['filter_status']);
                }
            }

            if ($filter['filter_search'] != '') {
                $CertiIb  = CertiIb::where('app_no', 'like', '%'.$filter['filter_search'].'%')->pluck('id');
                $Query = $Query->whereIn('app_certi_ib_id', $CertiIb);
            }
             //เจ้าหน้าที่ IB และไม่มีสิทธิ์ admin , ผอ , ผก , ลท.
            if(in_array("27",auth()->user()->RoleListId) && auth()->user()->SetRolesAdminCertify() == "false" ){
                $check = CertiIBCheck::where('user_id',auth()->user()->runrecno)->pluck('app_certi_ib_id'); // เช็คเจ้าหน้าที่ IB
                if(isset($check) && count($check) > 0  ) {
                     $Query = $Query->LeftJoin('app_certi_ib_check','app_certi_ib_check.app_certi_ib_id','=','app_certi_ib_auditors.app_certi_ib_id')
                                    ->where('user_id',auth()->user()->runrecno);  //เจ้าหน้าที่  IB ที่ได้มอบหมาย
                }else{
                    $Query = $Query->whereIn('id',['']);  // ไม่ตรงกับเงื่อนไข
                }
            }
            $auditors =  $Query->orderby('id','desc')
                                // ->sortable()
                                ->paginate($filter['perPage']);



            return view('certify/ib.auditor_ib.index', compact('auditors','filter'));
        }
        abort(403);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $model = str_slug('auditorib','-');
        if(auth()->user()->can('add-'.$model)) {
            $previousUrl = app('url')->previous();
            $app_no = [];
            //เจ้าหน้าที่ IB และไม่มีสิทธิ์ admin , ผอ , ผก , ลท.
           if(in_array("27",auth()->user()->RoleListId) && auth()->user()->SetRolesAdminCertify() == "false" ){
               $check = CertiIBCheck::where('user_id',auth()->user()->runrecno)->pluck('app_certi_ib_id'); // เช็คเจ้าหน้าที่ IB
               if(count($check) > 0 ){
                   $app_no= CertiIb::whereIn('id',$check)
                                    ->whereIn('status',[9,10,11])
                                    ->orderby('id','desc')
                                    ->pluck('app_no', 'id');
                }
           }else{
                   $app_no = CertiIb::whereIn('status',[9,10,11])
                                       ->orderby('id','desc')
                                       ->pluck('app_no', 'id');
           }
            $auditorib = new CertiIBAuditors;
            $auditors_status = [new CertiIBAuditorsStatus];
            return view('certify/ib.auditor_ib.create',['app_no' => $app_no,
                                                         'auditorib' => $auditorib,
                                                        'auditors_status' => $auditors_status,
                                                        'previousUrl' => $previousUrl
                                                        ]);
        }
        abort(403);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $model = str_slug('auditorib','-');
        if(auth()->user()->can('add-'.$model)) {
            $request->validate([
                'app_certi_ib_id' => 'required',
            ]);

            try {
                               
                    // update  (ถ้ามี)
                    if(isset($request->app_certi_ib_id)){
                        CertiIBAuditors::where('app_certi_ib_id',$request->app_certi_ib_id)->update(['state'=>0]);
                    }


                    $request->request->add(['created_by' => auth()->user()->getKey()]); //user create
                    $requestData = $request->all();
                    $requestData['vehicle'] = isset($request->vehicle) ? 1 : null ;
                    $requestData['status']  =   null ;
                    $requestData['step_id'] =  2  ;//ขอความเห็นแต่งคณะผู้ตรวจประเมิน
                    $auditors =  CertiIBAuditors::create($requestData);

                  // ไฟล์แนบ
                    if ($request->other_attach && $request->hasFile('other_attach')){
                        $this->set_attachs($request->other_attach, $auditors,"1");
                    }
                    if ($request->attach && $request->hasFile('attach')){
                        $this->set_attachs($request->attach, $auditors,"2");
                    }

                    //วันที่ตรวจประเมิน
                    $this->DataCertiIBAuditorsDate($auditors->id,$request);


                    $this->storeStatus($auditors->id,(array)$requestData['list']);

                    //ค่าใช้จ่าย
                    $this->storeItems($auditors->id,$request);

                    $certi_ib = CertiIb::findOrFail($auditors->app_certi_ib_id);
                    if(!is_null($certi_ib->email)){
                        if(isset($request->vehicle)){
                            $certi_ib->update(['status'=>10]); // อยู่ระหว่างดำเนินการ
                            // Log
                            $this->set_history($auditors,$certi_ib);
                            //E-mail
                            $this->set_mail($auditors,$certi_ib);

                        }else{
                            $certi_ib->update(['status'=>10]); //  อยู่ระหว่างดำเนินการ
                        }
                    }

                    if($request->previousUrl){
                        return redirect("$request->previousUrl")->with('flash_message', 'เรียบร้อยแล้ว!');
                    }else{
                        return redirect('certify/auditor-ib')->with('flash_message', 'เรียบร้อยแล้ว!');
                    }
                
            } catch (\Exception $e) {
                return redirect('certify/auditor-ib')->with('message_error', 'เกิดข้อผิดพลาดในการบันทึก');
            }

        }
        abort(403);
    }
//

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $model = str_slug('auditorib','-');
        if(auth()->user()->can('edit-'.$model)) {
            $previousUrl = app('url')->previous();
            $auditorib = CertiIBAuditors::findOrFail($id);

            $auditors_status = CertiIBAuditorsStatus::where('auditors_id',$id)->get();
            if(count($auditors_status) <= 0){
                $auditors_status = [new CertiIBAuditorsStatus];
            }

            return view('certify/ib.auditor_ib.edit', compact('auditorib','auditors_status','previousUrl'));
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $model = str_slug('auditorib','-');
        if(auth()->user()->can('edit-'.$model)) {

        //   try {
                $request->request->add(['updated_by' => auth()->user()->getKey()]); //user update
                $requestData = $request->all();
                $requestData['status'] =   null ; 
                $requestData['remark'] =   null ;
                $requestData['step_id'] =  2  ;//ขอความเห็นแต่งคณะผู้ตรวจประเมิน
                $requestData['vehicle'] = isset($request->vehicle) ? 1 : null ;
                $auditors = CertiIBAuditors::findOrFail($id);
                $auditors->update($requestData);
    
               // ไฟล์แนบ
                  if ($request->other_attach && $request->hasFile('other_attach')){
                    $this->set_attachs($request->other_attach, $auditors,"1");
                  }
                  if ($request->attach && $request->hasFile('attach')){
                    $this->set_attachs($request->attach, $auditors,"2");
                  }
    
                //วันที่ตรวจประเมิน
                $this->DataCertiIBAuditorsDate($auditors->id,$request);
    
                $this->storeStatus($auditors->id,(array)$requestData['list']);
    
                 //ค่าใช้จ่าย
                $this->storeItems($auditors->id,$request);
    
                $certi_ib = CertiIb::findOrFail($auditors->app_certi_ib_id);
                if(!is_null($certi_ib->email)){
                    if(isset($request->vehicle)){
                        $certi_ib->update(['status'=>10]); // อยู่ระหว่างดำเนินการ
                        //Log
                        $this->set_history($auditors,$certi_ib);
                        //E-mail
                         $this->set_mail($auditors,$certi_ib);
                    }else{
                         $certi_ib->update(['status'=>10]); //  อยู่ระหว่างดำเนินการ
                    }
                }
    
                if($request->previousUrl){
                    return redirect("$request->previousUrl")->with('flash_message', 'เรียบร้อยแล้ว!');
                }else{
                    return redirect('certify/auditor-ib')->with('flash_message', 'เรียบร้อยแล้ว!');
                }
            // } catch (\Exception $e) {
            //     return redirect('certify/auditor-ib')->with('message_error', 'เกิดข้อผิดพลาดในการบันทึก');
            // }


        }
        abort(403);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */


    public function set_attachs($attachs, $auditors,$number) {
          $tb                                     = new CertiIBAuditors;
          $certi_ib_attach_more                   = new CertiIBAttachAll();
          $certi_ib_attach_more->app_certi_ib_id  = $auditors->CertiIBCostTo->id ?? null;
          $certi_ib_attach_more->ref_id           = $auditors->id;
          $certi_ib_attach_more->table_name       = $tb->getTable();
          $certi_ib_attach_more->file_section     =  $number;
          $certi_ib_attach_more->file             = $this->storeFile($attachs,$auditors->CertiIBCostTo->app_no);
          $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($attachs->getClientOriginalName());
          $certi_ib_attach_more->token            = str_random(16);
          $certi_ib_attach_more->save();
    }
    public function storeFile($files, $app_no = 'files_ib',$name =null)
    {
        $no  = str_replace("RQ-","",$app_no);
        $no  = str_replace("-","_",$no);
        if ($files) {
            $attach_path  =  $this->attach_path.$no;
            $file_extension = $files->getClientOriginalExtension();
            $fileClientOriginal   =  HP::ConvertCertifyFileName($files->getClientOriginalName());
            $filename = pathinfo($fileClientOriginal, PATHINFO_FILENAME);
            $fullFileName =  str_random(10).'-date_time'.date('Ymd_hms') . '.' . $files->getClientOriginalExtension();

            $storagePath = Storage::putFileAs($attach_path, $files,  str_replace(" ","",$fullFileName) );
            $storageName = basename($storagePath); // Extract the filename
            return  $no.'/'.$storageName;
        }else{
            return null;
        }
    }


    public function DataCertiIBAuditorsDate($baId, $request) {
      CertiIBAuditorsDate::where('auditors_id',$baId)->delete();
    /* วันที่ตรวจประเมิน */
     foreach($request->start_date as $key => $itme) {
      $input = [];
      $input['auditors_id'] = $baId;
      $input['start_date'] = HP::convertDate( $itme ,true) ?? null;
      $input['end_date']   = HP::convertDate( $request->end_date[$key]  ,true)?? null;
      CertiIBAuditorsDate::create($input);
     }
   }
   public function storeStatus($baId, $list) {
     CertiIBAuditorsStatus::where('auditors_id',$baId)->delete();
     CertiIBAuditorsList::where('auditors_id',$baId)->delete();
      foreach($list['status'] as $key => $itme) {
        if($itme != null){
            $input = [];
            $input['auditors_id'] = $baId;
            $input['status'] =  $itme;
            $auditors_status =  CertiIBAuditorsStatus::create($input);
            $this->storeList($auditors_status,
                            $list['temp_users'][$auditors_status->status],
                            $list['user_id'][$auditors_status->status],
                            $list['temp_departments'][$auditors_status->status]
                           );
        }
      }
   }
   public function storeList($status,$temp_users,$user_id,$temp_departments) {
      foreach($temp_users as $key => $itme) {
        if($itme != null){
            $input = [];
            $input['auditors_status_id'] = $status->id;
            $input['auditors_id'] = $status->auditors_id;
            $input['status'] = $status->status;
            $input['temp_users'] =  $itme;
            $input['user_id'] =   $user_id[$key] ?? null;
            $input['temp_departments'] =  $temp_departments[$key] ?? null;
            CertiIBAuditorsList::create($input);
        }
      }
   }

  public function storeItems($baId, $items) {
         CertiIBAuditorsCost::where('auditors_id',$baId)->delete();
         $detail = (array)@$items['detail'];
        foreach($detail['detail'] as $key => $data ) {
            $item = new CertiIBAuditorsCost;
            $item->auditors_id = $baId;
            $item->detail = $data ?? null;
            $item->amount_date = $detail['amount_date'][$key] ?? 0;
            $item->amount =  !empty(str_replace(",","", $detail['amount'][$key]))?str_replace(",","",$detail['amount'][$key]):null;
            $item->save();
        }
}

    public function set_history($data,$certi_ib = null) {
            $tb = new CertiIBAuditors;
        $auditors = CertiIBAuditors::select('app_certi_ib_id', 'no','auditor')
                      ->where('id',$data->id)
                      ->first();

        $auditors_date = CertiIBAuditorsDate::select('start_date','end_date')
                                      ->where('auditors_id',$data->id)
                                      ->get()
                                      ->toArray();
        $auditors_list = CertiIBAuditorsList::select('status','temp_users','user_id','temp_departments')
                                      ->where('auditors_id',$data->id)
                                      ->get()
                                      ->toArray();
        $auditors_cost = CertiIBAuditorsCost::select('detail','amount_date','amount')
                                      ->where('auditors_id',$data->id)
                                      ->get()
                                      ->toArray();

       CertiIbHistory::create([
                                    'app_certi_ib_id'   => $certi_ib->id ?? null,
                                    'auditors_id'       =>  $data->id ?? null,
                                    'system'            => 5,
                                    'table_name'        => $tb->getTable(),
                                    'ref_id'            => $data->id,
                                    'details_one'       =>  json_encode($auditors) ?? null,
                                    'details_two'       =>  (count($auditors_date) > 0) ? json_encode($auditors_date) : null,
                                    'details_three'     =>  (count($auditors_list) > 0) ? json_encode($auditors_list) : null,
                                    'details_four'      =>  (count($auditors_cost) > 0) ? json_encode($auditors_cost) : null,
                                    'file'              => !empty($data->FileAuditors1->file) ?  $data->FileAuditors1->file  : null,
                                    'file_client_name'  => !empty($data->FileAuditors1->file_client_name) ?  $data->FileAuditors1->file_client_name  : null,
                                    'attachs'           => !empty($data->FileAuditors2->file) ? $data->FileAuditors2->file : null,
                                    'attach_client_name'=> !empty($data->FileAuditors2->file_client_name) ?  $data->FileAuditors2->file_client_name  : null,
                                    'created_by'        =>  auth()->user()->runrecno
                             ]);
    }
    public function set_mail($auditors,$certi_ib) {

      if(!is_null($certi_ib->email)){

            $config = HP::getConfig();
            $url    = !empty($config->url_acc) ? $config->url_acc : url('');

           if(!empty($certi_ib->DataEmailDirectorIBCC)){
                $mail_cc = $certi_ib->DataEmailDirectorIBCC;
                array_push($mail_cc, auth()->user()->reg_email) ;
            }

            $data_app = [
                            'title'        =>  'แต่งตั้งคณะผู้ตรวจประเมิน (IB)',
                            'auditors'     => $auditors,
                            'certi_ib'     => $certi_ib,
                            'url'          => $url.'certify/applicant-ib' ?? '-',
                            'email'        =>  !empty($certi_ib->DataEmailCertifyCenter) ? $certi_ib->DataEmailCertifyCenter : 'ib@tisi.mail.go.th',
                            'email_cc'     =>  !empty($mail_cc) ? $mail_cc : 'ib@tisi.mail.go.th',
                            'email_reply'  => !empty($certi_ib->DataEmailDirectorIBReply) ? $certi_ib->DataEmailDirectorIBReply : 'ib@tisi.mail.go.th'
                       ];

            $log_email =  HP::getInsertCertifyLogEmail($certi_ib->app_no,
                                                    $certi_ib->id,
                                                    (new CertiIb)->getTable(),
                                                    $auditors->id,
                                                    (new CertiIBAuditors)->getTable(),
                                                    3,
                                                    'การแต่งตั้งคณะผู้ตรวจประเมิน',
                                                    view('mail.IB.auditors', $data_app),
                                                    $certi_ib->created_by,
                                                    $certi_ib->agent_id,
                                                    auth()->user()->getKey(),
                                                    !empty($certi_ib->DataEmailCertifyCenter) ?  implode(',',(array)$certi_ib->DataEmailCertifyCenter)  :  'ib@tisi.mail.go.th',
                                                    $certi_ib->email,
                                                    !empty($mail_cc) ?  implode(',',(array)$mail_cc)  : 'ib@tisi.mail.go.th',
                                                    !empty($certi_ib->DataEmailDirectorIBReply) ?implode(',',(array)$certi_ib->DataEmailDirectorIBReply)   :   'ib@tisi.mail.go.th',
                                                    null
                                                    );
 ;
            $html = new IBAuditorsMail($data_app);
            $mail =  Mail::to($certi_ib->email)->send($html);

            if(is_null($mail) && !empty($log_email)){
                HP::getUpdateCertifyLogEmail($log_email->id);
            } 
 
      }
    }

    public function DataCertiNo($id) {

      $app_no =  CertiIb::findOrFail($id);
      if(!is_null($app_no)){
          $cost = CertiIBCost::where('app_certi_ib_id',$app_no->id)->orderby('id','desc')->first();
          if(!is_null($cost)){
              $cost_item = $cost->items;
          }
      }
      $cost_details =  StatusAuditor::orderbyRaw('CONVERT(title USING tis620)')->pluck('title', 'id');
      return response()->json([
                                'name'          => !empty($app_no->name) ? $app_no->name : ' ' ,
                                'id'            => !empty($app_no->id) ? $app_no->id : ' ' ,
                                'cost_item'     => isset($cost_item) ? $cost_item : '-',
                                'cost_details'  => $cost_details
                            ]);
  }

  public function ApiAuditorExpertise($id =null,$type= null)  {
    $name_th = [];
    $Auditor =  AuditorExpertise::where('type_of_assessment',$type) ->get();
   foreach($Auditor as $key => $item ) {
      $auditor_status =  explode(",",$item->auditor_status) ;
      if(in_array($id,$auditor_status) 
      && !is_null($item->auditor_id)  
      && !array_key_exists($item->auditor_id,$name_th) ){
        $data['id']         =  $item->auditor_id ?? '-';
        $data['NameTh']     =  $item->auditor->NameThTitle ?? '-';
        $data['department'] = $item->auditor->DepartmentTitle ?? '-';
        $data['position']   =  $item->auditor->position ?? '-';
        $data['branchable'] =  $item->BranchTitle ?? '-';
        $Expertise[]        = $data ;
        $name_th[$item->auditor_id] = $item->auditor_id;
      }
   }
    return response()->json([
        'expertise'=> isset($Expertise) ?  $Expertise : '-'
     ]);
 }

 public function update_delete(Request $request, $id)
 {
     $model = str_slug('auditorib','-');
     if(auth()->user()->can('delete-'.$model)) {
         
         try {
             $requestData = $request->all();
             $requestData['reason_cancel'] =  $request->reason_cancel ;
             $requestData['status_cancel'] =   1 ;
             $requestData['created_cancel'] =  auth()->user()->runrecno;
             $requestData['date_cancel'] =    date('Y-m-d H:i:s') ;
             $requestData['step_id'] =   12 ; // ยกเลิกแต่งตั้งคณะผู้ตรวจประเมิน
             $auditors = CertiIBAuditors::findOrFail($id);
             $auditors->update($requestData);
        
             $response = [];
             $response['reason_cancel']  =  $auditors->reason_cancel ?? null;
             $response['status_cancel']  =  $auditors->status_cancel ?? null;
             $response['created_cancel'] =  $auditors->created_cancel ?? null;    
             $response['date_cancel']    =  $auditors->date_cancel ?? null;
             $response['step_id']        =  $auditors->step_id ?? null;

             if(count($response) > 0){ // update log แต่งตั้งคณะกรรมการ
                CertiIbHistory::where('ref_id',$id)->where('table_name',(new CertiIBAuditors)->getTable())->update(['details_auditors_cancel' => json_encode($response) ]);
             }

             $CertiIb = CertiIb::findOrFail($auditors->app_certi_ib_id);
             if(!is_null($CertiIb)){

               $payin_one =  CertiIBPayInOne::where('app_certi_ib_id',$CertiIb->id)->where('app_certi_ib_id',$CertiIb->id)->orderby('id','desc')->first();
               if(!is_null($payin_one)){ // update log payin
                 // / update   payin
                 CertiIBPayInOne::where('auditors_id',$auditors->id)->update(['status'=>3]);
                 CertiIbHistory::where('ref_id',$payin_one->id)->where('table_name',(new CertiIBPayInOne)->getTable())->update(['details_auditors_cancel' => json_encode($response) ]);
               }
              // สถานะ แต่งตั้งคณะกรรมการ
              $auditor = CertiIBAuditors::where('app_certi_ib_id',$CertiIb->id)
                                        ->whereIn('step_id',[9,10])
                                        ->whereNull('status_cancel')
                                         ->get(); 
               if(count($auditor) == count($CertiIb->CertiIBAuditors)){
                   $report = new   CertiIBReview;  //ทบทวนฯ
                   $report->app_certi_ib_id  = $CertiIb->id;
                   $report->save();
                   $CertiIb->update(['review'=>1,'status'=>11]);  // ทบทวน
               }
             }

             return redirect('certify/auditor-ib')->with('flash_message', 'update ยกเลิกแต่งตั้งคณะผู้ตรวจประเมินเรียบร้อยแล้ว');
         } catch (\Exception $e) {
             return redirect('certify/auditor-ib')->with('flash_message', 'เกิดข้อผิดพลาดในการบันทึก');
         }
     }
     abort(403);
 }


}
