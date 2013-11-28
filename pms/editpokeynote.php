<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

	if(common::isAdmin())
	{
		$updateSQL = sprintf("UPDATE note SET NoteTitle=%s,Note=%s WHERE NoteId=%s",
	                       common::GetSQLValueString($_POST['NoteTitle'], "text"),
	                       common::GetSQLValueString($_POST['Note'], "text"),
	                       common::GetSQLValueString($_POST['NoteId'], "int"));

	}
	else
	{
		$updateSQL = sprintf("UPDATE note n inner join client c on n.ClientId = c.ClientId set SET n.NoteTitle=%s,n.Note=%s WHERE NoteId=%s and c.ClientId=$LoggedInClientId",
	                       common::GetSQLValueString($_POST['NoteTitle'], "text"),
	                       common::GetSQLValueString($_POST['Note'], "text"),
	                       common::GetSQLValueString($_POST['NoteId'], "int"));
	}

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());

 $updateGoTo = "pokeynotes.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsPost = "-1";
if (isset($_GET['NoteId'])) {
  $colname_rsPost = $_GET['NoteId'];
}
mysql_select_db($database_conn, $conn);
$query_rsPost = sprintf("SELECT n.* from note n inner join client c on n.ClientId=c.ClientId WHERE n.NoteId = %s and c.ClientId=$LoggedInClientId", common::GetSQLValueString($colname_rsPost, "int"));

$rs = mysql_query($query_rsPost, $conn) or die(mysql_error());
$row = mysql_fetch_assoc($rs);

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
      <td nowrap="nowrap" align="right">Title:</td>
      <td><input style="width:600px;height:30px;" type="text" name="NoteTitle" value="<?php echo htmlentities($row['NoteTitle'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
         <tr>
        	<td align="right" valign="top"><strong>Note:</strong></td>
            <td><textarea style="width:600px;height:400px" name="Note"><?php echo htmlentities($row['Note'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
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
  <input type="hidden" name="NoteId" value="<?php echo $row['NoteId']; ?>" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rs);
?>
<?php require_once('Includes/footer.php'); ?>