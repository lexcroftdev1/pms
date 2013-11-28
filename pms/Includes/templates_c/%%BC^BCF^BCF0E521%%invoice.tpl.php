<?php /* Smarty version 2.6.26, created on 2011-05-10 12:10:01
         compiled from invoice.tpl */ ?>
<?php echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
    <style>
        p {
            font-family: Tahoma, Arial, Verdana;
            color: #000000;
        }

        td {
            font-family: Tahoma, Arial, Verdana;
            font-size: 12px;
            color: #000000;
        }

        td.small {
            font-family: Tahoma, Arial, Verdana;
            font-size: 10px;
            color: #000000;
        }

        th {
            background-color: GRAY;
            color: #FFFFFF;
            margin-bottom: 2px;
            font-size: 14px;
            padding: 5px;
        }

        h2 {
            font-size: 16px;
            color: #000000;
            margin: 0px;
        }

        h3 {
            color: #000000;
            margin: 0px;
        }

        h1 {
            margin: 1px;
            color: #000000;
        }

        hr {
            margin: 0px;
            height: 1px;
            border: 0px;
            padding: 0px;
            background-color: GRAY;
            height: 1px;
        }
    </style>

</head>
<body>
<table width="500" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" width="50%">
'; ?>

    <img src="<?php echo $this->_tpl_vars['logo']; ?>
" border="0"><br>
</td>
</tr>
</table>
<br /><br />
<?php if ($this->_tpl_vars['extraHeader'] != ''): ?>
<table>
    <tr>
        <td align="top">
           <?php echo $this->_tpl_vars['extraHeader']; ?>

        </td>
    </tr>
</table>
<?php endif; ?>

<table width="500" cellspacing="0" cellpadding="0">
    <tr>
        <td valign="top" width="50%">
            <h3 style="margin-top:1px"><?php echo $this->_tpl_vars['SenderCompanyPayableName']; ?>
</h3>
        <?php echo $this->_tpl_vars['SenderAddress']; ?>
<br/>
        <?php if ($this->_tpl_vars['SenderTelephone'] != ''): ?>
            T: <?php echo $this->_tpl_vars['SenderTelephone']; ?>
<br/>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['SenderFAX'] != ''): ?>
            F: <?php echo $this->_tpl_vars['SenderFAX']; ?>

        <?php endif; ?>
        </td>
        <td valign="top" width="50%">
            <table>
                <tr>
                    <th align="top">Date:</th>
                    <td valign="top"><?php echo $this->_tpl_vars['invoice_date']; ?>
</td>
                </tr>
                <tr>
                    <th valign="top">Invoice No:</th>
                    <td valign="top"><?php echo $this->_tpl_vars['invoice_number']; ?>
</td>
                </tr>
                <tr>
                    <th valign="top">Customer:</th>
                    <td valign="top"><?php echo $this->_tpl_vars['RecipientName']; ?>
</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br/>
<table width="500" cellspacing="0" cellpadding="0">
    <tr>
        <td valign="top" width="50%">
            <h3>Bill To:</h3>
        <?php echo $this->_tpl_vars['RecipientCompanyPayableName']; ?>
<br/>
        <?php echo $this->_tpl_vars['RecipientAddress']; ?>
<br/>
        <?php if ($this->_tpl_vars['RecipientTelephone'] != ''): ?>
            T: <?php echo $this->_tpl_vars['RecipientTelephone']; ?>
<br/>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['RecipientFAX'] != ''): ?>
            F: <?php echo $this->_tpl_vars['RecipientFAX']; ?>

        <?php endif; ?>
        </td>
    </tr>
</table>
<table width="500" cellspacing="0" cellpadding="0">
    <tr>
        <th align="left" width="90">Date</th>
        <th align="left" width="350">Description</th>
        <th align="left" width="60">Amount</th>
    </tr>
<?php $_from = $this->_tpl_vars['projects']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['project']):
?>
    <tr>
        <td align="left" valign="top" width="90"><?php echo $this->_tpl_vars['project']['purchase_date']; ?>
</td>
        <td align="left" valign="top" width="350"><strong><?php echo $this->_tpl_vars['project']['project_title']; ?>
</strong></td>
        <td align="left" valign="top" width="60"><?php echo $this->_tpl_vars['project']['project_cost']; ?>
