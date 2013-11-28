<?php

# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
error_reporting(E_ALL ^ E_NOTICE);
include_once("constants.php");

$hostname_conn = "localhost";
$database_conn = "pms";
$username_conn = "root";
$password_conn = "root";
$conn = mysql_pconnect($hostname_conn, $username_conn, $password_conn) or trigger_error(mysql_error(), E_USER_ERROR);

if (!isset($_SESSION)) {
    session_start();
}

$Username = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "";
$UserGroup = isset($_SESSION['MM_UserGroup']) ? $_SESSION['MM_UserGroup'] : "";
$LoggedInClientId = isset($_SESSION['MM_ClientId']) ? $_SESSION['MM_ClientId'] : "";
$ServiceProviderInd = isset($_SESSION['MM_ServiceProviderInd']) ? $_SESSION['MM_ServiceProviderInd'] : "";
$ServiceBuyerInd = isset($_SESSION['MM_ServiceBuyerInd']) ? $_SESSION['MM_ServiceBuyerInd'] : "";
$userTimeZone =  isset($_SESSION['MM_UserTimeZone']) ? $_SESSION['MM_UserTimeZone'] : "UTC";
date_default_timezone_set('UTC');
include_once("common.php");
include_once("options.php");
include_once("PHPMailer/class.phpmailer.php");
include_once("emailer.php");