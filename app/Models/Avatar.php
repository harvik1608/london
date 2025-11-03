<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class Avatar extends Model
    {
        protected $table = 'photos';
        protected $primaryKey = 'id';
        protected $allowedFields = ['name','position','company_id','is_active','is_active','update_by','created_at','updated_at'];
    }