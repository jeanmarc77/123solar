<body>
<p align="center"><b><?php echo "$TITLE";?></b>&nbsp;<font size="-1">(<?php echo "$SUBTITLE";?>)</font></p>
<hr size=1 width="100%">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="left"><a href="index.php"><?php echo "$lgMINDEX";?></a> | 
<?php
if ($NUMINV>1) {
for ($i=1;$i<=$NUMINV;$i++) {
echo "<a href='index.php?selectinvt=$i'>$lgINVT$i</a> |";
}
}
?>
 <a href="detailed.php"><?php echo "$lgMDETAILED";?></a> | <a href="production.php"><?php echo "$lgMPRODUCTION";?></a> |  <a href="comparison.php"><?php echo "$lgMCOMPARISON";?></a> | <a href="info.php"><?php echo "$lgMINFO";?></a> || <a href="admin/">Admin</a></td>
<td align="right"></td>
</tr>
</table>
<!-- #BeginEditable "mainbox" -->
