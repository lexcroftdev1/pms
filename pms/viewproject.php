<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');


$colname_rsProject = "-1";
if (isset($_GET['ProjectId'])) {
    $colname_rsProject = $_GET['ProjectId'];
}
mysql_select_db($database_conn, $conn);
$query_rsProject = "SELECT ClientName,p.ProjectStatusId,ps.ProjectStatus,Currency,ProjectTitle,ProjectDescription," . common::SQLDate('DateWhenPosted') . " DateWhenPosted," . common::SQLDate('ProjectStartDate') . " ProjectStartDate," . common::SQLDate('ProjectEndDate') . " ProjectEndDate, ServiceProviderClientId,pp.ProjectPriority,pp.Icon ProjectPriorityIcon FROM project p inner join projectpriority pp on p.ProjectPriorityId=pp.ProjectPriorityId inner join projectstatus ps on p.ProjectStatusId=ps.ProjectStatusId inner join client c on p.ClientId = c.ClientId inner join discounttype dt on c.DiscountTypeId=dt.DiscountTypeId WHERE p.ProjectId=" . common::GetSQLValueString($colname_rsProject, "int");
//var_dump($query_rsProject);
$rsProject = mysql_query($query_rsProject, $conn) or die(mysql_error());
$row_rsProject = mysql_fetch_assoc($rsProject);
$totalRows_rsProject = mysql_num_rows($rsProject);

$colname_rsPOs = "-1";
if (isset($_GET['ProjectId'])) {
    $colname_rsPOs = $_GET['ProjectId'];
}
mysql_select_db($database_conn, $conn);
$query_rsPOs = "SELECT *," . common::SQLShortDate('POTimeStamp') . " SQLPOTimeStamp, " . common::SQLDate('TimeStampWhenActed') . " SQLTimeStampWhenActed, (case when po.AgreedDiscountTypeId=1 then ((po.`AgreedRate` * po.`AgreedHours`) - (( (po.`AgreedRate` * po.`AgreedHours`) * po.`AgreedDiscount`)/100)) else  ((po.`AgreedRate` * po.`AgreedHours`) - po.`AgreedDiscount`) end ) PONetValue FROM purchaseorder po inner join discounttype dt on po.AgreedDiscountTypeId=dt.DiscountTypeId WHERE po.ActiveInd='1' and ProjectId = " . common::GetSQLValueString($colname_rsPOs, "int");
$rsPOs = mysql_query($query_rsPOs, $conn) or die(mysql_error());
$row_rsPOs = mysql_fetch_assoc($rsPOs);
$totalRows_rsPOs = mysql_num_rows($rsPOs);

$colname_rsProjectPosts = "-1";
if (isset($_GET['ProjectId'])) {
    $colname_rsProjectPosts = $_GET['ProjectId'];
}
mysql_select_db($database_conn, $conn);
$query_rsProjectPosts = "SELECT *," . common::SQLDate('pp.DateWhenCreated') . "SQLDateWhenPosted FROM projectpost pp inner join client c on c.ClientId=pp.PosterId WHERE ProjectId = " . common::GetSQLValueString($colname_rsProjectPosts, "int") . " and pp.ActiveInd=1 order by DateWhenCreated asc";
$rsProjectPosts = mysql_query($query_rsProjectPosts, $conn) or die(mysql_error());
$row_rsProjectPosts = mysql_fetch_assoc($rsProjectPosts);
$totalRows_rsProjectPosts = mysql_num_rows($rsProjectPosts);

mysql_select_db($database_conn, $conn);
$query_rsSumPOs = sprintf("SELECT sum(po.`AgreedHours`) POTotalHours, sum(case when po.AgreedDiscountTypeId=1 then ((po.`AgreedRate` * po.`AgreedHours`) - (( (po.`AgreedRate` * po.`AgreedHours`) * po.`AgreedDiscount`)/100)) else  ((po.`AgreedRate` * po.`AgreedHours`) - po.`AgreedDiscount`) end ) POTotalNetValue FROM purchaseorder po WHERE po.ActiveInd='1' and ProjectId = %s group by ProjectId", common::GetSQLValueString($colname_rsPOs, "int"));
//var_dump($query_rsSumPOs);
$rsSumPOs = mysql_query($query_rsSumPOs, $conn) or die(mysql_error());
$row_rsSumPOs = mysql_fetch_assoc($rsSumPOs);

