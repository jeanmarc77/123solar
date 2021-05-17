<?php
/**
 * /srv/http/123solar/m.php
 *
 * @package default
 */


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<?php
define('checkaccess', TRUE);
include 'config/config_main.php';
include 'scripts/links.php';
date_default_timezone_set($DTZ);
include 'languages/' . $LANG . '.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
echo "<script src='$JSjquery'></script>";
?>
<meta name="theme-color" content="#666633">
<title><?php echo "$TITLE";?></title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link href="images/favicon.ico" rel="icon" type="image/x-icon">
</head>
<body style="background:#d3dae2">
<script type="text/javascript">
$(document).ready(function() {
  function updateit() {
    $.getJSON("programs/programmultilive.php", function(rdata){
    json = eval(rdata);
    GPTOT = eval(json.GPTOT);
      if(isNaN(GPTOT)){
      document.getElementById('GPTOT').innerHTML = '...';
      } else {
      document.getElementById('GPTOT').innerHTML = GPTOT;
      }
    })
  }
updateit();
setInterval(updateit, 2000);
});
</script>
<script type="text/javascript">
$(document).ready(function() {
  function updateit30() {
    $.getJSON("programs/programday.php?invtnum=0", function(rdata){
    json = eval(rdata);
    title = json['title'];
    document.getElementById('ptitle').innerHTML = title ;
    })
  }
updateit30();
setInterval(updateit30, 30000);
});
</script>
<span id='ptitle'>--</span><br>
<?php
echo $lgPOWER;
?> <span id='GPTOT'>--</span>W
<br><br><a href=index.php>Normal version</a>
</body>
</html>
