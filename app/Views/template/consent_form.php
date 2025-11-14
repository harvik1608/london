<!DOCTYPE html>
<html>
	<head>
	    <meta charset="UTF-8">
	    <link rel="preconnect" href="https://fonts.googleapis.com">
	    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
	    <style>
	    	body {
	    		font-family: Nunito, serif !important;
	    		font-optical-sizing: auto;
		        font-weight: 400;
		        font-style: normal;
	    	}
	    </style>
	    <title><?php echo $consent_form_name; ?> Consent Form</title>
	</head>
	<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px;">
	    <table style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 20px;">
	        <tr>
	            <td>
	                <p>Hi <?php echo $customer_name; ?>,</p>
	                <p>Thank you for submitting your <?php echo $consent_form_name; ?> Consent Form. We’ve successfully received your information.</p>

	                <p>Warm regards,<br>
	                <strong><?php echo $company_name; ?></strong><br>
	            </td>
	        </tr>
	    </table>
	</body>
</html>
