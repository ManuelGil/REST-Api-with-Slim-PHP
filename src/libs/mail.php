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
	public static function send($to, $name, $subject, $html, $text)
	{
		$mail = new PHPMailer(true);						// Passing `true` enables exceptions
			
		//Server settings
		$mail->SMTPDebug	=	0;							// Enable verbose debug output
		$mail->isSMTP();									// Set mailer to use SMTP
		$mail->Host			=	MAIL_HOST;					// Specify main and backup SMTP servers
		$mail->Username		=	MAIL_USER;					// SMTP username
		$mail->Password		=	MAIL_PASS;					// SMTP password
		$mail->SMTPAuth		=	true;						// Enable SMTP authentication
		$mail->SMTPSecure	=	'tls';						// Enable TLS encryption, `ssl` also accepted
		$mail->Port			=	587;						// TCP port to connect to

		//Recipients
		$mail->AddReplyTo(MAIL_USER, MAIL_NAME);			// Add a "Reply-To" address (Optional)
		$mail->SetFrom(MAIL_USER, MAIL_NAME);
		$mail->AddAddress($to, $name);						// Add a recipient
		$mail->addBCC(MAIL_USER);							// Add a "BCC" address (Optional)

		//Content
		$mail->isHTML(true);								// Set email format to HTML
		$mail->Subject		=	$subject;
		$mail->Body			=	$html;
		$mail->AltBody		=	$text;
		$mail->CharSet		=	'UTF-8';

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