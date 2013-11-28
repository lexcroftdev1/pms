<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

	$updateSQL = sprintf("UPDATE projectpost SET PostTitle=%s WHERE PostId=%s",
                       common::GetSQLValueString($_POST['PostTitle'], "text"),
                       common::GetSQLValueString($_GET['PostId'], "int"));

	$updateSQL = sprintf("UPDATE postdata SET PostData=%s WHERE PostId=%s",
                       common::GetSQLValueString($_POST['PostData'], "text"),
                       common::GetSQLValueString($_GET['PostId'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());

  $updateGoTo = "viewproject.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsPost = "-1";
if (isset($_GET['PostId'])) {
  $colname_rsPost = $_GET['PostId'];
}
mysql_select_db($database_conn, $conn);
$query_rsPost = sprintf("SELECT PostTitle,PostData from projectpost pp inner join postdata pd on pp.PostId=pd.PostId WHERE pp.PostId = %s", common::GetSQLValueString($colname_rsPost, "int"));

$rsPost = mysql_query($query_rsPost, $conn) or die(mysql_error());
$row_rsPost = mysql_fetch_assoc($rsPost);
$totalRows_rsPost = mysql_num_rows($rsPost);

require_once('Includes/header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>
<h1>Edit Post</h1>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table cellpadding="5" cellspacing="1" valign="top" align="center" width="90%">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">PostTitle:</td>
      <td><input style="width:600px;height:30px;" type="text" name="PostTitle" value="<?php echo htmlentities($row_rsPost['PostTitle'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
         <tr>
        	<td align="right" valign="top"><strong>Comments:</strong></td>
            <td><textarea style="width:600px;height:400px" name="PostData"><?php echo htmlentities($row_rsPost['PostData'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
         </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td>
		<a href="javascript:document.form1.submit();void(0);"><img style="vertical-align:middle;text-align:center;" src="images/submit.png" alt="Submit Project"></a>
		&nbsp;<a href="javascript:history.back();void(0);"><img style="vertical-align:bottom;text-align:bottom;" src="images/cancel.png" alt="Cancel and go back"></a></td>
      </td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="PostId" value="<?php echo $row_rsPost['PostId']; ?>" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsPost);
?>
<?php require_once('Includes/footer.php'); ?>