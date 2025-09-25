<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'postal.b-conf.com';   // SMTP hostname डालो
    $mail->SMTPAuth   = true;
    $mail->Username   = 'cp';                  // username डालो
    $mail->Password   = 'NhQOtMmjbZBb8WQKnOaxOXGQ';  // password डालो
    $mail->Port       = 25;                    // पहले 25 try करो
    $mail->SMTPSecure = false;                 // no encryption
    $mail->AuthType   = 'LOGIN';

    $mail->SMTPDebug  = 2;                     // debug on
    $mail->Debugoutput = 'echo';

    $mail->setFrom('test@yourdomain.com', 'SMTP Tester');
    $mail->addAddress('yourmail@gmail.com');   // कोई अपना test mail डालो

    $mail->Subject = 'SMTP Test Email';
    $mail->Body    = 'This is a test email sent via SMTP.';

    $mail->send();
    echo "Message sent successfully\n";
} catch (Exception $e) {
    echo "Message could not be sent. Error: {$mail->ErrorInfo}\n";
}
