<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

$id = $_GET['AttachmentId'];
$query = "SELECT AttachmentName,AttachmentSize,AttachmentType FROM attachment WHERE AttachmentId = '$id'";
mysql_select_db($database_conn, $conn);
$result = mysql_query($query, $conn) or die(mysql_error());
$row = mysql_fetch_assoc($result);

$name = $row[AttachmentName];
$size = $row[AttachmentSize];
$type = $row[AttachmentType];
$extension= '.'.end(explode(".",$name));

$target = UPLOADPATH.$id.$extension;

$fp = fopen($target, 'r');
$content = fread($fp, $size);
//$content = addslashes($content);
fclose($fp);

header("Content-length: $size");
header("Content-type: $type");
header("Content-Disposition: attachment; filename=".str_replace(' ','_',$name));
echo $content;
exit;
?>
