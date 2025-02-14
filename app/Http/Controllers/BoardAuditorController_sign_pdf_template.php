<?php

namespace App\Http\Controllers;

use HP;
use App\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\Besurv\Signer;
use Illuminate\Http\Response;
use App\Mail\Lab\MailBoardAuditor;
use Illuminate\Support\Facades\DB;
use App\Models\Certify\BoardAuditor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Models\Bcertify\StatusAuditor;
use App\Models\Certify\Applicant\Cost;
use Illuminate\Support\Facades\Storage;
use App\Mail\Lab\MailBoardAuditorSigner;


use App\Models\Certify\BoardAuditorDate;
use App\Models\Bcertify\AuditorExpertise;
use App\Models\Certify\BoardAuditorGroup;
use App\Models\Certify\Applicant\CertiLab;
use App\Models\Certify\CertificateHistory;
use App\Models\Bcertify\AuditorInformation;
use App\Models\Certify\BoardAuditorHistory;
use App\Models\Certify\Applicant\Assessment;
use App\Models\Certify\Applicant\CostDetails;
use App\Models\Certify\Applicant\CheckExaminer;
use App\Models\Certify\BoardAuditorInformation;
use App\Models\Certify\Applicant\CostAssessment;
use App\Models\Certify\MessageRecordTransaction;
use App\Models\Certify\Applicant\AssessmentGroup;

use App\Models\Certify\Applicant\CostItemConFirm;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Models\Certify\Applicant\AssessmentExaminer;
use App\Models\Certify\Applicant\AssessmentGroupAuditor;

class BoardAuditorController extends Controller
{
    private $attach_path;//ที่เก็บไฟล์แนบ
    public function __construct()
    {
        $this->middleware('auth');
        $this->attach_path = 'files/applicants/check_files/';
    }
 

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        
        $model = str_slug('board-auditor','-');
        if(auth()->user()->can('view-'.$model)) {
            $keyword = $request->get('search');
            $filter = [];
            $filter['filter_state'] = $request->get('filter_state', '');
            $filter['filter_search'] = $request->get('filter_search', '');
            $filter['filter_start_date'] = $request->get('filter_start_date', '');
            $filter['filter_start_date'] = $request->get('filter_start_date', '');
            $filter['filter_end_date'] = $request->get('filter_end_date', '');
            $filter['filter_product_group'] = $request->get('filter_product_group', '');
            $filter['perPage'] = $request->get('perPage', 10);

            $Query = new BoardAuditor;
            $Query = $Query->select('board_auditors.*');
            if ($filter['filter_state']!='') {
                $Query = $Query->where('state', $filter['filter_state']);
            }

            if ($filter['filter_search']!='') {
                 $Query = $Query->where('certi_no','LIKE','%'.$filter['filter_search'].'%')->orwhere('no','LIKE','%'.$filter['filter_search'].'%');
            }

            if ($filter['filter_start_date'] != null && $filter['filter_end_date'] != null){
                $start = Carbon::createFromFormat("d/m/Y",$filter['filter_start_date'])->addYear(-543)->formatLocalized('%Y-%m-%d');
                $end = Carbon::createFromFormat("d/m/Y",$filter['filter_end_date'])->addYear(-543)->formatLocalized('%Y-%m-%d');
                $Query = $Query->whereBetween('check_date', [$start,$end]);

            } elseif ($filter['filter_start_date'] != null && $filter['filter_end_date'] == null){
                $start = Carbon::createFromFormat("d/m/Y",$filter['filter_start_date'])->addYear(-543)->formatLocalized('%Y-%m-%d');
                $Query = $Query->whereDate('check_date',$start);
            }

                  //เจ้าหน้าที่ LAB และไม่มีสิทธิ์ admin , ผอ , ผก , ลท.
                  if(in_array("28",auth()->user()->RoleListId) && auth()->user()->SetRolesAdminCertify() == "false" ){
                    $check = AssessmentExaminer::where('user_id',auth()->user()->runrecno)->pluck('app_certi_lab_id'); // เช็คเจ้าหน้าที่ IB
                    if(isset($check) && count($check) > 0  ) {
                        $Query = $Query->LeftJoin('app_certi_lab_assessments_examiner','app_certi_lab_assessments_examiner.app_certi_lab_id','=','board_auditors.app_certi_lab_id')
                                         ->where('user_id',auth()->user()->runrecno);  // LAB เจ้าหน้าที่ที่ได้มอบหมาย
                    }else{
                        $Query = $Query->whereIn('id',['']);  // ไม่ตรงกับเงื่อนไข
                    }
                }
            $app_id = $request->app;
            $app = $app_id ? CertiLab::find($app_id) : null;
            if ($app) {
//                $ids = collect();
//                $groups = $app->assessment->groups;
//                foreach($groups as $group) {
//                    $auditors = $group->auditors;
//
//                    foreach ($auditors as $auditor) {
//                        if (!$ids->has($auditor->id)) {
//                            $ids->put($auditor->auditor_id, $auditor);
//                        }
//                    }
//                }

                $boardAuditors = BoardAuditor::where('certi_no', $app->app_no)->sortable()->with('user_created')
                    ->with('user_updated')   ->orderby('id','desc')
                    ->paginate($filter['perPage']);
            } else {
                $boardAuditors = $Query->sortable()->with('user_created')
                    ->with('user_updated')   ->orderby('id','desc')
                    ->paginate($filter['perPage']);
            }
 

            return view('certify.auditor.index', compact('boardAuditors', 'filter', 'app'));
        }
        abort(403);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param CertiLab|null $app
     * @return Response
     */
    public function create(Request $request)
    {
        // dd($request->all());
        // dd($request->current_url, $request->current_route);
        $model = str_slug('board-auditor','-');
        if(auth()->user()->can('add-'.$model)) {

        $status_auditor = array();
        foreach (StatusAuditor::where('kind',1)->orderbyRaw('CONVERT(title USING tis620)')->get() as $sa) {
            $status_auditor[$sa->id] = $sa->title;
        }

        $app_certi_lab_id = !empty($request->app_certi_lab_id) ?  $request->app_certi_lab_id : null;

         $app_certi_lab = [];
 
         if(in_array("28",auth()->user()->RoleListId) && auth()->user()->SetRolesAdminCertify() == "false" ){
            $check = CheckExaminer::where('user_id',auth()->user()->runrecno)->pluck('app_certi_lab_id'); // เช็คเจ้าหน้าที่ LAB
            if(count($check) > 0 ){
                $app_certi_lab = CertiLab::whereIn('id',$check)
                                            ->whereIn('status', [7,12])
                                            ->orderby('id','desc')
                                            ->pluck('app_no', 'id');
             }
         }else{
                $app_certi_lab = CertiLab::whereIn('status', [7,12])
                                            ->orderby('id','desc')
                                            ->pluck('app_no', 'id');
          }

          $Query = CertiLab::select('app_certi_labs.*')->where('status','>=','1');
          $examiner = CheckExaminer::where('user_id',auth()->user()->runrecno)->pluck('app_certi_lab_id'); //เจ้าหน้าที่ รับผิดชอบ  สก.
          $User =   User::where('runrecno',auth()->user()->runrecno)->first();
          $select_users = array();
          if($User->IsGetIdRoles() == 'false'){  //ไม่ใช่ admin , ผอ , ลท
  
              if(!is_null($examiner) && count($examiner) > 0  && !in_array('22',auth()->user()->RoleListId)){
                  $Query = $Query->LeftJoin((new CheckExaminer)->getTable().' AS check_exminer', 'check_exminer.app_certi_lab_id','=','app_certi_labs.id')
                                  ->where('user_id',auth()->user()->runrecno);  //เจ้าหน้าที่ที่ได้มอบหมาย
              }else{
                  if(isset($User) && !is_null($User->reg_subdepart) && (in_array('11',$User->BasicRoleUser) || in_array('22',$User->BasicRoleUser))  ) {  //ผู้อำนวยการกอง ของ สก.
                      $Query = $Query->where('subgroup',$User->reg_subdepart);
                  }else{
                      $Query = $Query->whereIn('id',['']);  // ไม่ตรงกับเงื่อนไข
                  }
              }
  
              $select_users  = User::where('reg_subdepart',$User->reg_subdepart)  //มอบ เจ้าหน้าที่ รับผิดชอบ  สก.
                              ->whereNotIn('runrecno',[$User->runrecno])
                              ->select(DB::raw("CONCAT(reg_fname,' ',reg_lname) AS title"),'runrecno')
                              ->orderbyRaw('CONVERT(title USING tis620)')
                              ->pluck('title','runrecno');
  
           }else{
  
               $select_users  = User::select(DB::raw("CONCAT(reg_fname,' ',reg_lname) AS title"),'runrecno')
                              ->whereIn('reg_subdepart',[1804,1805,1806])
                              ->orderbyRaw('CONVERT(title USING tis620)')
                              ->pluck('title','runrecno');
           }

        //    $signers = Signer::orderbyRaw('CONVERT(name USING tis620)')->pluck('name','id','position');
           $signers = Signer::all();
           $selectedCertiLab = CertiLab::find($app_certi_lab_id);
        //    dd($selectedCertiLab);
        //    dd(User::select(DB::raw("CONCAT(reg_fname,' ',reg_lname) AS title"),'runrecno')
        //    ->whereIn('reg_subdepart',[1804,1805,1806])
        //    ->orderbyRaw('CONVERT(title USING tis620)')
        //    ->pluck('title','runrecno'));

        // dd(Signer::all());

            return view('certify/auditor/create', [
                                                        'status_auditor'    => $status_auditor,
                                                        'app_certi_lab'     => $app_certi_lab,
                                                        'app_certi_lab_id'  => $app_certi_lab_id,
                                                        'select_users'  => $select_users,
                                                        'signers'  => $signers,
                                                        'selectedCertiLab'  => $selectedCertiLab,
                                                        'view_url'  => $request->current_url,
                                                 ]);
        }   
        abort(403);
    }

