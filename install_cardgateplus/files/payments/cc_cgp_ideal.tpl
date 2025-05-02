{*
$Id: cc_cgp_ideal.tpl,v 0.1 2008/08/21 09:53:14 max Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h3>CardGatePlus iDEAL</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<center>
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<br /><br />
<table border="0" cellspacing="10">
<tr>
	<td>Version:</td>
	<td>{$module_data.param01|escape}</td>
</tr>
<tr>
	<td>Control URL:</td>
	<td>{$http_location}/payment/cc_cgp_ideal.php</td>
</tr>
<tr>
	<td>Mode</td>
	<td>
		<select name="testmode">
			<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>Test</option>
			<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>Live</option>
		</select>
	</td>
</tr>
<tr>
	<td>Site ID:</td>
	<td><input type="text" name="param03" size="24" value="{$module_data.param03|escape}" /></td>
</tr>

<tr>
	<td>Hash key:</td>
	<td><input type="text" name="param04" size="24" value="{$module_data.param04|escape}" /></td>
</tr>
<tr>
	<td>Currency:<BR>(X-Cart currently does not support multiple currencies)</td>
	<td><select name="param05">
	<option value="USD"{if $module_data.param05 eq "USD"} selected="selected"{/if}>U.S. Dollars (USD)</option>
	<option value="EUR"{if $module_data.param05 eq "EUR"} selected="selected"{/if}>Euro (EUR)</option>
	<option value="GBP"{if $module_data.param05 eq "GBP"} selected="selected"{/if}>Pounds Sterling (GBP)</option>
</select></td>
</tr>
<tr>
	<td>Country:</td>
	<td><select name="param06">
	<option value="DETECT"{if $module_data.param06 eq "DETECT"} selected="selected"{/if}>User home address country</option>
</select></td>
</tr>
<tr>
	<td>Language:</td>
	<td><select name="param09">
	<option value="DETECT"{if $module_data.param09 eq "DETECT"} selected="selected"{/if}>User selected language</option>
	<option value="NL"{if $module_data.param09 eq "NL"} selected="selected"{/if}>Dutch</option>
	<option value="EN"{if $module_data.param09 eq "EN"} selected="selected"{/if}>English</option>
	<option value="DE"{if $module_data.param09 eq "DE"} selected="selected"{/if}>German</option>
</select></td>
</tr>
<tr>

	<td>Log to file:<BR>logs directory requires writing privileges (CHMOD 777)</td>
	<td>

<select name="param07">
	<option value="Y"{if $module_data.param07 eq "Y"} selected="selected"{/if}>Yes</option>
	<option value="N"{if $module_data.param07 eq "N"} selected="selected"{/if}>No</option>
</select>

	</td>
</tr>

<tr>
	<td>Order prefix:</td>
	<td>{$module_data.param08|escape}</td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update}" />
</form>
</center>
{/capture}

{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra="width=100%"}