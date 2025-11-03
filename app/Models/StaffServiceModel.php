<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class StaffServiceModel extends Model
    {
        protected $table = 'staff_services';
        protected $primaryKey = 'id';
        protected $allowedFields = ['staff_id','service_id','company_id','created_by','updated_by','created_at','updated_at'];
    }