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
if (isset($_GET['PurchaseOrderId'])) {
  $colname_Recordset1 = $_GET['PurchaseOrderId'];
}
mysql_select_db($database_conn, $conn);
$query_Recordset1 = sprintf("SELECT * FROM ponote pn inner join note n on pn.NoteId=n.NoteId inner join client c on n.ClientId=c.ClientId WHERE pn.PurchaseOrderId = %s  and n.ActiveInd = 1 order by n.DateWhenCreated ",common::GetSQLValueString($colname_Recordset1, "int"));
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

mysql_select_db($database_conn, $conn);

$query_rsProject = "SELECT ClientName,p.ProjectStatusId,Currency,ProjectTitle,ProjectDescription,".common::SQLShortDate('DateWhenPosted')." DateWhenPosted FROM project p inner join client c on p.ClientId = c.ClientId inner join discounttype dt on c.DiscountTypeId=dt.DiscountTypeId WHERE p.ProjectId=".common::GetSQLValueString($_GET[ProjectId], "int");
$rsProject = mysql_query($query_rsProject, $conn) or die(mysql_error());
$row_rsProject = mysql_fetch_assoc($rsProject);

$query_rsPOs = "SELECT *,".common::SQLShortDate('POTimeStamp')." SQLPOTimeStamp, ".common::SQLDate('TimeStampWhenActed')." SQLTimeStampWhenActed, (case when po.AgreedDiscountTypeId=1 then ((po.`AgreedRate` * po.`AgreedHours`) - (( (po.`AgreedRate` * po.`AgreedHours`) * po.`AgreedDiscount`)/100)) else  ((po.`AgreedRate` * po.`AgreedHours`) - po.`AgreedDiscount`) end ) PONetValue FROM purchaseorder po inner join purchaseorderstatus pos on po.PurchaseOrderStatusId=pos.PurchaseOrderStatusId inner join discounttype dt on po.AgreedDiscountTypeId=dt.DiscountTypeId WHERE po.PurchaseOrderId = ".common::GetSQLValueString($_GET[PurchaseOrderId], "int");
$rsPOs = mysql_query($query_rsPOs, $conn) or die(mysql_error());
$row_rsPOs = mysql_fetch_assoc($rsPOs);
require_once('Includes/header.php');
?>
<div style="float:left;margin-bottom:10px;margin-left:55px;vertical-align:middle;">
  <h1>Purchase details</h1>
</div>
<div style="float:right;margin-bottom:10px;margin-right:55px;vertical-align:middle;">
	<a href="viewproject.php?ProjectId=<?php echo $_GET[ProjectId]; ?>"><img src="images/set6/48x48/full_page.png" title="view project details" alt="view project details"></a>
	<?php if(common::isAdmin() || (int)$row_rsProject['ProjectStatusId'] !== 80)
	{?>
	    	<?php if((int)$row_rsPOs['PurchaseOrderStatusId'] !== 30){?>
	    	<a href="editpurchaseorder.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&PurchaseOrderId=<?php echo $row_rsPOs['PurchaseOrderId']; ?>" title="Reconcile purchase order"><img src="images/set6/48x48/refresh.png" alt="Reconcile purchase order"></a>
	    	<?php } if((int)$row_rsPOs['PurchaseOrderStatusId'] === 10){?>
	        	<a href="rejectpurchaseorder.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&PurchaseOrderId=<?php echo $row_rsPOs['PurchaseOrderId']; ?>" title="Reject purchase order"><img src="images/set6/48x48/remove_from_shopping_cart.png" alt="Reject purchase order"></a>
	        	<a href="acceptpurchaseorder.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&PurchaseOrderId=<?php echo $row_rsPOs['PurchaseOrderId']; ?>" title="Accept purchase order"><img src="images/set6/48x48/shopping_cart_accept.png" alt="Accept purchase order"></a>
	    	<?php }
	    	if((int)$UserGroup === 1)
			{?>
	    		<a href="javascript:void(0);" onclick="if(confirm('Delete this purchase order?')){document.location='deletepurchaseorder.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&PurchaseOrderId=<?php echo $row_rsPOs['PurchaseOrderId']; ?>';}" title="Cancel purchase order"><img alt="Cancel purchase order" src="images/set6/48x48/remove.png"></a>
		<?php }
	}
	?>
</div>
<br clear="all">
<form id="addpokeynote" name="addpokeynote" action="addpokeynote.php?PurchaseOrderId=<?php echo $_GET[PurchaseOrderId]; ?>" method="post">
  <table cellpadding="5" align="center" valign="top">
    <tr>
      <td align="right">PO Status</td>
        <td >
        	<img src="<?php echo common::getPurchaseOrderStatusIcon($row_rsPOs['PurchaseOrderStatusId']); ?>" title="<?php echo $row_rsPOs['PurchaseOrderStatus']; ?>" alt="<?php echo $row_rsPOs['PurchaseOrderStatus']; ?>" />
       	</td>
    </tr>
    <tr>
      <td align="right">net value</td>
        <td ><?php echo '<span style="font-weight:bold;color:darkgreen;font-size:13px;">'.$row_rsProject['Currency'].round($row_rsPOs['PONetValue'],2).'</span>'; ?></td>
    </tr>
    <tr>
      <td align="right">Rate</td>
      <td ><?php echo $row_rsPOs['AgreedRate']; ?></td>
   </tr>
    <tr>
      <td align="right">Discount</td>
      <td ><?php echo $row_rsPOs['AgreedDiscount']; ?><?php echo $row_rsPOs['DiscountType']; ?></td>
    </tr>
    <tr>
      <td align="right">Hours</th>
        <td ><?php echo $row_rsPOs['AgreedHours']; ?></td>
    </tr>
    <tr>
       <td align="right">Created on</th>
       <td  ><?php echo $row_rsPOs['SQLPOTimeStamp']; ?></td>
    </tr>
    <tr>
      <td align="right">Comments</td>
      <td  align="left"><?php if(!empty($row_rsPOs['Comments'])){echo $row_rsPOs['Comments']. ' Time: '.$row_rsPOs['SQLTimeStampWhenActed'];} else {echo 'n/a';} ?></td>
    </tr>
  </table>
<div style="float:left;margin-bottom:10px;margin-left:55px;vertical-align:middle;">
  <h1>Purchase Order's Keynotes</h1>
</div>
<br clear="all">
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
        <a href="editpokeynote.php?PurchaseOrderId=<?php echo $_GET[PurchaseOrderId]; ?>&NoteId=<?php echo $row_Recordset1['NoteId']; ?>" title="Edit this note"><img alt="Edit this note" src="images/set6/32x32/edit.png"></a>
        <?php if(common::isAdmin()){?>
        &nbsp;&nbsp; <a href="javascript:void(0);" onclick="if(confirm('Delete this note?')){document.location='deletenote.php?back=pokeynotes&PurchaseOrderId=<?php echo $_GET[PurchaseOrderId]; ?>&NoteId=<?php echo $row_Recordset1['NoteId']; ?>';}" title="Delete this note"> <img alt="Delete this post" src="images/set6/32x32/delete.png"> </a></td>
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
            	<div style="float:right;width:628px;"><a href="javascript:document.addpokeynote.submit();void(0);"><img src="images/submit.png" alt="Submit Project"></a></div>
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