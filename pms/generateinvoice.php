<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

require_once('Includes/smarty/Smarty.class.php');
require_once("Includes/dompdf/dompdf_config.inc.php");
require_once('Includes/conn.php');
require_once('Includes/isUserLoggedIn.php');

#require('Includes/fpdf/htmlpdf.php');

if(!empty($_GET[ServiceProviderClientId]))
{
	$invoiceRecipientClientId = $LoggedInClientId;
	$invoiceSenderClientId = $_GET[ServiceProviderClientId];
}
elseif(!empty($_GET[ClientId]))
{
	$invoiceRecipientClientId = $_GET[ClientId];
	$invoiceSenderClientId = $LoggedInClientId;
}

mysql_select_db($database_conn, $conn);

$query_rsInvoice = "SELECT distinct p.ProjectId,p.ProjectTitle,".common::SQLShortDate("p.DateWhenPosted")." DateWhenPosted,c.Currency FROM project p inner join projectstatus ps on p.ProjectStatusId=ps.ProjectStatusId inner join client c on p.ClientId = c.ClientId WHERE p.ActiveInd='1' AND ps.ProjectStatusId = '80'";
$query_rsInvoice .= " AND c.ClientId = ".$invoiceRecipientClientId;
$query_rsInvoice .= " GROUP BY p.ProjectId ";
$query_rsInvoice .= ' ORDER BY p.DateWhenPosted';

//print $query_rsInvoice;exit;

$rsInvoice = mysql_query($query_rsInvoice,$conn) or die(mysql_error());
$row_rsInvoice = mysql_fetch_assoc($rsInvoice);
$invoiceprojects = $projects = array();

$grosstotal = $discounttotal = $netttoal = $TransactionFee = 0;
$discountType = $AgreedDiscountTypeId = '';


do
{
	$query_rsPOs = "SELECT po.AgreedDiscountTypeId,dt.DiscountType,po.AgreedRate,po.AgreedHours,po.AgreedDiscount,(case when po.AgreedDiscountTypeId=1 then ((po.`AgreedRate` * po.`AgreedHours`) - (( (po.`AgreedRate` * po.`AgreedHours`) * po.`AgreedDiscount`)/100)) else  ((po.`AgreedRate` * po.`AgreedHours`) - po.`AgreedDiscount`) end ) PONetValue FROM purchaseorder po inner join discounttype dt on po.AgreedDiscountTypeId=dt.DiscountTypeId WHERE po.ActiveInd='1' and ProjectId = ".common::GetSQLValueString($row_rsInvoice[ProjectId], "int");
	$rsPOs = mysql_query($query_rsPOs, $conn) or die(mysql_error());
	$row_rsPOs = mysql_fetch_assoc($rsPOs);
	$pos = array();
	do
	{
		if(!in_array($row_rsInvoice[ProjectId], $invoiceprojects) && !empty($row_rsInvoice[ProjectId])){
			$invoiceprojects[] = $row_rsInvoice[ProjectId];
		}
		
		$grosstotal +=  $row_rsPOs[AgreedRate] * $row_rsPOs[AgreedHours];
		$discounttotal +=  (($row_rsPOs[AgreedRate] * $row_rsPOs[AgreedHours]) - $row_rsPOs[PONetValue]);
		$netttoal +=  $row_rsPOs[PONetValue];
		
		$discount = ($row_rsPOs[AgreedDiscountTypeId] === 1)? $row_rsPOs[DiscountType].''.round($row_rsPOs[AgreedDiscount],2):round($row_rsPOs[AgreedDiscount],2).''.$row_rsPOs[DiscountType];
		$discountType = $row_rsPOs[DiscountType];
		$AgreedDiscountTypeId = $row_rsPOs[AgreedDiscountTypeId];
		$pos[] = array('rate'=>round($row_rsPOs[AgreedRate],2),'hours'=>round($row_rsPOs[AgreedHours],2),'discount'=>$discount,'total'=>round($row_rsPOs[AgreedRate]*$row_rsPOs[AgreedHours],2),'netttoal'=>round($row_rsPOs[PONetValue],2));
	} while($row_rsPOs = mysql_fetch_assoc($rsPOs));
	$projects[] = array('purchase_date'=>$row_rsInvoice[DateWhenPosted],'project_title'=>$row_rsInvoice[ProjectTitle],'pos'=>$pos); 
}while($row_rsInvoice = mysql_fetch_assoc($rsInvoice));

$query_rsPOs = "SELECT * from client where ClientId=".common::GetSQLValueString($invoiceRecipientClientId, "int")." Limit 1";
$rsRecipient = mysql_query($query_rsPOs, $conn) or die(mysql_error());
$row_rsRecipient = mysql_fetch_assoc($rsRecipient);
//$row_rsRecipient[TransactionFee]
//$row_rsRecipient[Currency]

