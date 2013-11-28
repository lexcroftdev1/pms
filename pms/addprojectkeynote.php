<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

mysql_select_db($database_conn, $conn);

$sql = sprintf("INSERT INTO `note` (Note,NoteTitle,ClientId) VALUES (%s,%s,%s)",
				   common::GetSQLValueString($_POST['Note'], "text"),
				   common::GetSQLValueString($_POST['NoteTitle'], "text"),
				   common::GetSQLValueString($LoggedInClientId, "int"));

mysql_query($sql, $conn) or die(mysql_error());

$NoteId = mysql_insert_id();

$sql = sprintf("INSERT INTO projectnote(NoteId,ProjectId) VALUES (%s,%s)",
				   common::GetSQLValueString($NoteId, "text"),
				   common::GetSQLValueString($_GET[ProjectId], "text"));
mysql_query($sql, $conn) or die(mysql_error());



$insertGoTo = "projectkeynotes.php";
if (isset($_SERVER['QUERY_STRING']))
{
	$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	$insertGoTo .= $_SERVER['QUERY_STRING'];
}

header(sprintf("Location: %s", $insertGoTo));
?>