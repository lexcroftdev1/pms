<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1") && $_FILES['Attachment']['size'] >  0)
{
	$AttachmentId = md5(uniqid(mt_rand(), true));
	$fileName = $_FILES['Attachment']['name'];
	$fileSize = $_FILES['Attachment']['size'];
	$fileType = $_FILES['Attachment']['type'];

	$extension= '.'.end(explode(".", $_FILES['Attachment']['name']));
	$target = UPLOADPATH.$AttachmentId.$extension;
	if(!move_uploaded_file($_FILES['Attachment']['tmp_name'], $target)) die($_FILES["Attachment"]["error"]);

	$insertSQL = "INSERT INTO attachment(AttachmentId, AttachmentName,AttachmentSize,AttachmentType,ClientId) VALUES (".common::GetSQLValueString($AttachmentId, "text").",".common::GetSQLValueString($fileName, "text").",".common::GetSQLValueString($fileSize, "text").",".common::GetSQLValueString($fileType, "text").",'$LoggedInClientId')";
  	mysql_select_db($database_conn, $conn);
	mysql_query($insertSQL, $conn) or die(mysql_error());

	$insertSQL = "INSERT INTO projectattachment (ProjectId,AttachmentId) VALUES ('$_GET[ProjectId]',".common::GetSQLValueString($AttachmentId, "text").")";
	mysql_query($insertSQL, $conn) or die(mysql_error());
}


$colname_rsProjectAttachment = "-1";
if (isset($_GET['ProjectId'])) {
  $colname_rsProjectAttachment = $_GET['ProjectId'];
}
mysql_select_db($database_conn, $conn);
$query_rsProjectAttachment = sprintf("SELECT a.AttachmentId,AttachmentName,DateWhenCreated FROM attachment a inner join projectattachment pa on a.AttachmentId = pa.AttachmentId WHERE ProjectId = %s", common::GetSQLValueString($colname_rsProjectAttachment, "int"));
$rsProjectAttachment = mysql_query($query_rsProjectAttachment, $conn) or die(mysql_error());
$row_rsProjectAttachment = mysql_fetch_assoc($rsProjectAttachment);
$totalRows_rsProjectAttachment = mysql_num_rows($rsProjectAttachment);
require_once('Includes/header.php');

?>
<h1>Project Documents</h1><br clear="all" />
<ol>
  <?php
if($totalRows_rsProjectAttachment >0)
{
 do { ?>
      <li><?php echo $row_rsProjectAttachment['AttachmentName']; ?> created on <?php echo $row_rsProjectAttachment['DateWhenCreated']; ?>&nbsp;<a href="downloadattachment.php?AttachmentId=<?php echo $row_rsProjectAttachment['AttachmentId']; ?>">Download</a>&nbsp;<a href="deleteattachment.php?back=project&ProjectId=<?php echo $_GET[ProjectId]; ?>&AttachmentId=<?php echo $row_rsProjectAttachment['AttachmentId']; ?>">Delete</a></li>
    <?php } while ($row_rsProjectAttachment = mysql_fetch_assoc($rsProjectAttachment));
}
?>
    </ol>
<?php
mysql_free_result($rsProjectAttachment);
?>
<form enctype="multipart/form-data" method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table align="center">
    <tr valign="baseline">
      <td nowrap align="right">Attachment:</td>
      <td><input type="file" name="Attachment" id="Attachment" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="Insert record"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1">
  <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
</form>
<p>&nbsp;</p>
