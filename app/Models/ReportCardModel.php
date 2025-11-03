<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class ReportCardModel extends Model
    {
        protected $table = 'report_card';
        protected $primaryKey = 'id';
        protected $allowedFields = ['customer_id','customer_name','phone','skin_problem','skin_note','allergy_problem','allergy_note','patch_test','shield_size','lashesh_size','companyId','addedBy','updatedBy','createdAt','updatedAt'];
    }