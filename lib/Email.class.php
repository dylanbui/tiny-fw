<?php

require_once 'PHPMailer/class.PHPMailer.php';


class Email 
{
	var $to;
	var $to_name = '';
	var $subject;
	var $is_smtp = true;
	var $is_html = true;
	var $language = array ();
	var $body;
	var $cc;
	var $cc_name = '';
	var $attach = NULL;
	
	var $smtp;
	
	function Email($to, $subject, $body) 
	{
		$this->to = $to;
		$this->subject = $subject;
		$this->body = $body;
	}
	
	function connect($cfg) 
	{
		$this->smtp = $cfg;
	}
	
	function Send() 
	{
		$mail = new PHPMailer ();
		$mail->CharSet = 'UTF-8';
		$mail->Host = $this->smtp ['smtp_server'];
		$mail->Port = $this->smtp ['smtp_port'];
		
		if ($this->smtp ['smtp_enable']) 
		{
			$mail->IsSMTP ();
			$mail->Username = $this->smtp ['smtp_usr'];
			$mail->Password = $this->smtp ['smtp_psw'];
			$mail->SMTPAuth = $this->smtp ['smtp_auth'] ? true : false;
		}
		
		if ($this->smtp ['smtp_from_email']) 
		{
			$mail->SetFrom ( $this->smtp ['smtp_from_email'], $this->smtp ['smtp_from_name'] );
		} else 
		{
			$mail->SetFrom ( $this->smtp ['smtp_server'], $this->smtp ['smtp_usr'] );
		}
		
		if (is_array ( $this->to )) 
		{
			foreach ( $this->to as $key => $val ) 
			{
				$name = is_numeric ( $key ) ? "" : $key;
				$mail->AddAddress ( $val, $name );
			}
		} else 
		{
			$mail->AddAddress ( $this->to, $this->to_name );
		}
		
		if (!empty($this->smtp['smtp_reply_email'])) 
		{
			$mail->AddReplyTo ( $this->smtp ['smtp_reply_email'], $this->smtp ['smtp_reply_name'] );
		}
		
		if ($this->cc) 
		{
			if (is_array ( $this->cc )) {
				foreach ( $this->cc as $keyc => $valcc ) {
					$name = is_numeric ( $keyc ) ? "" : $keyc;
					$mail->AddCC ( $valcc, $name );
				}
			} else {
				$mail->AddCC ( $this->cc, $this->cc_name );
			}
		}
		
		if ($this->attach) 
		{
			if (is_array ( $this->attach )) 
			{
				foreach ( $this->attach as $key => $val ) 
				{
					$mail->AddAttachment ( $val );
				}
			} else 
			{
				$mail->AddAttachment ( $this->attach );
			}
		}
		
// 		$mail->SMTPSecure = 'ssl';
		$mail->SMTPSecure = "tls";
		$mail->WordWrap = 50;
		$mail->IsHTML($this->is_html);
		$mail->Subject = $this->subject;
		$mail->Body = $this->body;
		$mail->AltBody = "";
		// return $mail->Body;
		return $mail->Send();
	}
}
?>