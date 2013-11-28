<?php

require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

if(common::isUserWithLimitedAccess() === true) die("you do not have permissions to view this page");

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    $updateSQL = sprintf("UPDATE client SET ClientName=%s, Email=%s, InvoiceEmail=%s, ContactEmail=%s, Company=%s, Address=%s WHERE ClientId=%s",
            common::GetSQLValueString($_POST['ClientName'], "text"),
            common::GetSQLValueString($_POST['Email'], "text"),
            common::GetSQLValueString($_POST['InvoiceEmail'], "text"),
            common::GetSQLValueString($_POST['ContactEmail'], "text"),
            common::GetSQLValueString($_POST['Company'], "text"),
            common::GetSQLValueString($_POST['Address'], "text"),
            common::GetSQLValueString($_POST['ClientId'], "int"));

    mysql_select_db($database_conn, $conn);
    $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());

    // $updateGoTo = "myaccount.php";
    // if (isset($_SERVER['QUERY_STRING'])) {
    // $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    // $updateGoTo .= $_SERVER['QUERY_STRING'];
    // }
    // header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsClient = $LoggedInClientId;
mysql_select_db($database_conn, $conn);
$query_rsClient = sprintf("SELECT * FROM client c inner join discounttype dt on c.DiscountTypeId = dt.DiscountTypeId WHERE ClientId = %s", common::GetSQLValueString($colname_rsClient, "int"));
$query_limit_rsClient = sprintf("%s LIMIT 1", $query_rsClient);
$rsClient = mysql_query($query_limit_rsClient, $conn) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);

require_once('Includes/header.php');
?>
<table border="1" cellpadding="5" cellspacing="2" valign="top" style="border-collapse: collapse" bordercolor="#efefef" align="center">
    <tr>
        <td>Client Name</td>
        <td><?php echo $row_rsClient['ClientName']; ?></td>
    </tr>
    <tr>
        <td>Currency</td>
        <td><?php echo $row_rsClient['Currency']; ?></td>
    </tr>
    <tr>
        <td>Rate</td>
        <td><?php echo $row_rsClient['Rate']; ?></td>
    </tr>
    <tr>
        <td>Discount</td>
        <td><?php echo $row_rsClient['Discount']; ?></td>
    </tr>
    <tr>
        <td>Discount Type</td>
        <td><?php echo $row_rsClient['DiscountType']; ?>&nbsp;<?php echo $row_rsClient['Comment']; ?></td>
    </tr>
    <tr>
        <td>Primary Email</td>
        <td><?php echo $row_rsClient['Email']; ?></td>
    </tr>
    <tr>
        <td>Invoice Email</td>
        <td><?php echo $row_rsClient['InvoiceEmail']; ?></td>
    </tr>
    <tr>
        <td>Contact Email</td>
        <td><?php echo $row_rsClient['ContactEmail']; ?></td>
    </tr>
    <tr>
        <td>Company</td>
        <td><?php echo $row_rsClient['Company']; ?></td>
    </tr>
    <tr>
        <td>Address</td>
        <td><?php echo nl2br($row_rsClient['Address']); ?></td>
    </tr>
</tr>
</table>
<p>&nbsp;</p>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
    <table cellpadding="5" cellspacing="1" valign="top" align="center">
        <tr valign="baseline">
            <td nowrap="nowrap" align="right">ClientName:</td>
            <td><input type="text" name="ClientName" value="<?php echo htmlentities($row_rsClient['ClientName'], ENT_COMPAT, ''); ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
            <td nowrap="nowrap" align="right">Email:</td>
            <td><input type="text" name="Email" value="<?php echo htmlentities($row_rsClient['Email'], ENT_COMPAT, ''); ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
            <td nowrap="nowrap" align="right">Invoice Email:</td>
            <td><input type="text" name="InvoiceEmail" value="<?php echo htmlentities($row_rsClient['InvoiceEmail'], ENT_COMPAT, ''); ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
            <td nowrap="nowrap" align="right">Contact Email:</td>
            <td><input type="text" name="ContactEmail" value="<?php echo htmlentities($row_rsClient['ContactEmail'], ENT_COMPAT, ''); ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
            <td nowrap="nowrap" align="right">Company:</td>
            <td><input type="text" name="Company" value="<?php echo htmlentities($row_rsClient['Company'], ENT_COMPAT, ''); ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
            <td nowrap="nowrap" align="right" valign="top">Address:</td>
            <td><textarea name="Address" cols="50" rows="5"><?php echo htmlentities($row_rsClient['Address'], ENT_COMPAT, ''); ?></textarea></td>
        </tr>
        <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td>
                <br />
                <a href="javascript:document.form1.submit();void(0);"><img style="vertical-align:middle;text-align:center;" src="images/submit.png" alt="Submit Project"></a>
                &nbsp;<a href="javascript:history.back();void(0);"><img style="vertical-align:bottom;text-align:bottom;" src="images/cancel.png" alt="Cancel and go back"></a></td>
            </td>
        </tr>
    </table>
    <input type="hidden" name="MM_update" value="form1" />
    <input type="hidden" name="ClientId" value="<?php echo $row_rsClient['ClientId']; ?>" />
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?
require_once('Includes/footer.php');
?>
<?php
mysql_free_result($rsClient);
?>
