<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

if(common::isUserWithLimitedAccess() === true) die("you do not have permissions to view this page");

$currentPage = $_SERVER["PHP_SELF"];
$maxRows_rsProject = 25;
$pageNum_rsProject = 0;
if (isset($_GET['pageNum_rsProject'])) {
    $pageNum_rsProject = $_GET['pageNum_rsProject'];
}
$startRow_rsProject = $pageNum_rsProject * $maxRows_rsProject;

mysql_select_db($database_conn, $conn);

$query_rsProject = "SELECT c.ClientId,c.ClientName,c.Currency,sum(case when po.AgreedDiscountTypeId=1 then ((po.`AgreedRate` * po.`AgreedHours`) - (( (po.`AgreedRate` * po.`AgreedHours`) * po.`AgreedDiscount`)/100)) else ((po.`AgreedRate` * po.`AgreedHours`) - po.`AgreedDiscount`) end ) POTotalNetValue FROM  purchaseorder po inner join project p on p.ProjectId=po.ProjectId inner join projectstatus ps on p.ProjectStatusId=ps.ProjectStatusId inner join client c on p.ServiceProviderClientId = c.ClientId WHERE p.ActiveInd='1' AND po.ActiveInd='1' AND ps.ProjectStatusId = '80'
AND p.ClientId='$LoggedInClientId' group by c.ClientName  order by c.ClientName";


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
<table border="0">
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
        <th align="center">Payable to</th>
        <th align="center">Amount Payable</th>
        <th></th>
    </tr>
    <?php if($totalRows_rsProject) do {
            ?>
    <tr>
        <td align="center"><a href="showdebitedprojects.php?ServiceProviderClientId=<?php echo $row_rsProject['ClientId']; ?>"> <?php echo $row_rsProject['ClientName']; ?></a></td>
        <td align="center"><?php echo $row_rsProject[Currency].round($row_rsProject[POTotalNetValue],2); ?></td>
        <td>
            <a href="generateinvoice.php?ServiceProviderClientId=<?php echo $row_rsProject['ClientId']; ?>">Download Invoice</a>
        </td>
    </tr>
        <?php } while ($row_rsProject = mysql_fetch_assoc($rsProject)); ?>
</table>
<table border="0">
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