<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

$currentPage = $_SERVER["PHP_SELF"];
$maxRows_rsProject = 200;
$pageNum_rsProject = 0;

if (isset($_GET['pageNum_rsProject'])) {
    $pageNum_rsProject = $_GET['pageNum_rsProject'];
}
$startRow_rsProject = $pageNum_rsProject * $maxRows_rsProject;

mysql_select_db($database_conn, $conn);
if(common::isAdmin() === true) {
    $query_rsProject = "SELECT distinct p.NewInd,p.ProjectId,p.ProjectTitle,".common::SQLDate("p.DateWhenPosted")." DateWhenPosted,p.ProjectStatusId,ps.ProjectStatus,c.ClientName,c.Currency,p.ProjectPriorityId,count(pp.PostId) TotalPosts, sum(`BuyerUnreadInd`) BuyerUnread, sum(`ClientUnreadInd`) ClientUnread,c.ClientId,p.ServiceProviderClientId,".common::SQLDate("MAX(pp.DateWhenCreated)")." LastPostDateTime FROM project p inner join projectstatus ps on p.ProjectStatusId=ps.ProjectStatusId inner join client c on p.ClientId = c.ClientId left join projectpost pp on p.ProjectId=pp.ProjectId  WHERE p.ShowInd ='1' AND p.ActiveInd='1' AND ps.ProjectStatusId > '80'";
}
else {
    $query_rsProject = "SELECT distinct p.NewInd,p.ProjectId,p.ProjectTitle,".common::SQLDate("p.DateWhenPosted")." DateWhenPosted,p.ProjectStatusId,ps.ProjectStatus,c.ClientName,c.Currency,p.ProjectPriorityId,count(pp.PostId) TotalPosts, sum(`BuyerUnreadInd`) BuyerUnread, sum(`ClientUnreadInd`) ClientUnread,c.ClientId,p.ServiceProviderClientId,".common::SQLDate("MAX(pp.DateWhenCreated)")." LastPostDateTime FROM project p inner join projectstatus ps on p.ProjectStatusId=ps.ProjectStatusId left join client c on p.ClientId = c.ClientId  left join projectpost pp on p.ProjectId=pp.ProjectId  WHERE p.ShowInd ='1' AND p.ActiveInd='1' and (p.ClientId='$LoggedInClientId' or p.ServiceProviderClientId='$LoggedInClientId')  AND ps.ProjectStatusId > '80'";
}

$expire=time()+60*60*24*365;

if(isset($_GET[ProjectStatusId])) {
    $ProjectStatusId = $_GET[ProjectStatusId];
    setcookie("ProjectStatusId",$ProjectStatusId, $expire);
} 
else {
    $ProjectStatusId = $_COOKIE[ProjectStatusId];
}

if(isset($_GET[ClientId])) {
    $ClientId = $_GET[ClientId];
    setcookie("ClientId",$ClientId, $expire);
} 
else {
    $ClientId = $_COOKIE[ClientId];
}

if(isset($_GET[ProjectPriorityId])) {
    $ProjectPriorityId = $_GET[ProjectPriorityId];
    setcookie("ProjectPriorityId",$ProjectPriorityId, $expire);
} 
else {
    $ProjectPriorityId = $_COOKIE[ProjectPriorityId];
}

if(isset($_GET[SortOrder])) {
    $SortOrder = $_GET[SortOrder];
    setcookie("SortOrder",$SortOrder, $expire);
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
    $query_rsProject .= ' ORDER BY p.NewInd desc, MAX(pp.DateWhenCreated) DESC, p.DateWhenPosted DESC ';
}

//echo $query_rsProject;exit;

$query_limit_rsProject = sprintf("%s LIMIT %d, %d", $query_rsProject, $startRow_rsProject, $maxRows_rsProject);
$rsProject = mysql_query($query_limit_rsProject, $conn) or die(mysql_error());
$row_rsProject = mysql_fetch_assoc($rsProject);

