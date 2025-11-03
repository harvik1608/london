<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class Review extends Model
    {
        protected $table = 'reviews';
        protected $primaryKey = 'id';
        protected $allowedFields = ['star','given_by','comment','company_id','is_approved','created_at','updated_at','deleted_at'];
    }