    public function getAuditorFromStatus($id=null,$app_id=null) { // หา status
        $app = CertiLab::find($app_id);
 
         if (!$app) {
            return response()->json([
                'message' => 'Status Auditor not found.'
            ], 400);
        }
        if(!is_null($app->lab_type) && $app->lab_type == 3){
            $type = 4;
        }else{
            $type = 3;
        }
        $auditors = [];
        $name_th = [];
        $Auditor =  AuditorExpertise::where('type_of_assessment',$type) ->get();
        foreach($Auditor as $key => $item ) {
           $auditor_status =  explode(",",$item->auditor_status) ;
           if(in_array($id,$auditor_status) 
                && !is_null($item->auditor_id)  
                && !array_key_exists($item->auditor_id,$name_th) ){
                $data['id']             =  $item->auditor_id ?? '-';
                $data['name_th']        =  $item->auditor->NameThTitle ?? '-';
                $data['department']     =  $item->auditor->DepartmentTitle ?? '-';
                $data['position']       =  $item->auditor->position ?? '-';
                $data['branch']         =  $item->BranchTitle ?? '-';
                $auditors[]             = $data ;
                $name_th[$item->auditor_id] = $item->auditor_id;
           }
        }

        return response()->json([
            'success' => true,
            'auditors' => $auditors
        ]);
    }

    public function getAuditors($sa) { // หา auditor
        $auditors = array();
        foreach (AuditorExpertise::get() as $ae) {
            if (in_array($sa->id, $ae->status) && !in_array($ae->auditor->id, Arr::pluck($auditors, 'id'))) { 
                $auditor = $ae->auditor;
                $auditor->department;
                $auditor->branch =   $ae->InspectBranchTitle ?? '-'; // สาขา
                array_push($auditors, $auditor);
            }
        }
        return $auditors;
    }

