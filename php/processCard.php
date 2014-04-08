<?php

	$p_name = $_POST['name'];
	$p_contact = $_POST['contact'];
	$p_email = $_POST['email'];
	$p_age = $_POST['age'];
	$p_marital = $_POST['marital_status'];
	
	$p_reqtype = $_POST['request'];
	
	$p_prayer = $_POST['prayer_req'];
	$p_praise = $_POST['praise_rep'];
	
	$body .= "Please follow up on this request:<br/>";
	$body .= "<p>Name: $p_name<br/>";
	$body .= "Contact Number: $p_contact<br/>";
	$body .= "E-Mail: $p_email<br/>";
	$body .= "Age: $p_age<br/>";
	$body .= "Marital Status: $p_marital<br/>";
	$body .= "Request: $p_reqtype<br/>";
	$body .= "Prayer Request: $p_prayer<br/>";
	$body .= "Praise Report: $p_praise<br/>";
	$body .= "</p>";
	
	$to ="admin@woordenlewe.com";
	$subj ="WNL Icard tester - Do not react";
	$body="";
	$from="apollotester@apollolms.co.za";
	$fullsitelogo = '"' . "http://" . $_SERVER['HTTP_HOST'] . '/' . SITE_LOGO . '"';

	$headers = "From: $from" . "\r\n" . "Reply-To: $from" . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	mail($to, $subj, $body, $headers));
?> 
