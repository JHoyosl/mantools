<?php

namespace Gsys;

class mailer{


	//Variable globalr para usar la bd
	private $db;
	private $cfg;
	
	//Se declara la variable de respeusta
	private $send = array("");
	function __construct(){

		$this->db = new db();

		$this->cfg = parse_ini_file("cfg/cfg.ini",true);	


	}



	function sendInfo($mail, $body_content){
		

		
		
		require_once('libs/PHPMailer/class.phpmailer.php');

		require_once("libs/PHPMailer/class.smtp.php");

		
		$mailInfo = $this->cfg["mail"];

		$mail             = new \PHPMailer();
		$address = "jhoyosl@globalsys.co";
		

		$body             = $body_content; //file_get_contents('contents.html');
		//$body             = $info["HTML"]; //file_get_contents('contents.html');
		
		$mail->IsSMTP(); // telling the class to use SMTP

		$mail->Host       = $mailInfo["Host"]; // SMTP server
		$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												   // 2 = messages only


		$mail->SMTPAuth   = $mailInfo["SMTPAuth"];               // enable SMTP authentication
		$mail->SMTPSecure = $mailInfo["SMTPSecure"];;                 // sets the prefix to the servier
		$mail->Host       = $mailInfo["Host"];    // sets GMAIL as the SMTP server
		$mail->Port       = $mailInfo["Port"];                  // set the SMTP port for the GMAIL server
		$mail->Username   = $mailInfo["Username"];  // GMAIL username
		$mail->Password   = $mailInfo["Password"];           // GMAIL password

		$mail->SetFrom($mailInfo["SetFrom"], $mailInfo["SetFromText"]);

		// $mail->AddReplyTo("AddReplyTo@yourdomain.com","First Last");

		$mail->Subject    = ("Mensaje de prueba credito online");

		$mail->AltBody    = "Para ver el mensaje, porfavor utilice un visor de correo con compatibilidad HTML"; // optional, comment out and test

		$mail->MsgHTML($body);

		
			$mail->AddAddress($address, "pruebas2");

			// $mail->AddAttachment("images/phpmailer.gif");      // attachment
			// $mail->AddAttachment("images/phpmailer_mini.gif"); // attachment


			if(!$mail->Send()) {

			  throw new Exception($mail->ErrorInfo, 1);
			  
			} else {
			  
			  return true;
			}
		
			
		
	}



}


?>