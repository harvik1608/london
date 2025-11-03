<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class SubServiceModel extends Model
    {
        protected $table = 'services';
        protected $primaryKey = 'id';
        protected $allowedFields = ['service_group_id','name','price_type','extra_time_type','duration','bookedFrom','position','note','json','company_id','is_active','is_deleted','created_by','updated_by','created_at','updated_at'];
    }