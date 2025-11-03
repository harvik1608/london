<?php 
	if(empty($customers))
	{
		echo "<small>No result found.</small>";
	} else {
		foreach($customers as $customer)
		{
			$phone 	= addslashes($customer['phone']);
			$name 	= addslashes($customer['name']);
			$email 	= addslashes($customer['email']);
			$note 	= addslashes($customer['note']);
			$phone 	= preg_replace('!\s+!', ' ', $phone);
			$name 	= preg_replace('!\s+!', ' ', $name);
			$email 	= preg_replace('!\s+!', ' ', $email);
			$note 	= preg_replace('!\s+!', ' ', $note);
?>
			<a href="javascript:;" onclick="set_customer_info('<?php echo $phone; ?>','<?php echo $name; ?>','<?php echo $email; ?>','<?php echo $flag; ?>','<?php echo $note; ?>')">
				<small><?php echo format_text(1,$name); ?> (<?php echo $phone; ?>)</small>
			</a><br>
<?php
		}
	}
?>