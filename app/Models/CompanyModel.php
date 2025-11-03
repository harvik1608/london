<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class CompanyModel extends Model
    {
        protected $table = 'companies';
        protected $primaryKey = 'id';
        protected $allowedFields = ['company_name','company_email','company_phone','company_desc','company_address','company_logo','isActive','currency','company_stime','company_etime','about_company','smtp_email','smtp_password','smtp_host','smtp_port','from_email','from_name','website_url','code','google_contact','google_calendar','json','facebook_link','google_link','instagram_link','company_currency','banner','timezone','privacy_policy','parking_instructions','credential_file','google_code','company_sunday_stime','company_sunday_etime','company_service_groups','company_services','is_all_service_checked','createdBy','updatedBy','createdAt','updatedAt','banners','wa_phone_id','wa_token','company_whatsapp_phone','google_map'];
    }