//var_dump($query_limit_rsProject);
if (isset($_GET['totalRows_rsProject'])) {
    $totalRows_rsProject = $_GET['totalRows_rsProject'];
} else {
    $all_rsProject = mysql_query($query_rsProject);
    $totalRows_rsProject = mysql_num_rows($all_rsProject);
}
$totalPages_rsProject = ceil($totalRows_rsProject/$maxRows_rsProject)-1;

$queryString_rsProject = "";
if (!empty($_SERVER['QUERY_STRING'])) {
    $params = explode("&", $_SERVER['QUERY_STRING']);
    $newParams = array();
    foreach ($params as $param) {
        if (stristr($param, "pageNum_rsProject") == false &&
                stristr($param, "totalRows_rsProject") == false) {
            array_push($newParams, $param);
        }
    }
    if (count($newParams) != 0) {
        $queryString_rsProject = "&" . htmlentities(implode("&", $newParams));
    }
}
$queryString_rsProject = sprintf("&totalRows_rsProject=%d%s", $totalRows_rsProject, $queryString_rsProject);
require_once('Includes/header.php');

?>
<form name="form1" method="post">

    <div style="float:left;margin-bottom:10px;margin-left:55px;vertical-align:middle;">
        <h1>MY Projects</h1>
    </div>
    <div style="float:right;margin-bottom:10px;margin-right:55px;vertical-align:middle;">
        <a href="addproject.php"><b>Add new project</b></a>
    </div>
    <br clear="all" />
    <div style="float:left;margin-bottom:10px;margin-left:55px;vertical-align:middle;">
  		Sorted by: <? echo common::formSelect('SortOrder', array('DateWhenPosted'=>'Date when posted','ProjectPriorityId'=>'Project Priority','ProjectStatusId'=>'Project Status'),$SortOrder,false,'onchange="document.form1.action=\'archivedprojects.php?SortOrder=\'+this.value;document.form1.submit();"'); ?>
    </div>
    <div style="float:right;margin-bottom:10px;margin-right:55px;vertical-align:middle;">
  		Filter by:&nbsp;
        <select name="ProjectStatusId" onchange="javascript:document.location='archivedprojects.php?ProjectStatusId='+this.value;">
            <option value="" <?php if ($ProjectStatusId == "" ) {
                echo "selected";
                    } ?>>All</option>
            <option value="incomplete" <?php if ($ProjectStatusId == "incomplete" ) {
                echo "selected";
                    } ?>>Only incomplete ones</option>
        </select>
        <? echo common::SelectByEnum('ProjectPriorityId','projectpriority',$ProjectPriorityId,true,'onchange="document.form1.action=\'archivedprojects.php?ProjectPriorityId=\'+this.value;document.form1.submit();"'); ?>
        <?php
        if(common::isAdmin() === true) {
            echo common::SelectByEnum('ClientId','client',$ClientId,true,'onchange="document.form1.action=\'archivedprojects.php?ClientId=\'+this.value;document.form1.submit();"');
        }
        ?>
    </div>
    <br clear="all" />
</form>

<br clear="all" />
<table class="pagination">
    <tr>
        <td><?php if ($pageNum_rsProject > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsProject=%d%s", $currentPage, 0, $queryString_rsProject); ?>">First</a>
                <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsProject > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsProject=%d%s", $currentPage, max(0, $pageNum_rsProject - 1), $queryString_rsProject); ?>">Previous</a>
                <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsProject < $totalPages_rsProject) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsProject=%d%s", $currentPage, min($totalPages_rsProject, $pageNum_rsProject + 1), $queryString_rsProject); ?>">Next</a>
                <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsProject < $totalPages_rsProject) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsProject=%d%s", $currentPage, $totalPages_rsProject, $queryString_rsProject); ?>">Last</a>
                <?php } // Show if not last page ?></td>
    </tr>
