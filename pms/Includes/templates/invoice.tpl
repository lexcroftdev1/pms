{literal}
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
{/literal}
    <img src="{$logo}" border="0"><br>
</td>
</tr>
</table>
<br /><br />
{if $extraHeader!= '' }
<table>
    <tr>
        <td align="top">
           {$extraHeader}
        </td>
    </tr>
</table>
{/if}

<table width="500" cellspacing="0" cellpadding="0">
    <tr>
        <td valign="top" width="50%">
            <h3 style="margin-top:1px">{$SenderCompanyPayableName}</h3>
        {$SenderAddress}<br/>
        {if $SenderTelephone!= '' }
            T: {$SenderTelephone}<br/>
        {/if}
        {if $SenderFAX!= '' }
            F: {$SenderFAX}
        {/if}
        </td>
        <td valign="top" width="50%">
            <table>
                <tr>
                    <th align="top">Date:</th>
                    <td valign="top">{$invoice_date}</td>
                </tr>
                <tr>
                    <th valign="top">Invoice No:</th>
                    <td valign="top">{$invoice_number}</td>
                </tr>
                <tr>
                    <th valign="top">Customer:</th>
                    <td valign="top">{$RecipientName}</td>
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
        {$RecipientCompanyPayableName}<br/>
        {$RecipientAddress}<br/>
        {if $RecipientTelephone!= '' }
            T: {$RecipientTelephone}<br/>
        {/if}
        {if $RecipientFAX!= '' }
            F: {$RecipientFAX}
        {/if}
        </td>
    </tr>
</table>
<table width="500" cellspacing="0" cellpadding="0">
    <tr>
        <th align="left" width="90">Date</th>
        <th align="left" width="350">Description</th>
        <th align="left" width="60">Amount</th>
    </tr>
{foreach from=$projects item=project key=key}
    <tr>
        <td align="left" valign="top" width="90">{$project.purchase_date}</td>
        <td align="left" valign="top" width="350"><strong>{$project.project_title}</strong></td>
        <td align="left" valign="top" width="60">{$project.project_cost}</td>
    </tr>
    {section name=j loop=$project.pos}
        <tr>
            <td width="90"></td>
            <td width="350" align="left" class="small"><i>{$project.pos[j].rate} X {$project.pos[j].hours}h
                - {$project.pos[j].discount} Discount</i></td>
            <td width="60" align="left" class="small"><strong>{$project.pos[j].netttoal}</strong></td>
        </tr>
    {/section}
{/foreach}
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
        <td width="60" align="left">{$invoice_total}</td>
    </tr>
    <tr>
        <td width="90">&nbsp;</td>
        <td width="370" align="right">Discount:</td>
        <td width="60" align="left">{$invoice_discount}</td>
    </tr>
    <tr>
        <td width="90">&nbsp;</td>
        <td width="370" align="right">Net value:</td>
        <td width="60" align="left">{$invoice_net}</td>
    </tr>
    <tr>
        <td width="90">&nbsp;</td>
        <td width="370" align="right">Transaction fee:</td>
        <td width="60" align="left">{$invoice_transaction_fee}</td>
    </tr>
    <tr>
        <td width="90">&nbsp;</td>
        <td width="370" align="right">To Due:</td>
        <td width="60" align="left"><strong>{$inovice_balance}</strong></td>
    </tr>

</table>
<br /><br />
{if $extraOther!= '' }
<table  width="500" border="1" bordercolor="GRAY" cellspacing="0" cellpadding="10">
    <tr><th width="500" align="left">Other Comments</th></tr>
    <tr>
        <td align="top" width="500">
           {$extraOther}
        </td>
    </tr>
</table>
<br />
{/if}

{if $extraFooter!= '' }
<table>
    <tr>
        <td align="top">
           {$extraFooter}
        </td>
    </tr>
</table>
{/if}

</body>
</html>