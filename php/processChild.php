<?php

	include('func_email.php');
	include('func_security.php');
	
	$p_parNames = makeSafe($_POST['parent_name']);
	$p_childNames = makeSafe($_POST['child_name']);
	$p_contact = makeSafe($_POST['contact']);
	$p_email = makeSafe($_POST['email']);
	
	$target_path = "images_child/";
	$target_path .= basename($_FILES['childphoto']['name']);

	var_dump($_FILES);
	var_dump($_FILE);

	move_uploaded_file($_FILES['childphoto']['tmp_name'], $target_path);

	$filenames = array($target_path);
	
	$to = "stephanp@woordenlewe.com";
	$subj = "iCard Child Dedication Test - Do not respond";
	
	$msg = "";
	$msg .= "<p>Parent Names:<br/>$p_parNames</p>";
	$msg .= "<p>Children Names:<br/>$p_childNames</p>";
	$msg .= "<p>Contact Number:<br/>$p_contact</p>";
	$msg .= "<p>E-Mail</p>:<br/>$p_email</p>";
	
	$from = "wnl-icard@apollolms.co.za";
	
	$stat = mail_withAttachments($to,$subj,$msg,$from,$filenames);
	
	if(!$stat){
		echo 'There was an error processing your request';
	}else{
		echo 'Your request has been sent!';
	}
?>
