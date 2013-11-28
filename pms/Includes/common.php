<?php
class common {
    public static function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }
        $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
        switch ($theType) {
            case "text":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
                break;
            case "date":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }
    public static function calculatePONetValue($hours,$rate, $discount, $discountTypeId) {
        $retVal = '';
        if((int)$discountTypeId === 1) {
            #%
            $total = (float)$hours * (float)$rate;
            $totaldiscount = ((float)$total * (float)$discount) / 100.00;
            $retVal = (float)((float)$total - (float)$totaldiscount);
        }
        elseif((int)$discountTypeId === 2) {
            #flat
            $total = (float)$hours * (float)$rate;
            $totaldiscount = (float)$discount;
            $retVal = (float)((float)$total - (float)$totaldiscount);
        }
        return round($retVal,2);
    }
    public static function showPONetValue($hours,$rate, $discount, $discountTypeId, $currency) {
        return $currency.(string)self::calculatePONetValue($hours,$rate, $discount, $discountTypeId);
    }
    public static function makelink($text) {
        # this functions deserves credit to the fine folks at phpbb.com
        $text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);
        // pad it with a space so we can match things at the start of the 1st line.
        $ret = ' ' . $text;
        // matches an "xxxx://yyyy" URL at the start of a line, or after a space.
        // xxxx can only be alpha characters.
        // yyyy is anything up to the first space, newline, comma, double quote or <
        $ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
        // matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
        // Must contain at least 2 dots. xxxx contains either alphanum, or "-"
        // zzzz is optional.. will contain everything up to the first space, newline,
        // comma, double quote or <.
        $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
        // matches an email@domain type address at the start of a line, or after a space.
        // Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
        $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
        // Remove our padding..
        $ret = substr($ret, 1);
        return $ret;
    }
    public static function SQLDate($timestamp) {
        return "DATE_FORMAT($timestamp, '%W, %M %d at %r')";
    }
    public static function SQLShortDate($timestamp) {
        return "DATE_FORMAT($timestamp, '%m/%d/%y')";
    }
    public static function isAdmin() {
        global $UserGroup;
        return ((int)$UserGroup === 1);
    }
    public static function isUserWithLimitedAccess() {
        global $UserGroup;
        return ((int)$UserGroup === -1);
    }
    public static function isUserAGuest() {
        return (!isset($_SESSION['MM_Username']));
    }
    public static function autoLoginURL($url,$UserId) {
        global $database_conn, $conn;
        $token = md5(uniqid(mt_rand(), true));
        mysql_select_db($database_conn, $conn);
        $sql = sprintf("INSERT INTO secureconnection(URL,Token,UserId) VALUES(%s,%s,%s)",
        				self::GetSQLValueString($url, "text"),
        				self::GetSQLValueString($token, "text"),
        				self::GetSQLValueString($UserId, "int")
        			);
        mysql_query($sql, $conn)  or die('Invalid email token is sent. user may not be able to login');
        return URL.'token.php?token='.$token;
    }
	public static function getPurchaseOrderStatusColor($PurchaseOrderStatusId) {
		if($PurchaseOrderStatusId == "10") return "darkred";
		elseif($PurchaseOrderStatusId == "20") return "darkgreen";
		elseif($PurchaseOrderStatusId == "30") return "darkgrey";
		elseif($PurchaseOrderStatusId == "40") return "darkgrey";
	}
    public static function getPurchaseOrderStatusIcon($PurchaseOrderStatusId) {
        global $database_conn, $conn;
        mysql_select_db($database_conn, $conn);
        $sql = sprintf("select Icon from purchaseorderstatus where PurchaseOrderStatusId=%s",self::GetSQLValueString($PurchaseOrderStatusId, "int"));
        $rs = mysql_query($sql, $conn);
        $row = mysql_fetch_assoc($rs);
        return $row['Icon'];
    }
    public static function getPriorityIcon($id) {
        global $database_conn, $conn;
        mysql_select_db($database_conn, $conn);
        $sql = sprintf("select ProjectPriority, Icon from projectpriority where ProjectPriorityId=%s",self::GetSQLValueString($id, "inf"));
        $rs = mysql_query($sql, $conn);
        $row = mysql_fetch_assoc($rs);
        return array('ProjectPriority'=>$row['ProjectPriority'],'Icon'=>$row['Icon']);
    }
    public static function EnumData($table) {
        global $database_conn, $conn;
        $retVal = array();
        mysql_select_db($database_conn, $conn);
        $sql = "select * from ".$table;
        $rs = mysql_query($sql, $conn);

        $row = mysql_fetch_row($rs);
        do {
            $retVal[$row[0]] = $row[1];
        } while($row = mysql_fetch_row($rs));
        return $retVal;
    }
    public static function SelectByEnum($selectname, $tablename, $selected = null, $nulloption = true, $extraattributes = null, $nulloptionTitle=' - Select - ') {
        return self::formSelect($selectname,self::EnumData($tablename),$selected,$nulloption,$extraattributes,$nulloptionTitle);
    }
    public static function formSelect($selectname, $arydata, $selected = null, $nulloption = true, $extraattributes = null, $nulloptionTitle=' - Select - ') {
        $strselect = '<select name="'.$selectname.'"';
        if(!empty($extraattributes)) {
            $strselect .=  $extraattributes;
        }
        $strselect .= ' >';

        if($nulloption) {
            $strselect .= '<option value="">'.$nulloptionTitle.'</option>';
        }

        if(empty($selected)) {
            if(isset($_POST[$selectname])) {
                $selected = $_POST[$selectname];
            }
        }

        foreach($arydata as $intkey => $option) {
            $strselected = '';

            // set selected
            if(is_array($selected)) { // if array
                if(in_array($intkey,$selected)) {
                    $strselected = 'selected="selected"';
                }
            }
            else
            if (isset($_POST[$selectname])) {
                if($_POST[$selectname] == $intkey) {
                    $strselected = 'selected="selected"';
                }
            }
            else
            if($intkey == $selected) {
                $strselected = 'selected="selected"';
            }

            $strselect .="\n".'<option value="'.$intkey.'" '.$strselected.'>'.$option.'</option>' ;
        }
        $strselect .= '</select>';
        return $strselect ;
    }
    public static function getClientNameByClientId($clientid) {
        global $database_conn, $conn;
        $sql = sprintf("SELECT * FROM client WHERE ClientId = %s limit 1", common::GetSQLValueString($clientid, "int"));
        $rs = mysql_query($sql, $conn) or die(mysql_error());
        $row = mysql_fetch_assoc($rs);
        return $row["ClientName"];
    }
    public static function saveInvoice($FromClientId,$ToClientId,$InvoiceNumber,array $invoiceprojects,$InvoiceReportHTML) {
        $PaymentTerms = "Please pay now.\nthanks";

        global $database_conn, $conn;
        $sql = sprintf
                ("INSERT INTO `invoice` (`InvoiceId` ,`FromClientId` ,`ToClientId` ,`InvoiceDate` ,`PaymentTerms` ,`InvoiceNumber`) VALUES (NULL ,%s,%s,CURRENT_TIMESTAMP,%s,%s);",
                common::GetSQLValueString($FromClientId, "int"),
                common::GetSQLValueString($ToClientId, "int"),
                common::GetSQLValueString($PaymentTerms, "text"),
                common::GetSQLValueString($InvoiceNumber, "text")
        );
        mysql_query($sql, $conn) or die(mysql_error().$sql);
        $InvoiceId = mysql_insert_id();
        foreach($invoiceprojects as $projectid) {
            $sql = sprintf
                    ("INSERT IGNORE INTO `invoiceproject` (`InvoiceId` ,`ProjectId`) VALUES (%s,%s);",
                    common::GetSQLValueString($InvoiceId, "int"),
                    common::GetSQLValueString($projectid, "int")
            );
            mysql_query($sql, $conn) or die(mysql_error().$sql);
        }

        $sql = sprintf
                ("INSERT INTO `invoicereport` (`InvoiceId` ,`InvoiceReportHTML`) VALUES (%s,%s);",
                common::GetSQLValueString($InvoiceId, "int"),
                common::GetSQLValueString($InvoiceReportHTML, "text")
        );
        mysql_query($sql, $conn) or die(mysql_error().$sql);


    }
}
?>