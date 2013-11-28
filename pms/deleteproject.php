<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');
if (!empty($_GET['ProjectId']) && (int)$UserGroup === 1)
{
  $deleteSQL = sprintf("UPDATE project set ActiveInd = '0' WHERE ProjectId=%s",
                       common::GetSQLValueString($_GET['ProjectId'], "int"));

  mysql_select_db($database_conn, $conn);
  mysql_query($deleteSQL, $conn) or die(mysql_error());

}
  $deleteGoTo = "index.php";
  header(sprintf("Location: %s", $deleteGoTo));
?>
