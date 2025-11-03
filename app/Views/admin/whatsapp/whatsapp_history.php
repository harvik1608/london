<?php 
	if($messages) {
		foreach($messages as $message) {
			if($message["isManuallyMsg"] == 1) {
				$msg = $message["message"];
			} else {
				$messageFormat = json_decode($message["message"]);
				if($message["isReminderMsg"] == 1) {
					$name = isset($messageFormat[0]) ? $messageFormat[0] : '';
					$time = isset($messageFormat[1]) ? $messageFormat[1] : '';
					$services = isset($messageFormat[2]) ? $messageFormat[2] : '';
					if($message["company_id"] == 1) {
						$msg = "Hi ".$name."<br><br>This is a reminder for your upcoming appointment today at ".$time." with Embellish Hair and Beauty Team,<br>40, Shaftesbury Circle, Shaftesbury Parade, Harrow, HA2 0AH. <br>".$services."<br><br>Phone : 020 8423 9911";
					} else if($message["company_id"] == 3) {
						$msg = "Hi ".$name."<br><br>This is a reminder for your upcoming appointment today at ".$time." with Elsa Hair and Beauty Team,<br>54 Kew Bridge Road Brentford, London, TW8 0EW. <br>".$services."<br><br>Phone : 0208 568 7337";
					}
				} else {
					if($message["company_id"] == 1) {
						$name = isset($messageFormat[0]) ? $messageFormat[0] : '';
						$date = isset($messageFormat[1]) ? $messageFormat[1] : '';
						$time = isset($messageFormat[3]) ? $messageFormat[3] : '';
						$services = isset($messageFormat[2]) ? $messageFormat[2] : '';
					} else if($message["company_id"] == 3) {
						$name = isset($messageFormat[0]) ? $messageFormat[0] : '';
						$date = isset($messageFormat[1]) ? $messageFormat[1] : '';
						$time = isset($messageFormat[2]) ? $messageFormat[2] : '';
						$services = isset($messageFormat[3]) ? $messageFormat[3] : '';
					}
					if($message["company_id"] == 1) {
						$msg = "Hello ".$name."<br><br>Thank you for booking your appointment with Embellish Hair and Beauty. Here are the details of your appointment.<br><br>Date : ".$date."<br>Time : ".$time."<br>Services : ".$services."<br><br>If you need to make any changes or have any questions, feel free to contact us:<br><br>Phone: 020 8423 9911<br>WhatsApp: +44 7889 412000<br>Email: embellishlondon@gmail.com<br><br>We look forward to seeing you!<br><br>Warm Regards,<br>Embellish Beauty<br>40, Shaftesbury Circle, Shaftesbury Parade, Harrow, HA2 0AH. <br>embellishlondon@gmail.com<br>https://embellish-beauty.co.uk/";
					} else if($message["company_id"] == 3) {
						$msg = "Hello ".$name."<br><br>Thank you for booking your appointment with Elsa Hair and Beauty. Here are the details of your appointment.<br><br>Date : ".$date."<br>Time : ".$time."<br>Services : ".$services."<br><br>If you need to make any changes or have any questions, feel free to contact us:<br><br>Phone: 0208 568 7337<br>WhatsApp: 0744 574 9922<br>Email: elsakewbridge@gmail.com<br><br>We look forward to seeing you!<br><br>Warm Regards,<br>Elsa Hair and Beauty<br>54 Kew Bridge Road Brentford, London, TW8 0EW <br>elsakewbridge@gmail.com<br>https://elsakewbridge.com/";
					}
				}
			}
?>	
			<tr>
				<td class="<?php echo $message["isReply"] == 0 ? 'send-chat' : 'receive-chat'; ?>">
					<?php
						if($message["isReply"] == 0) {
					?>
							<p><?php echo $msg; ?><br><small style="text-align: right;font-weight: bold;">(<?php echo date('d M, h:i',strtotime($message['date'])); ?>)</small></p>
					<?php
						} else {
					?>
							<span><?php echo $msg; ?><br><small style="text-align: right;font-weight: bold;">(<?php echo date('d M, h:i',strtotime($message['date'])); ?>)</small></span>
					<?php
						}
					?>
				</td>
			</tr>
<?php
		}
	}
?>