    public function apiGetAuditor($ba) {
        $model = BoardAuditor::with('auditor_information.auditor')->find($ba);
        if ($model) {
            return response()->json([
                'ba' => $model
            ], 200);
        }
        return response()->json([
            'message' => 'Board Auditor not found.'
        ], 400);
    }

    public function DataCertiNo($id) {
        $app_no =  CertiLab::findOrFail($id);
        if(!is_null($app_no)){
            $cost = Cost::where('app_certi_lab_id',$app_no->id)->orderby('id','desc')->first();
            if(!is_null($cost)){
                $cost_item = $cost->items;
            }
        }
        $cost_details =  StatusAuditor::orderbyRaw('CONVERT(title USING tis620)')->pluck('title', 'id');
        return response()->json([
           'name'=> !empty($app_no->BelongsInformation->name) ? $app_no->BelongsInformation->name : ' ' ,
           'id'=> !empty($app_no->id) ? $app_no->id : ' ' ,
           'cost_item' => isset($cost_item) ? $cost_item : '-',
           'cost_details' => $cost_details
        ]);
    }
    public function DeleteFile($id) {
        $aoard =  BoardAuditor::findOrFail($id);
        $aoard->update(['file' => null]);
        return redirect('certify/auditor/'.$id.'/edit')->with('flash_message', 'Delete Complete!');

    }
    public function DeleteAttach($id) {
        $aoard =  BoardAuditor::findOrFail($id);
        $aoard->update(['attach' => null]);
        return redirect('certify/auditor/'.$id.'/edit')->with('flash_message', 'Delete Complete!');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
     
        // แปลง JSON ข้อมูลที่ได้รับจากฟอร์มกลับเป็น array

        // dd($request->all());
        // // ตรวจสอบและทำงานกับ $signaturePositions
        // if ($signaturePositions) {
        //     foreach ($signaturePositions as $signatureId => $positionData) {
        //         // ตัวอย่างการเข้าถึงข้อมูลแต่ละคีย์
        //         $page = $positionData['page'] ?? null;
        //         $x = $positionData['x'] ?? null;
        //         $y = $positionData['y'] ?? null;

        //          // แสดงผลลัพธ์
        //         echo "Signature ID: $signatureId<br>";
        //         echo "Page: $page<br>";
        //         echo "X: $x<br>";
        //         echo "Y: $y<br>";
        //         echo "<hr>";

        //     }
        // }

        // // ตรวจสอบและทำงานกับ $signatures
        // if ($signatures) {
        //     foreach ($signatures as $signature) {
        //         // ตัวอย่างการเข้าถึงข้อมูลแต่ละฟิลด์
        //         $id = $signature['id'] ?? null;
        //         $enable = $signature['enable'] ?? null;
        //         $showName = $signature['show_name'] ?? null;
        //         $showPosition = $signature['show_position'] ?? null;
        //         $signerName = $signature['signer_name'] ?? null;
        //         $signerId = $signature['signer_id'] ?? null;
        //         $signerPosition = $signature['signer_position'] ?? null;
        //         $lineSpace = $signature['line_space'] ?? null;

        //         // แสดงผลลัพธ์
        //         echo "Signature ID: $id<br>";
        //         echo "Enable: " . ($enable ? 'true' : 'false') . "<br>";
        //         echo "Show Name: " . ($showName ? 'true' : 'false') . "<br>";
        //         echo "Show Position: " . ($showPosition ? 'true' : 'false') . "<br>";
        //         echo "Signer Name: $signerName<br>";
        //         echo "Signer ID: $signerId<br>";
        //         echo "Signer Position: $signerPosition<br>";
        //         echo "Line Space: $lineSpace<br>";
        //         echo "<hr>";

        //     }
        // }
       
        // $certi_lab = CertiLab::where('id',$request->app_certi_lab_id)->orderby('id','desc')->first();
        // $dataMail = ['1804'=> 'lab1@tisi.mail.go.th','1805'=> 'lab2@tisi.mail.go.th','1806'=> 'lab3@tisi.mail.go.th'];
        // $EMail =  array_key_exists($certi_lab->subgroup,$dataMail)  ? $dataMail[$certi_lab->subgroup] :'admin@admin.com';

        // if(!empty($certi_lab->DataEmailDirectorLABCC)){
        //     $mail_cc = $certi_lab->DataEmailDirectorLABCC;
        //     array_push($mail_cc, auth()->user()->reg_email) ;
        // }

        // dd($dataMail,$EMail,$certi_lab->DataEmailDirectorLABCC,$certi_lab->DataEmailCertifyCenter,$certi_lab->DataEmailDirectorLABReply);

        // dd($request->view_url);   


        // $messageRecordTransactionIds = MessageRecordTransaction::where('board_auditor_id', $board->id)->pluck('signer_id')->toArray();
        // $sigerIds = Signer::whereIn('id', $messageRecordTransactionIds)->pluck('user_register_id')->toArray();
        // $signerEmails = User::whereIn('id', $sigerIds)->pluck('reg_email')->unique()->toArray();

        // if ($request->hasFile('message_record_file')) {
        //     $request->other_attach = $request->file('message_record_file')->store('attachments');
        // }
        // $otherAttachment = $request->message_record_file;
        $model = str_slug('board-auditor','-');
        if(auth()->user()->can('add-'.$model)) {
            $this->validate($request, [
                'app_certi_lab_id' => 'required|max:255',
                'no' => 'required|max:255',
                // 'other_attach' => 'required|file',
                'group' => 'required|array',
                'group.*.status' => 'required',
                'group.*.users' => 'required',
            ]);
            // dd($request->all());   
   try {

                $app = CertiLab::where('id',$request->app_certi_lab_id)->orderby('id','desc')->first();
 
                $input = [
                            'app_certi_lab_id'   => $request->app_certi_lab_id,
                            'certi_no'           => $app->app_no ?? null,
                            'no'                 => $request->no,
                            'file'               => (isset($request->message_record_file) && $request->hasFile('message_record_file'))  ? $this->storeFile($request->file('message_record_file'),$app->app_no) : null,
                            'file_client_name'   => (isset($request->message_record_file) && $request->hasFile('message_record_file'))  ?  HP::ConvertCertifyFileName($request->message_record_file->getClientOriginalName()) : null,
                            'attach'             => (isset($request->attach) && $request->hasFile('attach'))  ? $this->storeFile($request->file('attach'),$app->app_no)  : null,
                            'attach_client_name' => (isset($request->attach) && $request->hasFile('attach'))  ?  HP::ConvertCertifyFileName($request->attach->getClientOriginalName())  : null,
                            'state'              => 1,
                            'vehicle'            => isset($request->vehicle) ? 1 : null,
                            'created_by'         => auth()->user()->runrecno,
                            'step_id'            =>  2 , //ขอความเห็นแต่งคณะผู้ตรวจประเมิน  
                            'auditor'            => !empty($request->auditor) ? $request->auditor : null
                         ];
                if ($baId = $this->savingBoard($input)) {
                     $board  =  BoardAuditor::findOrFail($baId);
                    //  dd($board);
                   
                    $this->saveSignature($request,$baId,$app);
                        if(!is_null($baId)){
                                $ca = Assessment::where('app_certi_lab_id',$app->id)->where('auditor_id',$baId)->first();
                                if(is_null($ca)){
                                    $ca = new Assessment;
                                }
                                $ca->app_certi_lab_id =  $app->id;
                                $ca->auditor_id       =  $baId;
                                $ca->save();   

                                $group = AssessmentGroup::where('app_certi_assessment_id',$ca->id)->where('app_certi_lab_id',$app->id)->first();
                                if(is_null($group)){
                                    $group = new AssessmentGroup;
                                }
                                $group->app_certi_assessment_id = $ca->id;
                                $group->app_certi_lab_id        = $app->id ?? null;
                                $group->checker_id              = auth()->user()->runrecno;
                                $group->save();
 
                             
                                $ga = AssessmentGroupAuditor::where('app_certi_assessment_group_id',$group->id)->where('app_certi_lab_id',$app->id)->first();
                                if(is_null($ga)){
                                    $ga = new AssessmentGroupAuditor;
                                }
                                $ga->app_certi_assessment_group_id  = $group->id;
                                $ga->app_certi_lab_id               = $app->id ?? null;
                                $ga->auditor_id                     = $baId;
                                $ga->save();
                         
                            //  วันที่ตรวจประเมิน
                            $this->DataBoardAuditorDate($baId,$request);
                        }
    
                     $requestData = $request->all();
                     $this->storeItems($requestData, $board);
    
                    if ($this->storeGroup($baId, $request->group)) {
    
    
                        if(!is_null($app)){
                            if(isset($request->vehicle)){
                                $config = HP::getConfig();
                                $url  =   !empty($config->url_acc) ? $config->url_acc : url('');
                                // $app->update(['status'=>13]); // ขอความเห็นแต่งคณะผู้ตรวจประเมิน
                                //    $app->update(['status'=>10]); //  อยู่ระหว่างดำเนินการ
                                     $app->update(['status'=>7]); //  อยู่ระหว่างดำเนินการ
                                  //Log
                                  $this->CertificateHistory($baId,$request->group);
                                  //E-mail
                                //   $this->set_mail($board,$board->CertiLabs);
    
                            }else{
                                //  $app->update(['status'=>12]); // อยู่ระหว่างแต่งตั้งคณะผู้ตรวจประเมิน
                                //   $app->update(['status'=>10]); //  อยู่ระหว่างดำเนินการ
                                   $app->update(['status'=>7]); //  อยู่ระหว่างดำเนินการ
                            }
                        }
    
                        if($request->previousUrl){
                            return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
                        }else{
                            return redirect(route('certify.auditor.index', ['app' => $app ? $app->id : '']))->with('flash_message', 'แก้ไขเรียบร้อยแล้ว');
                        }
                    }
                    
                }
                return back()->withInput();
            } catch (\Exception $e) {
                return redirect(route('certify.auditor.index'))->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
            }
        }
        abort(403);
    }

