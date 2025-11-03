<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class Hospital_city extends Model
    {
        protected $table = 'hospital_cities';
        protected $primaryKey = 'id';
        protected $allowedFields = ['name','href','is_active','is_deleted','created_at','updated_at'];
    }