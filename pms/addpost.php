<?php

if(empty($_POST['PostData'])) die('Empty posts are not allowed. hit your browser\'s back button to go back to the prebious page.');
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');
mysql_select_db($database_conn, $conn);
$insertSQL = sprintf("INSERT INTO projectpost (ProjectId, PostTitle,PosterId,ActiveInd) VALUES (%s, %s,$LoggedInClientId,'1')",
				   common::GetSQLValueString($_GET['ProjectId'], "int"),
				   common::GetSQLValueString($_POST['PostTitle'], "text"));

$Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());

$PostId = mysql_insert_id();

$insertSQL = sprintf("INSERT INTO postdata (PostId, PostData) VALUES (%s, %s)",
				   common::GetSQLValueString($PostId, "int"),
				   common::GetSQLValueString(htmlentities($_POST['PostData'],ENT_QUOTES), "text"));
$Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());


if ($_FILES['Attachment']['size'] >  0)
{
	$AttachmentId = md5(uniqid(mt_rand(), true));
	$fileName = $_FILES['Attachment']['name'];
	$fileSize = $_FILES['Attachment']['size'];
	$fileType = $_FILES['Attachment']['type'];

	$extension= '.'.end(explode(".", $_FILES['Attachment']['name']));
	$target = UPLOADPATH.$AttachmentId.$extension;
	if(!move_uploaded_file($_FILES['Attachment']['tmp_name'], $target)) die($_FILES["Attachment"]["error"]);

	$insertSQL = "INSERT INTO attachment(AttachmentId, AttachmentName,AttachmentSize,AttachmentType,ClientId) VALUES (".common::GetSQLValueString($AttachmentId, "text").",".common::GetSQLValueString($fileName, "text").",".common::GetSQLValueString($fileSize, "text").",".common::GetSQLValueString($fileType, "text").",'$LoggedInClientId')";
  	mysql_select_db($database_conn, $conn);
	mysql_query($insertSQL, $conn) or die(mysql_error());

	$insertSQL = "INSERT INTO postattachment (PostId,AttachmentId) VALUES ('$PostId',".common::GetSQLValueString($AttachmentId, "text").")";
	mysql_query($insertSQL, $conn) or die(mysql_error());
}

$m = new PostMail($PostId);
$m->sendPostMail();

$insertGoTo = "viewproject.php";
if (isset($_SERVER['QUERY_STRING']))
{
	$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	$insertGoTo .= $_SERVER['QUERY_STRING'];
}
header(sprintf("Location: %s", $insertGoTo));
?>
