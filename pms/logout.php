<?php
// *** Logout the current user.
$logoutGoTo = "login.php";
if (!isset($_SESSION)) {
  session_start();
}

$_SESSION['MM_Username'] = NULL;
$_SESSION['MM_UserGroup'] = NULL;
$_SESSION['MM_ClientId'] = NULL;
$_SESSION['RedirectPage'] = NULL;

unset($_SESSION['MM_Username']);
unset($_SESSION['MM_UserGroup']);
unset($_SESSION['MM_ClientId']);
unset($_SESSION['RedirectPage']);

unset($_SESSION);

if ($logoutGoTo != "") {header("Location: $logoutGoTo");
exit;
}
?>