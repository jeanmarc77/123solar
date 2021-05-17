<body>
<table width="95%" border=0 cellspacing=0 cellpadding=0 align="center">
  <tr bgcolor="#FFFFFF" height=80> 
  <td class="cadretopleft" width=128><img src="styles/default/images/sun12880.png" width="128" alt="123Solar"></td>
  <td class="cadretop" align="center"><b><?php echo "$TITLE";?></b><br><font size="-1"><?php echo "$SUBTITLE";?></font></td>
  <td class="cadretopright" width=128 align="right"></td>
  </tr>
<tr bgcolor="#CCCC66" valign="top">
<td COLSPAN=3 class="cadre">
            <div class="menu"> 
              <font class="menu">
<table width="100%" border=0 cellspacing=0 cellpadding=0>
<tr><td align='left'>&nbsp;<a href="index.php"><?php echo "$lgMINDEX";?></a> 
<?php
if ($NUMINV>1) {
for ($i=1;$i<=$NUMINV;$i++) {
echo " | <a href='index.php?selectinvt=$i'>$lgINVT$i</a>";
}
}
?>
 | <a href="detailed.php"><?php echo "$lgMDETAILED";?></a> | <a href="production.php"><?php echo "$lgMPRODUCTION";?></a> | <a href="comparison.php"><?php echo "$lgMCOMPARISON";?></a> | <a href="info.php"><?php echo "$lgMINFO";?></a> || <font size="-2"><a href='admin/'>admin</a></font></td>
<td align='right'>&nbsp;</td></tr>
</table>
		</font>
            </div>
</td></tr>
<tr valign="top"> 
    <td COLSPAN=3 class="cadrebot" bgcolor="#d3dae2">
<!-- #BeginEditable "mainbox" -->
