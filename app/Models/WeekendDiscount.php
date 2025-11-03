<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class WeekendDiscount extends Model
    {
        protected $table = 'weekend_discounts';
        protected $primaryKey = 'id';
        protected $allowedFields = ['name','sdate','edate','week_days','percentage','service_group_ids','service_ids','is_all_service_checked','company_id','is_active','created_by','updated_by','created_at','updated_at'];
    }