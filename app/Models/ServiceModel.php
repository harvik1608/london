<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class ServiceModel extends Model
    {
        protected $table = 'service_groups';
        protected $primaryKey = 'id';
        protected $allowedFields = ['name','slug','color','avatar','note','position','company_id','is_active','created_by','updated_by','created_at','updated_at'];
    }