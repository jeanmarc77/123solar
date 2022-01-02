<?php
/**
 * /srv/http/123solar/admin/update.php
 *
 * @package default
 */


include 'secure.php';
include '../scripts/version.php';
include '../scripts/links.php';

$url = 'https://123solar.org/latest_version.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
<title>123Solar Administration</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="stylesheet" href="../styles/default/css/style.css" type="text/css">
<?php
echo "<script src='$JSjquery'></script>";
?>
<script type='text/javascript'>
$(document).ready(function()
{
var vers='<?php echo $VERSION; ?>';

$.ajax({
    url : '<?php echo $url; ?>',
    dataType: 'json',
    type: 'GET',
    success: function(response){
	json =eval(response);
	lastvers =json['LASTVERSION'];

	if (vers!=lastvers) {
	document.getElementById('status').src = '../images/24/sign-warning.png';
	document.getElementById('msg').innerHTML = 'You are running <?php echo $VERSION; ?>, ' + lastvers + ' is available '+
	'<br><br><form method=\'POST\' action=\'update2.php\'>'+
	'<input type=\'submit\' name=\'bntsubmit\' value=\'Update\' onclick=\"if(!confirm(\'The update procedure may require to log in\')){return false;}\">'
	'</form><br>';
	} else {
	document.getElementById('status').src = '../images/24/sign-check.png';
	document.getElementById('msg').innerHTML = 'Neat ! it\'s up to date';
	}
    },
    error: function(){
	document.getElementById('status').src = '../images/24/sign-question.png';
        document.getElementById('msg').innerHTML = 'Time out: can\'t retreive <?php echo $url; ?>';
    },
    timeout: 3000
});

})
</script>
</head>
<body>
<table width="95%" height="80%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr bgcolor="#FFFFFF" height="80">
  <td class="cadretopleft" width="128"><img src="../styles/default/images/sun12880.png" width="128" height="80" alt="123Solar"></td>
  <td class="cadretop" align="center"><b>123Solar Administration</font></td>
  <td class="cadretopright" width="128" align="right"></td>
  </tr>
  <tr bgcolor="#CCCC66">
<td align=right COLSPAN="3" class="cadre" height="10">
&nbsp;
</td></tr>
<tr valign="top">
    <td COLSPAN="3" class="cadrebot" bgcolor="#d3dae2">
<!-- #BeginEditable "mainbox" -->
<br>
<br>
<div align=center>
<img src='../images/24/sign-sync.png' id='status' width=24 height=24>
<span id='msg'>Checking<span>
</div>
<br>
<div align=center><INPUT TYPE='button' onClick="location.href='admin.php'" value='Back'></div>
<br>
<br>
<!-- #EndEditable -->
</td>
</tr>
</table>
</body>
</html>
