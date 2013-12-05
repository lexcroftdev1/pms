<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../images/favicon.png">

    <title>Lexcroft Solutions</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Customise Bootstrap core CSS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <!-- Custom styles for this template -->
    <link href="../css/carousel.css" rel="stylesheet">
    <link href="../css/customise-bootstrap.css" rel="stylesheet">
  <style id="holderjs-style" type="text/css"></style></head>
<!-- NAVBAR
================================================== -->
  <body style="">
    <!--/Header Start here -->
    <div class="navbar navbar-blue navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://lexcroft.com"><img src="../images/logo.png"></a>
        </div>
        <div class="navbar-collapse-blue collapse-blue">
            <?php if (!empty($LoggedInClientId)) { ?>
            <ul class="nav navbar-nav navbar-right">
                <li>Welcome</li>
                <li><a href="myaccount.php"> <?php echo $Username; ?></a></li>
                <li><a class="dropdown-toggle" href="logout.php">Logout</a></li>
            </ul>
            <?php } else { ?>
            <ul class="nav navbar-nav navbar-right">
                <li><a class="dropdown-toggle" href="logout.php">login</a></li>
            </ul>
            <?php } ?>
        </div><!--/.navbar-collapse -->
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li><a href="#"><span class="glyphicon glyphicon-home"></span> <strong>Home</strong></a></li>
              <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-tasks"></span> <strong>PMS</strong>
                <?php if (!empty($LoggedInClientId)) { ?>
                <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="projects.php">Projects</a></li>
                      <li><a href="archivedprojects.php">Archived</a></li>
                      <li><a href="hiddenprojects.php">Hidden</a></li>
                      <?php if (common::isUserWithLimitedAccess() === false) {
                        if ($ServiceProviderInd == 1) {
                            ?> 
                        <li><a href="credit.php">Credit</a></li>
                      <?php } ?>
                      <?php if ($ServiceBuyerInd == 1) { ?>
                        <li><a href="debit.php">Debit</a></li>
                      <?php } ?>
                      <li><a href="invoices.php">Invoices</a></li>
                      <li><a href="myaccount.php">Account</a></li>
                      <?php } ?>
                      <li><a href="sortProjects.php">Sort</a></li>
                    </ul>
                </li>
                <?php } ?>

                <li><a href="#"><span class="glyphicon glyphicon-list"></span> <strong>Services</strong></a></li>
                <li><a href="#"><span class="glyphicon glyphicon-phone-alt"></span>  <strong>Contact</strong></a></li>
            </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>
    <!--/ End of Header part -->

    <!-- Container start -->
    <div class="whitebg">
        <div class="container">
    