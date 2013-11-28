<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');


mysql_select_db($database_conn, $conn);
$sql = "SELECT ProjectStatusId FROM project where ProjectId = ".$_GET['ProjectId'];
$rs = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_fetch_assoc($rs);
if(!common::isAdmin() && ((int)$row['ProjectStatusId'] >= 80))exit;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE purchaseorder SET AgreedRate=%s, AgreedDiscount=%s, AgreedDiscountTypeId=%s, AgreedHours=%s, PurchaseOrderStatusId='10' WHERE PurchaseOrderId=%s",
                       common::GetSQLValueString($_POST['AgreedRate'], "double"),
                       common::GetSQLValueString($_POST['AgreedDiscount'], "double"),
                       common::GetSQLValueString($_POST['AgreedDiscountTypeId'], "int"),
                       common::GetSQLValueString($_POST['AgreedHours'], "double"),
                       common::GetSQLValueString($_POST['PurchaseOrderId'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());

	$PurchaseOrderId = $_GET['PurchaseOrderId'];
	$m = new POMail($_GET['PurchaseOrderId']);
	$m->sendPOChangedMail();

	$deleteSQL = sprintf("Update project SET ProjectStatusId='20' WHERE ProjectId=%s",
	common::GetSQLValueString($_GET['ProjectId'], "int"));
	mysql_query($deleteSQL, $conn) or die(mysql_error());

	$updateGoTo = "viewproject.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsClient = "-1";
if (isset($_GET['ProjectId'])) {
  $colname_rsClient = $_GET['ProjectId'];
}

mysql_select_db($database_conn, $conn);
$query_rsDiscountType = "SELECT * FROM discounttype";
$rsDiscountType = mysql_query($query_rsDiscountType, $conn) or die(mysql_error());
$row_rsDiscountType = mysql_fetch_assoc($rsDiscountType);
$totalRows_rsDiscountType = mysql_num_rows($rsDiscountType);

$colname_rsPO = "-1";
if (isset($_GET['PurchaseOrderId'])) {
  $colname_rsPO = $_GET['PurchaseOrderId'];
}
mysql_select_db($database_conn, $conn);
$query_rsPO = sprintf("SELECT * FROM purchaseorder WHERE PurchaseOrderId = %s", common::GetSQLValueString($colname_rsPO, "int"));
$rsPO = mysql_query($query_rsPO, $conn) or die(mysql_error());
$row_rsPO = mysql_fetch_assoc($rsPO);
$totalRows_rsPO = mysql_num_rows($rsPO);

require_once('Includes/header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
  <table cellpadding="5" cellspacing="1" valign="top" align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Rate:</td>
      <td><input type="text" name="AgreedRate" value="<?php echo htmlentities($row_rsPO['AgreedRate'], ENT_COMPAT, 'utf-8'); ?>" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Discount:</td>
      <td><input type="text" name="AgreedDiscount" value="<?php echo htmlentities($row_rsPO['AgreedDiscount'], ENT_COMPAT, 'utf-8'); ?>" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Discount Type:</td>
      <td><select name="AgreedDiscountTypeId">
        <?php
do {
?>
        <option value="<?php echo $row_rsDiscountType['DiscountTypeId']?>" <?php if (!(strcmp($row_rsDiscountType['DiscountTypeId'], htmlentities($row_rsPO['AgreedDiscountTypeId'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_rsDiscountType['DiscountType']?></option>
        <?php
} while ($row_rsDiscountType = mysql_fetch_assoc($rsDiscountType));
?>
      </select></td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Hours:</td>
      <td><input type="text" name="AgreedHours" value="<?php echo htmlentities($row_rsPO['AgreedHours'], ENT_COMPAT, 'utf-8'); ?>" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td>
			<a href="javascript:document.form2.submit();void(0);"><img style="vertical-align:middle;text-align:center;" src="images/submit.png" alt="Submit Project"></a>
			&nbsp;<a href="javascript:history.back();void(0);"><img style="vertical-align:bottom;text-align:bottom;" src="images/cancel.png" alt="Cancel and go back"></a></td>
      </td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form2" />
  <input type="hidden" name="PurchaseOrderId" value="<?php echo $row_rsPO['PurchaseOrderId']; ?>" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsDiscountType);

mysql_free_result($rsPO);
?>
<?php require_once('Includes/footer.php'); ?>