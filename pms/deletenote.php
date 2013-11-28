<?php
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

if(!common::isAdmin())exit;

if ((isset($_GET['NoteId'])) && ($_GET['NoteId'] != "")) {

	if(common::isAdmin())
	{
		$deleteSQL = sprintf("UPDATE note set ActiveInd = '0' WHERE NoteId=%s",common::GetSQLValueString($_GET['NoteId'], "int"));
	}
	else
	{
		$deleteSQL = sprintf("UPDATE note n inner join client c on n.ClientId = c.ClientId set n.ActiveInd = '0' WHERE n.NoteId=%s and c.ClientId=$LoggedInClientId",common::GetSQLValueString($_GET['NoteId'], "int"));
	}


  mysql_select_db($database_conn, $conn);
  mysql_query($deleteSQL, $conn) or die(mysql_error());

  if((string)$_GET[back] === 'pokeynotes')
  {
  	$deleteGoTo = "pokeynotes.php?PurchaseOrderId=".$_GET[PurchaseOrderId];
  }
  elseif((string)$_GET[back] === 'projectkeynotes')
  {
  	$deleteGoTo = "projectkeynotes.php?ProjectId=".$_GET[ProjectId];
  }



  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
?>
