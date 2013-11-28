<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>POMS</title>
    <link type="text/css" href="style/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="style/style.css"/>
    <script src="javascript/jquery.tools.min.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui-1.7.2.custom.min.js"></script>
</head>
<body class="content">
<div id="wrapper">
    <!--<div id="header"></div>-->
    <div id="feed">
        <h1></h1>

        <h2 style="text-align:left;">Project Order & Management System</h2>

        <?php if (!empty($LoggedInClientId)) { ?>
        <div style="vertical-align:middle;float:left; margin-left:55px;margin-bottom:10px;margin-top:15px;">Hi <a
            href="myaccount.php"><?php echo $Username; ?></a>!
        </div>
        <?php } ?>
        <div style="float:right;margin-right:55px;margin-bottom:10px;">
            <?php if (!empty($LoggedInClientId)) { ?>
            <a href="projects.php">Projects</a> | <a href="archivedprojects.php">Archived</a> | <a
                href="hiddenprojects.php">Hidden</a> <?php if (common::isUserWithLimitedAccess() === false) {
                if ($ServiceProviderInd == 1) {
                    ?> | <a href="credit.php">Credit</a><?php } ?> | <?php if ($ServiceBuyerInd == 1) { ?><a
                    href="debit.php">Debit</a><?php } ?> | <a href="invoices.php">Invoices</a> | <a
                    href="myaccount.php">Account</a> <?php } ?> | <a href="sortProjects.php">Sort</a> | <a
                href="logout.php">Logout</a>
            <?php } ?>
        </div>
        <br clear="all"/>
