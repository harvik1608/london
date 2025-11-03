<?php 
    namespace App\Models;
    use CodeIgniter\Model;

    class ConfirmationMessage extends Model
    {
        protected $table = 'confimation_messages';
        protected $primaryKey = 'id';
        protected $allowedFields = ['sent_to','received_from','message','type','date','company_id','is_sent','isReminderMsg','isReply','isManuallyMsg','is_read','ip'];
    }