$query_rsPOs = "SELECT * from client where ClientId=".common::GetSQLValueString($invoiceSenderClientId, "int")." Limit 1";
$rsSender = mysql_query($query_rsPOs, $conn) or die(mysql_error());
$row_rsSender = mysql_fetch_assoc($rsSender);

$smarty = new Smarty;

$smarty->compile_check = true;
$smarty->debugging = false;

$discount = ($AgreedDiscountTypeId === 1)? $discountType.''.round($row_rsRecipient[Discount],2):round($row_rsRecipient[Discount],2).''.$discountType;

$discountTotal = $row_rsRecipient[Currency].''.round($discounttotal,2);

$invoiceNumber = date('ymd').'-'.date('is');
//echo dirname(__FILE__).'/images/Client/Logo/'.$row_rsSender[Logo];exit;
//$smarty->assign("logo",dirname(__FILE__).'/images/Client/Logo/'.$row_rsSender[Logo]);
$smarty->assign("logo",'images/Client/Logo/'.$row_rsSender[Logo]);
$smarty->assign("invoice_date",date('m/d/Y'));
$smarty->assign("invoice_number",$invoiceNumber);
$smarty->assign("invoice_due_date", date('m/d/Y', mktime(0,0,0,date('m',strtotime(date(DATE_RFC822)))+1,date('d',strtotime(date(DATE_RFC822))),date('Y',strtotime(date(DATE_RFC822))))));

$smarty->assign("projects",$projects);

$smarty->assign("invoice_total",$row_rsRecipient[Currency].''.round($grosstotal,2));
$smarty->assign("invoice_discount",$discountTotal);
$smarty->assign("invoice_discount_type",$discount);
$smarty->assign("invoice_net",$row_rsRecipient[Currency].''.round($netttoal,2));

$smarty->assign("invoice_transaction_fee",$row_rsRecipient[Currency].''.round($row_rsRecipient[TransactionFee],2));
$smarty->assign("inovice_balance",$row_rsRecipient[Currency].''.round(($netttoal - $row_rsRecipient[TransactionFee]),2));

$smarty->assign("RecipientName",$row_rsRecipient[ClientName]);
$smarty->assign("RecipientCompanyPayableName",$row_rsRecipient[CompanyPayableName]);
$smarty->assign("RecipientAddress",$row_rsRecipient[Address]);
$smarty->assign("RecipientTelephone",$row_rsRecipient[Telephone]);
$smarty->assign("RecipientFAX",$row_rsRecipient[FAX]);

$smarty->assign("SenderName",$row_rsSender[ClientName]);
$smarty->assign("SenderCompanyPayableName",$row_rsSender[CompanyPayableName]);
$smarty->assign("SenderAddress",$row_rsSender[Address]);
$smarty->assign("SenderTelephone",$row_rsSender[Telephone]);
$smarty->assign("SenderFAX",$row_rsSender[FAX]);

$smarty->assign("extraHeader",$row_rsSender["extra_header"]);
$smarty->assign("extraFooter",$row_rsSender["extra_footer"]);
$smarty->assign("extraOther",$row_rsSender["extra_other"]);

ob_start();
$smarty->display('invoice.tpl');
$InvoiceReportHTML = ob_get_contents();
ob_end_clean();
ob_clean();


 if ( get_magic_quotes_gpc() )
    $InvoiceReportHTML = stripslashes($InvoiceReportHTML);

  //common::saveInvoice($invoiceSenderClientId,$invoiceRecipientClientId,$invoiceNumber,$invoiceprojects,$InvoiceReportHTML);
  
  if((string)$_GET['final'] === 'true')
  {
	foreach($invoiceprojects as $projectId)
	{
		$sql = "Update project set ProjectStatusId = '90' where ProjectId=".$projectId;
		$rsProject = mysql_query($sql, $conn) or die(mysql_error().$sql);
	}
	common::saveInvoice($invoiceSenderClientId,$invoiceRecipientClientId,$invoiceNumber,$invoiceprojects,$InvoiceReportHTML);
  }

	if((string)$_GET['view'] === 'printable')
	{
		echo $InvoiceReportHTML;
	}
	else
	{
	  	try 
	  	{
			global $_dompdf_warnings;
	  		$dompdf = new DOMPDF();
			
			$dompdf->load_html($InvoiceReportHTML);
			$dompdf->set_paper('A4', 'portrait');
			$dompdf->render();
			$dompdf->stream("invoice.pdf");
	  	
	  	}
	 	catch (Exception $e) 
	 	{
	    	echo 'Caught exception: ',  $e->getMessage(), "\n";
		}	  	
	}
 	exit;
?>