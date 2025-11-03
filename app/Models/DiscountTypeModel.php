<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class DiscountTypeModel extends Model
    {
        protected $table = 'discount_types';
        protected $primaryKey = 'id';
        protected $allowedFields = ['name','discount_type','discount_value',',position','is_active','is_deleted','company_id','created_by','updated_by','created_at','updated_at'];
    }