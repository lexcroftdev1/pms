<?php
require_once('../Includes/conn.php');
require_once('../Includes/isUserLoggedIn.php');
if (common::isAdmin() === true) {
mysql_select_db($database_conn, $conn);
$sql = sprintf("Update project SET ProjectDescription=%s where ProjectId=%d",
				   common::GetSQLValueString($_POST['ProjectDescription'], "text"),
				   common::GetSQLValueString($_POST['ProjectId'], "int"));
mysql_query($sql, $conn) or die(mysql_error());

$sql = sprintf("SELECT ProjectDescription FROM project WHERE ProjectId=%s limit 1", common::GetSQLValueString($_POST['ProjectId'], "int"));

$rs = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_fetch_assoc($rs);

echo common::makelink(nl2br($row['ProjectDescription']));
}?>
