<?php require_once('Includes/conn.php'); ?>
<?php
mysql_select_db($database_conn, $conn);
$sql = "SELECT project.ProjectStatusId, ps.ProjectStatus FROM project inner join projectstatus ps on project.ProjectStatusId=ps.ProjectStatusId where ProjectId = ".$_GET['ProjectId'];
$rs = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_fetch_assoc($rs);

if(!common::isAdmin() && ((int)$row['ProjectStatusId'] > 80)) exit;

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
		$deleteSQL = sprintf("Update purchaseorder SET PurchaseOrderStatusId='20', Comments='Approved by $clientname on behalf of client. Client Id is $LoggedInClientId', TimeStampWhenActed=now() WHERE PurchaseOrderId=%s",
		common::GetSQLValueString($_GET['PurchaseOrderId'], "int"));
	}
	else
	{
		$deleteSQL = sprintf("Update purchaseorder po inner join project p on po.ProjectId=p.ProjectId inner join client c on c.ClientId=p.ClientId SET PurchaseOrderStatusId='20', Comments='Approved by $clientname. Client Id is $LoggedInClientId', po.TimeStampWhenActed=now() WHERE po.PurchaseOrderId=%s and c.ClientId =$LoggedInClientId",
						   common::GetSQLValueString($_GET['PurchaseOrderId'], "int"));

	}


  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($deleteSQL, $conn) or die(mysql_error());

$deleteSQL = sprintf("Update project SET ProjectStatusId='30' WHERE ProjectId=%s",
	common::GetSQLValueString($_GET['ProjectId'], "int"));
	mysql_query($deleteSQL, $conn) or die(mysql_error());

	$PurchaseOrderId = $_GET['PurchaseOrderId'];
	$m = new POMail($_GET['PurchaseOrderId'],true);
	$m->sendPurchaseOrderStatusChangedMail();

  $deleteGoTo = "viewproject.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
?>
