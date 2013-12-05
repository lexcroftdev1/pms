<?php require_once('Includes/conn.php'); ?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction= $_SERVER["PHP_SELF"];
$MM_redirectLoginSuccess = "index.php";
if (!empty($_SESSION['RedirectPage']))
{
  $MM_redirectLoginSuccess = $_SESSION['RedirectPage'];
}

if (isset($_POST['UserName']))
{
  $loginUsername=$_POST['UserName'];
  $password=$_POST['Password'];
  $MM_fldUserAuthorization = "AdminInd";

  $MM_redirectLoginFailed = "loginfailed.php";
  $MM_redirecttoReferrer = true;
  mysql_select_db($database_conn, $conn);

  $LoginRS__query=sprintf("SELECT u.UserName, u.Password, u.AdminInd,u.ClientId,c.ServiceProviderInd,c.ServiceBuyerInd FROM `user` u inner join client c on c.ClientId=u.ClientId WHERE u.UserName=%s AND u.Password=%s",
  common::GetSQLValueString($loginUsername, "text"), common::GetSQLValueString($password, "text"));

  $LoginRS = mysql_query($LoginRS__query, $conn) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {

    $loginStrGroup  = mysql_result($LoginRS,0,'AdminInd');
    $LoggedInClientId  = mysql_result($LoginRS,0,'ClientId');

    $ServiceProviderInd  = mysql_result($LoginRS,0,'ServiceProviderInd');
    $ServiceBuyerInd  = mysql_result($LoginRS,0,'ServiceBuyerInd');

    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;
    $_SESSION['MM_ClientId'] = $LoggedInClientId;

    $_SESSION['MM_ServiceProviderInd'] = $ServiceProviderInd;
    $_SESSION['MM_ServiceBuyerInd'] = $ServiceBuyerInd;
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
require_once('Includes/header.php');
?>

<div class="row">
  <div class="col-md-6">
    <form id="form1" name="form1" method="POST" action="<?php echo $loginFormAction; ?>" class="form-signin">
      <h2 class="form-signin-heading">Please sign in</h2>
      <input type="text" id="UserName" class="form-control" name="UserName" placeholder="User Name" required autofocus>
      <input type="password" id="Password" name="Password" class="form-control" placeholder="Password" required>
      <label class="checkbox">
        <input type="checkbox" value="remember-me"> Remember me
      </label>
      <button class="btn btn-lg btn-primary btn-block" name="Login" id="Login" type="submit">Sign in</button>
    </form>
  </div>
  <div class="col-md-6">
    <form id="form1" name="form1" method="POST" action="<?php echo $loginFormAction; ?>" class="form-signin">
      <h2 class="form-signin-heading">Create New Account</h2>
      <input type="text" id="Name" class="form-control" name="Name" placeholder="Enter Full Name" required autofocus>
      <input type="text" id="UserName" class="form-control" name="UserName" placeholder="Select User Name" required autofocus>
      <input type="password" id="password" class="form-control" name="password" placeholder="Password" required autofocus>
      <input type="password" id="password" class="form-control" name="password" placeholder="Repeat Password" required autofocus>
      <input type="email" id="email" class="form-control" name="email" placeholder="Enter Email" required autofocus>
      <label class="checkbox">
        By cliking on "Sign up" you agree <br>to <a href="#"><strong>Lexcroft's Terms & Conditions</strong></a>
      </label>
      <button class="btn btn-lg btn-primary btn-block" name="Login" id="Login" type="submit">Create</button>
    </form>
  </div>
</div>

<br />
<?php
require_once('Includes/footer.php');
?>