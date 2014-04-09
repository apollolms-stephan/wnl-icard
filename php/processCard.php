<?php
	$p_name = $_POST['name'];
	$p_contact = $_POST['contact'];
	$p_email = $_POST['email'];
	$p_age = $_POST['age'];
	$p_marital = $_POST['marital_status'];
	$p_lang = $_POST['lang'];
	$p_occupation = $_POST['occupation'];
	
	$p_reqtype = $_POST['request'];
	
	$p_reqtype_text = "";
		if(isset($_POST['req_acceptjesus'])){
			$p_reqtype_text = "I want to accept Jesus into my life";
		}
		if(isset($_POST['req_newtoarea'])){
			$p_reqtype_text = "I am new to the area";
		}
		if(isset($_POST['req_needspirithelp'])){
			$p_reqtype_text = "I need spritirual help";
		}
		if(isset($_POST['req_detailschange'])){
			$p_reqtype_text = "My details have changed";
		}
		if(isset($_POST['req_newmember'])){
			$p_reqtype_text = "I want to become a new member";
		}
		if(isset($_POST['req_newvolunteer'])){
			$p_reqtype_text = "I want to become a volunteer";
		}
	

	$p_prayer = $_POST['prayer_req'];
	$p_praise = $_POST['praise_rep'];
	
	$body = "";
	$body .= "Please follow up on this request:<br/>";
	$body .= "<p>Name: $p_name<br/>";
	$body .= "Contact Number: $p_contact<br/>";
	$body .= "E-Mail: $p_email<br/>";
	$body .= "Age: $p_age<br/>";
	$body .= "Marital Status: $p_marital<br/>";
	$body .= "Request: $p_reqtype_text<br/>";
	$body .= "Language Preference: $p_lang<br/>";
	$body .= "Occupation: $p_occupation<br/>";
	$body .= "Prayer Request: $p_prayer<br/>";
	$body .= "Praise Report: $p_praise<br/>";
	$body .= "</p>";
	
	$prayer_body = "";
	$prayer_body .= "From: $p_name<br/>";
	$prayer_body .= $p_prayer;
	
	if($p_email != ''){
			$to = $p_email;
	}else{
			$to = "stephanp@woordenlewe.com";//"admin@woordenlewe.com";
	}

	$prayer_to = "gebed@woordenlewe.com";
	$subj ="WNL iCard tester - Do not react";
	
	$from="wnl-icard@apollolms.co.za";

	$headers = "From: $from" . "\r\n" . "Reply-To: $from" . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// mail followup
	mail($to, $subj, $body, $headers);
	
	// mail prayer
	mail($prayer_to, "Prayer Request", $prayer_body, $headers);
?>

Thank you!
{insert redirect}
