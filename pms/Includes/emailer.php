<?php

class Email {
    public $sendTo = array();

    public $subject;
    public $message;
    public $data = array();
    public $attachments = array();
    public $ProjectId;
    public $PostId;

    private function doSendMail($recipient_name, $recipient_email, $timeZone = 'UTC') {
        date_default_timezone_set($timeZone);
        $to = '"'.$recipient_name . '" <' . $recipient_email . '>';
        $subject = htmlspecialchars_decode($this->subject, ENT_QUOTES);
        $message = htmlspecialchars_decode($this->message, ENT_QUOTES);
        $headers = 'From: "' . EMAILFROMNAME . '" <' . EMAILFROM . ">\r\n" .
            'Reply-To: "' . REPLYTONAME . '" <' . REPLYTO . ">\r\n" .
            'X-Mailer: lexcroft.com/pms';
        //echo($headers);exit;
        return mail($to, $subject, $message, $headers);
    }
    public function sendMail() {
        $orignalData = $this->data;
        $orignalSubject = $this->subject;
        $orignalmessage = $this->message;

        foreach ($this->sendTo as $recipient) {
            $this->data['Email'] = $recipient['Email'];
            $this->data['FULL_NAME'] = $recipient['FULL_NAME'];
            if ($this->PostId !== null) {
                $this->data['URL'] = common::autoLoginURL(URL . 'viewproject.php?ProjectId=' . $this->ProjectId . '#post_' . $this->PostId, $recipient['UserId']);
            } else {
                $this->data['URL'] = common::autoLoginURL(URL . 'viewproject.php?ProjectId=' . $this->ProjectId, $recipient['UserId']);
            }
            $this->substitueData();
            $this->doSendMail($recipient['FULL_NAME'], $recipient['Email'], $recipient['UserTimeZone']);

            $this->data = $orignalData;
            $this->subject = $orignalSubject;
            $this->message = $orignalmessage;
        }

    }

    private function substitueData() {
        foreach ($this->data as $key => $value) {
            $this->message = str_replace('{' . $key . '}', $value, $this->message);
        }
        foreach ($this->data as $key => $value) {
            $this->subject = str_replace('{' . $key . '}', $value, $this->subject);
        }
    }

    public function setRecipientsByClientId($id) {
        global $database_conn, $conn;
        $sql = sprintf("SELECT * FROM user WHERE ClientId = %s", common::GetSQLValueString($id, "int"));
        $rs = mysql_query($sql, $conn) or die(mysql_error());
        $row = mysql_fetch_assoc($rs);
        $this->sendTo = array();
        do {
            $this->sendTo[] = array('ClientId' => $id, 'FULL_NAME' => $row['Name'], 'Email' => $row['Email'], 'AdminInd' => $row['AdminInd'], 'UserId' => $row['UserId'], 'UserTimeZone' => $row['UserTimeZone']);
        } while ($row = mysql_fetch_assoc($rs));
    }

}

class ProjectMail extends Email {
    private $__ProjectId;

    public function __construct($ProjectId) {
        $this->__ProjectId = $ProjectId;
    }

    public function sendProjectStatusChangedMail() {
        $o = new Options();
        eval('$this->message = $o->Project_ChangedStatus_Template;');
        $this->subject = Project_ChangedStatus_Template;
        $this->doSendProjectMail();

    }

    public function sendProjectMail() {
        $o = new Options();
        eval('$this->message = $o->Project_Posted_Template;');
        $this->subject = Project_Posted_Template;
        $this->doSendProjectMail();
    }

    private function doSendProjectMail() {
        global $database_conn, $conn, $LoggedInClientId;

        mysql_select_db($database_conn, $conn);
        $sql = sprintf("SELECT p.ProjectTitle,p.ProjectDescription,p.ClientId,p.ServiceProviderClientId,ps.ProjectStatus FROM project p inner join projectstatus ps on p.ProjectStatusId=ps.ProjectStatusId WHERE p.ProjectId = %s", common::GetSQLValueString($this->__ProjectId, "int"));
        $rs = mysql_query($sql, $conn) or die(mysql_error());

        $projectLine = mysql_fetch_assoc($rs);

        #by default buyer would be the recipient
        $id = $projectLine[ServiceProviderClientId];

        #if logged in user is buyer of this project then
        #the recipient is the client of this project

        if ((int)$projectLine[ServiceProviderClientId] === (int)$LoggedInClientId) {
            $id = $projectLine[ClientId];
        }

        $this->setRecipientsByClientId($id);
        $this->ProjectId = $this->__ProjectId;
        $this->PostId = null;
        $this->subject .= $projectLine['ProjectTitle'];
        $this->data = array('TITLE' => $projectLine['ProjectTitle'], 'DESCRIPTION' => $projectLine['ProjectDescription'], 'STATUS' => $projectLine['ProjectStatus'], 'URL' => $url);

        //var_dump($this->data);
        $this->sendMail();
    }
}

