<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class PaymentTypeModel extends Model
    {
        protected $table = 'payment_types';
        protected $primaryKey = 'id';
        protected $allowedFields = ['name','position','company_id','is_active','is_deleted','update_by','created_at','updated_at'];
    }