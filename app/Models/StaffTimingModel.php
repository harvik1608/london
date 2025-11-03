<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class StaffTimingModel extends Model
    {
        protected $table = 'staff_timings';
        protected $primaryKey = 'id';
        protected $allowedFields = ['staffId','date','stime','etime','isRepeat','companyId','addedBy','updatedBy','createdAt','updatedAt'];
    }