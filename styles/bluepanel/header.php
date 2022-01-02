<body>
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center" height="90%">
  <tr bgcolor="#e5e5e5"> 
  <td class="cadretopleft" width="128"><img src="styles/bluepanel/images/panel12880.jpg" width="128" height="80" alt="123Aurora"></td>
  <td class="cadretop" align="center"><b><?php echo "$TITLE";?></b><br><font size="-1"><?php echo "$SUBTITLE";?></font></td>
  <td class="cadretopright" width="128" align="right"></td>
  </tr>
  <tr valign="top"> 
    <td height="100%" COLSPAN="3"> 
      <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" height="100%">
        <tr valign="top"> 
          <td width="128" class="cadrebotleft" bgcolor="#4572A7" height="98%"> 
            <div class="menu"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="5" align="left">
                <tr> 
                  <td></td>
                </tr>
                <tr> 
                  <td><font class="menu"><img src="styles/bluepanel/images/ex2.gif" width="13" height="9"><a href="index.php"><?php echo "$lgMINDEX";?></a></font></td>
                </tr>
                <?php
		  if ($NUMINV>1) {
                for ($i=1;$i<=$NUMINV;$i++) {
                echo "<tr> 
                  <td><font class='menu'><img src='styles/bluepanel/images/ex2.gif' width='13' height='9'><a href='index.php?selectinvt=$i'>$lgINVT$i</a></font></td>
                </tr>";
                }
		  }
                ?>
                <tr> 
                  <td><font class="menu"><img src="styles/bluepanel/images/ex2.gif" width="13" height="9"><a href="detailed.php"><?php echo "$lgMDETAILED";?></a></font></td>
                </tr>

                <tr> 
                  <td><font class="menu"><img src="styles/bluepanel/images/ex2.gif" width="13" height="9"><a href="production.php"><?php echo "$lgMPRODUCTION";?></a></font></td>
                </tr>
		<tr> 
                  <td><font class="menu"><img src="styles/bluepanel/images/ex2.gif" width="13" height="9"><a href="comparison.php"><?php echo "$lgMCOMPARISON";?></a></font></td>
                </tr>
                <tr> 
                  <td><font class="menu"><img src="styles/bluepanel/images/ex2.gif" width="13" height="9"><a href="info.php"><?php echo "$lgMINFO";?></a></font></td>
                </tr>
                <tr> 
                  <td><font class="menu"><img src="styles/bluepanel/images/ex2.gif" width="13" height="9"><a href="admin/">Admin</a></font></td>
                </tr>
                <tr> 
                  <td></td>
                </tr>
              </table>
            </div>
          </td>
          <td class="cadrebotright" bgcolor="#d3dae2" height="98%"> 
            <table border="0" cellspacing="10" cellpadding="0" width="100%" height="100%" align="center">
              <tr valign="top"> 
                <td> <!-- #BeginEditable "mainbox" -->
