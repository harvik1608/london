<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class CartModel extends Model
    {
        protected $table = 'carts';
        protected $primaryKey = 'id';
        protected $allowedFields = ['uniq_id','appointmentId','date','stime','duration','etime','staffId','serviceId','serviceNm','serviceSubId','amount','message','isComplete','color','isStaffBusy','is_turn','is_done','companyId','is_cancelled','is_removed_from_cart','addedBy','updatedBy','createdAt','updatedAt','caption','actual_amount'];
    }