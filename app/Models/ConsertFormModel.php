<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class ConsertFormModel extends Model
    {
        protected $table = 'consent_forms';
        protected $primaryKey = 'id';
        protected $allowedFields = ['title','description','duration','company_id','is_active','created_by','updated_by','deleted_by','created_at','updated_at','deleted_at'];
    }