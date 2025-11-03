<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class WebsiteEntry extends Model
    {
        protected $table = 'website_entries';
        protected $primaryKey = 'id';
        protected $allowedFields = ['customer_id','caption','amount','duration','service_id','company_id','service_name','service_group_id','discount_amount','datetime','is_final'];
    }