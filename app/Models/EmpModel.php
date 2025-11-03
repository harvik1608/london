<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class EmpModel extends Model
    {
        protected $table = 'staffs';
        protected $primaryKey = 'id';
        protected $allowedFields = ['fname','lname','phone','email','password','color','wages','roles','is_active','user_type','is_all_service','company_id','created_by','updated_by','created_at','updated_at','avatar','is_shown_on_website'];
    }