    public function saveSignature($request,$baId,$app)
    {
        $signaturePositions = json_decode($request->input('signaturePositionsJson'), true);
        $signatures = json_decode($request->input('signaturesJson'), true);
        // $viewUrl = $request->view_url;
        $viewUrl = url('/certify/auditor/'.$baId.'/edit/'.$request->app_certi_lab_id);
        // $viewUrl = "view_url";
        // dd($viewUrl);
        // ตรวจสอบและทำงานกับ $signaturePositions
        if ($signaturePositions) {
            foreach ($signaturePositions as $signatureId => $positionData) {
                // เข้าถึงข้อมูลแต่ละคีย์ใน $positionData
                $page = $positionData['page'] ?? null;
                $x = $positionData['x'] ?? null;
                $y = $positionData['y'] ?? null;

                // ใช้ firstWhere เพื่อหาตัวที่ตรงกับ $signatureId ใน $signatures
                $matchingSignature = collect($signatures)->firstWhere('id', $signatureId);

                // แสดงข้อมูลตำแหน่งของ signature
                // echo "Signature ID: $signatureId<br>";
                // echo "Page: $page<br>";
                // echo "X: $x<br>";
                // echo "Y: $y<br>";

                // ตรวจสอบว่า $matchingSignature มีค่าและแสดงข้อมูลเพิ่มเติม
                if ($matchingSignature) {
                    $enable = $matchingSignature['enable'] ?? null;
                    $showName = $matchingSignature['show_name'] ?? null;
                    $showPosition = $matchingSignature['show_position'] ?? null;
                    $signerName = $matchingSignature['signer_name'] ?? null;
                    $signerId = $matchingSignature['signer_id'] ?? null;
                    $signerPosition = $matchingSignature['signer_position'] ?? null;
                    $lineSpace = $matchingSignature['line_space'] ?? null;

                    // echo "Enable: " . ($enable ? 'true' : 'false') . "<br>";
                    // echo "Show Name: " . ($showName ? 'true' : 'false') . "<br>";
                    // echo "Show Position: " . ($showPosition ? 'true' : 'false') . "<br>";
                    // echo "Signer Name: $signerName<br>";
                    // echo "Signer ID: $signerId<br>";
                    // echo "Signer Position: $signerPosition<br>";
                    // echo "Line Space: $lineSpace<br>";
                    MessageRecordTransaction::create([
                        'board_auditor_id' => $baId,
                        'signer_id' => $signerId,
                        'certificate_type' => 2,
                        'app_id' => $app->app_no,
                        'view_url' => $viewUrl,
                        'signature_id' => $signatureId,
                        'is_enable' => $enable,
                        'show_name' => $showName,
                        'show_position' => $showPosition,
                        'signer_name' => $signerName,
                        'signer_position' => $signerPosition,
                        'signer_order' => preg_replace('/[^0-9]/', '', $signatureId),
                        'file_path' => (isset($request->message_record_file) && $request->hasFile('message_record_file'))  ? $this->storeFile($request->file('message_record_file'),$app->app_no) : null,
                        'page_no' => $page,
                        'pos_x' => $x,
                        'pos_y' => $y,
                        'linesapce' => $lineSpace,
                        'approval' => 0,
                    ]);
                } else {
                    echo "No matching signature found for ID: $signatureId<br>";
                }

                echo "<hr>";
            }
        }
        $board  =  BoardAuditor::findOrFail($baId);
        // $this->set_mail($board,$board->CertiLabs);
        $this->sendMailToSigner($board,$board->CertiLabs); 
    }

