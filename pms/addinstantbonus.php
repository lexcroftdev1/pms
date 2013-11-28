<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}   

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO purchaseorder (AgreedRate, AgreedDiscount, AgreedDiscountTypeId, AgreedHours, ProjectId,PurchaseOrderStatusId) VALUES (%s, %s, %s, %s, %s,'25')",
                       common::GetSQLValueString($_POST['AgreedRate'], "double"),
                       common::GetSQLValueString($_POST['AgreedDiscount'], "double"),
                       common::GetSQLValueString($_POST['AgreedDiscountTypeId'], "int"),
                       common::GetSQLValueString($_POST['AgreedHours'], "double"),
                       common::GetSQLValueString($_POST['ProjectId'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());

  $PurchaseOrderId = mysql_insert_id();


if(!empty($_POST['Note']))
{
	$sql = sprintf("INSERT INTO `note` (Note,NoteTitle,ClientId) VALUES (%s,'',%s)",
					   common::GetSQLValueString($_POST['Note'], "text"),
					   common::GetSQLValueString($LoggedInClientId, "int"));

	mysql_query($sql, $conn) or die(mysql_error());

	$NoteId = mysql_insert_id();

	$sql = sprintf("INSERT INTO ponote(NoteId,PurchaseOrderId) VALUES (%s,%s)",
					   common::GetSQLValueString($NoteId, "text"),
					   common::GetSQLValueString($PurchaseOrderId, "text"));
	mysql_query($sql, $conn) or die(mysql_error());
}

	$m = new POMail($PurchaseOrderId);
	$m->sendBonusMail();

  $insertGoTo = "viewproject.php?ProjectId=".$_GET[ProjectId];
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsClient = "-1";
if (isset($_GET['ProjectId'])) {
  $colname_rsClient = $_GET['ProjectId'];
}
mysql_select_db($database_conn, $conn);
$query_rsClient = sprintf("SELECT c.* FROM client c inner join project p on c.ClientId = p.ClientId WHERE ProjectId = %s", common::GetSQLValueString($colname_rsClient, "int"));
$rsClient = mysql_query($query_rsClient, $conn) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);

mysql_select_db($database_conn, $conn);
$query_rsDiscountType = "SELECT * FROM discounttype";
$rsDiscountType = mysql_query($query_rsDiscountType, $conn) or die(mysql_error());
$row_rsDiscountType = mysql_fetch_assoc($rsDiscountType);
$totalRows_rsDiscountType = mysql_num_rows($rsDiscountType);
require_once('Includes/header.php');
?>
<h1>Add Purchase order</h1>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <input type="hidden" name="AgreedDiscount" value="0" />
  <input type="hidden" name="AgreedDiscountTypeId" value="1" />
  <input type="hidden" name="AgreedHours" value="1" />

  <table cellpadding="15" cellspacing="1" valign="top" align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Award Bonus :</td>
      <td><input type="text" name="AgreedRate" value="" size="32" /></td>
    </tr>
     <tr>
    	<td nowrap="nowrap" align="right">Additional Notes:</td>
    	<td>
        	<textarea style="width:620px;height:400px;font-size:12px" name="Note"></textarea>
        </td>
     </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td>
		<a href="javascript:document.form1.submit();void(0);"><img style="vertical-align:middle;text-align:center;" src="images/submit.png" alt="Award Bonus"></a>
		&nbsp;<a href="javascript:history.back();void(0);"><img style="vertical-align:bottom;text-align:bottom;" src="images/cancel.png" alt="Cancel and go back"></a></td>
      </td>
    </tr>
  </table>
  <input type="hidden" name="ProjectId" value="<?php echo $_GET[ProjectId]; ?>" />
  <input type="hidden" name="MM_insert" value="form1" />
</form>
<p>&nbsp;</p>
<?php
mysql_free_result($rsClient);
mysql_free_result($rsDiscountType);
?>
<?php require_once('Includes/footer.php'); ?>