<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1"))
{
	$updateSQL = sprintf("UPDATE project SET ProjectStatusId=%s WHERE ProjectId=%s",common::GetSQLValueString($_POST['ProjectStatusId'], "int"),common::GetSQLValueString($_POST['ProjectId'], "int"));
	mysql_select_db($database_conn, $conn);
	mysql_query($updateSQL, $conn) or die(mysql_error());


	$m = new ProjectMail($_GET['ProjectId']);
	$m->sendProjectStatusChangedMail();

	$updateGoTo = "index.php";
	if (isset($_SERVER['QUERY_STRING']))
	{
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $_SERVER['QUERY_STRING'];
	 }
	header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsProject = "-1";
if (isset($_GET['ProjectId'])) {
  $colname_rsProject = $_GET['ProjectId'];
}
mysql_select_db($database_conn, $conn);
$query_rsProject = sprintf("SELECT ProjectStatusId FROM project WHERE ProjectId = %s", common::GetSQLValueString($colname_rsProject, "int"));
$rsProject = mysql_query($query_rsProject, $conn) or die(mysql_error());
$row_rsProject = mysql_fetch_assoc($rsProject);
$totalRows_rsProject = mysql_num_rows($rsProject);

require_once('Includes/header.php');
?>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right"></td>
      <td>
          <?php echo common::SelectByEnum('ProjectStatusId','projectstatus',$row_rsProject['ProjectStatusId'],false,'style="width:230px;font-size:15px;"'); ?>
       </td>
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
  <input type="hidden" name="ProjectId" value="<?php echo $_GET['ProjectId']; ?>" />
</form>
<p>&nbsp;</p>
<?php mysql_free_result($rsProject); ?>
<?php require_once('Includes/footer.php'); ?>