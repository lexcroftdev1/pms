<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

if(!common::isAdmin())exit;

if ((isset($_GET['PostId'])) && ($_GET['PostId'] != "")) {
  $deleteSQL = sprintf("UPDATE projectpost set ActiveInd = '0' WHERE PostId=%s",
                       common::GetSQLValueString($_GET['PostId'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($deleteSQL, $conn) or die(mysql_error());

  $deleteGoTo = "viewproject.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
?>
