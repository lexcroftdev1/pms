<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

if(common::isUserWithLimitedAccess() === true) die("you do not have permissions to view this page");

mysql_select_db($database_conn, $conn);
$sql = sprintf("SELECT InvoiceReportHTML FROM invoicereport WHERE InvoiceId = %s LIMIT 1", common::GetSQLValueString($_GET['InvoiceId'], "int"));
$Recordset1 = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_fetch_assoc($Recordset1);
?>
<table border="0" cellpadding="5" cellspacing="2" valign="top" width="90%" align="center">
    <tr>
        <td width="100%" align="left"><?php echo $row['InvoiceReportHTML']; ?></td>
    </tr>
</table>
<script language="Javascript1.2">
    <!--
    function printpage() {
        window.print();
    }
    window.onload = printpage;
    //-->
</script>
<?php
mysql_free_result($Recordset1);
?>