$POTotalNetValue = $row_rsSumPOs[POTotalNetValue];
$POTotalHours = $row_rsSumPOs[POTotalHours];

#set unread to read now...
if (common::isAdmin() === true) {
    $sql = sprintf("Update `projectpost` pp inner join project p on p.ProjectId=pp.ProjectId set pp.BuyerUnreadInd=0 WHERE p.ProjectId = %d	AND ServiceProviderClientId = %d", common::GetSQLValueString($_GET['ProjectId'], "int"), common::GetSQLValueString($LoggedInClientId, "int"));
    mysql_query($sql, $conn) or die(mysql_error());

    $sql = sprintf("Update `projectpost` pp inner join project p on p.ProjectId=pp.ProjectId set pp.ClientUnreadInd=0 WHERE p.ProjectId = %d	AND ClientId = %d", common::GetSQLValueString($_GET['ProjectId'], "int"), common::GetSQLValueString($LoggedInClientId, "int"));
    mysql_query($sql, $conn) or die(mysql_error());

    $sql = sprintf("Update project set NewInd='0' WHERE ProjectId=%d", common::GetSQLValueString($_GET['ProjectId'], "int"));
    mysql_query($sql, $conn) or die(mysql_error());
}

$colname_rsProjectAttachment = "-1";
if (isset($_GET['ProjectId'])) {
    $colname_rsProjectAttachment = $_GET['ProjectId'];
}
mysql_select_db($database_conn, $conn);
$query_rsProjectAttachment = sprintf("SELECT a.AttachmentId,AttachmentName,DateWhenCreated FROM attachment a inner join projectattachment pa on a.AttachmentId = pa.AttachmentId WHERE ProjectId = %s  and a.ActiveInd=1", common::GetSQLValueString($colname_rsProjectAttachment, "int"));
$rsProjectAttachment = mysql_query($query_rsProjectAttachment, $conn) or die(mysql_error());
$row_rsProjectAttachment = mysql_fetch_assoc($rsProjectAttachment);
$totalRows_rsProjectAttachment = mysql_num_rows($rsProjectAttachment);

require_once('Includes/header.php');
ob_start();
?>
<div style="float:left;margin-bottom:10px;margin-left:40px;vertical-align:middle;"><h1>
        <u><?php echo strtoupper($row_rsProject['ProjectTitle']); ?></u></h1></div>
<br clear="all"/>

