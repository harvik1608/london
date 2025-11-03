<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class ServicePriceModel extends Model
    {
        protected $table = 'service_prices';
        protected $primaryKey = 'id';
        protected $allowedFields = ['service_group_id','service_id','price_type','extra_time_type','duration','bookedFrom','note','json','company_id'];
    }