    public function storeGroup($baId, $groupInput) {
        $ba = BoardAuditor::findOrFail($baId);
        foreach ($ba->groups as $group) {
            $group->auditors()->delete();
            $group->delete();
        }

        foreach ($groupInput as $group) {
            $sa = StatusAuditor::find($group['status']);
            if ($sa) {
                $input = [
                    'board_auditor_id' => $baId,
                    'status_auditor_id' => $sa->id,
                ];
                if ($groupId = $this->savingGroup($input)) {
                    if (!$this->storeAuditor($groupId, $group['users'])) {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }
        return true;
    }

    public function storeAuditor($groupId, $strAuditorIds) {
        $auditorIds = explode(";", $strAuditorIds);
        foreach ($auditorIds as $auditorId) {
            $ai = AuditorInformation::find($auditorId);
            if ($ai) {
                $input = [
                    'group_id' => $groupId,
                    'auditor_id' => $auditorId
                ];
                if (!$this->savingAuditor($input)) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    public function savingBoard($input)
    {
        $input['created_at'] = $input['updated_at'] = now();
        $id = BoardAuditor::insertGetId($input);
        if ($id) {
            return $id;
        }
        return false;
    }

    public function savingGroup($input) {
        $input['created_at'] = $input['updated_at'] = now();
        $id = BoardAuditorGroup::insertGetId($input);
        if ($id) {
            return $id;
        }
        return false;
    }

    public function savingAuditor($input) {
        $input['created_at'] = $input['updated_at'] = now();
        $id = BoardAuditorInformation::insertGetId($input);
        if ($id) {
            return $id;
        }
        return false;
    }
    public function storeFile($files, $app_no = 'files_lab',$name =null)
    {

        $no  = str_replace("RQ-","",$app_no);
        $no  = str_replace("-","_",$no); 
        if ($files) {
            $attach_path  =  $this->attach_path.$no;
            $file_extension = $files->getClientOriginalExtension();
            $fileClientOriginal   =  HP::ConvertCertifyFileName($files->getClientOriginalName());
            $filename = pathinfo($fileClientOriginal, PATHINFO_FILENAME);
            $fullFileName =   str_random(10).'-date_time'.date('Ymd_hms') . '.' . $files->getClientOriginalExtension();

            $storagePath = Storage::putFileAs($attach_path, $files,  str_replace(" ","",$fullFileName) );
            $storageName = basename($storagePath); // Extract the filename
            return  $no.'/'.$storageName;
        }else{
            return null;
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(BoardAuditor $ba)
    {

        $model = str_slug('board-auditor','-');
        if(auth()->user()->can('view-'.$model)) {
            return view('certify/auditor/show', compact('ba'));
        }
        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param BoardAuditor $ba
     * @param CertiLab|null $app
     * @return Response
     */
    public function edit(BoardAuditor $ba, CertiLab $app = null)
    {
        
        $messageRecordTransaction = MessageRecordTransaction::where('board_auditor_id',$ba->id)->first();
        $messageRecordTransactions = MessageRecordTransaction::where('board_auditor_id',$ba->id)->get();
        // dd($messageRecordTransactions);
        $model = str_slug('board-auditor','-');
        if(auth()->user()->can('edit-'.$model)) {
            $previousUrl = app('url')->previous();
            $status_auditor = array();
            foreach (StatusAuditor::orderbyRaw('CONVERT(title USING tis620)')->get() as $sa) {
                $status_auditor[$sa->id] = $sa->title;
            }
            $confirm = CostItemConFirm::select('desc','amount_date','amount')
                                        ->where('board_auditors_id',$ba->id)
                                        ->get();
            if($confirm->count() <= 0){
                $confirm = [new CostItemConFirm];
            }
    
            return view('certify/auditor/edit', compact('ba', 'status_auditor', 'app','previousUrl','confirm','messageRecordTransaction','messageRecordTransactions'));
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, BoardAuditor $ba)
    {
        
        $model = str_slug('board-auditor','-');
        if(auth()->user()->can('edit-'.$model)) {
            $this->validate($request, [
                'certi_no' => 'required|max:255',
                'no' => 'required|max:255',
                // 'other_attach' => 'nullable|file',
                'group' => 'required|array',
                'group.*.status' => 'required',
                'group.*.users' => 'required',
            ]);

            // try {
                $app = CertiLab::where('app_no',$ba->certi_no)->first();
                $input = [
                    'certi_no'           => $request->certi_no,
                    'no'                 => $request->no,
                    'file'               => (isset($request->other_attach) && $request->hasFile('other_attach'))  ? $this->storeFile($request->file('other_attach'),$app->app_no) :  @$ba->file,
                    'file_client_name'   => (isset($request->other_attach) && $request->hasFile('other_attach'))  ? HP::ConvertCertifyFileName($request->other_attach->getClientOriginalName())  : @$ba->file_client_name,
                    'attach'             => (isset($request->attach) && $request->hasFile('attach'))  ? $this->storeFile($request->file('attach'),$app->app_no)  : @$ba->attach,
                    'attach_client_name' => (isset($request->attach) && $request->hasFile('attach'))  ?  HP::ConvertCertifyFileName($request->attach->getClientOriginalName())  : @$ba->attach_client_name,
                    'updated_by'         => auth()->user()->runrecno,
                    'state'              => 1,
                    'vehicle'            => isset($request->vehicle) ? 1 : null,
                    'status'             => null,
                    'step_id'            =>  2 , //ขอความเห็นแต่งคณะผู้ตรวจประเมิน  
                    'auditor'            => !empty($request->auditor) ? $request->auditor : null
                ];
    
                if ($ba->update($input)) {
    
                        $requestData = $request->all();

                      
                        $this->storeItems($requestData, $ba);
    
                      //  วันที่ตรวจประเมิน
                      $this->DataBoardAuditorDate($ba->id,$request);

                    if ($this->storeGroup($ba->id, $request->group)) {
    
                        if(!is_null($app)){
                            if(isset($request->vehicle)){
                                $config = HP::getConfig();
                                $url  =   !empty($config->url_acc) ? $config->url_acc : url('');
                                //    $app->update(['status'=>13]);
                                // $app->update(['status'=>10]); //  อยู่ระหว่างดำเนินการ
                                  $app->update(['status'=>7]); //  อยู่ระหว่างดำเนินการ
                                  //Log
                                 $this->CertificateHistory($ba->id,$request->group);
                                //E-mail
                                $this->set_mail($ba,$ba->CertiLabs);
    
                            }else{
                                // $app->update(['status'=>12]); // อยู่ระหว่างแต่งตั้งคณะผู้ตรวจประเมิน
                                // $app->update(['status'=>10]); //  อยู่ระหว่างดำเนินการ
                                $app->update(['status'=>7]); //  อยู่ระหว่างดำเนินการ
                            }
                        }

                        if($request->previousUrl){
                            return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
                        }else{
                            return redirect(route('certify.auditor.index', ['app' => $app ? $app->id : '']))->with('flash_message', 'แก้ไขเรียบร้อยแล้ว');
                        }
    
                   
                    }
                }
                return back()->withInput();
            // } catch (\Exception $e) {
            //     return redirect(route('certify.auditor.index'))->with('flash_message', 'เกิดข้อผิดพลาดในการบันทึก');
            // }


        }
        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BoardAuditor $ba
     * @param CertiLab|null $app
     * @return Response
     */
    public function destroy(BoardAuditor $ba, CertiLab $app = null)
    {
        $model = str_slug('board-auditor','-');
        if(auth()->user()->can('delete-'.$model)) {

            $this->deleting($ba);
            AssessmentGroup::where('app_certi_assessment_id',$ba->id)->delete();
            AssessmentGroupAuditor::where('auditor_id',$ba->id)->delete();
            BoardAuditorDate::where('board_auditors_id',$ba->id)->delete();
            return redirect(route('certify.auditor.index', ['app' => $app ? $app->id : '']))->with('flash_message', 'ลบข้อมูลเรียบร้อยแล้ว!');
        }
        abort(403);
    }
    
    public function update_delete(Request $request, $id)
    {
        $model = str_slug('board-auditor','-');
        if(auth()->user()->can('delete-'.$model)) {
            
            try {
                $requestData = $request->all();
                $requestData['step_id']          =   12 ;
                $requestData['reason_cancel']    =  $request->reason_cancel ;
                $requestData['status_cancel']    =   1 ;
                $requestData['created_cancel']   =  auth()->user()->runrecno;
                $requestData['date_cancel']     =    date('Y-m-d H:i:s') ;
                $auditors = BoardAuditor::findOrFail($id);
                $auditors->update($requestData);

                $response = [];
                $response['reason_cancel']  =  $auditors->reason_cancel ?? null;
                $response['status_cancel']  =  $auditors->status_cancel ?? null;
                $response['created_cancel'] =  $auditors->created_cancel ?? null;    
                $response['date_cancel']    =  $auditors->date_cancel ?? null;
                $response['step_id']        =  $auditors->step_id ?? null;

                CertificateHistory::where('ref_id',$auditors->id)->where('table_name',(new BoardAuditor)->getTable())->update(['details_auditors_cancel' => json_encode($response) ]);

                $certi_lab = CertiLab::where('id',$auditors->app_certi_lab_id)->first();
    
                if(!is_null($certi_lab)){
                    $certi_lab->status = 7; // 
                    $certi_lab->save();

                    $cost =  CostAssessment::where('app_certi_lab_id',$certi_lab->id)->orderby('id','desc')->first();
                    if(!is_null($cost)){ // update log payin
                      // / update   payin
                      CostAssessment::where('app_certi_lab_id',$certi_lab->id)->update(['status_confirmed' =>3,'amount'=>'0.00']);
                      CertificateHistory::where('ref_id',$cost->id)->where('table_name',(new CostAssessment)->getTable())->update(['details_auditors_cancel' => json_encode($response) ]);
                    }

                }else{
                    return redirect(route('certify.auditor.index'))->with('flash_message', 'เกิดข้อผิดพลาดในการบันทึก');
                }
                return redirect(route('certify.auditor.index'))->with('flash_message', 'update ยกเลิกแต่งตั้งคณะผู้ตรวจประเมินเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                return redirect(route('certify.auditor.index'))->with('flash_message', 'เกิดข้อผิดพลาดในการบันทึก');
            }
        }
        abort(403);
    }

    
    public function deleting(BoardAuditor $ba) {
        try {
            foreach ($ba->groups as $group) {
                $group->auditors()->delete();
                $group->delete();
            }

            $destinationPath = storage_path('/files/board_auditor_files/');
            $path = $destinationPath . $ba->file;
            if (File::exists($path)) {
                File::delete($path);
            }

            $ba->delete();
            return true;
        } catch (Exception $x) {
            return false;
        }
    }

    /**
     * @param Request $request
     * @param CertiLab|null $app
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroyMultiple(Request $request, CertiLab $app = null)
    {
        $model = str_slug('board_auditor','-');
        if(auth()->user()->can('delete-'.$model)) {

            foreach ($request->cb as $baId) {
                $ba = BoardAuditor::findOrFail($baId);
                $this->deleting($ba);

            }

            return redirect(route('certify.auditor.index', ['app' => $app ? $app->id : '']))->with('flash_message', 'ลบข้อมูลเรียบร้อยแล้ว!');
        }
        abort(403);
    }
    public function DataBoardAuditorDate($baId, $request) {
        BoardAuditorDate::where('board_auditors_id',$baId)->delete();
        /* วันที่ตรวจประเมิน */
        foreach($request->start_date as $key => $itme) {
            $input = [];
            $input['board_auditors_id'] = $baId;
            $input['start_date'] = HP::convertDate( $itme ,true) ?? null;
            $input['end_date']   = HP::convertDate( $request->end_date[$key]  ,true)?? null;
            BoardAuditorDate::create($input);
        }

    }

        public function   CertificateHistory($baId,$group) {
            $ao = new BoardAuditor;
            $ba = BoardAuditor::findOrFail($baId);

            $Date = BoardAuditorDate::select('start_date','end_date')
                                ->where('board_auditors_id',$baId)
                                ->get()->toArray();
            $confirm = CostItemConFirm::select('board_auditors_id','desc','amount_date','amount')
                                ->where('board_auditors_id',$baId)
                                ->get()->toArray();
            CertificateHistory::create([
                                        'app_no'=> $ba->certi_no ?? null,
                                        'system'=>2,
                                        'table_name'=> $ao->getTable(),
                                        'ref_id'=> $baId,
                                        'details'=> $ba->no ?? null,
                                        'details_one'=> $ba->auditor ?? null,
                                        'details_table'=>  json_encode($group) ?? null,
                                        'details_date'=>   json_encode($Date) ?? null,
                                        'details_cost_confirm' =>  json_encode($confirm) ?? null,
                                        'attachs'=> $ba->attach ?? null,
                                        'attach_client_name'=> $ba->attach_client_name ?? null,
                                        'file'=> $ba->file ?? null,
                                        'file_client_name'=> $ba->file_client_name ?? null,
                                        'created_by' =>  auth()->user()->runrecno
                                      ]);
        }

        public function storeItems($items, $board) {
            try {
                CostItemConFirm::where('board_auditors_id',$board->id)->delete();
                $detail = (array)@$items['detail'];
                foreach($detail['desc'] as $key => $data ) {
                    $item = new CostItemConFirm;
                    $item->app_certi_lab_id = $board->app_certi_lab_id ?? null;
                    $item->board_auditors_id = $board->id;
                    $item->desc = $data ?? null;
                    $item->amount_date = $detail['nod'][$key] ?? 0;
                    $item->amount =  !empty(str_replace(",","", $detail['cost'][$key]))?str_replace(",","",$detail['cost'][$key]):null;
                    $item->save();
                }
            } catch (Exception $x) {
                throw $x;
            }
        }
        public function set_mail($auditors,$certi_lab) 
        {
 
            if(!is_null($certi_lab->email)){

                $config = HP::getConfig();
                $url  =   !empty($config->url_acc) ? $config->url_acc : url('');
                $dataMail = ['1804'=> 'lab1@tisi.mail.go.th','1805'=> 'lab2@tisi.mail.go.th','1806'=> 'lab3@tisi.mail.go.th'];
                $EMail =  array_key_exists($certi_lab->subgroup,$dataMail)  ? $dataMail[$certi_lab->subgroup] :'admin@admin.com';

                if(!empty($certi_lab->DataEmailDirectorLABCC)){
                    $mail_cc = $certi_lab->DataEmailDirectorLABCC;
                    array_push($mail_cc, auth()->user()->reg_email) ;
                }
    
                $data_app = [
                                'email'=>  auth()->user()->email ?? 'admin@admin.com',
                                'auditors' => $auditors,
                                'certi_lab'=> $certi_lab,
                                'url' => $url.'certify/applicant/auditor/'.$certi_lab->token,
                                'email'=>  !empty($certi_lab->DataEmailCertifyCenter) ? $certi_lab->DataEmailCertifyCenter : $EMail,
                                'email_cc'=>  !empty($mail_cc) ? $mail_cc :  $EMail,
                                'email_reply' => !empty($certi_lab->DataEmailDirectorLABReply) ? $certi_lab->DataEmailDirectorLABReply :  $EMail
                            ];
            
                $log_email =  HP::getInsertCertifyLogEmail( $certi_lab->app_no,
                                                            $certi_lab->id,
                                                            (new CertiLab)->getTable(),
                                                            $auditors->id,
                                                            (new BoardAuditor)->getTable(),
                                                            1,
                                                            'การแต่งตั้งคณะผู้ตรวจประเมิน',
                                                            view('mail.Lab.mail_board_auditor', $data_app),
                                                            $certi_lab->created_by,
                                                            $certi_lab->agent_id,
                                                            auth()->user()->getKey(),
                                                            !empty($certi_lab->DataEmailCertifyCenter) ?  implode(',',(array)$certi_lab->DataEmailCertifyCenter)  : $EMail,
                                                            $certi_lab->email,
                                                            !empty($mail_cc) ? implode(',',(array)$mail_cc)   :  $EMail,
                                                            !empty($certi_lab->DataEmailDirectorLABReply) ?implode(',',(array)$certi_lab->DataEmailDirectorLABReply)   :  $EMail,
                                                            null
                                                            );
        
                 $html = new  MailBoardAuditor($data_app);
                  $mail = Mail::to($certi_lab->email)->send($html);
    
                  if(is_null($mail) && !empty($log_email)){
                       HP::getUpdateCertifyLogEmail($log_email->id);
                  }
 
            }
        }

        public function sendMailToSigner($board,$certi_lab) 
        {
            if(!is_null($certi_lab->email)){

                $config = HP::getConfig();
                $url  =   !empty($config->url_center) ? $config->url_center : url('');
                $dataMail = ['1804'=> 'lab1@tisi.mail.go.th','1805'=> 'lab2@tisi.mail.go.th','1806'=> 'lab3@tisi.mail.go.th'];
                $EMail =  array_key_exists($certi_lab->subgroup,$dataMail)  ? $dataMail[$certi_lab->subgroup] :'admin@admin.com';

                if(!empty($certi_lab->DataEmailDirectorLABCC)){
                    $mail_cc = $certi_lab->DataEmailDirectorLABCC;
                    array_push($mail_cc, auth()->user()->reg_email) ;
                }
    
                $data_app = [
                                'email'=>  auth()->user()->email ?? 'admin@admin.com',
                                'auditors' => $board,
                                'certi_lab'=> $certi_lab,
                                'url' => $url.'certify/auditor-assignment/',
                                'email'=>  !empty($certi_lab->DataEmailCertifyCenter) ? $certi_lab->DataEmailCertifyCenter : $EMail,
                                'email_cc'=>  !empty($mail_cc) ? $mail_cc :  $EMail,
                                'email_reply' => !empty($certi_lab->DataEmailDirectorLABReply) ? $certi_lab->DataEmailDirectorLABReply :  $EMail
                            ];
            
                $log_email =  HP::getInsertCertifyLogEmail( $certi_lab->app_no,
                                                            $certi_lab->id,
                                                            (new CertiLab)->getTable(),
                                                            $board->id,
                                                            (new BoardAuditor)->getTable(),
                                                            1,
                                                            'ลงนามแต่งตั้งบันทึกข้อความ การแต่งตั้งคณะผู้ตรวจประเมิน',
                                                            view('mail.Lab.mail_board_auditor_signer', $data_app),
                                                            $certi_lab->created_by,
                                                            $certi_lab->agent_id,
                                                            auth()->user()->getKey(),
                                                            !empty($certi_lab->DataEmailCertifyCenter) ?  implode(',',(array)$certi_lab->DataEmailCertifyCenter)  : $EMail,
                                                            $certi_lab->email,
                                                            !empty($mail_cc) ? implode(',',(array)$mail_cc)   :  $EMail,
                                                            !empty($certi_lab->DataEmailDirectorLABReply) ?implode(',',(array)$certi_lab->DataEmailDirectorLABReply)   :  $EMail,
                                                            null
                                                            );
                // $signerEmails = ['signer_one@gmail.com', 'signer_two@gmail.com'];
                // $messageRecordTransactionIds = MessageRecordTransaction::where('board_auditor_id', $board->id)->pluck('signer_id')->toArray();
                // $sigerIds = Signer::whereIn('id', $messageRecordTransactionIds)->pluck('user_register_id')->toArray();
                // $signerEmails = User::whereIn('id', $sigerIds)->pluck('reg_email')->unique()->toArray();


                $signerEmails = $board->messageRecordTransactions()
                ->with('signer.user')
                ->get()
                ->pluck('signer.user.reg_email')
                ->filter() // กรองค่า null ออก
                ->unique()
                ->toArray();

                $html = new  MailBoardAuditorSigner($data_app);
                $mail = Mail::to($signerEmails)->send($html);
                // $mail = Mail::to($certi_lab->email)->send($html);
    
                if(is_null($mail) && !empty($log_email)){
                    HP::getUpdateCertifyLogEmail($log_email->id);
                }
 
            }
        }

    //     public function BoardAuditorHistory($baId,$group) {
    //         $Date = BoardAuditorDate::select('start_date','end_date')->where('board_auditors_id',$baId)->get()->toArray();
    //         $Board = BoardAuditor::findOrFail($baId);
    //         if(count($Date) > 0 && !is_null($Board)){
    //             BoardAuditorHistory::create(['board_auditor_id' => $baId,
    //                                         'no' => $Board->no  ?? null,
    //                                         'details_date' => json_encode($Date) ?? null,
    //                                         'file' => $Board->file ?? null,
    //                                         'attach' => $Board->attach ?? null,
    //                                         'groups' => json_encode($group) ?? null,
    //                                       ]);

    //         }
    //   }
}
