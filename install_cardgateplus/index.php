<?php

include_once( "includes/configuration.php" );
include_once( "includes/controller.php" );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CardGatePlus X-Cart patch installer</title>
<style type="text/css">
<!--
.infotable {
	border: 1px solid #D9D9D9;
	font-size: 11px;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #333;
}

}
.installertitle {
	height:35px;
 	align:center;
	valign:middle;
	background-color:#ff0000;
}
.steptitle {
	font-size: 12px;
}
.patchdata {
	font-size: 12px;
	font-weight: bold;
}
.noticetable {
	border: 1px solid #C00;
	font-size: 11px;
	color: #C00;
}
.notice {
	color: #C00;
}
-->
</style>
</head>

<body>
<table style="width:628px; border:0; margin-left:auto;margin-right:auto; cellpadding:0; cellspacing:0;">
  <tr>
    <td height="117" align="center" valign="top">
    <table width="88%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="53%" align="center" valign="middle"><img src="img/cardgateplus.png" width="207" height="58" /></td>
        <td width="47%" align="center" valign="middle"><img src="img/xcart_logo.gif" width="170" height="67" /></td>
      </tr>
      <tr>
        <td align="center" valign="top" class="steptitle"><span class="patchdata">Module version <?php echo CARDGATEPLUS_PLUGIN_VERSION ?></span></td>
        <td align="center" valign="top" class="patchdata">Compatible with <?php echo XCART_COMPATIBILITY ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td style="height:113px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;">
    <table width="100%" height="109" border="0" cellpadding="2" cellspacing="0">
      <tr>
        <td colspan=5 align="center" style="background-color:#94b8ff" class="installertitle">CardGatePlus payment module patch  for X-Cart</td>
        </tr>
      <tr style="background-color:#c0c0c0">
        <td width="115" align="center" valign="middle" class="steptitle">1. Welcome<?php echo $statimg[0] ?></td>
        <td width="115" align="center" valign="middle" class="steptitle">2. Configuration<?php echo $statimg[1] ?></td>
        <td width="115" align="center" valign="middle" class="steptitle">3. Files<?php echo $statimg[2] ?></td>
        <td width="115" align="center" valign="middle" class="steptitle">4. Database<?php echo $statimg[3] ?></td>
        <td width="115" align="center" valign="middle" class="steptitle">5. Completed<?php echo $statimg[4] ?></td>
      </tr>
    </table></td>
  </tr>
  <tr style="background-color:#c0c0c0">
    <td align="center" valign="top">
<?php
if ($step["notify"]){
?>
      <br /><table width="100%" border="0" cellpadding="6" cellspacing="0" class="infotable">
        <tr>
          <td height="68" align="center" valign="top"><p><?php echo $step["notify"] ?></p></td>
        </tr>
    </table>
<?php
};
if ($step["error"]){
?>
    <br />
    <table width="100%" border="0" cellpadding="6" cellspacing="0" class="noticetable">
      <tr>
        <td height="68" align="center" valign="top"><p class="notice"><?php echo $step["error"] ?></p></td>
      </tr>
    </table>
<?php
};
?>
	</td>
  </tr>
  <tr>
    <td align="center" valign="top">
<?php
if ($step["status"] == "ok" && (int)$current_step != $laststep){
?>
<table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="171" height="51" align="center" valign="bottom"><a href="?cs=<?php echo(((int)$current_step + 1)) ?>"><img src="img/next.gif" width="175" height="34" border="0" /></a></td>
      </tr>
    </table>
<?php
};
if ((int)$current_step == $laststep){
?>
    <table width="80%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td align="center" valign="middle"><a href="../admin/payment_methods.php" target="_blank">Open your X-cart Admin </a></td>
      </tr>
    </table>
<?php
};
?>
	</td>
  </tr>
</table>
</body>
</html>
