<html>
    <body>
        <table>
            <tr>
                <td>Hi <?php echo $name; ?></td>
            </tr>
            <tr>
                <td>Thank you for reaching out to us via our website. We’ve received your message and one of our team members will get back to you as soon as possible.</td>
            </tr>
            <tr>
                <td>Here’s a summary of your submission:</td>
            </tr>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td>Name</td>
                            <td>: <?php echo $name; ?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>: <?php echo $email; ?></td>
                        </tr>
                        <tr>
                            <td>Subject</td>
                            <td>: <?php echo $phone; ?></td>
                        </tr>
                        <tr>
                            <td>Message</td>
                            <td>: <?php echo $message; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr>
                <td>We appreciate your interest and will be in touch shortly.</td>
            </tr>
            <tr>
                <td>Best regards,</td>
            </tr>
            <tr>
                <td><?php echo $company_name; ?></td>
            </tr>
            <tr>
                <td><a href="<?php echo $website_url; ?>" target="_blank"><?php echo $website_url; ?></a></td>
            </tr>
            <tr>
                <td><?php echo $company_email; ?></td>
            </tr>
        </table>
    </body>
</html>