<?php

namespace App\Http\Controllers\Certify;

use HP;
use DB; 

use HP_DGA;
use QrCode;
use App\User;
use Storage; 
use App\Http\Requests;

use App\CertificateExport;
use Illuminate\Http\Request;
use  App\Models\Besurv\Signer;
use Yajra\Datatables\Datatables;
use App\Models\Basic\SubDepartment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

use App\Models\Certify\SendCertificates;
use App\Models\Certify\Applicant\CertiLab;
use App\Models\Certify\SignCertificateOtp;

use App\Models\Certify\ApplicantCB\CertiCb;
use App\Models\Certify\ApplicantIB\CertiIb;
use App\Models\Certify\SendCertificateLists;
use App\Models\Certify\SendCertificateHistory;
use App\Services\CreateLabAssessmentReportPdf;
use App\Models\Certify\SignCertificateConfirms;
use App\Models\Certify\MessageRecordTransaction;
use App\Models\Certify\ApplicantCB\CertiCBExport;
use App\Models\Certify\ApplicantIB\CertiIBExport;
use App\Models\Certify\SignAssessmentReportTransaction;

class SignAssessmentReportController extends Controller
{
    private $attach_path;//ที่เก็บไฟล์แนบ

    public function __construct()
    {
        $this->middleware('auth');

        $this->attach_path = 'files/sendcertificatelists';
    }

    public function index(Request $request)
    {
        $model = str_slug('assessment_report_assignment','-');
        if(auth()->user()->can('view-'.$model)) {
            return view('certify.assessment-report-assignment.index');
        }
        abort(403);

    }

