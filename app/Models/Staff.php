<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class Staff extends Model
    {
        protected $table = 'staffs';
        protected $primaryKey = 'id';
        protected $allowedFields = ['fname','lname','phone','email','password','code','color','wages','roles','designation','user_type','is_all_service','is_active','is_deleted','created_by','updated_by','created_at','updated_at'];
    }