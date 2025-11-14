<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class CustomerConsentModel extends Model
    {
        protected $table = 'customer_consent_forms';
        protected $primaryKey = 'id';
        protected $allowedFields = ['customer_id','consent_form_id','consent_form_question_id','consent_form_question_answer_id','answer','company_id','date','signature','jsondata','created_by','updated_by','created_at','updated_at'];
    }