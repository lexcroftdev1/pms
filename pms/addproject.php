<?php

require_once('Includes/conn.php');

require_once('Includes/isUserLoggedIn.php');

$editFormAction = $_SERVER['PHP_SELF'];

if (isset($_SERVER['QUERY_STRING'])) {

  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);

}



if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1"))
{
  if(common::isAdmin() === true)
  {
  	
	$insertSQL = sprintf("INSERT INTO project
						(ProjectTitle, ProjectDescription,ClientId,ProjectStartDate,ProjectEndDate,ProjectPriorityId,ServiceProviderClientId)
						VALUES (%s,%s,%s,STR_TO_DATE(%s,'%%m/%%d/%%Y %%H:%%i:%%s'),STR_TO_DATE(%s,'%%m/%%d/%%Y %%H:%%i:%%s'),%s,%s)",

					   common::GetSQLValueString($_POST[ProjectTitle], "text"),
                       common::GetSQLValueString($_POST[ProjectDescription], "text"),
                       common::GetSQLValueString($_POST[ClientId], "int"),
					   common::GetSQLValueString($_POST[ProjectStartDate].' 00:00:00', "text"),
					   common::GetSQLValueString($_POST[ProjectEndDate].' 23:59:59', "text"),
					   common::GetSQLValueString($_POST[ProjectPriorityId], "int"),
					   common::GetSQLValueString($_POST[ServiceProviderClientId], "int")
					   );
  }
  else
  {
  	$insertSQL = sprintf("INSERT INTO project
						(ProjectTitle, ProjectDescription,ClientId,ProjectStartDate,ProjectEndDate,ProjectPriorityId)
						VALUES (%s,%s,'$LoggedInClientId',STR_TO_DATE(%s,'%%m/%%d/%%Y %%H:%%i:%%s'),STR_TO_DATE(%s,'%%m/%%d/%%Y %%H:%%i:%%s'),%s)",

					   common::GetSQLValueString($_POST[ProjectTitle], "text"),
                       common::GetSQLValueString($_POST[ProjectDescription], "text"),
                       common::GetSQLValueString($_POST[ProjectStartDate].' 00:00:00', "text"),
					   common::GetSQLValueString($_POST[ProjectEndDate].' 23:59:59', "text"),
					   common::GetSQLValueString($_POST[ProjectPriorityId], "int")
					   );

  }
	//var_dump($insertSQL);exit;

  mysql_select_db($database_conn, $conn);

  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());



	$ProjectId = mysql_insert_id();

	$m = new ProjectMail($ProjectId);

	$m->sendProjectMail();





  //mysql_insert_id()



  $insertGoTo = "index.php";

  if (isset($_SERVER['QUERY_STRING'])) {

    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";

    $insertGoTo .= $_SERVER['QUERY_STRING'];

  }

  header(sprintf("Location: %s", $insertGoTo));

}

require_once('Includes/header.php');

?>
<script type="text/javascript">
	$(function()
	{
		$('#ProjectStartDate').datepicker();
		$('#ProjectEndDate').datepicker();
	});
</script>

<h1>Add new project</h1>

<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">

  <table cellpadding="5" cellspacing="1" valign="top" align="center" width="90%">

    	<tr>

        	<td align="right" valign="top"><strong>Title</strong></td><td>

            	<input style="width:600px;height:30px;" type="text" name="ProjectTitle" value="" />

            </td>

        </tr>

         <tr>

        	<td align="right" valign="top"><strong>Description</strong></td>

            <td><textarea style="width:600px;height:400px" name="ProjectDescription"></textarea></td>

         </tr>
         <tr>
        	<td align="right" valign="top"><strong>Priority</strong></td>
            <td><?php echo common::SelectByEnum('ProjectPriorityId','projectpriority')?></td>
         </tr>
         <?php if(common::isAdmin() === true){ ?>
         <tr>
        	<td align="right" valign="top"><strong>Service Buyer</strong></td>
            <td><?php echo common::SelectByEnum('ClientId','client')?></td>
         </tr>
         <tr>
        	<td align="right" valign="top"><strong>Service Provider</strong></td>
            <td><?php echo common::SelectByEnum('ServiceProviderClientId','client')?></td>
         </tr>
         <?php } ?>
         <tr>
        	<td align="right" valign="top"><strong>Start date</strong></td>
            <td>
				<input type="text" id="ProjectStartDate" name="ProjectStartDate" />
            </td>
         </tr>
         <tr>
        	<td align="right" valign="top"><strong>End date</strong></td>
            <td>
				<input type="text" id="ProjectEndDate" name="ProjectEndDate" />
            </td>
         </tr>
         <tr>

           <td></td>

            <td align="left">
			<a href="javascript:document.form1.submit();void(0);"><img style="vertical-align:middle;text-align:center;" src="images/submit.png" alt="Submit Project"></a>
            &nbsp;<a href="javascript:history.back();void(0);"><img style="vertical-align:bottom;text-align:bottom;" src="images/cancel.png" alt="Cancel and go back"></a></td>

         </tr>

  		<input type="hidden" name="MM_insert" value="form1" />

  	</table>

</form>

<?php require_once('Includes/footer.php'); ?>