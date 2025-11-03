<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class OrderPaymentModel extends Model
    {
        protected $table = 'order_payments';
        protected $primaryKey = 'id';
        protected $allowedFields = ['appointmentId','paymentMethod','paymentAmount','companyId','addedBy','updatedBy','createdAt','updatedAt'];
    }