<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

$id = $_GET['AttachmentId'];
if(common::isAdmin())
{
	$query = "Update attachment set ActiveInd='0' WHERE AttachmentId = '$id'";
}
else
{
	$query = "Update attachment set ActiveInd='0' WHERE AttachmentId = '$id' and ClientId=$LoggedInClientId";
}

mysql_select_db($database_conn, $conn);
mysql_query($query, $conn) or die(mysql_error());
$url = "viewproject.php?ProjectId=".$_GET[ProjectId];

if((string)$_GET[back] === 'project')
{
	$url = "projectattachments.php?ProjectId=".$_GET[ProjectId];
}
header("Location:$url");
exit;
?>
