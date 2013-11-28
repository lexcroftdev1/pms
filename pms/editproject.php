<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

	$updateSQL = sprintf("UPDATE project " .
						" SET ProjectTitle=%s,ProjectDescription=%s,ProjectStartDate=STR_TO_DATE(%s,'%%m/%%d/%%Y %%H:%%i:%%s'),ProjectEndDate=STR_TO_DATE(%s,'%%m/%%d/%%Y %%H:%%i:%%s'),ProjectPriorityId=%s WHERE ProjectId=%s",
                       common::GetSQLValueString($_POST['ProjectTitle'], "text"),
                       common::GetSQLValueString($_POST['ProjectDescription'], "text"),
                       common::GetSQLValueString($_POST['ProjectStartDate'], "text"),
                       common::GetSQLValueString($_POST['ProjectEndDate'], "text"),
                       common::GetSQLValueString($_POST['ProjectPriorityId'], "text"),
                       common::GetSQLValueString($_POST['ProjectId'], "int"));

  mysql_select_db($database_conn, $conn);
  mysql_query($updateSQL, $conn) or die(mysql_error());
  //echo $updateSQL;exit;
  $updateGoTo = "index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsPost = "-1";
if (isset($_GET['ProjectId'])) {
  $colname_rsPost = $_GET['ProjectId'];
}
mysql_select_db($database_conn, $conn);
$sql = "SELECT ProjectTitle,ProjectDescription,".common::SQLShortDate('ProjectStartDate')." ProjectStartDate,".common::SQLShortDate('ProjectEndDate')." ProjectEndDate, ProjectPriorityId from project WHERE ProjectId=".common::GetSQLValueString($colname_rsPost, "int");

$rs = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_fetch_assoc($rs);

require_once('Includes/header.php');
?>
<script type="text/javascript">
	$(function()
	{
		$('#ProjectStartDate').datepicker();
		$('#ProjectEndDate').datepicker();
	});
</script>

<h1>Add new porject</h1>

<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">

  <table cellpadding="5" cellspacing="1" valign="top" align="center" width="90%">

    	<tr>

        	<td align="right" valign="top"><strong>Title</strong></td><td>

            	<input style="width:600px;height:30px;" type="text" name="ProjectTitle" value="<?php echo $row[ProjectTitle];?>" />

            </td>

        </tr>

         <tr>

        	<td align="right" valign="top"><strong>Description</strong></td>

            <td><textarea style="width:600px;height:400px" name="ProjectDescription"><?php echo $row[ProjectDescription];?></textarea></td>

         </tr>
         <tr>
        	<td align="right" valign="top"><strong>Priority</strong></td>
            <td><?php echo common::SelectByEnum('ProjectPriorityId','projectpriority',$row[ProjectPriorityId])?></td>
         </tr>
         <tr>
        	<td align="right" valign="top"><strong>Start date</strong></td>
            <td>
				<input type="text" id="ProjectStartDate" name="ProjectStartDate" value="<?php echo $row[ProjectStartDate];?>" />
            </td>
         </tr>
         <tr>
        	<td align="right" valign="top"><strong>End date</strong></td>
            <td>
				<input type="text" id="ProjectEndDate" name="ProjectEndDate" value="<?php echo $row[ProjectEndDate];?>" />
            </td>
         </tr>
         <tr>

           <td></td>

            <td align="left">
			<a href="javascript:document.form1.submit();void(0);"><img style="vertical-align:middle;text-align:center;" src="images/submit.png" alt="Submit Project"></a>
            &nbsp;<a href="javascript:history.back();void(0);"><img style="vertical-align:bottom;text-align:bottom;" src="images/cancel.png" alt="Cancel and go back"></a></td>

         </tr>

		<input type="hidden" name="ProjectId" value="<?php echo $_GET[ProjectId]; ?>" />
  		<input type="hidden" name="MM_update" value="form1" />

  	</table>

</form>

<?php require_once('Includes/footer.php'); ?>