<?php

namespace App\Models\Tis;

use App\Models\Basic\Department;
use App\Models\Tis\NoteStdDraft;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ListenStdDraft extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tis_listen_std_drafts';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['note_std_draft_id', 'comment', 'name', 'tel', 'email', 'department_id', 'department_name', 'attach', 'state', 'created_by', 'updated_by'];

    /*
      Sorting
    */
    public $sortable = ['note_std_draft_id', 'comment', 'name', 'tel', 'email', 'department_id', 'department_name', 'state', 'created_by', 'updated_by'];


    /*
      User Relation
    */
    public function user_created(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_updated(){
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getCreatedNameAttribute() {
      return !empty($this->user_created->reg_fname) && !empty($this->user_created->reg_lname)?$this->user_created->reg_fname.' '.$this->user_created->reg_lname:'n/a';
    }

    public function getUpdatedNameAttribute() {
        return @$this->user_updated->reg_fname.' '.@$this->user_updated->reg_lname;
    }

    public function department(){
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function getDepartmentNameNameAttribute() {
        $departments = !empty($this->department_id)?$this->department->title:@$this->department_name;
        return $departments ?? 'n/a';
    }

    public function note_std_draft()
    {
        return $this->belongsTo(NoteStdDraft::class, 'note_std_draft_id');
    }

    public function getCommentNameAttribute() {
        $arr = [
            'confirm_standard'=>'ยืนยันตามมาตรฐานดังกล่าว',
            'revise_standard'=>'เห็นควรแก้ไขปรับปรุงมาตรฐานดังกล่าว',
            'cancel_standard'=>'ยกเลิกมาตรฐานดังกล่าว',
            'no_comment'=>'ไม่มีข้อคิดเห็น'
        ];
        return @$arr[$this->comment];
    }


}
