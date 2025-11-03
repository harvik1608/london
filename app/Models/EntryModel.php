<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class EntryModel extends Model
    {
        protected $table = 'entries';
        protected $primaryKey = 'id';
        protected $allowedFields = ['appointment_id','uniq_id','service_group_id','service_id','staff_id','cart_id','date','stime','duration','etime','price','company_id','is_turn','showbusystaff','flag','resource_id','caption','created_at','is_removed_from_cart','actual_price','sub_caption'];
    }