<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

if(common::isUserWithLimitedAccess() === true) die("you do not have permissions to view this page");

$maxRows_Recordset1 = 25;
$pageNum_Recordset1 = 0;
if (isset($_GET['pageNum_Recordset1'])) {
    $pageNum_Recordset1 = $_GET['pageNum_Recordset1'];
}
$startRow_Recordset1 = $pageNum_Recordset1 * $maxRows_Recordset1;
mysql_select_db($database_conn, $conn);
$query_Recordset1 = sprintf("SELECT * FROM invoice WHERE (FromClientId = %s OR ToClientId = %s) ORDER BY InvoiceDate DESC", common::GetSQLValueString($LoggedInClientId, "int"),common::GetSQLValueString($LoggedInClientId, "int"));
$query_limit_Recordset1 = sprintf("%s LIMIT %d, %d", $query_Recordset1, $startRow_Recordset1, $maxRows_Recordset1);
$Recordset1 = mysql_query($query_limit_Recordset1, $conn) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);

if (isset($_GET['totalRows_Recordset1'])) {
    $totalRows_Recordset1 = $_GET['totalRows_Recordset1'];
} else {
    $all_Recordset1 = mysql_query($query_Recordset1);
    $totalRows_Recordset1 = mysql_num_rows($all_Recordset1);
}
$totalPages_Recordset1 = ceil($totalRows_Recordset1/$maxRows_Recordset1)-1;
require_once('Includes/header.php');
?>
<div style="float:left;margin-bottom:10px;margin-left:55px;vertical-align:middle;">
    <h1>MY Invoices</h1>
</div>
<br clear="all" />
<table border="1" cellpadding="5" cellspacing="2" valign="top" width="90%" style="border-collapse:collapse;overflow:auto;" bordercolor="#efefef" align="center">
    <?php if ($totalRows_Recordset1 > 0) { ?>
    <tr>
        <th>Invoice #</th>
        <th>From</th>
        <th>To</th>
        <th>Date</th>
    </tr>
        <?php do {

            $query_rsPOs = "SELECT ClientName from client where ClientId=".common::GetSQLValueString($row_Recordset1['FromClientId'], "int")." Limit 1";
            $rsSender = mysql_query($query_rsPOs, $conn) or die(mysql_error());
            $row_rsSender = mysql_fetch_assoc($rsSender);

            $query_rsPOs = "SELECT ClientName from client where ClientId=".common::GetSQLValueString($row_Recordset1['ToClientId'], "int")." Limit 1";
            $rsRecipient = mysql_query($query_rsPOs, $conn) or die(mysql_error());
            $row_rsRecipient = mysql_fetch_assoc($rsRecipient);

            ?>
    <tr>
        <td><?php echo $row_Recordset1['InvoiceNumber']; ?></td>
        <td><?php echo $row_rsSender['ClientName']; ?></td>
        <td><?php echo $row_rsSender['ClientName']; ?></td>
        <td><?php echo $row_Recordset1['InvoiceDate']; ?></td>
        <td><a href="invoiceHTML.php?InvoiceId=<?php echo $row_Recordset1['InvoiceId']; ?>" target="_blank">Printable</a></td>
        <td><a href="invoicePDF.php?InvoiceId=<?php echo $row_Recordset1['InvoiceId']; ?>">Download PDF</a></td>
    </tr>
            <?php } while ($row_Recordset1 = mysql_fetch_assoc($Recordset1));
    }
    else
        echo '<tr><td>No invoices found</td></tr>';
    ?>
</table>

<?php
require_once('Includes/footer.php');
?>
<?php
mysql_free_result($Recordset1);
?>
