<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class ConsertFormQuestionAnswerModel extends Model
    {
        protected $table = 'consent_form_question_answers';
        protected $primaryKey = 'id';
        protected $allowedFields = ['consert_form_id','consent_form_question_id','question','answer_type','option','company_id','is_active','created_by','updated_by','deleted_by','created_at','updated_at','deleted_at'];
    }