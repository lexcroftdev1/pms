<?php require_once('Includes/conn.php'); ?>
<?php
if (!empty($_GET['token']))
{
  $token = $_GET['token'];
  mysql_select_db($database_conn, $conn);

  $sql = sprintf("
  					SELECT 
  							u.UserName, u.Password, u.AdminInd,u.ClientId,c.ServiceProviderInd,c.ServiceBuyerInd,URL 
  					FROM `user` u inner join client c on c.ClientId=u.ClientId 
  					inner join secureconnection sc on u.UserId=sc.UserId 
  					where date_sub(now(),INTERVAL 31 DAY) <  sc.TimeStamp and token=%s",common::GetSQLValueString($token, "text")
  				);

  $LoginRS = mysql_query($sql, $conn) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser)
  {
	$loginUsername  = mysql_result($LoginRS,0,'UserName');
    $loginStrGroup  = mysql_result($LoginRS,0,'AdminInd');
    $LoggedInClientId  = mysql_result($LoginRS,0,'ClientId');

    $ServiceProviderInd  = mysql_result($LoginRS,0,'ServiceProviderInd');
    $ServiceBuyerInd  = mysql_result($LoginRS,0,'ServiceBuyerInd');
	$URL = mysql_result($LoginRS,0,'URL');
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;
    $_SESSION['MM_ClientId'] = $LoggedInClientId;

    $_SESSION['MM_ServiceProviderInd'] = $ServiceProviderInd;
    $_SESSION['MM_ServiceBuyerInd'] = $ServiceBuyerInd;
    header("Location:$URL");exit;
  }
  else die('invalid url or URL is expired. you can only use the URL for 31 days');
}
header("Location: login.php");exit;
?>