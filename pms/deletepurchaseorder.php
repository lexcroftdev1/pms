<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');
mysql_select_db($database_conn, $conn);
$query_rsClient = "SELECT ClientName FROM  client WHERE ClientId = $LoggedInClientId";
//echo $query_rsClient;exit;
$rsClient = mysql_query($query_rsClient, $conn) or die(mysql_error());

$row= mysql_fetch_assoc($rsClient);
$clientname = $row[ClientName];



if ((isset($_GET['PurchaseOrderId'])) && ($_GET['PurchaseOrderId'] != ""))
{
	if((int)$UserGroup === 1)
	{
		$deleteSQL = sprintf("Update purchaseorder SET ActiveInd='0', Comments='Deleted by $clientname on behalf of client. Client Id is $LoggedInClientId' WHERE PurchaseOrderId=%s",
		common::GetSQLValueString($_GET['PurchaseOrderId'], "int"));
	}
	else
	{
		$deleteSQL = sprintf("Update purchaseorder po inner join project p on po.ProjectId=p.ProjectId inner join client c on c.ClientId=p.ClientId SET po.ActiveInd='0', Comments='Deleted by  $clientname. Client Id is $LoggedInClientId' WHERE po.PurchaseOrderId=%s and c.ClientId =$LoggedInClientId",
						   common::GetSQLValueString($_GET['PurchaseOrderId'], "int"));

	}

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($deleteSQL, $conn) or die(mysql_error());

	$PurchaseOrderId = $_GET['PurchaseOrderId'];
	$m = new POMail($_GET['PurchaseOrderId']);
	$m->sendPODeletedMail();

  $deleteGoTo = "viewproject.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
?>
