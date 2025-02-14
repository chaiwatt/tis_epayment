<?php

namespace App\Http\Controllers\Certify\CB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SaveAssessmentCB;
use Illuminate\Http\Request;
use Exception;
use stdClass;
use Storage;
use HP;
use DB;

use App\User;
use App\Models\Certify\ApplicantCB\CertiCb;
use App\Models\Certify\ApplicantCB\CertiCbHistory;
use App\Models\Certify\ApplicantCB\CertiCBAttachAll;
use App\Models\Certify\ApplicantCB\CertiCBAuditors;
use App\Models\Certify\ApplicantCB\CertiCBAuditorsList;
use App\Models\Certify\ApplicantCB\CertiCBSaveAssessment;
use App\Models\Certify\ApplicantCB\CertiCBSaveAssessmentBug;
use App\Models\Certify\ApplicantCB\CertiCBReport;
use App\Models\Certify\ApplicantCB\CertiCBCheck;
use App\Models\Certify\ApplicantCB\CertiCBReview;

use Illuminate\Support\Facades\Mail;
use App\Mail\CB\CBSaveAssessmentMail;
use App\Mail\CB\CheckSaveAssessment;
use App\Mail\CB\CBSaveAssessmentPastMail;
class SaveAssessmentCBController extends Controller
{
    private $attach_path;//ที่เก็บไฟล์แนบ
    public function __construct()
    {
        $this->middleware('auth');
        $this->attach_path = 'files/applicants/check_files_cb/';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function index(Request $request)
    {
        $model = str_slug('saveassessmentcb','-');
        if(auth()->user()->can('view-'.$model)) {

           $keyword = $request->get('search');
            $filter = [];

            $filter['filter_degree'] = $request->get('filter_degree', '');
            $filter['filter_search'] = $request->get('filter_search', '');
            $filter['perPage'] = $request->get('perPage', 10);

            $Query = new CertiCBSaveAssessment;
            $Query = $Query->select('app_certi_cb_assessment.*');
            if ($filter['filter_degree']!='') {
                if($filter['filter_degree'] == '0'){
                    $Query = $Query->where('bug_report', '!=', '1');
                }else if($filter['filter_degree'] == '1'){
                    $Query = $Query->where('bug_report', '==', $filter['filter_degree']);
                }
            }

            if ($filter['filter_search'] != '') {
                $CertiCb  = CertiCb::where('app_no', 'like', '%'.$filter['filter_search'].'%')->pluck('id');
                $Query = $Query->whereIn('app_certi_cb_id', $CertiCb);
            }
              //เจ้าหน้าที่ CB และไม่มีสิทธิ์ admin , ผอ , ผก , ลท.
             if(in_array("29",auth()->user()->RoleListId) && auth()->user()->SetRolesAdminCertify() == "false" ){
                $check = CertiCBCheck::where('user_id',auth()->user()->runrecno)->pluck('app_certi_cb_id'); // เช็คเจ้าหน้าที่ IB
                if(isset($check) && count($check) > 0  ) {
                     $Query = $Query->LeftJoin('app_certi_cb_check','app_certi_cb_check.app_certi_cb_id','=','app_certi_cb_assessment.app_certi_cb_id')
                                    ->where('user_id',auth()->user()->runrecno);  //เจ้าหน้าที่  IB ที่ได้มอบหมาย
                }else{
                    $Query = $Query->whereIn('id',['']);  // ไม่ตรงกับเงื่อนไข
                }
            }
            $assessment = $Query ->orderby('id','desc')->sortable()

                                ->paginate($filter['perPage']);

            return view('certify/cb/save_assessment_cb.index', compact('assessment', 'filter'));
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
        $model = str_slug('saveassessmentcb','-');
        if(auth()->user()->can('add-'.$model)) {
            $previousUrl = app('url')->previous();
            $assessment = new CertiCBSaveAssessment;
            $bug = [new CertiCBSaveAssessmentBug];

            $app_no = [];
            //เจ้าหน้าที่ CB และไม่มีสิทธิ์ admin , ผอ , ผก , ลท.
           if(in_array("29",auth()->user()->RoleListId) && auth()->user()->SetRolesAdminCertify() == "false" ){
               $check = CertiCBCheck::where('user_id',auth()->user()->runrecno)->pluck('app_certi_cb_id'); // เช็คเจ้าหน้าที่ IB
               if(count($check) > 0 ){
                   $auditor= CertiCBAuditors::select('id','app_certi_cb_id','auditor')
                                    ->whereIn('step_id',[6])
                                    ->whereIn('app_certi_cb_id',$check)
                                    ->orderby('id','desc')
                                    ->get();
                 if(count($auditor) > 0 ){
                   foreach ($auditor as $item){
                     $app_no[$item->id] = $item->auditor . " ( ". @$item->CertiCbCostTo->app_no . " )";
                    }
                  }
                }
            }else{
                   $auditor = CertiCBAuditors::select('id','app_certi_cb_id','auditor')
                                            ->whereIn('step_id',[6])
                                           ->orderby('id','desc')
                                           ->get();
                  if(count($auditor) > 0 ){
                    foreach ($auditor as $item){
                         $app_no[$item->id] = $item->auditor . " ( ". @$item->CertiCbCostTo->app_no . " )";
                    }
                  }
             }

            return view('certify/cb/save_assessment_cb.create',['app_no'=> $app_no,
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

        $model = str_slug('saveassessmentcb','-');
        if(auth()->user()->can('add-'.$model)) {

            $request->validate([
                'app_certi_cb_id' => 'required',
                'auditors_id' => 'required',
            ]);


            $request->request->add(['created_by' => auth()->user()->getKey()]);
            $requestData = $request->all();
            $requestData['report_date']    =  HP::convertDate($request->report_date,true) ?? null;

            if($request->bug_report == 1){
                $requestData['main_state'] = isset($request->main_state) ? 2 : 1;
            }else{
                $requestData['main_state'] = 1;
            }

            $auditors = CertiCBSaveAssessment::create($requestData);

            //
            $CertiCb = CertiCb::findOrFail($auditors->app_certi_cb_id);
            $tb = new CertiCBSaveAssessment;

            // ข้อบกพร่อง/ข้อสังเกต
            if(isset($requestData["detail"])){
                $this->storeDetail($auditors,$requestData["detail"]);
            }

            // รายงานการตรวจประเมิน
             if($request->file  && $request->hasFile('file') ){
                        $certi_cb_attach_more                   = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id  = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id           = $auditors->id;
                        $certi_cb_attach_more->table_name       = $tb->getTable();
                        $certi_cb_attach_more->file_section     = '1';
                        $certi_cb_attach_more->file             = $this->storeFile($request->file,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name = HP::ConvertCertifyFileName($request->file->getClientOriginalName());
                        $certi_cb_attach_more->token            = str_random(16);
                        $certi_cb_attach_more->save();
            }
if($auditors->bug_report == 2){

            // รายงาน Scope
            if($request->file_scope  && $request->hasFile('file_scope')){

                foreach ($request->file_scope as $index => $item){
                        $certi_cb_attach_more = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id      = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id               = $auditors->id;
                        $certi_cb_attach_more->table_name           = $tb->getTable();
                        $certi_cb_attach_more->file_section         = '2';
                        $certi_cb_attach_more->file                 = $this->storeFile($item,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name     = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_cb_attach_more->token                = str_random(16);
                        $certi_cb_attach_more->save();
                }
            }
           // รายงาน สรุปรายงานการตรวจทุกครั้ง
            if($request->file_report  && $request->hasFile('file_report')){
                foreach ($request->file_report as $index => $item){
                        $certi_cb_attach_more                       = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id      = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id               = $auditors->id;
                        $certi_cb_attach_more->table_name           = $tb->getTable();
                        $certi_cb_attach_more->file_section         = '3';
                        $certi_cb_attach_more->file                 = $this->storeFile($item,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name     = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_cb_attach_more->token                = str_random(16);
                        $certi_cb_attach_more->save();
                }
            }
}
 // ไฟล์แนบ
 if($request->attachs  && $request->hasFile('attachs')  && $auditors->bug_report == 1){
                foreach ($request->attachs as $index => $item){
                        $certi_cb_attach_more = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id      = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id               = $auditors->id;
                        $certi_cb_attach_more->table_name           = $tb->getTable();
                        $certi_cb_attach_more->file_section         = '4';
                        $certi_cb_attach_more->file                 = $this->storeFile($item,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name     = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_cb_attach_more->token                = str_random(16);
                        $certi_cb_attach_more->save();
                }
 }


    // สถานะ แต่งตั้งคณะกรรมการ
       $committee = CertiCBAuditors::findOrFail($auditors->auditors_id);
       if(in_array($auditors->degree,[1,8])  && $auditors->bug_report == 1){
                //Log
                $this->set_history_bug($auditors);
                //  Mail
                   $this->set_mail($auditors,$CertiCb);
               if($auditors->main_state == 1 ){
                    $committee->step_id = 8; // แก้ไขข้อบกพร่อง/ข้อสังเกต
                    $committee->save();

                }else{
                    $committee->step_id = 9; // ไม่ผ่านการตรวจสอบประเมิน
                    $committee->save();

               // สถานะ แต่งตั้งคณะกรรมการ
                $auditor = CertiCBAuditors::where('app_certi_cb_id',$CertiCb->id)
                                            ->whereIn('step_id',[9,10])
                                            ->whereNull('status_cancel')
                                            ->get();

                if(count($auditor) == count($CertiCb->CertiCBAuditorsManyBy)){
                    $report = new   CertiCBReview;  //ทบทวนฯ
                    $report->app_certi_cb_id  = $CertiCb->id;
                    $report->save();
                    $CertiCb->update(['review'=>1,'status'=>11]);  // ทบทวน
                 }
                }

        }

        if($auditors->degree == 4){
             $committee->step_id = 7; // ผ่านการตรวจสอบประเมิน
             $committee->save();
             $this->set_history($auditors);
             $this->set_mail_past($auditors,$CertiCb);

        }

        if($request->previousUrl){
            return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
        }else{
            return redirect('certify/save_assessment-cb')->with('message', 'เรียบร้อยแล้ว!');
        }

        }
        abort(403);
    }


    public function edit($id)
    {
        $model = str_slug('saveassessmentcb','-');
        if(auth()->user()->can('edit-'.$model)) {
            $previousUrl = app('url')->previous();
            $assessment = CertiCBSaveAssessment::findOrFail($id);
            $bug = CertiCBSaveAssessmentBug::where('assessment_id',$id)->get();
            if(count($bug) <= 0){
                $bug = [new CertiCBSaveAssessmentBug];
            }
                $found = [];
                $auditors_id = CertiCBAuditors::where('app_certi_cb_id',$assessment->app_certi_cb_id)->pluck('id');
                if(count($auditors_id) > 0){
                   $auditors_list =   CertiCBAuditorsList::select('user_id','temp_users')
                                                        ->whereIn('auditors_id',$auditors_id)
                                                        ->distinct('user_id')
                                                        ->get();
                    if(count($auditors_list) > 0){
                      foreach ($auditors_list as $index => $item){
                            $found[$item->user_id] =  $item->temp_users ;
                      }

                    }
                }
            $attach_path = $this->attach_path;//path ไฟล์แนบ
            return view('certify/cb/save_assessment_cb.edit', compact('assessment','bug','found','previousUrl','attach_path'));
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
        $model = str_slug('saveassessmentcb','-');
        if(auth()->user()->can('edit-'.$model)) {

            $request->request->add(['updated_by' => auth()->user()->getKey()]); //user update
                $requestData = $request->all();
                $requestData['report_date'] =  HP::convertDate($request->report_date,true) ?? null;
                if($request->bug_report == 1){
                    $requestData['main_state'] = isset($request->main_state) ? 2 : 1;
                }else{
                    $requestData['main_state'] = 1;
                }
          $tb = new CertiCbSaveAssessment;
            $auditors = CertiCBSaveAssessment::findOrFail($id);
            $auditors->update($requestData);

            $CertiCb = CertiCb::findOrFail($auditors->app_certi_cb_id);
            // ข้อบกพร่อง/ข้อสังเกต
            if(isset($requestData["detail"])){
                $this->storeDetail($auditors,$requestData["detail"]);
            }

            // รายงานการตรวจประเมิน
             if($request->file  && $request->hasFile('file')){
                        $certi_cb_attach_more = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id  = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id           = $auditors->id;
                        $certi_cb_attach_more->table_name       = $tb->getTable();
                        $certi_cb_attach_more->file_section     = '1';
                        $certi_cb_attach_more->file             = $this->storeFile($request->file,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name = HP::ConvertCertifyFileName($request->file->getClientOriginalName());
                        $certi_cb_attach_more->token            = str_random(16);
                        $certi_cb_attach_more->save();
            }


if($auditors->bug_report == 2){
            // รายงาน Scope
            if($request->file_scope  && $request->hasFile('file_scope')){
                foreach ($request->file_scope as $index => $item){
                        $certi_cb_attach_more = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id  = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id           = $auditors->id;
                        $certi_cb_attach_more->table_name       = $tb->getTable();
                        $certi_cb_attach_more->file_section     = '2';
                        $certi_cb_attach_more->file             = $this->storeFile($item,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_cb_attach_more->token            = str_random(16);
                        $certi_cb_attach_more->save();
                }
            }
           // รายงาน สรุปรายงานการตรวจทุกครั้ง
            if($request->file_report  && $request->hasFile('file_report')){
                foreach ($request->file_report as $index => $item){
                        $certi_cb_attach_more = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id  = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id           = $auditors->id;
                        $certi_cb_attach_more->table_name       = $tb->getTable();
                        $certi_cb_attach_more->file_section     = '3';
                        $certi_cb_attach_more->file             = $this->storeFile($item,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_cb_attach_more->token            = str_random(16);
                        $certi_cb_attach_more->save();
                }
            }
}

 // ไฟล์แนบ
 if($request->attachs   && $request->hasFile('attachs') &&  $auditors->bug_report == 1){
                foreach ($request->attachs as $index => $item){
                        $certi_cb_attach_more = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id  = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id           = $auditors->id;
                        $certi_cb_attach_more->table_name       = $tb->getTable();
                        $certi_cb_attach_more->file_section     = '4';
                        $certi_cb_attach_more->file             = $this->storeFile($item,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_cb_attach_more->token = str_random(16);
                        $certi_cb_attach_more->save();
                }
 }


      // สถานะ แต่งตั้งคณะกรรมการ
         $committee = CertiCBAuditors::findOrFail($auditors->auditors_id);
         if(in_array($auditors->degree,[1,8])  && $auditors->bug_report == 1){
                //Log
                  $this->set_history_bug($auditors);
                  //  Mail
                $this->set_mail($auditors,$CertiCb);
                 if($auditors->main_state == 1 ){
                      $committee->step_id = 8; // แก้ไขข้อบกพร่อง/ข้อสังเกต
                      $committee->save();

                  }else{
                      $committee->step_id = 9; // ไม่ผ่านการตรวจสอบประเมิน
                      $committee->save();

                    // สถานะ แต่งตั้งคณะกรรมการ
                    $auditor = CertiCBAuditors::where('app_certi_cb_id',$CertiCb->id)
                                                ->whereIn('step_id',[9,10])
                                                ->whereNull('status_cancel')
                                                ->get();

                    if(count($auditor) == count($CertiCb->CertiCBAuditorsManyBy)){
                        $report = new   CertiCBReview;  //ทบทวนฯ
                        $report->app_certi_cb_id  = $CertiCb->id;
                        $report->save();
                        $CertiCb->update(['review'=>1,'status'=>11]);  // ทบทวน
                    }
                  }

          }

          if($auditors->degree == 4){
               $committee->step_id = 7; // ผ่านการตรวจสอบประเมิน
               $committee->save();
               $this->set_history($auditors);
               $this->set_mail_past($auditors,$CertiCb);

          }

        if($request->previousUrl){
            return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
        }else{
            return redirect('certify/save_assessment-cb')->with('message', 'เรียบร้อยแล้ว!');
        }

        }
        abort(403);

    }

    public function DataCertiCb($id) {
        $auditor = CertiCBAuditors::findOrFail($id);
        $certi_cb =  CertiCb::findOrFail($auditor->app_certi_cb_id);
        $certi_cb->tis             = !empty($certi_cb->FormulaTo->title) ?  str_replace("มอก.","",$certi_cb->FormulaTo->title) :'' ;
        $certi_cb->app_certi_cb_id =  @$certi_cb->id ?? null ;

        return response()->json([
           'certi_cb'=> $certi_cb ?? '-'
        ]);
    }
    public function storeDetail($data,$notice) {

        $data->CertiCBBugMany()->delete();
        $detail = (array)@$notice;
        foreach ($detail['notice'] as $key => $item) {
                $bug = new CertiCBSaveAssessmentBug;
                $bug->assessment_id = $data->id;
                $bug->remark        = $item;
                $bug->report        = $detail["report"][$key] ?? null;
                $bug->no            = $detail["no"][$key] ?? null;
                $bug->type          = $detail["type"][$key] ?? null;
                $bug->reporter_id   = $detail["found"][$key] ?? null;
                $bug->save();
        }
    }

        //พบข้อบกพร่อง/ข้อสังเกต  ผู้ประกอบการ +  ผก.
    public function set_mail($data,$certi_cb) {
 
        if(!is_null($certi_cb->email)){

            $config = HP::getConfig();
            $url  =   !empty($config->url_acc) ? $config->url_acc : url('');

            $data_app =[
                            'certi_cb'       => $certi_cb ?? '-',
                            'assessment'     => $data ?? '-',
                            'url'            => $url.'certify/applicant-ib' ,
                            'email'          =>  !empty($certi_cb->DataEmailCertifyCenter) ? $certi_cb->DataEmailCertifyCenter : 'cb@tisi.mail.go.th',
                            'email_cc'       =>  !empty($certi_cb->DataEmailDirectorCBCC) ? $certi_cb->DataEmailDirectorCBCC : 'cb@tisi.mail.go.th',
                            'email_reply'    => !empty($certi_cb->DataEmailDirectorCBReply) ? $certi_cb->DataEmailDirectorCBReply : 'cb@tisi.mail.go.th'
                        ];

            $log_email =  HP::getInsertCertifyLogEmail($certi_cb->app_no,
                                                    $certi_cb->id,
                                                    (new CertiCb)->getTable(),
                                                    $data->id,
                                                    (new CertiCBSaveAssessment)->getTable(),
                                                    3,
                                                    'นำส่งรายงานการตรวจประเมิน',
                                                    view('mail.CB.save_assessment', $data_app),
                                                    $certi_cb->created_by,
                                                    $certi_cb->agent_id,
                                                    auth()->user()->getKey(),
                                                    !empty($certi_cb->DataEmailCertifyCenter) ?  implode(',',(array)$certi_cb->DataEmailCertifyCenter)  :  'cb@tisi.mail.go.th',
                                                    $certi_cb->email,
                                                    !empty($certi_cb->DataEmailDirectorCBCC) ? implode(',',(array)$certi_cb->DataEmailDirectorCBCC)   :   'cb@tisi.mail.go.th',
                                                    !empty($certi_cb->DataEmailDirectorCBReply) ?implode(',',(array)$certi_cb->DataEmailDirectorCBReply)   :   'cb@tisi.mail.go.th',
                                                    null
                                                    );

            $html = new CBSaveAssessmentMail($data_app);
            $mail =  Mail::to($certi_cb->email)->send($html);

            if(is_null($mail) && !empty($log_email)){
                HP::getUpdateCertifyLogEmail($log_email->id);
            }
       }
    }
    public function set_check_mail($data,$certi_cb) {
 
        if(!is_null($certi_cb->email)){

            $config = HP::getConfig();
            $url  =   !empty($config->url_acc) ? $config->url_acc : url('');

            $data_app = [
                            'certi_cb'       => $certi_cb ?? '-',
                            'assessment'     => $data ?? '-',
                            'url'            => $url.'certify/applicant-ib',
                            'email'          => !empty($certi_cb->DataEmailCertifyCenter) ? $certi_cb->DataEmailCertifyCenter : 'cb@tisi.mail.go.th',
                            'email_cc'       => !empty($certi_cb->DataEmailDirectorCBCC) ? $certi_cb->DataEmailDirectorCBCC : 'cb@tisi.mail.go.th',
                            'email_reply'    => !empty($certi_cb->DataEmailDirectorCBReply) ? $certi_cb->DataEmailDirectorCBReply : 'cb@tisi.mail.go.th'
                        ];

            $log_email =  HP::getInsertCertifyLogEmail($certi_cb->app_no,
                                                    $certi_cb->id,
                                                    (new CertiCb)->getTable(),
                                                    $data->id,
                                                    (new CertiCBSaveAssessment)->getTable(),
                                                    3,
                                                    !is_null($data->FileAttachAssessment5To) ? 'แจ้งผลการประเมินหลักฐานการแก้ไขข้อบกพร่อง' : 'แจ้งผลการประเมินแนวทางแก้ไขข้อบกพร่อง',
                                                    view('mail.CB.check_save_assessment', $data_app),
                                                    $certi_cb->created_by,
                                                    $certi_cb->agent_id,
                                                    auth()->user()->getKey(),
                                                    !empty($certi_cb->DataEmailCertifyCenter) ?  implode(',',(array)$certi_cb->DataEmailCertifyCenter)  :  'cb@tisi.mail.go.th',
                                                    $certi_cb->email,
                                                    !empty($certi_cb->DataEmailDirectorCBCC) ? implode(',',(array)$certi_cb->DataEmailDirectorCBCC)   :   'cb@tisi.mail.go.th',
                                                    !empty($certi_cb->DataEmailDirectorCBReply) ?implode(',',(array)$certi_cb->DataEmailDirectorCBReply)   :   'cb@tisi.mail.go.th',
                                                    null
                                                    );

            $html = new CheckSaveAssessment($data_app);
            $mail =  Mail::to($certi_cb->email)->send($html);

            if(is_null($mail) && !empty($log_email)){
                HP::getUpdateCertifyLogEmail($log_email->id);
            }

       }
    }


     //การตรวจประเมิน   ผู้ประกอบการ +  ผก.
     public function set_mail_past($data,$certi_cb) {
 
        if(!is_null($certi_cb->email)){

            $config = HP::getConfig();
            $url  =   !empty($config->url_acc) ? $config->url_acc : url('');

            $data_app = [
                            'certi_cb'      => $certi_cb ?? '-',
                            'assessment'    => $data ?? '-',
                            'url'           => $url.'certify/applicant-ib' ,
                            'email'         =>  !empty($certi_cb->DataEmailCertifyCenter) ? $certi_cb->DataEmailCertifyCenter : 'cb@tisi.mail.go.th',
                            'email_cc'      =>  !empty($certi_cb->DataEmailDirectorCBCC) ? $certi_cb->DataEmailDirectorCBCC : 'cb@tisi.mail.go.th',
                            'email_reply'   => !empty($certi_cb->DataEmailDirectorCBReply) ? $certi_cb->DataEmailDirectorCBReply : 'cb@tisi.mail.go.th'
                        ];

            $log_email =  HP::getInsertCertifyLogEmail($certi_cb->app_no,
                                                    $certi_cb->id,
                                                    (new CertiCb)->getTable(),
                                                    $data->id,
                                                    (new CertiCBSaveAssessment)->getTable(),
                                                    3,
                                                    'รายงานการปิดข้อบกพร้อง/แจ้งยืนยันขอบข่าย',
                                                    view('mail.CB.save_assessment_past', $data_app),
                                                    $certi_cb->created_by,
                                                    $certi_cb->agent_id,
                                                    auth()->user()->getKey(),
                                                    !empty($certi_cb->DataEmailCertifyCenter) ?  implode(',',(array)$certi_cb->DataEmailCertifyCenter)  :  'cb@tisi.mail.go.th',
                                                    $certi_cb->email,
                                                    !empty($certi_cb->DataEmailDirectorCBCC) ? implode(',',(array)$certi_cb->DataEmailDirectorCBCC)   :   'cb@tisi.mail.go.th',
                                                    !empty($certi_cb->DataEmailDirectorCBReply) ?implode(',',(array)$certi_cb->DataEmailDirectorCBReply)   :   'cb@tisi.mail.go.th',
                                                    null
                                                    );

            $html = new CBSaveAssessmentPastMail($data_app);
            $mail =  Mail::to($certi_cb->email)->send($html);

            if(is_null($mail) && !empty($log_email)){
                HP::getUpdateCertifyLogEmail($log_email->id);
            }
 
       }
    }
    public function set_history_bug($data)
    {
        $tb = new CertiCBSaveAssessment;
        $assessment = CertiCBSaveAssessment::select('name','auditors_id', 'laboratory_name', 'report_date', 'bug_report', 'degree')
                      ->where('id',$data->id)
                      ->first();

        $bug = CertiCBSaveAssessmentBug::select('report','remark','no','type','reporter_id','details','status','comment','file_status','file_comment','attachs')
                              ->where('assessment_id',$data->id)
                              ->get()
                              ->toArray();
       CertiCbHistory::create([
                                    'app_certi_cb_id'   => $data->app_certi_cb_id ?? null,
                                    'auditors_id'       =>  $data->auditors_id ?? null,
                                    'system'            => 7,
                                    'table_name'        => $tb->getTable(),
                                    'ref_id'            => $data->id,
                                    'details_one'       =>  json_encode($assessment) ?? null,
                                    'details_two'       =>  (count($bug) > 0) ? json_encode($bug) : null,
                                    'details_three'     =>  !empty($data->FileAttachAssessment1To->file) ? $data->FileAttachAssessment1To->file : null,
                                    'file_client_name'  =>  !empty($data->FileAttachAssessment1To->file_client_name) ? $data->FileAttachAssessment1To->file_client_name : null,
                                    'details_four'      =>  (count($data->FileAttachAssessment2Many) > 0) ? json_encode($data->FileAttachAssessment2Many) : null,
                                    'attachs'           => (count($data->FileAttachAssessment3Many) > 0) ? json_encode($data->FileAttachAssessment3Many) : null,
                                    'file'              =>   (count($data->FileAttachAssessment4Many) > 0) ? json_encode($data->FileAttachAssessment4Many) : null,
                                    'attachs_car'       =>   !empty($data->FileAttachAssessment5To->file) ? $data->FileAttachAssessment5To->file : null, // ปิด car
                                    'attach_client_name'=>  !empty($data->FileAttachAssessment5To->file_client_name) ? $data->FileAttachAssessment5To->file_client_name : null,
                                    'created_by'        =>  auth()->user()->runrecno
                             ]);
   }
   public function set_history($data)
   {
       $tb = new CertiCBSaveAssessment;
       $assessment = CertiCBSaveAssessment::select('name','auditors_id', 'laboratory_name', 'report_date', 'bug_report', 'degree')
                     ->where('id',$data->id)
                     ->first();
      CertiCbHistory::create([
                                   'app_certi_cb_id'    => $data->app_certi_cb_id ?? null,
                                   'auditors_id'        =>  $data->auditors_id ?? null,
                                   'system'             => 8,
                                   'table_name'         => $tb->getTable(),
                                   'ref_id'             => $data->id,
                                   'details_one'        =>  json_encode($assessment) ?? null,
                                   'details_two'        =>   null,
                                   'details_three'      =>  !empty($data->FileAttachAssessment1To->file) ? $data->FileAttachAssessment1To->file : null,
                                   'file_client_name'   =>  !empty($data->FileAttachAssessment1To->file_client_name) ? $data->FileAttachAssessment1To->file_client_name : null,
                                   'details_four'       =>  (count($data->FileAttachAssessment2Many) > 0) ? json_encode($data->FileAttachAssessment2Many) : null,
                                   'attachs'            => (count($data->FileAttachAssessment3Many) > 0) ? json_encode($data->FileAttachAssessment3Many) : null,
                                   'file'               =>  (count($data->FileAttachAssessment4Many) > 0) ? json_encode($data->FileAttachAssessment4Many) : null,
                                   'attachs_car'        =>  !empty($data->FileAttachAssessment5To->file) ? $data->FileAttachAssessment5To->file : null, // ปิด car
                                   'attach_client_name' =>  !empty($data->FileAttachAssessment5To->file_client_name) ? $data->FileAttachAssessment5To->file_client_name : null,
                                   'created_by'         =>  auth()->user()->runrecno
                            ]);
   }

 public function DataAssessment($id) {
    $previousUrl = app('url')->previous();
    $assessment = CertiCbSaveAssessment::findOrFail($id);
    $attach_path = $this->attach_path;//path ไฟล์แนบ
    return view('certify/cb.save_assessment_cb.form_assessment', compact('assessment','previousUrl','attach_path'));
 }
public function UpdateAssessment(Request $request, $id){

    $auditors = CertiCbSaveAssessment::findOrFail($id);
    $tb = new CertiCbSaveAssessment;
    $CertiCb = CertiCb::findOrFail($auditors->app_certi_cb_id);

try {

if($auditors->degree != 5){

        $ids = $request->input('id');
        if(isset($ids)){
        foreach ($ids as $key => $item) {
                $bug = CertiCbSaveAssessmentBug::where('id',$item)->first();
            if(!is_null($bug)){
                $bug->status        = $request->status[$bug->id] ??  @$bug->status;
                $bug->comment       = $request->comment[$bug->id] ?? @$bug->comment;
                $bug->file_status   = $request->file_status[$bug->id] ??  @$bug->file_status;
                $bug->file_comment  = $request->file_comment[$bug->id] ?? null;
                // $bug->details =   null; //  แนวทางการแก้ไข
                $bug->save();
            }
         }

          if($request->hasFile('file_car')){
                    $auditors->main_state   = 1;
                    $auditors->degree       = 4;
                    $auditors->date_car     = date("Y-m-d"); // วันที่ปิด Car
                    $auditors->bug_report   = 2;
           }else{
                 if(isset($request->main_state)){
                    $auditors->main_state   =  2 ;
                    $auditors->degree       = 8;
                  }else{
                    $auditors->main_state   = 1;
                    $auditors->degree       = 3;
                  }
           }
            $auditors->save();


         // รายงานการตรวจประเมิน
        if($request->file  &&  $request->hasFile('file')){
                    $certi_cb_attach_more                   = new CertiCBAttachAll();
                    $certi_cb_attach_more->app_certi_cb_id  = $auditors->app_certi_cb_id ?? null;
                    $certi_cb_attach_more->ref_id           = $auditors->id;
                    $certi_cb_attach_more->table_name       = $tb->getTable();
                    $certi_cb_attach_more->file_section     = '1';
                    $certi_cb_attach_more->file             = $this->storeFile($request->file,$CertiCb->app_no);
                    $certi_cb_attach_more->file_client_name = HP::ConvertCertifyFileName($request->file->getClientOriginalName());
                    $certi_cb_attach_more->token            = str_random(16);
                    $certi_cb_attach_more->save();
        }

if($auditors->main_state == 1){
            // รายงาน Scope
            if($request->file_scope &&  $request->hasFile('file_scope')){
                foreach ($request->file_scope as $index => $item){
                        $certi_cb_attach_more                       = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id      = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id               = $auditors->id;
                        $certi_cb_attach_more->table_name           = $tb->getTable();
                        $certi_cb_attach_more->file_section         = '2';
                        $certi_cb_attach_more->file                 = $this->storeFile($item,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name     = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_cb_attach_more->token                = str_random(16);
                        $certi_cb_attach_more->save();
                }
            }
           // รายงาน สรุปรายงานการตรวจทุกครั้ง
            if($request->file_report &&  $request->hasFile('file_report')){
                foreach ($request->file_report as $index => $item){
                        $certi_cb_attach_more                       = new CertiCBAttachAll();
                        $certi_cb_attach_more->app_certi_cb_id      = $auditors->app_certi_cb_id ?? null;
                        $certi_cb_attach_more->ref_id               = $auditors->id;
                        $certi_cb_attach_more->table_name           = $tb->getTable();
                        $certi_cb_attach_more->file_section         = '3';
                        $certi_cb_attach_more->file                 = $this->storeFile($item,$CertiCb->app_no);
                        $certi_cb_attach_more->file_client_name     = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $certi_cb_attach_more->token                = str_random(16);
                        $certi_cb_attach_more->save();
                }
            }
}

        // ไฟล์แนบ
        if($request->attachs &&  $request->hasFile('attachs')){
            foreach ($request->attachs as $index => $item){
                    $certi_cb_attach_more                       = new CertiCBAttachAll();
                    $certi_cb_attach_more->app_certi_cb_id      = $auditors->app_certi_cb_id ?? null;
                    $certi_cb_attach_more->ref_id               = $auditors->id;
                    $certi_cb_attach_more->table_name           = $tb->getTable();
                    $certi_cb_attach_more->file_section         = '4';
                    $certi_cb_attach_more->file                 = $this->storeFile($item,$CertiCb->app_no);
                    $certi_cb_attach_more->file_client_name     = HP::ConvertCertifyFileName($item->getClientOriginalName());
                    $certi_cb_attach_more->token                = str_random(16);
                    $certi_cb_attach_more->save();
            }
         }

        // รายงาน Car
        if($request->file_car &&  $request->hasFile('file_car')){
            $certi_cb_attach_more                       = new CertiCBAttachAll();
            $certi_cb_attach_more->app_certi_cb_id      = $auditors->app_certi_cb_id ?? null;
            $certi_cb_attach_more->ref_id               = $auditors->id;
            $certi_cb_attach_more->table_name           = $tb->getTable();
            $certi_cb_attach_more->file_section         = '5';
            $certi_cb_attach_more->file                 = $this->storeFile($request->file_car,$CertiCb->app_no);
            $certi_cb_attach_more->file_client_name     = HP::ConvertCertifyFileName($request->file_car->getClientOriginalName());
            $certi_cb_attach_more->token                = str_random(16);
            $certi_cb_attach_more->save();
        }

   //

         //  Log
        $this->set_history_bug($auditors);
      // สถานะ แต่งตั้งคณะกรรมการ
         $committee = CertiCBAuditors::findOrFail($auditors->auditors_id);
         if($auditors->degree == 3){

            $committee->step_id = 8; // แก้ไขข้อบกพร่อง/ข้อสังเกต
            $committee->save();
            $this->set_check_mail($auditors,$CertiCb);
        }elseif($auditors->degree == 4){
             $committee->step_id = 7; // ผ่านการตรวจสอบประเมิน
             $committee->save();
               //  Log
               $this->set_history($auditors);
               //การตรวจประเมิน   ผู้ประกอบการ +  ผก.
               $this->set_mail_past($auditors,$CertiCb);

        }else{
            $committee->step_id = 9; // ไม่ผ่านการตรวจสอบประเมิน
            $committee->save();
                 // สถานะ แต่งตั้งคณะกรรมการ
                $auditor = CertiCBAuditors::where('app_certi_cb_id',$CertiCb->id)
                                            ->whereIn('step_id',[9,10])
                                            ->whereNull('status_cancel')
                                            ->get();

                if(count($auditor) == count($CertiCb->CertiCBAuditorsManyBy)){
                    $report                   = new  CertiCBReview;  //ทบทวนฯ
                    $report->app_certi_cb_id  = $CertiCb->id;
                    $report->save();
                    $CertiCb->update(['review'=>1,'status'=>11]);  // ทบทวน
                 }
        }

     }
 }else{
            // รายงาน Scope
            if($request->file_scope  && $request->hasFile('file_scope')){
                $file_scope = [];
                foreach ($request->file_scope as $index => $item){
                        $data                   = new stdClass;
                        $data->file             = $this->storeFile($item,$CertiCb->app_no);
                        $data->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $file_scope[] = $data;
                }
            }
           // รายงาน สรุปรายงานการตรวจทุกครั้ง
            if($request->file_report  && $request->hasFile('file_report')){
                         $file_report = [];
                foreach ($request->file_report as $index => $item){
                         $data                   = new stdClass;
                         $data->file             = $this->storeFile($item,$CertiCb->app_no);
                         $data->file_client_name = HP::ConvertCertifyFileName($item->getClientOriginalName());
                        $file_report[] = $data;
                }
            }

        $auditors->degree = 4;
        $auditors->save();
        $committee = CertiCBAuditors::findOrFail($auditors->auditors_id);
        $committee->step_id = 7; // ผ่านการตรวจสอบประเมิน
        $committee->save();
        $tb = new CertiCBSaveAssessment;
       $assessment = CertiCBSaveAssessment::select('name','auditors_id', 'laboratory_name', 'report_date', 'bug_report', 'degree')
                     ->where('id',$id)
                     ->first();
       CertiCbHistory::create([
                                   'app_certi_cb_id'    =>  $auditors->app_certi_cb_id ?? null,
                                   'auditors_id'        =>  $assessment->auditors_id ?? null,
                                   'system'             =>  8,
                                   'table_name'         =>  $tb->getTable(),
                                   'ref_id'             =>  $auditors->id,
                                   'details_one'        =>  json_encode($assessment) ?? null,
                                   'details_two'        =>  null,
                                   'details_three'      =>  !empty($auditors->FileAttachAssessment1To->file) ? $auditors->FileAttachAssessment1To->file : null,
                                   'file_client_name'   =>  !empty($auditors->FileAttachAssessment1To->file_client_name) ? $auditors->FileAttachAssessment1To->file_client_name : null,
                                   'details_four'       =>  isset($file_scope) ?  json_encode($file_scope): null,
                                   'attachs'            =>  isset($file_report) ?  json_encode($file_report): null,
                                   'file'               =>  (count($auditors->FileAttachAssessment4Many) > 0) ? json_encode($auditors->FileAttachAssessment4Many) : null,
                                   'attachs_car'        =>  !empty($auditors->FileAttachAssessment5To->file) ? $auditors->FileAttachAssessment5To->file : null, // ปิด car
                                   'attach_client_name' =>  !empty($auditors->FileAttachAssessment5To->file_client_name) ? $auditors->FileAttachAssessment5To->file_client_name : null,
                                   'created_by'         =>  auth()->user()->runrecno
                            ]);

        // $auditors->update(['degree'=>6]);
  //การตรวจประเมิน   ผู้ประกอบการ +  ผก.
 $this->set_mail_past($auditors,$CertiCb);
    }


    if($request->previousUrl){
        return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
    }else{
        return redirect('certify/save_assessment-cb')->with('message', 'เรียบร้อยแล้ว!');
    }

} catch (\Exception $e) {
    return redirect('certify/save_assessment-cb/assessment/'.$auditors->id.'/edit')->with('message', 'เกิดข้อผิดพลาด!');
 }

}


        // สำหรับเพิ่มรูปไปที่ store
        public function storeFile($files, $app_no = 'files_cb', $name = null)
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
