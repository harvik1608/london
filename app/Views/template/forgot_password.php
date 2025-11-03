<!DOCTYPE html>
<html>
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
	<title></title>
  <style>
    body {
        font-family: "Nunito", serif !important;
		font-optical-sizing: auto;
		font-weight: 400;
		font-style: normal;
    }
    .button {
        font-family: "Nunito", serif !important;
			font-optical-sizing: auto;
			font-weight: 400;
			font-style: normal;
      background-color: #007bff;
      border: none;
      color: #FFFFFF !important;
      padding: 12px 24px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin-top: 20px;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <p>Hi <?php echo $customer_name; ?>,</p>
  <p>We received a request to reset your password.</p>
  <p>Click the button below to create a new one:</p>

  <a href="<?php echo $reset_link; ?>" class="button">Reset Password</a>

  <p>If you didn't request this, you can safely ignore this email.</p>
  <p>This link will expire in 30 minutes for security purposes.</p>

  <p>Thanks,<br>The <?php echo $company_name; ?> Team</p>
</body>
</html>
