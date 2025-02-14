<?php

namespace App\Models\Certify;

use App\Models\Besurv\Signer;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Certify\LabReportInfo;
use Illuminate\Database\Eloquent\Model;

class SignAssessmentReportTransaction extends Model
{
    use Sortable;
    protected $table = 'sign_assessment_report_transactions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'lab_report_info_id',
        'signer_id',
        'app_id',
        'certificate_type',
        'signer_name',
        'signer_position',
        'signer_order',
        'file_path',
        'linesapce',
        'view_url',
        'approval',
    ];

    public function labReportInfo(){
        return $this->belongsTo(LabReportInfo::class, 'lab_report_info_id', 'id');
    }

    public function signer(){
        return $this->belongsTo(Signer::class, 'signer_id', 'id');
    }
}
