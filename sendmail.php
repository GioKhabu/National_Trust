<?php
error_reporting(E_ALL ); ini_set('display_errors', '1');

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
/*use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;*/

//Load Composer's autoloader
require 'PHPMailer6/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// require 'PHPMailer524/class.phpmailer.php';


function sendMail($To,$ToName,$From,$FromName,$Reply,$ReplyName,$Attachments,$Subject,$HTMLBody,$TXTBody,$debug=false){
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP


try { 
	
  	if($debug) $mail->SMTPDebug  = 2;

	$mail->Host       = 'mail.nationaltrustofgeorgia.org.ge'; 
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'info@nationaltrustofgeorgia.org.ge';                    
    $mail->Password   = 'xuPo8UMZcaFL'; 
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;            
	$mail->CharSet    = "utf-8";


/*	$mail->Encoding = 'quoted-printable';
	$mail->DKIM_domain = 'icomos.org.ge';
	$mail->DKIM_private = '/home/voting/ssl/keys/dkim_priv.key';
	$mail->DKIM_selector = 'phpmailer';
	$mail->DKIM_passphrase = 'voting';
	$mail->DKIM_identity = $mail->From;	*/
	
  $mail->AddAddress($To, $ToName);
  $mail->SetFrom($From, $FromName);
  $mail->AddReplyTo($Reply, $ReplyName);
  $mail->Subject = $Subject;
  $mail->AltBody = $TXTBody; 
  $mail->MsgHTML($HTMLBody);
  if(is_array($Attachments))
	foreach($Attachments as $Attachment)
		$mail->AddAttachment($Attachment);      // attachment
  
  $mail->Send();
  // echo "Message Sent OK</p>\n";
	return 1;
} catch (phpmailerException $e) {
  // echo $e->errorMessage(); //Pretty error messages from PHPMailer
	return 0;
} catch (Exception $e) {
  //echo $e->getMessage(); //Boring error messages from anything else!
	return 0;
}
	
	
}
?>