<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class AppointmentModel extends Model
    {
        protected $table = 'appointments';
        protected $primaryKey = 'id';
        protected $allowedFields = ['uniq_id','customerId','subTotal','discountAmt','discountId','finalAmt','bookingDate','status','bookedFrom','note','type','flag','companyId','salon_note','extra_discount','addedDate','addedBy','updatedBy','createdAt','updatedAt','old_booking_id','is_booked_from_website'];
    }