</td>
    </tr>
    <?php unset($this->_sections['j']);
$this->_sections['j']['name'] = 'j';
$this->_sections['j']['loop'] = is_array($_loop=$this->_tpl_vars['project']['pos']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['j']['show'] = true;
$this->_sections['j']['max'] = $this->_sections['j']['loop'];
$this->_sections['j']['step'] = 1;
$this->_sections['j']['start'] = $this->_sections['j']['step'] > 0 ? 0 : $this->_sections['j']['loop']-1;
if ($this->_sections['j']['show']) {
    $this->_sections['j']['total'] = $this->_sections['j']['loop'];
    if ($this->_sections['j']['total'] == 0)
        $this->_sections['j']['show'] = false;
} else
    $this->_sections['j']['total'] = 0;
if ($this->_sections['j']['show']):

            for ($this->_sections['j']['index'] = $this->_sections['j']['start'], $this->_sections['j']['iteration'] = 1;
                 $this->_sections['j']['iteration'] <= $this->_sections['j']['total'];
                 $this->_sections['j']['index'] += $this->_sections['j']['step'], $this->_sections['j']['iteration']++):
$this->_sections['j']['rownum'] = $this->_sections['j']['iteration'];
$this->_sections['j']['index_prev'] = $this->_sections['j']['index'] - $this->_sections['j']['step'];
$this->_sections['j']['index_next'] = $this->_sections['j']['index'] + $this->_sections['j']['step'];
$this->_sections['j']['first']      = ($this->_sections['j']['iteration'] == 1);
$this->_sections['j']['last']       = ($this->_sections['j']['iteration'] == $this->_sections['j']['total']);
?>
        <tr>
            <td width="90"></td>
            <td width="350" align="left" class="small"><i><?php echo $this->_tpl_vars['project']['pos'][$this->_sections['j']['index']]['rate']; ?>
 X <?php echo $this->_tpl_vars['project']['pos'][$this->_sections['j']['index']]['hours']; ?>
h
                - <?php echo $this->_tpl_vars['project']['pos'][$this->_sections['j']['index']]['discount']; ?>
 Discount</i></td>
            <td width="60" align="left" class="small"><strong><?php echo $this->_tpl_vars['project']['pos'][$this->_sections['j']['index']]['netttoal']; ?>
</strong></td>
        </tr>
    <?php endfor; endif; ?>
<?php endforeach; endif; unset($_from); ?>
</table>
<table width="520" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="3" align="left">
            Summary
        </th>
    </tr>
    <tr>
        <td width="90">&nbsp;</td>
        <td width="370" align="right">Total Amount:</td>
        <td width="60" align="left"><?php echo $this->_tpl_vars['invoice_total']; ?>
</td>
    </tr>
    <tr>
        <td width="90">&nbsp;</td>
        <td width="370" align="right">Discount:</td>
        <td width="60" align="left"><?php echo $this->_tpl_vars['invoice_discount']; ?>
</td>
    </tr>
    <tr>
        <td width="90">&nbsp;</td>
        <td width="370" align="right">Net value:</td>
        <td width="60" align="left"><?php echo $this->_tpl_vars['invoice_net']; ?>
</td>
    </tr>
    <tr>
        <td width="90">&nbsp;</td>
        <td width="370" align="right">Transaction fee:</td>
        <td width="60" align="left"><?php echo $this->_tpl_vars['invoice_transaction_fee']; ?>
</td>
    </tr>
    <tr>
        <td width="90">&nbsp;</td>
        <td width="370" align="right">To Due:</td>
        <td width="60" align="left"><strong><?php echo $this->_tpl_vars['inovice_balance']; ?>
</strong></td>
    </tr>

</table>
<br /><br />
<?php if ($this->_tpl_vars['extraOther'] != ''): ?>
<table  width="500" border="1" bordercolor="GRAY" cellspacing="0" cellpadding="10">
    <tr><th width="500" align="left">Other Comments</th></tr>
    <tr>
        <td align="top" width="500">
           <?php echo $this->_tpl_vars['extraOther']; ?>

        </td>
    </tr>
</table>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['extraFooter'] != ''): ?>
<table>
    <tr>
        <td align="top">
           <?php echo $this->_tpl_vars['extraFooter']; ?>

        </td>
    </tr>
</table>
<?php endif; ?>

</body>
</html>