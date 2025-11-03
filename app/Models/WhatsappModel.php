<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class WhatsappModel extends Model
    {
        protected $table = 'whatsapp_msgs';
        protected $primaryKey = 'id';
        protected $allowedFields = ['company_id','customer_id','phone','type','msg_type','sent_msg','received_msg','code','created_at'];
    }