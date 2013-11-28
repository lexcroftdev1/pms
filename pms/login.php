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
<form id="form1" name="form1" method="POST" action="<?php echo $loginFormAction; ?>">
<table cellpadding="10" valign="top" align="center">
  <tr>
	<td align="right" valign="top"><label>User Name</label></td>
	<td><input type="text" name="UserName" id="UserName" /></td>

  </tr>
  <tr>
	<td align="right" valign="top"><label>Password</label></td>
	<td><input type="password" name="Password" id="Password" /></td>
  </tr>
  <tr>
	<td></td>
    <td>
      <input type="submit" name="Login" id="Login" value="Login" />
    </td>
  </tr>
</table>
</form>
<?php
require_once('Includes/footer.php');
?>