<?php

function inform_users_aboutNewCourse($cid){
	
	$q = "SELECT name FROM courses WHERE id='$cid' LIMIT 1";
	$d = sql_execute($q);
	$r = sql_get($d);
	$courseName = $r['name'];
	
	$q = "SELECT id,email FROM members";
	$d = sql_execute($q);

	$subj = "A new course is nou available to you!";
	$msgbody = "A new course, " . $courseName . " is now available to you. Logon to the site to register.";
	
	while($r = sql_get($d)){
		if(userHasCoursePermission($r['id'],$cid)){
			mail_inform($r['email'],$subj,$msgbody);
		}
	}
	return true;
}

/**
 * @param
 * filenames - array with names of files to be included
 */
function mail_withAttachments($to,$subject,$message,$from,$filenames){
	// array with filenames to be sent as attachment
	$files = $filenames;

	// email fields: to, from, subject, and so on
	$headers = "From: $from";

	// boundary
	$semi_rand = md5(time());
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

	// headers for attachment
	$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

	// multipart boundary
	$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
	$message .= "--{$mime_boundary}\n";

	// preparing attachments
	for ($x = 0; $x < count($files); $x++) {
		$file = fopen($files[$x], "rb");
		$data = fread($file, filesize($files[$x]));
		fclose($file);
		$data = chunk_split(base64_encode($data));
		$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files[$x]\"\n" . "Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" . "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
		$message .= "--{$mime_boundary}\n";
	}

	// send

	$ok = mail($to, $subject, $message, $headers);
	if ($ok) {
		//echo "<p>mail sent to $to!</p>";
		return true;
	} else {
		//echo "<p>mail could not be sent!</p>";
		return false;
	}
}

/**
 *
 */
function mail_inform($to, $subj, $body) {
	sendEmail($to, $subj, $body, SITE_EMAIL_AUTOMATED);
}

/**
 *
 *
 */
function mail_informUser($uid, $subj, $body) {
	$q = "SELECT * FROM members WHERE id='$uid' LIMIT 1";
	$r = sql_execute($q);
	$d = sql_get($r);

	sendEmail($d['EMAIL'], $subj, $body, SITE_EMAIL_AUTOMATED);
}

function mail_informAllUsers($subj, $body) {
	$q = "SELECT EMAIL FROM members";
	$r = sql_execute($q);

	while ($d = sql_get($r)) {
		$allmails[] = $d['EMAIL'];
	}

	if (isset($allmails)) {
		$toppl = implode(",", $allmails);
		if (sendEmail($toppl, $subj, $body, SITE_EMAIL_AUTOMATED)) {
			echo "All users have been notified of your request";
		} else {echo "An error occured while sending the mail. Please try again later.";
		}
	} else {
		echo "No mails were sent to anyone";
	}
}

/**
 *
 *
 */
function mail_informGroupUsers($gid, $subj, $body) {
	$q = "SELECT * FROM members WHERE groups LIKE %'$gid'%";
	$r = sql_execute($q);

	while ($d = sql_get($r)) {
		$isInGroup = xmlHasSpecifiedNode($d['GROUPS'], array('tagname' => 'group', 'id' => $gid));
		if ($isInGroup) {
			$allmails[] = $d['EMAIL'];
		}
	}

	if (isset($allmails)) {
		$toppl = implode(",", $allmails);
		if (sendEmail($toppl, $subj, $body, SITE_EMAIL_AUTOMATED)) {
			echo "All the group users have been notified of your request";
		} else {echo "An error occured while sending the mail. Please try again later.";
		}
	} else {
		echo "No mails were sent to anyone";
	}
}

/**
 * Wrapper to email all the admins of a group
 * Params:
 * 	gid - id of group
 * subj
 * body
 *
 */
function mail_informGroupAdmins($gid, $subj, $body) {
	$q = "SELECT ADMINUSERS FROM groupslist WHERE id='$gid' LIMIT 1";
	$r = sql_execute($q);
	$d = sql_get($r);

	$doc = new DOMDocument;

	if ($d['ADMINUSERS'] == "") {
		$d['ADMINUSERS'] = '<root></root>';
	}

	$doc -> loadXML($d['ADMINUSERS']);
	$docRoot = $doc -> documentElement;

	foreach ($docRoot->childNodes as $child) {
		if ($child -> hasAttributes()) {
			$membersArr[] = $child -> getAttribute('id');
		}
	}

	if (isset($membersArr)) {
		$biguserlist = implode("','", $membersArr);
		$q = "SELECT EMAIL FROM members WHERE id IN ('" . $biguserlist . "')";
		$r = sql_execute($q);
		//$allmails = array();
		while ($d = sql_get($r)) {
			$allmails[] = $d['EMAIL'];
		}
	}

	if (isset($allmails)) {
		$toppl = implode(",", $allmails);
		sendEmail($toppl, $subj, $body, SITE_EMAIL_AUTOMATED);
		echo "Group admins have been notified of your request";
	} else {
		echo "No mails were sent to anyone";
	}
}

/**
 * Wrapper to send an email to site admin
 *
 */
function mail_informAdmin($subj, $body) {
	$to = SITE_EMAIL;
	$from = SITE_EMAIL;
	sendEmail($to, $subj, $body, $from);
}

/**
 * Function to send emails - always wrap this function
 * Param:
 * 	to - email adresses
 * subj - msg subject
 * body - msg content
 * from - from email address
 */
function sendEmail($to, $subj, $body, $from) {
	$fullsitelogo = '"' . "http://" . $_SERVER['HTTP_HOST'] . '/' . SITE_LOGO . '"';

	chdir(dirname(__FILE__));

	$head = file_get_contents(TEMPLATE_PATH . 'emails/header.php');

	$headshape = preg_replace('/REPLACE_SITE_NAME/', SITE_NAME, $head);
	$headshape = preg_replace('/REPLACE_SITE_LOGO/', $fullsitelogo, $headshape);

	$foot = file_get_contents(TEMPLATE_PATH . 'emails/footer.php');
	$footshape = preg_replace('/REPLACE_SITE_LOGO/', $fullsitelogo, $foot);

	$headers = "From: $from" . "\r\n" . "Reply-To: $from" . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	$nbody = $headshape . $body . $footshape;

	if (mail($to, $subj, $nbody, $headers)) {
		return true;
	} else {
		return false;
	}
}
?>