class POMail extends Email {
    private $__PurchaseOrderId;
    private $checkPurchaseOrderStatus;

    public function __construct($PurchaseOrderId, $POApproved = true) {
        $this->__PurchaseOrderId = $PurchaseOrderId;
        $this->checkPurchaseOrderStatus = $POApproved;
    }

    public function sendPurchaseOrderStatusChangedMail() {
        $o = new Options();
        if ($this->checkPurchaseOrderStatus === true) {
            eval('$this->message = $o->PO_Approved_Template;');
            $this->subject = PO_Approved_Template;
        } else {
            eval('$this->message = $o->PO_Rejected_Template;');
            $this->subject = PO_Rejected_Template;
        }
        $this->SendPOMail();
    }

    public function sendAddPOMail() {
        $o = new Options();

        eval('$this->message = $o->PO_Added_Template;');
        $this->subject = PO_Added_Template;

        $this->SendPOMail();

    }

    public function sendBonusMail() {
        $o = new Options();

        eval('$this->message = $o->PO_Bonus_Template;');
        $this->subject = PO_Bonus_Template;

        $this->SendPOMail();

    }

    public function sendPOChangedMail() {
        $o = new Options();

        eval('$this->message = $o->PO_Changed_Template;');
        $this->subject = PO_Changed_Template;

        $this->SendPOMail();

    }

    public function sendPODeletedMail() {
        $o = new Options();

        eval('$this->message = $o->PO_Deleted_Template;');
        $this->subject = PO_Deleted_Template;

        $this->SendPOMail();

    }

    private function SendPOMail() {
        global $database_conn, $conn, $LoggedInClientId;

        mysql_select_db($database_conn, $conn);
        $sql = sprintf("SELECT p.ProjectId,p.ServiceProviderClientId,p.ClientId,p.ProjectTitle,p.ProjectDescription FROM project p inner join purchaseorder po on p.ProjectId=po.ProjectId WHERE po.PurchaseOrderId = %s", common::GetSQLValueString($this->__PurchaseOrderId, "int"));
        $rs = mysql_query($sql, $conn) or die(mysql_error());
        $projectLine = mysql_fetch_assoc($rs);

        #by default buyer would be the recipient
        $id = $projectLine[ServiceProviderClientId];

        #if logged in user is buyer of this project then
        #the recipient is the client of this project

        if ((int)$projectLine[ServiceProviderClientId] === (int)$LoggedInClientId) {
            $id = $projectLine[ClientId];
        }

        $this->setRecipientsByClientId($id);
        $this->ProjectId = $projectLine['ProjectId'];
        $this->PostId = null;
        $this->data = array('TITLE' => $projectLine['ProjectTitle'], 'DESCRIPTION' => $projectLine['ProjectDescription'], 'URL' => $url);
        $this->sendMail();

    }
}

class PostMail extends Email {
    private $__PostId;

    public function __construct($PostId) {
        $this->__PostId = $PostId;
    }

    public function sendPostMail() {
        $o = new Options();

        eval('$this->message = $o->Message_Posted_Template;');
        $this->subject = Message_Posted_Template;

        global $database_conn, $conn, $LoggedInClientId;

        mysql_select_db($database_conn, $conn);
        $sql = sprintf("SELECT p.ProjectId,p.ProjectTitle,p.ProjectDescription,p.ServiceProviderClientId,p.ClientId,pp.PosterId,pp.PostTitle FROM project p inner join projectpost pp on p.ProjectId=pp.ProjectId WHERE pp.PostId = %s", common::GetSQLValueString($this->__PostId, "int"));
        $rs = mysql_query($sql, $conn) or die(mysql_error());
        $projectLine = mysql_fetch_assoc($rs);


        #by default buyer would be the recipient
        $id = $projectLine[ServiceProviderClientId];

        #if logged in user is buyer of this project then
        #the recipient is the client of this project

        if ((int)$projectLine[ServiceProviderClientId] === (int)$LoggedInClientId) {
            $id = $projectLine[ClientId];
        }

        $this->setRecipientsByClientId($id);


        $sql = sprintf("SELECT PostData FROM postdata WHERE PostId = %s", common::GetSQLValueString($this->__PostId, "int"));
        $rsMsg = mysql_query($sql, $conn) or die(mysql_error());
        $rowMsg = mysql_fetch_assoc($rsMsg);
        $this->subject .= $projectLine['ProjectTitle'];
        $this->ProjectId = $projectLine['ProjectId'];
        $this->PostId = $this->__PostId;

        $this->data = array('TITLE' => $projectLine['ProjectTitle'], 'DESCRIPTION' => $projectLine['ProjectDescription'], 'URL' => $url, 'SUBJECT' => $projectLine['PostTitle'], 'MESSAGE' => $rowMsg['PostData']);

        $this->sendMail();
    }
}

?>