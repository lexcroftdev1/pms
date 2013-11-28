<?php
require_once("Includes/dompdf/dompdf_config.inc.php");
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

if(common::isUserWithLimitedAccess() === true) die("you do not have permissions to view this page");

mysql_select_db($database_conn, $conn);
$sql = sprintf("SELECT InvoiceReportHTML FROM invoicereport WHERE InvoiceId = %s LIMIT 1", common::GetSQLValueString($_GET['InvoiceId'], "int"));
$Recordset1 = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_fetch_assoc($Recordset1);

$dompdf = new DOMPDF();
$dompdf->load_html($row['InvoiceReportHTML']);
$dompdf->set_paper('letter', 'portrait');
$dompdf->render();

$dompdf->stream("invoice.pdf");
exit(0);

mysql_free_result($Recordset1);
?>
