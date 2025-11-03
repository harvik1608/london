<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class CustomerModel extends Model
    {
        protected $table = 'customers';
        protected $primaryKey = 'id';
        protected $allowedFields = ['resource_id','name','phone','email','marketing_email','note','json','url','is_sync_with_google','companyId','is_deleted','addedBy','updatedBy','createdAt','updatedAt','password','code','old_customer_id','gender','promotional_sms','referral_source_id','notification_settings','code_sentAt','isConfirmationEmailSend'];
    }