<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class Service_group extends Model
    {
        protected $table = 'service_groups';
        protected $primaryKey = 'id';
        protected $allowedFields = ['name','slug','color','note','avatar','position','company_id','is_active','is_deleted','created_by','updated_by','created_at','updated_at'];
    }