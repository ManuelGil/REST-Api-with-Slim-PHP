<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{

	/**
	 * This method sends a email
	 *
	 * @param	string	$fro	 	Email address origin 
	 * @param	string	$to			Email addess destination
	 * @param	string	$name		Name of addressee
	 * @param	string	$subject	Topic of message
	 * @param	string	$html		Message HTML
	 * @param	string	$text		Message simple
	 *
	 * @return	boolean
	 */
	public static function send($from, $to, $name, $subject, $html, $text)
	{
		$mail = new PHPMailer(true);						// Passing `true` enables exceptions
			
		//Server settings
		$mail->SMTPDebug = 0;								// Enable verbose debug output
		$mail->isSMTP();									// Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';						// Specify main and backup SMTP servers
		$mail->SMTPAuth = true;								// Enable SMTP authentication
		$mail->Username = 'username@gmail.com';				// SMTP username
		$mail->Password = 'yourpassword';					// SMTP password
		$mail->SMTPSecure = 'tls';							// Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;									// TCP port to connect to

		//Recipients
		$mail->AddReplyTo($from, 'Admin Fav Quote');
		$mail->SetFrom($from, 'Admin Fav Quote');
		$mail->AddAddress($to, $name);						// Add a recipient
		$mail->addBCC($from);

		//Content
		$mail->isHTML(true);								// Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body = $html;
		$mail->AltBody = $text;
		$mail->CharSet = 'UTF-8';

		if (filter_var($to, FILTER_VALIDATE_EMAIL) !== false) {
			$result = $mail->send();
		} else {
			return false;
		}

		if ($result) {
			return true;
		} else {
			return false;
		}
	}

}

?>