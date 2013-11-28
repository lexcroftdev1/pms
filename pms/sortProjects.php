<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

mysql_select_db($database_conn, $conn);

if ($_POST["MM_sort"] === "sortProjects") {

	$index = 0;
	foreach($_POST[projects] as $ProjectId)
	{
		$updateSQL = sprintf("UPDATE project SET `index`=%s WHERE ProjectId=%s", common::GetSQLValueString($index++, "int"),common::GetSQLValueString($ProjectId, "int"));
		mysql_select_db($database_conn, $conn);
		mysql_query($updateSQL, $conn) or die(mysql_error());
	}
	header("Location: index.php");
}


if(common::isAdmin() === true) {
    $query_rsProject = "SELECT distinct p.NewInd,p.ProjectId,p.ProjectTitle,".common::SQLDate("p.DateWhenPosted")." DateWhenPosted,p.ProjectStatusId,ps.ProjectStatus,c.ClientName,c.Currency,p.ProjectPriorityId,count(pp.PostId) TotalPosts, sum(`BuyerUnreadInd`) BuyerUnread, sum(`ClientUnreadInd`) ClientUnread,c.ClientId,p.ServiceProviderClientId,".common::SQLDate("MAX(pp.DateWhenCreated)")." LastPostDateTime FROM project p inner join projectstatus ps on p.ProjectStatusId=ps.ProjectStatusId inner join client c on p.ClientId = c.ClientId left join projectpost pp on p.ProjectId=pp.ProjectId  WHERE p.ShowInd ='1' AND p.ActiveInd='1' AND ps.ProjectStatusId < '80'";
}
else {
    $query_rsProject = "SELECT distinct p.NewInd,p.ProjectId,p.ProjectTitle,".common::SQLDate("p.DateWhenPosted")." DateWhenPosted,p.ProjectStatusId,ps.ProjectStatus,c.ClientName,c.Currency,p.ProjectPriorityId,count(pp.PostId) TotalPosts, sum(`BuyerUnreadInd`) BuyerUnread, sum(`ClientUnreadInd`) ClientUnread,c.ClientId,p.ServiceProviderClientId,".common::SQLDate("MAX(pp.DateWhenCreated)")." LastPostDateTime FROM project p inner join projectstatus ps on p.ProjectStatusId=ps.ProjectStatusId left join client c on p.ClientId = c.ClientId  left join projectpost pp on p.ProjectId=pp.ProjectId  WHERE p.ShowInd ='1' AND p.ActiveInd='1' and (p.ClientId='$LoggedInClientId' or p.ServiceProviderClientId='$LoggedInClientId')  AND ps.ProjectStatusId < '80'";
}


if(isset($_GET[ProjectStatusId])) {
    $ProjectStatusId = $_GET[ProjectStatusId];
} 
else {
    $ProjectStatusId = $_COOKIE[ProjectStatusId];
}

if(isset($_GET[ClientId])) {
    $ClientId = $_GET[ClientId];
} 
else {
    $ClientId = $_COOKIE[ClientId];
}

if(isset($_GET[ProjectPriorityId])) {
    $ProjectPriorityId = $_GET[ProjectPriorityId];
} 
else {
    $ProjectPriorityId = $_COOKIE[ProjectPriorityId];
}

if(isset($_GET[SortOrder])) {
    $SortOrder = $_GET[SortOrder];
} 
else {
    $SortOrder = $_COOKIE[SortOrder];
}


if(!empty($ProjectStatusId)) {
    $query_rsProject .= " AND p.ProjectStatusId < 80 ";
}

if(common::isAdmin() === true && !empty($ClientId)) {
    $query_rsProject .= " AND c.ClientId = ".$ClientId;
}
if(!empty($ProjectPriorityId)) {
    $query_rsProject .= " AND p.ProjectPriorityId = ".$ProjectPriorityId;
}

$query_rsProject .= " GROUP BY p.ProjectId ";


if((string)$SortOrder === "ProjectPriorityId") {
    $query_rsProject .= ' ORDER BY p.NewInd desc, p.ProjectPriorityId DESC, p.DateWhenPosted DESC';
}
elseif((string)$SortOrder === "ProjectStatusId") {
    $query_rsProject .= ' ORDER BY p.NewInd desc, p.ProjectStatusId, p.DateWhenPosted DESC';
}
else {
    $query_rsProject .= ' ORDER BY `index`, p.NewInd desc, MAX(pp.DateWhenCreated) DESC, p.DateWhenPosted DESC ';
}

$rsProject = mysql_query($query_rsProject, $conn) or die(mysql_error());
$row_rsProject = mysql_fetch_assoc($rsProject);

require_once('Includes/header.php');
?>
<br clear="all">
<form id="sortProjects" name="sortProjects" action="sortProjects.php" method="post">
<div align="center">
   <table>
    <tr>
        <td width="90%">
                <?php
                if(!empty($row_rsProject)){?>
                <select size="10" name="projects[]" ID="ListBox1" multiple="multiple" style="width:90%;height:400px;">
                <?php do { 
				?>
                <option value="<?php echo $row_rsProject['ProjectId']; ?>"><?php echo $row_rsProject['ProjectTitle']; ?></option>
                <?php } while ($row_rsProject = mysql_fetch_assoc($rsProject));?>
                </select>
                <?php }?>

        </td>
        <td valign="middle">
            <input type="button" value="Move Up" onclick="javascript:MoveUp()"><br />
            <input type="button" value="Move Dn" onclick="javascript:MoveDown()">
       </td>
    </tr>
	<tr>
		<td >
			<a href="javascript:selectAllOptions();document.sortProjects.submit();void(0);"><img style="vertical-align:middle;text-align:center;" src="images/submit.png" alt="Sort Projects"></a>
            &nbsp;<a href="javascript:history.back();void(0);"><img style="vertical-align:bottom;text-align:bottom;" src="images/cancel.png" alt="Cancel and go back"></a></td>
			<input type="hidden" name="MM_sort" value="sortProjects" />
		</td>
	</tr>
  </table>
</div>
<script type="text/javascript">
 function MoveDown() {
     var selectedOption = $("#ListBox1").find(':selected');

     //var selectedOption = $('#ListBox1 > option[selected]');
     var nextOption = $("#ListBox1").find(':selected').next("option");
     if ($(nextOption).text() != "") {
         $(selectedOption).remove();
         $(nextOption).after($(selectedOption));
     }
 }
 function MoveUp() {
     var selectedOption = $("#ListBox1").find(':selected');
     var prevOption = $("#ListBox1").find(':selected').prev("option");
     if ($(prevOption).text() != "") {
         $(selectedOption).remove();
         $(prevOption).before($(selectedOption));
     }
   }
	function selectAllOptions()
	{
		var lb = document.getElementById("ListBox1");
		for(i=0; i<lb.options.length; i++)
		lb.options[i].selected = true;
	}
</script>
</form>
<?php
require_once('Includes/footer.php');
?>