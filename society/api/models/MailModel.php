<?php

class MailModel{

	public function Email($type)
	{
		include 'core/config.php';
		
		if($type="subscribe")
		{		
			$name = $_POST['name'];
			$number = $_POST['number'];
			$email = $_POST['emailid'];
			$city = $_POST['city'];
			if(isset($_POST['newslet']))
			{
				$news = $_POST['newslet'];
			}
			else
			{
				$news = " ";
			}
			
			$mail_to = 'info@rideonbike.com';
			
			$subject = 'Coming Soon';
			
			$body_message = 'From: '.$name."\n";
			$body_message .= 'E-mail: '.$email."\n";
			$body_message .= 'Phone Number: '.$number."\n";
			$body_message .= 'City: '.$city."\n";
			$body_message .= 'News: '.$news."\n";
			
			$headers = 'From:'.$email.">\r\n";
			$headers .= "Reply-To: $email\r\n";
		}
		else if($type="enquiry")
		{
			$name = $_POST['name'];
			$number = $_POST['phone'];
			$email = $_POST['email'];
			$message = $_POST['message'];
			
			$mail_to = 'info@rideonbike.com';
			
			$subject = 'Contact Form';
			
			$body_message = 'From: '.$name."\n";
			$body_message .= 'E-mail: '.$email."\n";
			$body_message .= 'Phone Number: '.$number."\n";
			$body_message .= 'City: '.$city."\n";
			$body_message .= 'News: '.$news."\n";
			
			$headers = 'From:'.$emailid.">\r\n";
			$headers .= "Reply-To: $email\r\n";
		}
		$mailquery = mail($mail_to, $subject, $body_message, $headers);
		
		if($mailquery)
		{
			header('HTTP/1.1 200 Success');
			header('Content-Type: text/xml; charset=UTF-8');
			echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
			echo("<message>Thank You</message>");
			return;
		}
		{
			$this->returnerror("Mail error");
		}
	}
	
	
	public function returnerror($errorstring)
	{
		header('HTTP/1.1 400 Bad Request');
		header('Content-Type: text/xml; charset=UTF-8');
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		echo("<error>".$errorstring."</error>");
		return;
	}
}

?>