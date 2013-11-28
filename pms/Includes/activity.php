<?php

class Activity
{
	public $__Activity;
	public $__ProjectId;
	public $__UserId;

	public function setProjectActivity()
	{
		global $database_conn, $conn;
		

		$sql = sprintf("INSERT INTO `projectactivity` (`Activity` ,`ProjectId` ,`ActivityTimeStamp` ,`UserId`) VALUES (%s,%s,CURRENT_TIMESTAMP,%s);", common::GetSQLValueString($__Activity, "text"),common::GetSQLValueString($__ProjectId, "int"),common::GetSQLValueString($__UserId, "int"));
		mysql_query($sql, $conn) or die(mysql_error());
	}
	public function getProjectActivityByProjectId()
	{
		global $database_conn, $conn;
		$sql = sprintf("SELECT * FROM projectactivity WHERE ProjectId = %s", common::GetSQLValueString($__ProjectId, "int"));
		$rs = mysql_query($sql, $conn) or die(mysql_error());
		$row = mysql_fetch_assoc($rs);
		$this->projectactivities = array();
		if(!empty($row)) 
		{
			do
			{
				$this->projectactivities[] = array('TimeStamp'=>$row['ActivityTimeStamp'],'Activity'=>$row['Activity'],'UserId'=>$row['UserId']);
			} while($row = mysql_fetch_assoc($rs));
		}
		
		return projectactivities;
	}

}?>