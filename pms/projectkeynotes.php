<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

$maxRows_Recordset1 = 20;
$pageNum_Recordset1 = 0;
if (isset($_GET['pageNum_Recordset1'])) {
  $pageNum_Recordset1 = $_GET['pageNum_Recordset1'];
}
$startRow_Recordset1 = $pageNum_Recordset1 * $maxRows_Recordset1;

$colname_Recordset1 = "-1";
if (isset($_GET['ProjectId'])) {
  $colname_Recordset1 = $_GET['ProjectId'];
}
mysql_select_db($database_conn, $conn);
$query_Recordset1 = sprintf("SELECT * FROM projectnote pn inner join note n on pn.NoteId=n.NoteId inner join client c on n.ClientId=c.ClientId WHERE pn.ProjectId = %s and n.ActiveInd = 1 order by n.DateWhenCreated ",common::GetSQLValueString($colname_Recordset1, "int"));
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
  <h1>Keynotes</h1>
</div>
<div style="float:right;margin-bottom:10px;margin-right:55px;vertical-align:middle;"> <a href="viewproject.php?ProjectId=<?php echo $_GET[ProjectId]; ?>"><img style="vertical-align:middle;" src="images/set6/48x48/full_page.png" title="view project details" alt="view project details"></a> </div>
<br clear="all">
<form id="addProjectKeyNote" name="addProjectKeyNote" action="addprojectkeynote.php?ProjectId=<?php echo $_GET[ProjectId]; ?>" method="post">

<table border="1" cellpadding="10" cellspacing="1" valign="top" align="center" width="90%" style="border-collapse: collapse" bordercolor="#ccc">
  <?php
  if(!empty($row_Recordset1)){
  do { ?>
    <tr>
      <td valign="top" width="200"><a name="note_<?php echo $row_Recordset1['NoteId']; ?>"></a> <strong><?php echo $row_Recordset1['ClientName']; ?></strong><br />
        <small><?php echo $row_Recordset1['SQLDateWhenPosted']; ?></small><br /></td>
      <td valign="top" width="600"><?php
                if(!empty($row_Recordset1['NoteTitle'])){
            ?>
        Title: <strong><?php echo common::makelink(nl2br($row_Recordset1['NoteTitle'])); ?></strong><br>
        <br>
        <?php } ?>
        <?php
                if((int)$row_Recordset1['ClientId'] === (int)$LoggedInClientId)
                {
                    echo '<span style="color:#777">'.common::makelink(nl2br($row_Recordset1['Note'])).'</span>';
                }
                else
                {
                    echo '<span style="color:#070">'.common::makelink(nl2br($row_Recordset1['Note'])).'</span>';
                }
            ?>
        <br>
        <br>
        <a href="editprojectkeynote.php?ProjectId=<?php echo $_GET[ProjectId]; ?>&NoteId=<?php echo $row_Recordset1['NoteId']; ?>" title="Edit this note"><img alt="Edit this note" src="images/set6/32x32/edit.png"></a>
        <?php if(common::isAdmin()){?>
        &nbsp;&nbsp; <a href="javascript:void(0);" onclick="if(confirm('Delete this note?')){document.location='deletenote.php?back=projectkeynotes&ProjectId=<?php echo $_GET[ProjectId]; ?>&NoteId=<?php echo $row_Recordset1['NoteId']; ?>';}" title="Delete this note"> <img alt="Delete this post" src="images/set6/32x32/delete.png"> </a></td>
        <?php } ?>
    </tr>
    <?php } while ($row_Recordset1 = mysql_fetch_assoc($Recordset1));
	}?>

    	<tr>
        	<td colspan="2" align="right" valign="top" width="800">
            	<span style="float:right;width:628px;font-weight:bold">Title</span><br clear="all">
            	<input style="float:right;width:620px;height:30px;font-weight:bold;font-size:16px" type="text" name="NoteTitle" value="" />
            </td>
        </tr>
         <tr>
        	<td colspan="2" align="right" valign="top"  width="800">
            	<span style="float:right;width:628px;font-weight:bold">Note</span><br clear="all">
            	<textarea style="float:right;width:620px;height:400px;font-size:12px" name="Note"></textarea>
            </td>
         </tr>
         <tr>
            <td colspan="2" align="right" valign="top"  width="800">
            	<div style="float:right;width:628px;"><a href="javascript:document.addProjectKeyNote.submit();void(0);"><img src="images/submit.png" alt="Submit Project"></a></div>
            </td>
         </tr>

</table>
</form>
<?php
require_once('Includes/footer.php');
?>
<?php
mysql_free_result($Recordset1);
?>