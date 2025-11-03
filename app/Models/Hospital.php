<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class Hospital extends Model
    {
        protected $table = 'hospitals';
        protected $primaryKey = 'id';
        protected $allowedFields = ['hospital_state','hospital_city','href','info','name','phone','address','is_active','is_deleted','created_at','updated_at'];
    }