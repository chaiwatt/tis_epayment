<?php

namespace App\Http\Controllers\Certify\IB;

use App\Http\Requests;
use App\Http\Controllers\Controller; 

use Storage;
use stdClass;
use HP;
use App\User;

use App\Models\Certify\ApplicantIB\CertiIb;
use App\Models\Certify\ApplicantIB\CertiIBAttachAll; 
use App\Models\Certify\ApplicantIB\CertiIbHistory; 
use App\Models\Certify\ApplicantIB\CertiIBAuditors; 
use App\Models\Certify\ApplicantIB\CertiIBAuditorsList;
use App\Models\Certify\ApplicantIB\CertiIBSaveAssessment;
use App\Models\Certify\ApplicantIB\CertiIBSaveAssessmentBug;
use App\Models\Certify\ApplicantIB\CertiIBReport;
use App\Models\Certify\ApplicantIB\CertiIBCheck;
use App\Models\Certify\ApplicantIB\CertiIBReview;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;    
use App\Mail\IB\IBSaveAssessmentMail;
use App\Mail\IB\IBCheckSaveAssessment;
use App\Mail\IB\IBSaveAssessmentPastMail;
class SaveAssessmentIbController extends Controller
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
        $model = str_slug('saveassessmentib','-');
        if(auth()->user()->can('view-'.$model)) {
         
           $keyword = $request->get('search');
            $filter = [];
       
            $filter['filter_degree'] = $request->get('filter_degree', '');
            $filter['filter_search'] = $request->get('filter_search', '');
            $filter['perPage'] = $request->get('perPage', 10);

               $Query = new CertiIBSaveAssessment;
               $Query = $Query->select('app_certi_ib_assessment.*');
            if ($filter['filter_degree']!='') {
                if($filter['filter_degree'] == '0'){
                    $Query = $Query->where('bug_report', '!=', '1');
                }else if($filter['filter_degree'] == '1'){
                    $Query = $Query->where('bug_report', '==', $filter['filter_degree']);
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
                     $Query = $Query->LeftJoin('app_certi_ib_check','app_certi_ib_check.app_certi_ib_id','=','app_certi_ib_assessment.app_certi_ib_id')
                                    ->where('user_id',auth()->user()->runrecno);  //เจ้าหน้าที่  IB ที่ได้มอบหมาย 
                }else{
                    $Query = $Query->whereIn('id',['']);  // ไม่ตรงกับเงื่อนไข
                } 
            }
            $assessment = $Query->orderby('id','desc')->sortable()
                                ->paginate($filter['perPage']);
        
            return view('certify/ib/save_assessment_ib.index', compact('assessment', 'filter'));
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
        $model = str_slug('saveassessmentib','-');
        if(auth()->user()->can('add-'.$model)) {
            $previousUrl = app('url')->previous();
            $assessment = new CertiIBSaveAssessment;
             $bug = [new CertiIBSaveAssessmentBug];
             $app_no = [];
             //เจ้าหน้าที่ IB และไม่มีสิทธิ์ admin , ผอ , ผก , ลท.
            if(in_array("27",auth()->user()->RoleListId) && auth()->user()->SetRolesAdminCertify() == "false" ){ 
                 $check = CertiIBCheck::where('user_id',auth()->user()->runrecno)->pluck('app_certi_ib_id'); // เช็คเจ้าหน้าที่ IB
                 if(count($check) > 0 ){
                     $auditor= CertiIBAuditors::select('id','app_certi_ib_id','auditor')
                                      ->whereIn('step_id',[6])
                                      ->whereIn('app_certi_ib_id',$check)
                                      ->orderby('id','desc')
                                      ->get();
                   if(count($auditor) > 0 ){
                     foreach ($auditor as $item){
                       $app_no[$item->id] = $item->auditor . " ( ". @$item->CertiIBCostTo->app_no . " )";
                      }
                    } 
                  } 
            }else{
                 $auditor = CertiIBAuditors::select('id','app_certi_ib_id','auditor')
                                            ->whereIn('step_id',[6])
                                           ->orderby('id','desc')
                                           ->get();
                  if(count($auditor) > 0 ){
                    foreach ($auditor as $item){
                         $app_no[$item->id] = $item->auditor . " ( ". @$item->CertiIBCostTo->app_no . " )";
                    }
                  }
            }
   
            return view('certify/ib/save_assessment_ib.create',['app_no'=> $app_no,
                                                                'assessment'=>$assessment,
                                                                'bug'=>$bug,
                                                                'previousUrl'=> $previousUrl
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
        $model = str_slug('saveassessmentib','-');
        if(auth()->user()->can('add-'.$model)) {
            $request->validate([
                'app_certi_ib_id' => 'required',
            ]);

 
            $request->request->add(['created_by' => auth()->user()->getKey()]); 
            $requestData = $request->all();
            $requestData['report_date'] =  HP::convertDate($request->report_date,true) ?? null;
            if($request->bug_report == 1){
                $requestData['main_state'] = isset($request->main_state) ? 2 : 1;
            }else{
                $requestData['main_state'] = 1;
            }
            $auditors = CertiIBSaveAssessment::create($requestData);
            $CertiIb = CertiIb::findOrFail($auditors->app_certi_ib_id);
            $tb = new CertiIBSaveAssessment;
              
            // ข้อบกพร่อง/ข้อสังเกต
            if(isset($requestData["detail"])){
                $this->storeDetail($auditors,$requestData["detail"]);
            }
    
            // รายงานการตรวจประเมิน
             if($request->file  && $request->hasFile('file')){
                        $certi_ib_attach_more                   = new CertiIBAttachAll();
                        $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                        $certi_ib_attach_more->ref_id           = $auditors->id;
                        $certi_ib_attach_more->table_name       = $tb->getTable();
                        $certi_ib_attach_more->file_section     = '1';
                        $certi_ib_attach_more->file             = $this->storeFile($request->file,$CertiIb->app_no);
                        $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($request->file->getClientOriginalName());
                        $certi_ib_attach_more->token            = str_random(16);
                        $certi_ib_attach_more->save();
            }
 if($auditors->bug_report == 2){
                // รายงาน Scope
                if($request->file_scope  && $request->hasFile('file_scope')){
                    foreach ($request->file_scope as $index => $item){
                            $certi_ib_attach_more = new CertiIBAttachAll();
                            $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                            $certi_ib_attach_more->ref_id           = $auditors->id;
                            $certi_ib_attach_more->table_name       = $tb->getTable();
                            $certi_ib_attach_more->file_section     = '2';
                            $certi_ib_attach_more->file             = $this->storeFile($item,$CertiIb->app_no);
                            $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                            $certi_ib_attach_more->token            = str_random(16);
                            $certi_ib_attach_more->save();
                    }
                }
               // รายงาน สรุปรายงานการตรวจทุกครั้ง
                if($request->file_report  && $request->hasFile('file_report')){
                    foreach ($request->file_report as $index => $item){
                            $certi_ib_attach_more                   = new CertiIBAttachAll();
                            $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                            $certi_ib_attach_more->ref_id           = $auditors->id;
                            $certi_ib_attach_more->table_name       = $tb->getTable();
                            $certi_ib_attach_more->file_section     = '3';
                            $certi_ib_attach_more->file             = $this->storeFile($item,$CertiIb->app_no);
                            $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                            $certi_ib_attach_more->token            = str_random(16);
                            $certi_ib_attach_more->save();
                    }
                }
    }
            // ไฟล์แนบ
            if($request->attachs  && $request->hasFile('attachs')){
                foreach ($request->attachs as $index => $item){
                        $certi_ib_attach_more                   = new CertiIBAttachAll();
                        $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                        $certi_ib_attach_more->ref_id           = $auditors->id;
                        $certi_ib_attach_more->table_name       = $tb->getTable();
                        $certi_ib_attach_more->file_section     = '4';
                        $certi_ib_attach_more->file             = $this->storeFile($item,$CertiIb->app_no);
                        $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_ib_attach_more->token            = str_random(16);
                        $certi_ib_attach_more->save();
                }
             }

       //
       //
  
         // สถานะ แต่งตั้งคณะกรรมการ
        $committee = CertiIBAuditors::findOrFail($auditors->auditors_id); 
       if(($auditors->degree == 1 || $auditors->degree == 8) && $auditors->bug_report == 1){
                //Log  //  Mail
                $this->set_history_bug($auditors);
                $this->set_mail($auditors,$CertiIb);
               if($auditors->main_state == 1 ){
                    $committee->step_id = 8; // แก้ไขข้อบกพร่อง/ข้อสังเกต
                    $committee->save();
                     
                }else{
                    $committee->step_id = 9; // ไม่ผ่านการตรวจสอบประเมิน
                    $committee->save();
                   // สถานะ แต่งตั้งคณะกรรมการ
                   $auditor = CertiIBAuditors::where('app_certi_ib_id',$CertiIb->id)
                                            ->whereIn('step_id',[9,10])
                                            ->whereNull('status_cancel')
                                             ->get(); 
                    if(count($auditor) == count($CertiIb->CertiIBAuditorsManyBy)){
                        $report = new   CertiIBReview;  //ทบทวนฯ
                        $report->app_certi_ib_id  = $certi_ib->id;
                        $report->save();
                        $CertiIb->update(['review'=>1,'status'=>11]);  // ทบทวน
                    }
                }
        }


            if($auditors->degree == 4){
                $committee->step_id = 7; // ผ่านการตรวจสอบประเมิน
                $committee->save();
                   //  Log
                 $this->set_history($auditors);
                $this->set_mail_past($auditors,$CertiIb);  
            }
            if($request->previousUrl){
                return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
            }else{
                return redirect('certify/save_assessment-ib')->with('message', 'เรียบร้อยแล้ว!');
            }
        }
        abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $model = str_slug('saveassessmentib','-');
        if(auth()->user()->can('view-'.$model)) {
            $assessment = SaveAssessmentIb::findOrFail($id);
            return view('certify/ib.save-assessment-ib.show', compact('assessment'));
        }
        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $model = str_slug('saveassessmentib','-');
        if(auth()->user()->can('edit-'.$model)) {
            $previousUrl = app('url')->previous();
            $assessment = CertiIBSaveAssessment::findOrFail($id);
            $bug = CertiIBSaveAssessmentBug::where('assessment_id',$id)->get();
            if(count($bug) <= 0){
                $bug = [new CertiIBSaveAssessmentBug];
            }
                $found = [];
                $auditors_id = CertiIBAuditors::where('app_certi_ib_id',$assessment->app_certi_ib_id)->pluck('id');
                if(count($auditors_id) > 0){
                   $auditors_list =   CertiIBAuditorsList::select('user_id','temp_users')
                                                        ->whereIn('auditors_id',$auditors_id)
                                                        ->distinct('user_id')
                                                        ->get();
                    if(count($auditors_list) > 0){
                      foreach ($auditors_list as $index => $item){
                            $found[$item->user_id] =  $item->temp_users ;
                      }

                    }
                }
               
            return view('certify/ib.save_assessment_ib.edit', compact('assessment','bug','found','previousUrl'));
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
        $model = str_slug('saveassessmentib','-');
        if(auth()->user()->can('edit-'.$model)) {
            
            $request->request->add(['updated_by' => auth()->user()->getKey()]); //user update
            $requestData = $request->all();
            $requestData['report_date'] =  HP::convertDate($request->report_date,true) ?? null;
            if($request->bug_report == 1){
                $requestData['main_state'] = isset($request->main_state) ? 2 : 1;
            }else{
                $requestData['main_state'] = 1;
            }
            $auditors = CertiIBSaveAssessment::findOrFail($id);
            $auditors->update($requestData);
            $CertiIb = CertiIb::findOrFail($auditors->app_certi_ib_id);
            $tb = new CertiIBSaveAssessment;
              
            // ข้อบกพร่อง/ข้อสังเกต
            if(isset($requestData["detail"])){
                $this->storeDetail($auditors,$requestData["detail"]);
            }
    
            // รายงานการตรวจประเมิน
             if($request->file && $request->hasFile('file')){
                        $certi_ib_attach_more                   = new CertiIBAttachAll();
                        $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                        $certi_ib_attach_more->ref_id           = $auditors->id;
                        $certi_ib_attach_more->table_name       = $tb->getTable();
                        $certi_ib_attach_more->file_section     = '1';
                        $certi_ib_attach_more->file             = $this->storeFile($request->file,$CertiIb->app_no);
                        $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($request->file->getClientOriginalName());
                        $certi_ib_attach_more->token            = str_random(16);
                        $certi_ib_attach_more->save();
            }
  if($auditors->bug_report == 2){
                // รายงาน Scope
                if($request->file_scope && $request->hasFile('file_scope')){
                    foreach ($request->file_scope as $index => $item){
                            $certi_ib_attach_more                   = new CertiIBAttachAll();
                            $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                            $certi_ib_attach_more->ref_id           = $auditors->id;
                            $certi_ib_attach_more->table_name       = $tb->getTable();
                            $certi_ib_attach_more->file_section     = '2';
                            $certi_ib_attach_more->file             = $this->storeFile($item,$CertiIb->app_no);
                            $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                            $certi_ib_attach_more->token            = str_random(16);
                            $certi_ib_attach_more->save();
                    }
                }
               // รายงาน สรุปรายงานการตรวจทุกครั้ง
                if($request->file_report && $request->hasFile('file_report')){
                    foreach ($request->file_report as $index => $item){
                            $certi_ib_attach_more                   = new CertiIBAttachAll();
                            $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                            $certi_ib_attach_more->ref_id           = $auditors->id;
                            $certi_ib_attach_more->table_name       = $tb->getTable();
                            $certi_ib_attach_more->file_section     = '3';
                            $certi_ib_attach_more->file             = $this->storeFile($item,$CertiIb->app_no);
                            $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                            $certi_ib_attach_more->token            = str_random(16);
                            $certi_ib_attach_more->save();
                    }
                }
    }
            // ไฟล์แนบ
            if($request->attachs && $request->hasFile('attachs')){
                foreach ($request->attachs as $index => $item){
                        $certi_ib_attach_more                       = new CertiIBAttachAll();
                        $certi_ib_attach_more->app_certi_ib_id      = $auditors->app_certi_ib_id ?? null;
                        $certi_ib_attach_more->ref_id               = $auditors->id;
                        $certi_ib_attach_more->table_name           = $tb->getTable();
                        $certi_ib_attach_more->file_section         = '4';
                        $certi_ib_attach_more->file                 = $this->storeFile($item,$CertiIb->app_no);
                        $certi_ib_attach_more->file_client_name     = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_ib_attach_more->token = str_random(16);
                        $certi_ib_attach_more->save();
                }
             }

              //
   
         // สถานะ แต่งตั้งคณะกรรมการ
        $committee = CertiIBAuditors::findOrFail($auditors->auditors_id); 
        if(($auditors->degree == 1 || $auditors->degree == 8) && $auditors->bug_report == 1){
                //Log  
                $this->set_history_bug($auditors);
                $this->set_mail($auditors,$CertiIb);   
               if($auditors->main_state == 1 ){
                    $committee->step_id = 8; // แก้ไขข้อบกพร่อง/ข้อสังเกต
                    $committee->save();
                }else{
                    $committee->step_id = 9; // ไม่ผ่านการตรวจสอบประเมิน
                    $committee->save();
                    // สถานะ แต่งตั้งคณะกรรมการ
                    $auditor = CertiIBAuditors::where('app_certi_ib_id',$CertiIb->id)
                                                ->whereIn('step_id',[9,10])
                                                ->whereNull('status_cancel')
                                               ->get(); 
                    if(count($auditor) == count($CertiIb->CertiIBAuditorsManyBy)){
                        $report = new   CertiIBReview;  //ทบทวนฯ
                        $report->app_certi_ib_id  = $certi_ib->id;
                        $report->save();
                        $CertiIb->update(['review'=>1,'status'=>11]);  // ทบทวน
                    }
                }
        }


            if($auditors->degree == 4){
                $committee->step_id = 7; // ผ่านการตรวจสอบประเมิน
                $committee->save();
                //  Log
                $this->set_history($auditors);
                  //  Mail
                $this->set_mail_past($auditors,$CertiIb);  
              
            }


            if($request->previousUrl){
                return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
            }else{
                return redirect('certify/save_assessment-ib')->with('message', 'เรียบร้อยแล้ว!');
            }
        }
        abort(403);

    }



    public function DataAssessment($id) {
        $previousUrl = app('url')->previous();
        $assessment = CertiIBSaveAssessment::findOrFail($id);
        return view('certify/ib.save_assessment_ib.form_assessment', compact('assessment','previousUrl'));
    }
    public function UpdateAssessment(Request $request, $id){
 
        $auditors = CertiIBSaveAssessment::findOrFail($id);
        $CertiIb = CertiIb::findOrFail($auditors->app_certi_ib_id);
        // สถานะ แต่งตั้งคณะกรรมการ
        $committee = CertiIBAuditors::findOrFail($auditors->auditors_id); 
        $tb = new CertiIBSaveAssessment;

if($auditors->degree != 5){  // ข้อบกพร่อง/ข้อสังเกต

            $ids = $request->input('id');
            if(isset($ids)){
            foreach ($ids as $key => $item) {
                $bug = CertiIBSaveAssessmentBug::where('id',$item)->first();
               if(!is_null($bug)){ 
                   $bug->status       = $request->status[$bug->id] ??  @$bug->status;
                   $bug->comment      = $request->comment[$bug->id] ?? @$bug->comment;
                   $bug->file_status  = $request->file_status[$bug->id] ??  @$bug->file_status;
                   $bug->file_comment = $request->file_comment[$bug->id] ?? null;
                   $bug->save(); 
               }
             } 

            if($request->hasFile('file_car')){
                    $auditors->main_state = 1;
                    $auditors->degree = 4;
                    $auditors->date_car = date("Y-m-d"); // วันที่ปิด Car
                    $auditors->bug_report = 2; 
             }else{
                 if(isset($request->main_state)){
                    $auditors->main_state =  2 ;
                    $auditors->degree = 8;
                  }else{
                    $auditors->main_state = 1;
                    $auditors->degree = 3;
                  }
             }
 
             $auditors->save();
      
      
     
             // รายงานการตรวจประเมิน
            if($request->file && $request->hasFile('file')){
                        $certi_ib_attach_more                   = new CertiIBAttachAll();
                        $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                        $certi_ib_attach_more->ref_id           = $auditors->id;
                        $certi_ib_attach_more->table_name       = $tb->getTable();
                        $certi_ib_attach_more->file_section     = '1';
                        $certi_ib_attach_more->file             = $this->storeFile($request->file,$CertiIb->app_no);
                        $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($request->file->getClientOriginalName());
                        $certi_ib_attach_more->token            = str_random(16);
                        $certi_ib_attach_more->save();
            }
if($auditors->main_state == 1){
            // รายงาน Scope
            if($request->file_scope && $request->hasFile('file_scope')){
                foreach ($request->file_scope as $index => $item){
                        $certi_ib_attach_more                   = new CertiIBAttachAll();
                        $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                        $certi_ib_attach_more->ref_id           = $auditors->id;
                        $certi_ib_attach_more->table_name       = $tb->getTable();
                        $certi_ib_attach_more->file_section     = '2';
                        $certi_ib_attach_more->file             = $this->storeFile($item,$CertiIb->app_no);
                        $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_ib_attach_more->token            = str_random(16);
                        $certi_ib_attach_more->save();
                }
            }
           // รายงาน สรุปรายงานการตรวจทุกครั้ง
            if($request->file_report  && $request->hasFile('file_report')){
                foreach ($request->file_report as $index => $item){
                        $certi_ib_attach_more                   = new CertiIBAttachAll();
                        $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                        $certi_ib_attach_more->ref_id           = $auditors->id;
                        $certi_ib_attach_more->table_name       = $tb->getTable();
                        $certi_ib_attach_more->file_section     = '3';
                        $certi_ib_attach_more->file             = $this->storeFile($item,$CertiIb->app_no);
                        $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_ib_attach_more->token            = str_random(16);
                        $certi_ib_attach_more->save();
                }
            }
}
            // ไฟล์แนบ
            if($request->attachs && $request->hasFile('attachs')){
                foreach ($request->attachs as $index => $item){
                        $certi_ib_attach_more                   = new CertiIBAttachAll();
                        $certi_ib_attach_more->app_certi_ib_id  = $auditors->app_certi_ib_id ?? null;
                        $certi_ib_attach_more->ref_id           = $auditors->id;
                        $certi_ib_attach_more->table_name       = $tb->getTable();
                        $certi_ib_attach_more->file_section     = '4';
                        $certi_ib_attach_more->file             = $this->storeFile($item,$CertiIb->app_no);
                        $certi_ib_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_ib_attach_more->token            = str_random(16);
                        $certi_ib_attach_more->save();
                }
             }

            // รายงาน Car
            if($request->file_car && $request->hasFile('file_car')){
                $certi_ib_attach_more = new CertiIBAttachAll();
                $certi_ib_attach_more->app_certi_ib_id      = $auditors->app_certi_ib_id ?? null;
                $certi_ib_attach_more->ref_id               = $auditors->id;
                $certi_ib_attach_more->table_name           = $tb->getTable();
                $certi_ib_attach_more->file_section         = '5';
                $certi_ib_attach_more->file                 = $this->storeFile($request->file_car,$CertiIb->app_no);
                $certi_ib_attach_more->file_client_name     = HP::ConvertCertifyFileName($request->file_car->getClientOriginalName());
                $certi_ib_attach_more->token                = str_random(16);
                $certi_ib_attach_more->save();
            }


         //  Log
        $this->set_history_bug($auditors);

         if($auditors->degree == 3){

            $committee->step_id = 8; // แก้ไขข้อบกพร่อง/ข้อสังเกต
            $committee->save();
            //การตรวจประเมิน   ผู้ประกอบการ +  ผก.
            $this->set_check_mail($auditors,$CertiIb);  
        }elseif($auditors->degree == 4){
             $committee->step_id = 7; // ผ่านการตรวจสอบประเมิน
             $committee->save();
             //  Log
              $this->set_history($auditors);
              //การตรวจประเมิน   ผู้ประกอบการ +  ผก.
              $this->set_mail_past($auditors,$CertiIb);  
        }else{
            $committee->step_id = 9; // ไม่ผ่านการตรวจสอบประเมิน
            $committee->save();

                      // สถานะ แต่งตั้งคณะกรรมการ
              $auditor = CertiIBAuditors::where('app_certi_ib_id',$CertiIb->id)
                                        ->whereIn('step_id',[9,10])
                                        ->whereNull('status_cancel')
                                        ->get(); 
            if(count($auditor) == count($CertiIb->CertiIBAuditorsManyBy)){
                $report = new   CertiIBReview;  //ทบทวนฯ
                $report->app_certi_ib_id  = $CertiIb->id;
                $report->save();
                $CertiIb->update(['review'=>1,'status'=>11]);  // ทบทวน
            }
        }
    //  return redirect('certify/save_assessment-ib')->with('flash_message', 'เรียบร้อยแล้ว!'); // เข้าสรุปรายงานและเสนออนุกรรมการฯ
     }
 }else{
       // รายงาน Scope
            if($request->file_scope  && $request->hasFile('file_scope')){
                $file_scope = [];
                foreach ($request->file_scope as $index => $item){
                        $data = new stdClass;
                        $data->file = $this->storeFile($item,$CertiIb->app_no);
                        $file_scope[] = $data;
                }
            }
           // รายงาน สรุปรายงานการตรวจทุกครั้ง
            if($request->file_report && $request->hasFile('file_report')){
                         $file_report = [];
                foreach ($request->file_report as $index => $item){
                         $data = new stdClass;
                         $data->file = $this->storeFile($item,$CertiIb->app_no);
                        $file_report[] = $data;
                }
            }
            
        $auditors->degree = 4;
        $auditors->save();

        $committee->step_id = 7; // ผ่านการตรวจสอบประเมิน
        $committee->save();
        $tb = new CertiIBSaveAssessment;
       $assessment = CertiIBSaveAssessment::select('name','auditors_id', 'laboratory_name', 'report_date', 'bug_report', 'degree')
                     ->where('id',$id)
                     ->first();
       CertiIbHistory::create([
                                   'app_certi_ib_id'    => $auditors->app_certi_ib_id ?? null,
                                   'auditors_id'        => $assessment->auditors_id ?? null,
                                   'system'             => 8,
                                   'table_name'         => $tb->getTable(),
                                   'ref_id'             => $auditors->id, 
                                   'details_one'        => json_encode($assessment) ?? null,
                                   'details_two'        => null,
                                   'details_three'      => !empty($auditors->FileAttachAssessment1To->file) ? $auditors->FileAttachAssessment1To->file : null,
                                   'file_client_name'   =>  !empty($auditors->FileAttachAssessment1To->file_client_name) ? $auditors->FileAttachAssessment1To->file_client_name : null,
                                   'details_four'       => isset($file_scope) ?  json_encode($file_scope): null,
                                   'attachs'            => isset($file_report) ?  json_encode($file_report): null,
                                   'file'               => (count($auditors->FileAttachAssessment4Many) > 0) ? json_encode($auditors->FileAttachAssessment4Many) : null,
                                   'attachs_car'        => !empty($auditors->FileAttachAssessment5To->file) ? $auditors->FileAttachAssessment5To->file : null, // ปิด car
                                   'attach_client_name' =>  !empty($auditors->FileAttachAssessment5To->file_client_name) ? $auditors->FileAttachAssessment5To->file_client_name : null,
                                   'created_by'         =>  auth()->user()->runrecno
                            ]);

        // $auditors->update(['degree'=>6]);
       //การตรวจประเมิน   ผู้ประกอบการ +  ผก.
        $this->set_mail_past($auditors,$CertiIb);  
}

    if($request->previousUrl){
        return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
    }else{
        return redirect('certify/save_assessment-ib')->with('message', 'เรียบร้อยแล้ว!');
    }
 }


    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeDetail($data,$notice) {
 
            $data->CertiIBBugMany()->delete();
            $detail = (array)@$notice;
            foreach ($detail['notice'] as $key => $item) {
                    $bug = new CertiIBSaveAssessmentBug;
                    $bug->assessment_id = $data->id;
                    $bug->remark = $item;
                    $bug->report = $detail["report"][$key] ?? null;
                    $bug->no = $detail["no"][$key] ?? null;
                    $bug->type =  $detail["type"][$key] ?? null;
                    $bug->reporter_id =  $detail["found"][$key] ?? null;
                    $bug->save();
            }
    }


    //พบข้อบกพร่อง/ข้อสังเกต  ผู้ประกอบการ +  ผก.
    public function set_mail($data,$certi_ib) {

         if(!is_null($certi_ib->email)){
            $config = HP::getConfig();
            $url  =   !empty($config->url_acc) ? $config->url_acc : url('');

            $data_app = [ 
                        'certi_ib'    => $certi_ib,
                        'assessment'  => $data ?? '-',
                        'url'         => $url.'certify/applicant-ib' ?? '-',
                        'email'       =>  !empty($certi_ib->DataEmailCertifyCenter) ? $certi_ib->DataEmailCertifyCenter : 'ib@tisi.mail.go.th',
                        'email_cc'    =>  !empty($certi_ib->DataEmailDirectorIBCC) ? $certi_ib->DataEmailDirectorIBCC : 'ib@tisi.mail.go.th',
                        'email_reply' => !empty($certi_ib->DataEmailDirectorIBReply) ? $certi_ib->DataEmailDirectorIBReply : 'ib@tisi.mail.go.th'
                       ];
                
            $log_email =  HP::getInsertCertifyLogEmail($certi_ib->app_no,
                                                    $certi_ib->id,
                                                    (new CertiIb)->getTable(),
                                                    $data->id,
                                                    (new CertiIBSaveAssessment)->getTable(),
                                                    2,
                                                    'นำส่งรายงานการตรวจประเมิน',
                                                    view('mail.IB.save_assessment', $data_app),
                                                    $certi_ib->created_by,
                                                    $certi_ib->agent_id,
                                                    auth()->user()->getKey(),
                                                    !empty($certi_ib->DataEmailCertifyCenter) ?  implode(',',(array)$certi_ib->DataEmailCertifyCenter)  :  'ib@tisi.mail.go.th',
                                                    $certi_ib->email,
                                                    !empty($certi_ib->DataEmailDirectorIBCC) ? implode(',',(array)$certi_ib->DataEmailDirectorIBCC)   :   'ib@tisi.mail.go.th',
                                                    !empty($certi_ib->DataEmailDirectorIBReply) ?implode(',',(array)$certi_ib->DataEmailDirectorIBReply)   :   'ib@tisi.mail.go.th',
                                                    null
                                                    );

            $html = new IBSaveAssessmentMail($data_app);
            $mail =  Mail::to($certi_ib->email)->send($html);

            if(is_null($mail) && !empty($log_email)){
                HP::getUpdateCertifyLogEmail($log_email->id);
            }   
        }
     }

     public function set_check_mail($data,$certi_ib) {

        if(!is_null($certi_ib->email)){
            $config = HP::getConfig();
            $url  =   !empty($config->url_acc) ? $config->url_acc : url('');

            $data_app = [ 
                        'certi_ib'    => $certi_ib ?? '-',
                        'assessment'  => $data ?? '-',
                        'url'         => $url.'certify/applicant-ib' ?? '-',
                        'email'       => !empty($certi_ib->DataEmailCertifyCenter) ? $certi_ib->DataEmailCertifyCenter : 'ib@tisi.mail.go.th',
                        'email_cc'    => !empty($certi_ib->DataEmailDirectorIBCC) ? $certi_ib->DataEmailDirectorIBCC : 'ib@tisi.mail.go.th',
                        'email_reply' => !empty($certi_ib->DataEmailDirectorIBReply) ? $certi_ib->DataEmailDirectorIBReply : 'ib@tisi.mail.go.th'
                       ];
                
            $log_email =  HP::getInsertCertifyLogEmail($certi_ib->app_no,
                                                    $certi_ib->id,
                                                    (new CertiIb)->getTable(),
                                                    $data->id,
                                                    (new CertiIBSaveAssessment)->getTable(),
                                                    2,
                                                    !is_null($data->FileAttachAssessment5To) ? 'แจ้งผลการประเมินหลักฐานการแก้ไขข้อบกพร่อง' : 'แจ้งผลการประเมินแนวทางแก้ไขข้อบกพร่อง',
                                                    view('mail.IB.check_save_assessment', $data_app),
                                                    $certi_ib->created_by,
                                                    $certi_ib->agent_id,
                                                    auth()->user()->getKey(),
                                                    !empty($certi_ib->DataEmailCertifyCenter) ?  implode(',',(array)$certi_ib->DataEmailCertifyCenter)  :  'ib@tisi.mail.go.th',
                                                    $certi_ib->email,
                                                    !empty($certi_ib->DataEmailDirectorIBCC) ? implode(',',(array)$certi_ib->DataEmailDirectorIBCC)   :   'ib@tisi.mail.go.th',
                                                    !empty($certi_ib->DataEmailDirectorIBReply) ?implode(',',(array)$certi_ib->DataEmailDirectorIBReply)   :   'ib@tisi.mail.go.th',
                                                    null
                                                    );

            $html = new IBCheckSaveAssessment($data_app);
            $mail =  Mail::to($certi_ib->email)->send($html);

            if(is_null($mail) && !empty($log_email)){
                HP::getUpdateCertifyLogEmail($log_email->id);
            }   
       }
    }



     //การตรวจประเมิน   ผู้ประกอบการ +  ผก.
     public function set_mail_past($data,$certi_ib) {
 
        if(!is_null($certi_ib->email)){
            $config = HP::getConfig();
            $url  =   !empty($config->url_acc) ? $config->url_acc : url('');

            $data_app = [ 
                        'certi_ib'    => $certi_ib,
                        'assessment'  => $data ?? '-',
                        'url'         => $url.'certify/applicant-ib' ?? '-',
                        'email'       =>  !empty($certi_ib->DataEmailCertifyCenter) ? $certi_ib->DataEmailCertifyCenter : 'ib@tisi.mail.go.th',
                        'email_cc'    =>  !empty($certi_ib->DataEmailDirectorIBCC) ? $certi_ib->DataEmailDirectorIBCC : 'ib@tisi.mail.go.th',
                        'email_reply' => !empty($certi_ib->DataEmailDirectorIBReply) ? $certi_ib->DataEmailDirectorIBReply : 'ib@tisi.mail.go.th'
                       ];
                
            $log_email =  HP::getInsertCertifyLogEmail($certi_ib->app_no,
                                                    $certi_ib->id,
                                                    (new CertiIb)->getTable(),
                                                    $data->id,
                                                    (new CertiIBSaveAssessment)->getTable(),
                                                    2,
                                                    'รายงานการปิดข้อบกพร้อง/แจ้งยืนยันขอบข่าย',
                                                    view('mail.IB.save_assessment_past', $data_app),
                                                    $certi_ib->created_by,
                                                    $certi_ib->agent_id,
                                                    auth()->user()->getKey(),
                                                    !empty($certi_ib->DataEmailCertifyCenter) ?  implode(',',(array)$certi_ib->DataEmailCertifyCenter)  :  'ib@tisi.mail.go.th',
                                                    $certi_ib->email,
                                                    !empty($certi_ib->DataEmailDirectorIBCC) ? implode(',',(array)$certi_ib->DataEmailDirectorIBCC)   :   'ib@tisi.mail.go.th',
                                                    !empty($certi_ib->DataEmailDirectorIBReply) ?implode(',',(array)$certi_ib->DataEmailDirectorIBReply)   :   'ib@tisi.mail.go.th',
                                                    null
                                                    );

            $html = new IBSaveAssessmentPastMail($data_app);
            $mail =  Mail::to($certi_ib->email)->send($html);

            if(is_null($mail) && !empty($log_email)){
                HP::getUpdateCertifyLogEmail($log_email->id);
            }   
 
       }
    }

     
    public function set_history_bug($data) // ข้อบกพร่อง/ข้อสังเกต
    {
        $tb = new CertiIBSaveAssessment;
        $assessment = CertiIBSaveAssessment::select('name', 'laboratory_name', 'report_date', 'bug_report', 'degree')
                      ->where('id',$data->id)
                      ->first();
      
        $bug = CertiIBSaveAssessmentBug::select('report','remark','no','type','reporter_id','details','status','comment','file_status','file_comment','attachs','attach_client_name')
                              ->where('assessment_id',$data->id)
                              ->get()
                              ->toArray();
       CertiIbHistory::create([
                                    'app_certi_ib_id'   => $data->app_certi_ib_id ?? null,
                                    'auditors_id'       => $data->auditors_id ?? null,
                                    'system'            => 7,
                                    'table_name'        => $tb->getTable(),
                                    'ref_id'            => $data->id, 
                                    'details_one'       => json_encode($assessment) ?? null,
                                    'details_two'       => (count($bug) > 0) ? json_encode($bug) : null,
                                    'details_three'     => !empty($data->FileAttachAssessment1To->file) ? $data->FileAttachAssessment1To->file : null,
                                    'file_client_name'  =>  !empty($data->FileAttachAssessment1To->file_client_name) ? $data->FileAttachAssessment1To->file_client_name : null,
                                    'details_four'      => (count($data->FileAttachAssessment2Many) > 0) ? json_encode($data->FileAttachAssessment2Many) : null,
                                    'attachs'           => (count($data->FileAttachAssessment3Many) > 0) ? json_encode($data->FileAttachAssessment3Many) : null,
                                    'file'              =>  (count($data->FileAttachAssessment4Many) > 0) ? json_encode($data->FileAttachAssessment4Many) : null,
                                    'attachs_car'       =>  !empty($data->FileAttachAssessment5To->file) ? $data->FileAttachAssessment5To->file : null, // ปิด car
                                    'attach_client_name'=>  !empty($data->FileAttachAssessment5To->file_client_name) ? $data->FileAttachAssessment5To->file_client_name : null,
                                    'created_by'        =>  auth()->user()->runrecno
                             ]);
   }
   public function set_history($data) //บันทึกผลการตรวจประเมิน
   {
       $tb = new CertiIBSaveAssessment;
       $assessment = CertiIBSaveAssessment::select('name', 'laboratory_name', 'report_date', 'bug_report', 'degree')
                     ->where('id',$data->id)
                     ->first();
      CertiIbHistory::create([
                                   'app_certi_ib_id'    => $data->app_certi_ib_id ?? null,
                                   'auditors_id'        => $data->auditors_id ?? null,
                                   'system'             => 8,
                                   'table_name'         => $tb->getTable(),
                                   'ref_id'             => $data->id, 
                                   'details_one'        => json_encode($assessment) ?? null,
                                   'details_two'        => null,
                                   'details_three'      => !empty($data->FileAttachAssessment1To->file) ? $data->FileAttachAssessment1To->file : null,
                                   'file_client_name'   =>  !empty($data->FileAttachAssessment1To->file_client_name) ? $data->FileAttachAssessment1To->file_client_name : null,
                                   'details_four'       => (count($data->FileAttachAssessment2Many) > 0) ? json_encode($data->FileAttachAssessment2Many) : null,
                                   'attachs'            => (count($data->FileAttachAssessment3Many) > 0) ? json_encode($data->FileAttachAssessment3Many) : null,
                                   'file'               => (count($data->FileAttachAssessment4Many) > 0) ? json_encode($data->FileAttachAssessment4Many) : null,
                                   'attachs_car'        => !empty($data->FileAttachAssessment5To->file) ? $data->FileAttachAssessment5To->file : null, // ปิด car
                                   'attach_client_name' =>  !empty($data->FileAttachAssessment5To->file_client_name) ? $data->FileAttachAssessment5To->file_client_name : null,
                                   'created_by'         => auth()->user()->runrecno
                            ]);
   }
    public function DataCertiIb($id) {
        $auditor = CertiIBAuditors::findOrFail($id);  
        $certi_ib =  CertiIb::findOrFail($auditor->app_certi_ib_id); 
        return response()->json([
           'certi_ib'=> $certi_ib ?? '-' 
        ]);
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
}