</table>
<table border="1" cellpadding="5" cellspacing="2" valign="top" width="90%" style="border-collapse:collapse;overflow:auto;" bordercolor="#efefef" align="center">
    <tr>
        <th align="center">Project #</th>
        <th align="center">Summary</th>
        <?php if(common::isUserWithLimitedAccess()) {?>
        <th align="center">Hours Purchased</th>
            <?php } else {?>
        <th align="center">Total Cost</th>
            <?php } ?>
        <th align="center">Status</th>
        <td align="center"></td>
    </tr>
    <?php if($totalRows_rsProject) do {

            $priorityIcon = common::getPriorityIcon($row_rsProject['ProjectPriorityId']);

            $notifications = "";

            $notifications .=  '<img style="float:left;text-align:left;vertical-align:middle;" border="0" src="'.$priorityIcon[Icon].'" title="This project has '.$priorityIcon[ProjectPriority].' priority" alt="This project has '.$priorityIcon[ProjectPriority].' priority">';

            $query_rsSumPOs = sprintf("SELECT sum(po.`AgreedHours`) POTotalHours, sum(case when po.AgreedDiscountTypeId=1 then ((po.`AgreedRate` * po.`AgreedHours`) - (( (po.`AgreedRate` * po.`AgreedHours`) * po.`AgreedDiscount`)/100)) else ((po.`AgreedRate` * po.`AgreedHours`) - po.`AgreedDiscount`) end ) POTotalNetValue FROM purchaseorder po WHERE po.ActiveInd='1' and ProjectId = %s group by ProjectId", common::GetSQLValueString($row_rsProject['ProjectId'], "int"));

            $rsSumPOs = mysql_query($query_rsSumPOs, $conn) or die(mysql_error());
            $row_rsSumPOs = mysql_fetch_assoc($rsSumPOs);

            $POTotalNetValue =  $row_rsSumPOs[POTotalNetValue];
            $POTotalHours =  $row_rsSumPOs[POTotalHours];


            $unreadgAndNewFlag = false;
            if(((int)$LoggedInClientId === (int)$row_rsProject[ClientId]) && (int)$row_rsProject[ClientUnread] > 0) {
                $unreadgAndNewFlag = true;
                $notifications .=  '<img style="float:left;text-align:left;vertical-align:middle;" border="0" src="images/set4/16x16/mail_receive.png" title="'.$row_rsProject[ClientUnread].' unread messages and '.$row_rsProject[TotalPosts].' total messages" alt="You have '.$row_rsProject[ClientUnread].' messages">';
            }
            elseif(((int)$LoggedInClientId === (int)$row_rsProject[ServiceProviderClientId]) && (int)$row_rsProject[BuyerUnread] > 0) {
                $unreadgAndNewFlag = true;
                $notifications .=  '<img style="float:left;text-align:left;vertical-align:middle;" border="0" src="images/set4/16x16/mail_receive.png" title="'.$row_rsProject[BuyerUnread].' unread messages and '.$row_rsProject[TotalPosts].' total messages" alt="You have '.$row_rsProject[BuyerUnread].' messages" >';
            }
            if((int)$row_rsProject[NewInd] === 1) {
                $unreadgAndNewFlag = true;
                $notifications .=  '<img style="float:left;text-align:left;vertical-align:middle;" border="0" src="images/set4/16x16/favorite.png" title="New project" alt="New project">';
            }

            if((float)$POTotalNetValue > 0 && (int)$row_rsProject['ProjectStatusId']>=30) {
                $notifications .=  '<img style="float:left;text-align:left;vertical-align:middle;" border="0" src="images/set4/16x16/dollar_currency_sign.png" title="PO Approved" alt="PO Approved">';
            }

            $sql = "SELECT PostId,c.ClientName poster FROM `projectpost` pp inner join client c on c.ClientId=pp.PosterId and pp.ProjectId=$row_rsProject[ProjectId] order by pp.DateWhenCreated desc limit 1";
            $rsPost = mysql_query($sql, $conn) or die(mysql_error());
            $row_post = mysql_fetch_assoc($rsPost);

            ?>
    <tr>
        <td align="center"><?php echo $row_rsProject['ProjectId']; ?></td>
        <td width="40%" align="center">
            <div style="width:20%px;float:left;"><?php echo $notifications; ?></div>
            <div style="float:right;width:80%">
                <a title="<?php echo $row_rsProject[TotalPosts]; ?> total messages" href="viewproject.php?ProjectId=<?php echo $row_rsProject['ProjectId']; ?>"><?php if($unreadgAndNewFlag === true) {
                                echo '<span style="font-weight:bold;color:darkred!important;font-size:15px;">'.$row_rsProject['ProjectTitle'].'</span>';
                            }else {
                                echo $row_rsProject['ProjectTitle'];
                            } ?></a>
                <br clear="all" /><small>Published on <?php echo $row_rsProject[DateWhenPosted]; ?></small>
                <br clear="all" /><small>By <?php echo $row_rsProject['ClientName']; ?></small>
                        <?php if(!empty($row_rsProject[LastPostDateTime])) {?>
                <br clear="all" /><small>Last <a href="viewproject.php?ProjectId=<?php echo $row_rsProject['ProjectId']; ?>#post_<?php echo $row_post[PostId]; ?>">message</a> on <?php echo $row_rsProject[LastPostDateTime]; ?></small>
                <br clear="all" /><small>by: <?php echo $row_post[poster]; ?></small>

                            <?php } ?>
            </div>
        </td>

        <td align="center">
                    <?php
                    if((int)$row_rsProject['ProjectStatusId'] === 10) {
                        $font='<span style="color:#000000;">';
                    }
                    elseif((int)$row_rsProject['ProjectStatusId'] === 20) {
                        $font='<span style="color:#aa0000;font-size:18px;font-weight:bold;">';
                    }
                    else {
                        $font='<span style="color:#00aa00;font-size:16px;font-weight:bold;">';
                    }

                    if(common::isUserWithLimitedAccess()) {
                        echo $font.round($POTotalHours,2).' Hours</span>';
                    }
                    else {
                        echo $font.$row_rsProject['Currency'].round($POTotalNetValue,2).'</span>';
                    }
                    ?>
        </td>
        <td align="center"><?php echo $row_rsProject[ProjectStatus];?></td>
        <td>
            <div style="margin-left:15px;text-align:left;">
                <a href="projectkeynotes.php?ProjectId=<?php echo $row_rsProject['ProjectId']; ?>" title="key notes" >view project notes</a><br>
                <a href="projectattachments.php?ProjectId=<?php echo $row_rsProject['ProjectId']; ?>" title="Attachments" >view project attachments</a><br>

                        <?php if(common::isAdmin() === true) { ?>
                <a href="#" onclick="if(confirm('Delete this project?')){document.location='deleteproject.php?ProjectId=<?php echo $row_rsProject['ProjectId']; ?>';}else{void(0);}" title="Delete this project">Delete project</a><br>
                            <?php } ?>
                <a href="#" onclick="if(confirm('Hide this project?')){document.location='hideproject.php?ProjectId=<?php echo $row_rsProject['ProjectId']; ?>';}else{void(0);}" title="Hide this project">Hide project</a><br>
            </div>
        </td>
    </tr>
            <?php } while ($row_rsProject = mysql_fetch_assoc($rsProject)); ?>
</table>
<table class="pagination">
    <tr>
        <td><?php if ($pageNum_rsProject > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsProject=%d%s", $currentPage, 0, $queryString_rsProject); ?>">First</a>
                <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsProject > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsProject=%d%s", $currentPage, max(0, $pageNum_rsProject - 1), $queryString_rsProject); ?>">Previous</a>
                <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsProject < $totalPages_rsProject) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsProject=%d%s", $currentPage, min($totalPages_rsProject, $pageNum_rsProject + 1), $queryString_rsProject); ?>">Next</a>
                <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsProject < $totalPages_rsProject) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsProject=%d%s", $currentPage, $totalPages_rsProject, $queryString_rsProject); ?>">Last</a>
                <?php } // Show if not last page ?></td>
    </tr>
</table>
<?php
mysql_free_result($rsProject);
require_once('Includes/footer.php');
?>