    public function dataList(Request $request)
    {
        
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'ผู้ใช้ไม่ได้เข้าสู่ระบบ'], 401);
        }

        $userId = $user->runrecno;
        // ดึงข้อมูล signer โดยใช้ user_register_id
        $signer = Signer::where('user_register_id', $userId)->first();

        // dd($signer);

        // ตรวจสอบว่าพบข้อมูลหรือไม่
        if ($signer) {
            // dd(MessageRecordTransaction::where('signer_id',$signer->id)->get());
            $filter_approval = $request->input('filter_state');
            $filter_certificate_type = $request->input('filter_certificate_type');
        
            $query = SignAssessmentReportTransaction::query();
            $query->where('signer_id',$signer->id);
        
            if ($filter_approval) {
                $query->where('approval', $filter_approval);
            }else{
                $query->where('approval', 0);
            }
        
            if ($filter_certificate_type !== null) {
                
                $query->where('certificate_type', $filter_certificate_type);
            }
        
            $data = $query->get();
            $data = $data->map(function($item, $index) {
                $item->DT_Row_Index = $index + 1;

                // แปลง certificate_type เป็นข้อความ
                switch ($item->certificate_type) {
                    case 0:
                        $item->certificate_type = 'CB';
                        break;
                    case 1:
                        $item->certificate_type = 'IB';
                        break;
                    case 2:
                        $item->certificate_type = 'LAB';
                        break;
                    default:
                        $item->certificate_type = 'Unknown';
                }

                // แปลง approval เป็นข้อความ
                $item->approval = $item->approval == 0 ? 'รอดำเนินการ' : 'ลงนามเรียบร้อย';

                return $item;
            });
                
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    // สร้างปุ่มสองปุ่มที่ไม่มี action พิเศษ
                    $button1 = '<a href="' . $item->view_url . '" class="btn btn-info btn-xs" target="_blank"><i class="fa fa-eye"></i></a>';
                    $button2 = '<a type="button" class="btn btn-warning btn-xs btn-sm sign-document" data-id="'.$item->signer_id.'"  data-transaction_id="'.$item->id.' "><i class="fa fa-file-text"></i></a>';
                    
                    return $button1 . ' ' . $button2; // รวมปุ่มทั้งสองเข้าด้วยกัน
                })
                ->editColumn('certificate_type', function ($item) {
                    switch ($item->certificate_type) {
                        case 0:
                            return 'CB';
                        case 1:
                            return 'IB';
                        case 2:
                            return 'LAB';
                        default:
                            return '-';
                    }
                })
                ->editColumn('approval', function ($item) {
                    return $item->approval == 1 ? 'ลงนามเรียบร้อย' : 'รอดำเนินการ';
                })
                ->order(function ($query) {
                    $query->orderBy('id', 'DESC');
                })
                ->make(true);
        }else{
            return response()->json(['error' => 'ไม่พบข้อมูล signer'], 404);
        }
    }

    public function apiGetSigners()
    {
        $signers = Signer::all();

        return response()->json([
            'signers'=> $signers,
         ]);
    }

    public function getSigner(Request $request)
    {
        // รับ signer_id จาก request
        $signer_id = $request->input('signer_id');

        // ดึงข้อมูล Signer ตาม ID ที่ส่งมา
        $signer = Signer::find($signer_id);

        // ตรวจสอบว่า AttachFileAttachTo มีข้อมูลหรือไม่
        $attach = !empty($signer->AttachFileAttachTo) ? $signer->AttachFileAttachTo : null;

        if ($attach !== null) {
            // สร้าง URL สำหรับ sign_url
            $sign_url = url('funtions/get-view/' . $attach->url . '/' . (!empty($attach->filename) ? $attach->filename : basename($attach->url)));
        } else {
            $sign_url = null; // กรณีที่ไม่มีไฟล์แนบ
        }

        // ตรวจสอบว่าพบข้อมูลหรือไม่
        if ($signer) {
            // เพิ่ม sign_url เข้าไปใน response data
            return response()->json([
                'success' => true,
                'data' => array_merge($signer->toArray(), [
                    'sign_url' => $sign_url
                ])
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลผู้ลงนามที่ต้องการ'
            ], 404);
        }
    }

    public function signDocument(Request $request)
    {
        // dd($request->all());
        SignAssessmentReportTransaction::find($request->id)->update([
            'approval' => 1
        ]);

        $signAssessmentReportTransaction = SignAssessmentReportTransaction::find($request->id);
        $signAssessmentReportTransactions = SignAssessmentReportTransaction::where('lab_report_info_id',$signAssessmentReportTransaction->lab_report_info_id)
                                ->whereNotNull('signer_id')
                                ->where('approval',0)
                                ->get();           

        if($signAssessmentReportTransactions->count() == 0){
            $pdfService = new CreateLabAssessmentReportPdf($signAssessmentReportTransaction->lab_report_info_id,"ia");
            $pdfContent = $pdfService->generateLabAssessmentReportPdf();

        }                        
        
    }

    // public function set_mail($signAssessmentReportTransaction) 
    // {

    //     if(!is_null($certi_lab->email)){

    //         $config = HP::getConfig();
    //         $url  =   !empty($config->url_acc) ? $config->url_acc : url('');
    //         $dataMail = ['1804'=> 'lab1@tisi.mail.go.th','1805'=> 'lab2@tisi.mail.go.th','1806'=> 'lab3@tisi.mail.go.th'];
    //         $EMail =  array_key_exists($certi_lab->subgroup,$dataMail)  ? $dataMail[$certi_lab->subgroup] :'admin@admin.com';

    //         if(!empty($certi_lab->DataEmailDirectorLABCC)){
    //             $mail_cc = $certi_lab->DataEmailDirectorLABCC;
    //         }
           
    //         $data_app = [
    //                         'email'=>  'admin@admin.com',
    //                         'auditors' => $auditors,
    //                         'certi_lab'=> $certi_lab,
    //                         'url' => $url.'certify/applicant/auditor/'.$certi_lab->token,
    //                         'email'=>  !empty($certi_lab->DataEmailCertifyCenter) ? $certi_lab->DataEmailCertifyCenter : $EMail,
    //                         'email_cc'=>  !empty($mail_cc) ? $mail_cc :  $EMail,
    //                         'email_reply' => !empty($certi_lab->DataEmailDirectorLABReply) ? $certi_lab->DataEmailDirectorLABReply :  $EMail
    //                     ];
        
    //         $log_email =  HP::getInsertCertifyLogEmail( $certi_lab->app_no,
    //                                                     $certi_lab->id,
    //                                                     (new CertiLab)->getTable(),
    //                                                     $auditors->id,
    //                                                     (new BoardAuditor)->getTable(),
    //                                                     1,
    //                                                     'การแต่งตั้งคณะผู้ตรวจประเมิน',
    //                                                     view('mail.Lab.mail_board_auditor', $data_app),
    //                                                     $certi_lab->created_by,
    //                                                     $certi_lab->agent_id,
    //                                                     auth()->user()->getKey(),
    //                                                     !empty($certi_lab->DataEmailCertifyCenter) ?  implode(',',(array)$certi_lab->DataEmailCertifyCenter)  : $EMail,
    //                                                     $certi_lab->email,
    //                                                     !empty($mail_cc) ? implode(',',(array)$mail_cc)   :  $EMail,
    //                                                     !empty($certi_lab->DataEmailDirectorLABReply) ?implode(',',(array)$certi_lab->DataEmailDirectorLABReply)   :  $EMail,
    //                                                     null
    //                                                     );
    
    //          $html = new  MailBoardAuditor($data_app);
    //           $mail = Mail::to($certi_lab->email)->send($html);

    //           if(is_null($mail) && !empty($log_email)){
    //                HP::getUpdateCertifyLogEmail($log_email->id);
    //           }

    //     }
    // }

}
