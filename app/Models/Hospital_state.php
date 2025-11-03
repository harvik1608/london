<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class Hospital_state extends Model
    {
        protected $table = 'hospital_states';
        protected $primaryKey = 'id';
        protected $allowedFields = ['name','href','is_turn','is_active','is_deleted','created_at','updated_at'];
    }