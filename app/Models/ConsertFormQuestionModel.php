<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class ConsertFormQuestionModel extends Model
    {
        protected $table = 'consent_form_questions';
        protected $primaryKey = 'id';
        protected $allowedFields = ['consert_form_id','title','company_id','is_active','created_by','updated_by','deleted_by','created_at','updated_at','deleted_at'];
    }