<table cellpadding="5" valign="top" width="90%" align="center">
    <?php if (common::isUserAGuest() == false) { ?>
        <tr>
            <td width="80%" valign="top" align="left">
                <table cellpadding="5" valign="top" width="100%" align="center">
                    <tr>
                        <td width="15%"><strong>Client:</strong></td>
                        <td width="85%"><?php echo $row_rsProject['ClientName']; ?></td>
                    </tr>
                    <tr>
                        <td width="15%"><strong>Service Provider:</strong></td>
                        <td width="85%"><?php echo common::getClientNameByClientId($row_rsProject['ServiceProviderClientId']); ?></td>
                    </tr>
                    <tr>
                        <td width="15%"><strong>Project Priority:</strong></td>
                        <td width="85%"><img
                                src="<?php echo $row_rsProject['ProjectPriorityIcon']; ?>">&nbsp;<?php echo $row_rsProject['ProjectPriority']; ?>
                        </td>
                    </tr>

                    <tr>
                        <td width="15%"><strong>Initiated on:</strong></td>
                        <td width="85%"><?php echo $row_rsProject['DateWhenPosted']; ?></td>
                    </tr>
                    <tr>
                        <td width="15%"><strong>Start Date:</strong></td>
                        <td width="85%"><?php echo $row_rsProject['ProjectStartDate']; ?></td>
                    </tr>
                    <tr>
                        <td width="15%"><strong>End date:</strong></td>
                        <td width="85%"><?php echo $row_rsProject['ProjectEndDate']; ?></td>
                    </tr>
                    <tr>
                        <td width="15%"><strong>Current status:</strong></td>
                        <td width="85%"><?php echo $row_rsProject['ProjectStatus']; ?></td>
                    </tr>
                    <?php
                    if ($totalRows_rsProjectAttachment > 0) {
                        ?>
                        <tr>
                            <td width="15%"><strong>Attachments:</strong></td>
                            <td width="85%" align="left">
                                <ol>
                                    <?php do { ?>
                                        <li>
                                            <?php echo $row_rsProjectAttachment['AttachmentName']; ?> created
                                            on <?php echo $row_rsProjectAttachment['DateWhenCreated']; ?>&nbsp;
                                            <a href="downloadattachment.php?AttachmentId=<?php echo $row_rsProjectAttachment['AttachmentId']; ?>">Download</a>&nbsp;
                                            <a href="deleteattachment.php?back=project&ProjectId=<?php echo $_GET[ProjectId]; ?>&AttachmentId=<?php echo $row_rsProjectAttachment['AttachmentId']; ?>">Delete</a>
                                        </li>
                                    <?php
                                    } while ($row_rsProjectAttachment = mysql_fetch_assoc($rsProjectAttachment));
                                    ?>
                                </ol>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php
                    mysql_free_result($rsProjectAttachment);
                    ?>
                    <?php if (!common::isUserWithLimitedAccess()) { ?>
                        <tr>
                            <td width="15%"><strong>Total net value:</strong></td>
                            <td width="85%">
                        <span style="font-weight:bold;color:darkgreen;font-size:15pt;">
                            <?php
                            if ((int)$row_rsProject['ProjectStatusId'] === 10) {
                                $font = '<span style="color:darkgrey;">';
                            } elseif ((int)$row_rsProject['ProjectStatusId'] === 20) {
                                $font = '<span style="color:darkred;font-size:18px;font-weight:bold;">';
                            } else {
                                $font = '<span style="color:darkgreen;font-size:16px;font-weight:bold;">';
                            }
                            if (common::isUserAGuest()) {
                                echo $font . '&nbsp;</span>';
                            } elseif (common::isUserWithLimitedAccess()) {
                                echo $font . round($POTotalHours, 2) . ' Hours</span>';
                            } else {
                                echo $font . $row_rsProject['Currency'] . round($POTotalNetValue, 2) . '</span>';
                                echo '<span style="padding-left:10px;"><a href="addpurchaseorder.php?ProjectId=' . $_GET[ProjectId] . '" title="Add new purchase order">Add new purchase order</a></span>';
                                echo '<span style="padding-left:10px;"><a href="addinstantbonus.php?ProjectId=' . $_GET[ProjectId] . '" title="Award instant bonus">Award instant bonus</a></span>';
                            }
                            //echo $font.$row_rsProject['Currency'].round($POTotalNetValue,2).'</span>';
                            ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
            <td width="20%" valign="top" align="right">
                <?php if (!common::isUserWithLimitedAccess()) { ?>
                    <div style="margin-left:15px;text-align:left;">
                        <a href="editproject.php?ProjectId=<?php echo $_GET['ProjectId']; ?>" title="Edit project details">Edit
                            project details</a><br>
                        <a href="changeprojectstatus.php?ProjectId=<?php echo $_GET['ProjectId']; ?>"
                           title="Change project status">Change project status</a><br>
                        <a href="projectkeynotes.php?ProjectId=<?php echo $_GET['ProjectId']; ?>" title="key notes">view project
                            notes</a><br>
                        <a href="projectattachments.php?ProjectId=<?php echo $_GET['ProjectId']; ?>" title="Attachments">view
                            project attachments</a><br>
                        <a href="addpurchaseorder.php?ProjectId=<?php echo $_GET['ProjectId']; ?>"
                           title="Add new purchase order">Add new purchase order</a>

                        <?php if (common::isAdmin() === true) { ?>
                            <a href="#"
                               onclick="if(confirm('Delete this project?')){document.location='deleteproject.php?ProjectId=<?php echo $_GET['ProjectId']; ?>';}else{void(0);}"
                               title="Delete this project">Delete project</a><br>
                            <a href="#"
                               onclick="if(confirm('Hide this project?')){document.location='hideproject.php?ProjectId=<?php echo $_GET['ProjectId']; ?>';}else{void(0);}"
                               title="Hide this project">Hide project</a><br>
                        <?php } ?>
                    </div>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>

    <tr>
        <td colspan="2" width="100%" valign="top" align="left">
            <table cellpadding="5" valign="top" width="100%" align="center">
                <tr>
                    <td valign="top" width="15%"><strong>Project Summary:</strong></td>
                    <td valign="top" width="85%">
                        <div class="break-word"
                             id="divProjectDescription"><?php echo common::makelink(nl2br(htmlentities($row_rsProject['ProjectDescription']))); ?></div>
                        <br>
                        <?php if (common::isUserAGuest() == false) { ?>
                            <?php if (common::isAdmin() === true) { ?>
                                <button class="modalInput" rel="#editprojectsummary">Edit project summary</button>
                                <div class="modal" id="editprojectsummary">
                                    <span>Edit project summary</span><br><br>

                                    <form id="frmeditprojectsummary" name="frmeditprojectsummary">
                                        <p style="text-align:left;padding-left:15px;">
                                            <input type="hidden" id="ProjectId" name="ProjectId"
                                                   value="<?php echo $_GET['ProjectId']; ?>">
                                            <textarea id="ProjectDescription"
                                                      style="float:right;width:780px;height:400px;font-size:12px"
                                                      name="ProjectDescription"><?php echo htmlentities($row_rsProject['ProjectDescription']); ?></textarea>
                                        </p>

                                        <p align="center">
                                            <br/><br/>
                                            <button type="submit"> Save project summary</button>
                                            <button type="button" class="close"> Cancel</button>
                                        </p>
                                    </form>
                                </div>
                                <script>
                                    $(document).ready(function () {

                                        var triggers = $("button.modalInput").overlay({
                                            expose: {
                                                color: '#333',
                                                loadSpeed: 0,
                                                opacity: 0.9
                                            },

                                            closeOnClick: true
                                        });
                                        $("#frmeditprojectsummary").submit(function (e) {
                                            triggers.eq(0).overlay().close();
                                            $.ajax
                                            ({
                                                type: "POST",
                                                url: "ajaxrequest/editproject.php",
                                                data: ($("#frmeditprojectsummary").serialize()),
                                                dataType: "html",
                                                cache: false,
                                                success: function (msg) {
                                                    $("#divProjectDescription").html(msg);
                                                },
                                                error: function (msg) {
                                                    alert(msg);
                                                }
                                            });
                                            return e.preventDefault();
                                        });

                                    });

                                </script>
                                </p>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php if (($totalRows_rsPOs > 0 && common::isUserWithLimitedAccess() === false && common::isUserAGuest() == false) || ($totalRows_rsPOs > 0 && common::isAdmin())) { ?>
    <h2><?php echo $totalRows_rsPOs; ?> Purchase orders</h2>
    <br clear="all"/>
    <table border="1" cellpadding="1" cellspacing="1" valign="top" align="center" width="90%"
           style="border-collapse: collapse" bordercolor="#efefef">
        <tr>
            <th align="center">Status</th>
            <th align="center">Quote ID</th>
            <th align="center">Cost</th>
            <?php if (common::isAdmin()) { ?>
                <th align="center">Rate</th><?php } ?>
            <?php if (common::isAdmin()) { ?>
                <th align="center">Hours</th><?php } ?>
            <?php if (common::isAdmin() || (string)$row_rsProject['ProjectStatus'] !== 'Project Complete') { ?>
                <th align="center"></th>
            <?php } ?>
        </tr>
        <?php do { ?>
            <tr>

                <td align="center"><img
                        src="<?php echo common::getPurchaseOrderStatusIcon($row_rsPOs['PurchaseOrderStatusId']); ?>"
                        title="<?php echo $row_rsPOs['PurchaseOrderStatus']; ?>"
                        alt="<?php echo $row_rsPOs['PurchaseOrderStatus']; ?>"/></td>
                <td align="center"><?php echo $row_rsPOs['PurchaseOrderId']; ?></td>
                <td align="center"><?php echo '<span style="font-weight:bold;color:' . common::getPurchaseOrderStatusColor($row_rsPOs['PurchaseOrderStatusId']) . ';font-size:13px;">' . $row_rsProject['Currency'] . round($row_rsPOs['PONetValue'], 2) . '</span>'; ?></td>

                <?php if (common::isAdmin()) { ?>
                    <td align="center"><?php echo $row_rsPOs['AgreedRate'] . "&nbsp;&nbsp;";
                        if ($row_rsPOs['AgreedDiscount'] > 0) {
                            echo "(" . $row_rsPOs['AgreedDiscount'] . $row_rsPOs['DiscountType'] . " discount)";
                        } ?></td>
                <?php } ?>
                <?php if (common::isAdmin()) { ?>
                    <td align="center"><?php echo $row_rsPOs['AgreedHours']; ?></td><?php } ?>
                <?php if (common::isAdmin() || (string)$row_rsProject['ProjectStatus'] !== 'Project Complete') { ?>
                    <td align="center">
                    <a href="pokeynotes.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&PurchaseOrderId=<?php echo $row_rsPOs['PurchaseOrderId']; ?>"><img
                            src="images/set6/32x32/text_page.png" title="PO's key notes'" alt="PO's key notes"></a>
                    <?php if ((int)$row_rsPOs['PurchaseOrderStatusId'] !== 30) { ?>
                        <a href="editpurchaseorder.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&PurchaseOrderId=<?php echo $row_rsPOs['PurchaseOrderId']; ?>"
                           title="Reconcile purchase order"><img src="images/set6/32x32/refresh.png" alt="Reconcile purchase order"></a>
                        &nbsp;
                    <?php
                    }
                    if


                    ((int)$row_rsPOs['PurchaseOrderStatusId'] === 10
                    ) {
                        ?>
                        <a href="rejectpurchaseorder.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&PurchaseOrderId=<?php echo $row_rsPOs['PurchaseOrderId']; ?>"
                           title="Reject purchase order"><img src="images/set6/32x32/remove_from_shopping_cart.png"
                                                              alt="Reject purchase order"></a>&nbsp;
                        <a href="acceptpurchaseorder.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&PurchaseOrderId=<?php echo $row_rsPOs['PurchaseOrderId']; ?>"
                           title="Accept purchase order"><img src="images/set6/32x32/shopping_cart_accept.png"
                                                              alt="Accept purchase order"></a>
                    <?php
                    }
                    if ((int)$UserGroup === 1) {
                        ?>
                        &nbsp;<a href="javascript:void(0);"
                                 onclick="if(confirm('Delete this purchase order?')){document.location='deletepurchaseorder.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&PurchaseOrderId=<?php echo $row_rsPOs['PurchaseOrderId']; ?>';}"
                                 title="Cancel purchase order"><img alt="Cancel purchase order" src="images/set6/32x32/remove.png"></a>
                        </td>
                    <?php
                    }
                }
                ?>
            </tr>
        <?php } while ($row_rsPOs = mysql_fetch_assoc($rsPOs)); ?>
    </table>
<?php } ?>

<form id="addPost" name="addPost" enctype="multipart/form-data"
      action="addpost.php?ProjectId=<?php echo $_GET[ProjectId]; ?>" method="post">
    <table border="1" cellpadding="10" cellspacing="1" valign="top" align="center" width="90%"
           style="border-collapse: collapse" bordercolor="#efefef">
        <?php
        if ($totalRows_rsProjectPosts > 0) {
            ?>
            <?php
            do {

                mysql_select_db($database_conn, $conn);
                $query_rsPostData = sprintf("SELECT Postdata FROM postdata WHERE PostId = %s", common::GetSQLValueString($row_rsProjectPosts['PostId'], "int"));
                $rsPostData = mysql_query($query_rsPostData, $conn) or die(mysql_error());
                $row_rsPostData = mysql_fetch_assoc($rsPostData);
                $Postdata = common::makelink(nl2br($row_rsPostData[Postdata]));
                mysql_free_result($rsPostData);
                ?>
                <tr>
                    <td valign="top" width="200">
                        <a name="post_<?php echo $row_rsProjectPosts['PostId']; ?>"></a>
                        <?php if (common::isUserAGuest() == false) { ?>
                            <strong><?php echo $row_rsProjectPosts['ClientName']; ?></strong><br/>
                        <?php } ?>
                        <small><?php echo $row_rsProjectPosts['SQLDateWhenPosted']; ?></small>
                        <br/>
                    </td>
                    <td valign="top" width="600">
                        <?php
                        if (!empty($row_rsProjectPosts['PostTitle'])) {
                            ?>
                            Title:
                            <strong><?php echo common::makelink(nl2br($row_rsProjectPosts['PostTitle'])); ?></strong>
                            <br><br>
                        <?php } ?>
                        <?php
                        if ((int)$row_rsProjectPosts['ClientId'] === (int)$LoggedInClientId) {
                            echo '<span style="color:#777">' . $Postdata . '</span>';
                        } else {
                            echo '<span style="color:#070">' . $Postdata . '</span>';
                        }
                        ?>
                        <br><br>
                        <?php

                        mysql_select_db($database_conn, $conn);
                        $query_rsProjectAttachment = sprintf("SELECT a.AttachmentId,AttachmentName,DateWhenCreated FROM attachment a inner join postattachment pa on a.AttachmentId = pa.AttachmentId WHERE pa.PostId = %s and a.ActiveInd=1 ", common::GetSQLValueString($row_rsProjectPosts['PostId'], "int"));
                        $rsProjectAttachment = mysql_query($query_rsProjectAttachment, $conn) or die(mysql_error());
                        $row_rsProjectAttachment = mysql_fetch_assoc($rsProjectAttachment);
                        $totalRows_rsProjectAttachment = mysql_num_rows($rsProjectAttachment);
                        ?>
                        <ol>
                            <?php
                            if ($totalRows_rsProjectAttachment > 0) {
                                do {
                                    ?>
                                    <li><?php echo $row_rsProjectAttachment['AttachmentName']; ?> created
                                        on <?php echo $row_rsProjectAttachment['DateWhenCreated']; ?>&nbsp;<a
                                            href="downloadattachment.php?AttachmentId=<?php echo $row_rsProjectAttachment['AttachmentId']; ?>">Download</a>&nbsp;|&nbsp;<a
                                            href="deleteattachment.php?ProjectId=<?php echo $_GET['ProjectId']; ?>&AttachmentId=<?php echo $row_rsProjectAttachment['AttachmentId']; ?>">Delete</a>
                                    </li>
                                <?php
                                } while ($row_rsProjectAttachment = mysql_fetch_assoc($rsProjectAttachment));
                            }
                            ?>
                        </ol>
                        <br><br>
                        <?php if (common::isUserAGuest() == false) { ?>
                            <a href="editpost.php?ProjectId=<?php echo $_GET[ProjectId]; ?>&PostId=<?php echo $row_rsProjectPosts['PostId']; ?>"
                               title="Edit this post"><img alt="Edit this post" src="images/set6/32x32/edit.png"></a>
                            <?php if (common::isAdmin()) { ?>
                                &nbsp;&nbsp;
                                <a href="javascript:void(0);"
                                   onclick="if(confirm('Delete this purchase order?')){document.location='deletepost.php?ProjectId=<?php echo $_GET[ProjectId]; ?>&PostId=<?php echo $row_rsProjectPosts['PostId']; ?>';}"
                                   title="Delete this post">
                                    <img alt="Delete this post" src="images/set6/32x32/delete.png">
                                </a>
                            <?php
                            }
                        } ?>
                    </td>
                </tr>
            <?php
            } while ($row_rsProjectPosts = mysql_fetch_assoc($rsProjectPosts));
        }?>
        <?php if (common::isUserAGuest() == false) { ?>
            <tr>
                <td colspan="2" align="right" valign="top" width="800">
                    <span style="float:right;width:85%;font-weight:bold">Title</span><br clear="all">
                    <input style="float:right;width:85%;height:30px;font-weight:bold;font-size:16px" type="text"
                           name="PostTitle" value=""/>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right" valign="top" width="800">
                    <span style="float:right;width:85%;font-weight:bold">Comments</span><br clear="all">
                    <textarea style="float:right;width:85%;height:400px;font-size:12px" name="PostData"></textarea><br
                        clear="all">
                    <span style="float:right;width:85%;"><strong>Attachment</strong></span><br clear="all">
                <span style="float:right;width:85%;"><input type="file" name="Attachment" id="Attachment"
                                                            size="32"></span><br clear="all"><br clear="all">

                    <div style="float:right;width:85%;"><a href="javascript:document.addPost.submit();void(0);"><img
                                src="images/submit.png" alt="Submit Project"></a></div>
                </td>
            </tr>
        <?php } ?>
    </table>
</form>
<?php
$output = ob_get_contents();
ob_end_clean();
mysql_free_result($rsProject);
mysql_free_result($rsPOs);
mysql_free_result($rsProjectPosts);
//$output = str_replace("{project}", $Infusion_item[Id], $output);
$output = preg_replace("/{project (\d+)}/", "<a target=\"_blank\" href=\"viewproject.php?ProjectId=$1\"><span>project</span> $1</a>", $output);
$output = preg_replace("/project (\d+)/", "<a target=\"_blank\" href=\"viewproject.php?ProjectId=$1\"><span>project</span> $1</a>", $output);

echo $output;

require_once('Includes/footer.php');


?>
