{* config_load file="test.conf" section="setup" *}
{include file="header.tpl" title = #title#|capitalize}
<PRE>
养老保险，医疗保险，失业保险，工伤保险，生育保险，五险扣除金额{$salary}
试用期日工资{$onedaysalary}

{* 从配置文件读取是否粗体 *}
    {if #bold#}<b>{/if}
        {* 首字母转为大写 *}
        Title: {#title#|capitalize}
        {if #bold#}</b>{/if}

现在时间是 {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}

全局变量 $SCRIPT_NAME 是 {$SCRIPT_NAME}

获取的服务器SERVER_NAME: {$smarty.server.SERVER_NAME}

{ldelim}$Name{rdelim}的值是<b>{$Name}</b>

变量修饰器示例：{ldelim}$Name|upper{rdelim}转大写后为：<b>{$Name|upper}</b>

一个section选择器循环

    {section name=outer
    loop=$FirstName}
        {if $smarty.section.outer.index is odd by 2}
            {$smarty.section.outer.rownum} . {$FirstName[outer]} {$LastName[outer]}
        {else}
            {$smarty.section.outer.rownum} * {$FirstName[outer]} {$LastName[outer]}
        {/if}
        {sectionelse}
        none
    {/section}

    An example of section looped key values:

    {section name=sec1 loop=$contacts}
        phone: {$contacts[sec1].phone}
        <br>
            fax: {$contacts[sec1].fax}
        <br>
            cell: {$contacts[sec1].cell}
        <br>
    {/section}
    <p>
        testing strip tags
        {strip}
<table border=0>
    <tr>
        <td>
            <A HREF="{$SCRIPT_NAME}">
                <font color="red">This is a test </font>
            </A>
        </td>
    </tr>
</table>
    {/strip}
</PRE>
<script language="JavaScript" type="text/javascript">
    function myJsFunction1(){ldelim}
        alert("The server name\n{$smarty.server.SERVER_NAME}\n{$smarty.server.SERVER_ADDR}");
    {rdelim}
</script>
<a href="javascript:myJsFunction1()">Click here for Server Info</a>
This is an example of the html_select_date function:

<form>
    {html_select_date start_year=1998 end_year=2010}
</form>

This is an example of the html_select_time function:

<form>
    {html_select_time use_24_hours=false}
</form>

This is an example of the html_options function:

<form>
    <select name=states>
        {html_options values=$option_values selected=$option_selected output=$option_output}
    </select>
</form>

{include file="footer.tpl"}
