<?php
/**
 * /srv/http/123solar/detailed.php
 *
 * @package default
 */

include "styles/globalheader.php";
include 'config/config_main.php';
date_default_timezone_set("$DTZ");

if (!empty($_POST['invtnum']) && is_numeric($_POST['invtnum'])) {
	$invtnum = $_POST['invtnum'];
} else if (!empty($_GET['invtnum']) && is_numeric($_GET['invtnum'])) {
	$invtnum = $_GET['invtnum'];
} else {
	$invtnum = 1;
}
include "config/config_invt$invtnum.php";

$dir    = 'data/invt' . $invtnum . '/csv/';
$output = glob($dir . "*.csv");
sort($output);
$contalogs = count($output);

$ollog  = $output[0];
$lstlog = $output[$contalogs - 1];

$startdate = (substr($ollog, -12, 4)) . "," . (substr($ollog, -8, 2)) . "-1," . (substr($ollog, -6, 2));
$stopdate  = (substr($lstlog, -12, 4)) . "," . (substr($lstlog, -8, 2)) . "-1," . (substr($lstlog, -6, 2));
echo "
<script>
  $(function() {
	$('#datepickid' ).datepicker({ dateFormat: 'dd/mm/yy' ,minDate: new Date($startdate), maxDate: new Date($stopdate)});
	$('#oneDayBack').click(function() {
		var new_dateb = $('#datepickid').datepicker('getDate');
		new_dateb.setDate(new_dateb.getDate() - 1);
		$('#datepickid').datepicker('setDate', new_dateb);
	});
	$('#oneDayFwd').click(function() {
		var new_datef = $('#datepickid').datepicker('getDate');
		new_datef.setDate(new_datef.getDate() + 1);
		$('#datepickid').datepicker('setDate', new_datef);
	});
    });
</script>";

$regexp = "/[0-9]{1,2}+\/[0-9]{1,2}+\/[0-9]{4}/";
if (!empty($_POST['date1']) && preg_match($regexp, $_POST['date1'])) {
	$date1 = $_POST['date1'];
} else {
	$date1 = (substr($lstlog, -6, 2)) . "/" . (substr($lstlog, -8, 2)) . "/" . (substr($lstlog, -12, 4));
}
if (!empty($_GET['date2']) && is_numeric($_GET['date2'])) {
	$date2 = $_GET['date2'];
}
if (!empty($date2)) {
	$ts    = strftime("%s", floor($date2 / 1000));
	$date1 = date('d/m/Y', $ts);
}

if (!empty($_POST['checkavgpower'])) {
	$checkavgpower = 'on';
} else {
	$checkavgpower = '';
}
if (!empty($_POST['checkPROD'])) {
	$checkPROD = 'on';
} else {
	$checkPROD = '';
}
if (!empty($_POST['checkPERF'])) {
	$checkPERF = 'on';
} else {
	$checkPERF = '';
}
if (!empty($_POST['checkIV1'])) {
	$checkIV1 = 'on';
} else {
	$checkIV1 = '';
}
if (!empty($_POST['checkIA1'])) {
	$checkIA1 = 'on';
} else {
	$checkIA1 = '';
}
if (!empty($_POST['checkIP1'])) {
	$checkIP1 = 'on';
} else {
	$checkIP1 = '';
}
if (!empty($_POST['checkPERF1'])) {
	$checkPERF1 = 'on';
} else {
	$checkPERF1 = '';
}
if (!empty($_POST['checkIV2'])) {
	$checkIV2 = 'on';
} else {
	$checkIV2 = '';
}
if (!empty($_POST['checkIA2'])) {
	$checkIA2 = 'on';
} else {
	$checkIA2 = '';
}
if (!empty($_POST['checkIP2'])) {
	$checkIP2 = 'on';
} else {
	$checkIP2 = '';
}
if (!empty($_POST['checkPERF2'])) {
	$checkPERF2 = 'on';
} else {
	$checkPERF2 = '';
}
if (!empty($_POST['checkIV3'])) {
	$checkIV3 = 'on';
} else {
	$checkIV3 = '';
}
if (!empty($_POST['checkIA3'])) {
	$checkIA3 = 'on';
} else {
	$checkIA3 = '';
}
if (!empty($_POST['checkIP3'])) {
	$checkIP3 = 'on';
} else {
	$checkIP3 = '';
}
if (!empty($_POST['checkPERF3'])) {
	$checkPERF3 = 'on';
} else {
	$checkPERF3 = '';
}
if (!empty($_POST['checkIV4'])) {
	$checkIV4 = 'on';
} else {
	$checkIV4 = '';
}
if (!empty($_POST['checkIA4'])) {
	$checkIA4 = 'on';
} else {
	$checkIA4 = '';
}
if (!empty($_POST['checkIP4'])) {
	$checkIP4 = 'on';
} else {
	$checkIP4 = '';
}
if (!empty($_POST['checkPERF4'])) {
	$checkPERF4 = 'on';
} else {
	$checkPERF4 = '';
}
if (!empty($_POST['checkG1V'])) {
	$checkG1V = 'on';
} else {
	$checkG1V = '';
}
if (!empty($_POST['checkG1A'])) {
	$checkG1A = 'on';
} else {
	$checkG1A = '';
}
if (!empty($_POST['checkG1P'])) {
	$checkG1P = 'on';
} else {
	$checkG1P = '';
}
if (!empty($_POST['checkG2V'])) {
	$checkG2V = 'on';
} else {
	$checkG2V = '';
}
if (!empty($_POST['checkG2A'])) {
	$checkG2A = 'on';
} else {
	$checkG2A = '';
}
if (!empty($_POST['checkG2P'])) {
	$checkG2P = 'on';
} else {
	$checkG2P = '';
}
if (!empty($_POST['checkG3V'])) {
	$checkG3V = 'on';
} else {
	$checkG3V = '';
}
if (!empty($_POST['checkG3A'])) {
	$checkG3A = 'on';
} else {
	$checkG3A = '';
}
if (!empty($_POST['checkG3P'])) {
	$checkG3P = 'on';
} else {
	$checkG3P = '';
}
if (!empty($_POST['checkFRQ'])) {
	$checkFRQ = 'on';
} else {
	$checkFRQ = '';
}
if (!empty($_POST['checkEFF'])) {
	$checkEFF = 'on';
} else {
	$checkEFF = '';
}
if (!empty($_POST['checkINVT'])) {
	$checkINVT = 'on';
} else {
	$checkINVT = '';
}
if (!empty($_POST['checkBOOT'])) {
	$checkBOOT = 'on';
} else {
	$checkBOOT = '';
}
if (!empty($_POST['checkSR'])) {
	$checkSR = 'on';
} else {
	$checkSR = '';
}

//Nothing selected
if ($checkavgpower != 'on' && $checkPROD != 'on' && $checkPERF != 'on' && $checkIV1 != 'on' && $checkIA1 != 'on' && $checkIP1 != 'on' && $checkPERF1 != 'on' && $checkIV2 != 'on' && $checkIA2 != 'on' && $checkIP2 != 'on' && $checkPERF2 != 'on' && $checkIV3 != 'on' && $checkIA3 != 'on' && $checkIP3 != 'on' && $checkPERF3 != 'on' && $checkIV4 != 'on' && $checkIA4 != 'on' && $checkIP4 != 'on' && $checkPERF4 != 'on' && $checkG1V != 'on' && $checkG1A != 'on' && $checkG1P != 'on' && $checkG2V != 'on' && $checkG2A != 'on' && $checkG2P != 'on' && $checkG3V != 'on' && $checkG3A != 'on' && $checkG3P != 'on' && $checkFRQ != 'on' && $checkEFF != 'on' && $checkINVT != 'on' && $checkBOOT != 'on' && $checkSR != 'on') {
	$checkavgpower = 'on';
}
$titledate = substr($date1, 0, 10);
$csvdate1  = (substr($date1, 6, 4)) . (substr($date1, 3, 2)) . (substr($date1, 0, 2)) . ".csv";

echo "
<script type='text/javascript'>

$(document).ready(function()
{
Highcharts.setOptions({
global: {
useUTC: true
},
lang: {
decimalPoint: '$DPOINT',
thousandsSep: '$THSEP',
drillUpText: '$lgDRILLUP',
loading: '$lgLOAD',
printChart: '$lgPRINT',
resetZoom: '$lgRESETZ'
}
});

var Mychart, options = {
chart: {
backgroundColor: null,
zoomType: 'xy',
resetZoomButton: {
                position: {
                    align: 'right',
                    verticalAlign: 'top'
                }
},
loading: {
 labelStyle: { top: '45%' },
 style: { backgroundColor: null }
},
spaceRight:20
},
credits: {enabled: false},
title: {
text: 'loading..',
style: {fontSize: '1em'}
},
subtitle: { text: '$lgDETAILSUBTITLE' },
xAxis: {
type: 'datetime',
maxZoom: 300000,
dateTimeLabelFormats: {minute: '%H:%M'}
},
yAxis: [";
if ($checkavgpower == 'on' || $checkIP1 == 'on' || $checkIP2 == 'on' || $checkIP3 == 'on' || $checkIP4 == 'on' || $checkG1P == 'on' || $checkG2P == 'on' || $checkG3P == 'on') {
	echo "{
labels: { formatter: function() { return this.value +'W';}},
title: { text: '$lgPOWER'}
},";
}
if ($checkPROD == 'on') {
	echo "
{
labels: { formatter: function() { return this.value +'kWh';}},
title: { text: 'Prod'},
opposite: true
},";
}
if ($checkPERF == 'on' || $checkPERF1 == 'on' || $checkPERF2 == 'on' || $checkPERF3 == 'on' || $checkPERF4 == 'on') {
	echo "
{
labels: { formatter: function() { return this.value +'mW/mWp';}},
title: { text: 'Perf'}
},";
}
if ($checkIV1 == 'on' || $checkIV2 == 'on' || $checkIV3 == 'on' || $checkIV4 == 'on' || $checkG1V == 'on' || $checkG2V == 'on' || $checkG3V == 'on') {
	echo "{
labels: { formatter: function() { return this.value +'V';}},
title: { text: '$lgVOLTAGE'}
},";
}
if ($checkIA1 == 'on' || $checkIA2 == 'on' || $checkIA3 == 'on' || $checkIA4 == 'on' || $checkG1A == 'on' || $checkG2A == 'on' || $checkG3A == 'on') {
	echo "{
labels: { formatter: function() { return this.value +'A';}},
title: { text: '$lgCURRENT'}
},";
}
if ($checkFRQ == 'on') {
	echo "{
labels: { formatter: function() { return this.value +'Hz';}},
title: { text: '$lgFREQ'},
opposite: true
},";
}
if ($checkEFF == 'on') {
	echo "{
labels: { formatter: function() { return this.value +'%';}},
title: { text: '$lgEFF'},
opposite: true
},";
}
if ($checkINVT == 'on' || $checkBOOT == 'on') {
	echo "{
labels: { formatter: function() { return this.value +'c';}},
title: { text: '$lgTEMP'},
opposite: true
},";
}
if ($checkSR == 'on') {
	echo "{
labels: { formatter: function() { return this.value +'W/m²';}},
title: { text: '$lgSENSOR'},
opposite: true
},";
}
echo "
],
tooltip: {
formatter: function() {
  var unit = {
  '$lgDPOWERAVG': 'W',
  '$lgPROD': 'kWh',
  '$lgDPERF' : 'mW/mWp',
  '$lgARRAY 1 $lgDCURRENT': 'A',
  '$lgARRAY 2 $lgDCURRENT': 'A',
  '$lgARRAY 3 $lgDCURRENT': 'A',
  '$lgARRAY 4 $lgDCURRENT': 'A',
  '$lgARRAY 1 $lgDVOLTAGE': 'V',
  '$lgARRAY 2 $lgDVOLTAGE': 'V',
  '$lgARRAY 3 $lgDVOLTAGE': 'V',
  '$lgARRAY 4 $lgDVOLTAGE': 'V',
  '$lgARRAY 1 $lgDPOWER': 'W',
  '$lgARRAY 2 $lgDPOWER': 'W',
  '$lgARRAY 3 $lgDPOWER': 'W',
  '$lgARRAY 4 $lgDPOWER': 'W',
  '$lgARRAY 1 $lgDPERF' : 'mW/mWp',
  '$lgARRAY 2 $lgDPERF' : 'mW/mWp',
  '$lgARRAY 3 $lgDPERF' : 'mW/mWp',
  '$lgARRAY 4 $lgDPERF' : 'mW/mWp',
  '$lgGRID $lgDVOLTAGE $lgDPHASE 1' : 'V',
  '$lgGRID $lgDVOLTAGE $lgDPHASE 2' : 'V',
  '$lgGRID $lgDVOLTAGE $lgDPHASE 3' : 'V',
  '$lgGRID $lgDCURRENT $lgDPHASE 1' : 'A',
  '$lgGRID $lgDCURRENT $lgDPHASE 2' : 'A',
  '$lgGRID $lgDCURRENT $lgDPHASE 3' : 'A',
  '$lgGRID $lgDPOWER $lgDPHASE 1' : 'W',
  '$lgGRID $lgDPOWER $lgDPHASE 2' : 'W',
  '$lgGRID $lgDPOWER $lgDPHASE 3' : 'W',
  '$lgDFREQ': 'Hz',
  '$lgDEFF': '%',
  '$lgDINVERTER $lgDTEMP': 'c',
  '$lgDBOOSTER $lgDTEMP': 'c',
  '$lgSENSOR': 'W/m²'
  }[this.series.name];
return '<b>' + Highcharts.numberFormat(this.y,'1') + ' ' + unit + '</b><br/>' + Highcharts.dateFormat('%H:%M', this.x)
}
},
legend: {
layout: 'horizontal',
align: 'center',
floating: false,
backgroundColor: '#FFFFFF'
},
plotOptions: {
 areaspline: {
 threshold: null,
 softThreshold: true,
 fillOpacity: 0.3
 }
},
exporting: {
filename: '123Solar-chart',
width: 1200
},
series: []
};
"; // End of echo

// transmit the value to proceed them via _GET
echo "
var invtnum = $invtnum
var date1 = '$csvdate1'
var checkavgpower='$checkavgpower'
var checkPROD='$checkPROD'
var checkPERF='$checkPERF'
var checkIV1='$checkIV1'
var checkIA1='$checkIA1'
var checkIP1='$checkIP1'
var checkPERF1='$checkPERF1'
var checkIV2='$checkIV2'
var checkIA2='$checkIA2'
var checkIP2='$checkIP2'
var checkPERF2='$checkPERF2'
var checkIV3='$checkIV3'
var checkIA3='$checkIA3'
var checkIP3='$checkIP3'
var checkPERF3='$checkPERF3'
var checkIV4='$checkIV4'
var checkIA4='$checkIA4'
var checkIP4='$checkIP4'
var checkPERF4='$checkPERF4'
var checkG1V='$checkG1V'
var checkG1A='$checkG1A'
var checkG1P='$checkG1P'
var checkG2V='$checkG2V'
var checkG2A='$checkG2A'
var checkG2P='$checkG2P'
var checkG3V='$checkG3V'
var checkG3A='$checkG3A'
var checkG3P='$checkG3P'
var checkFRQ='$checkFRQ'
var checkEFF='$checkEFF'
var checkINVT='$checkINVT'
var checkBOOT='$checkBOOT'
var checkSR='$checkSR'

Mychart= Highcharts.chart('container',options);
Mychart.showLoading();
Mychart.setTitle({text: '...'});
  $.getJSON('programs/programdetailed.php', { invtnum: invtnum, date1: date1, checkPROD: checkPROD, checkPERF: checkPERF, checkavgpower: checkavgpower, checkIV1: checkIV1, checkIA1: checkIA1, checkIP1: checkIP1, checkPERF1: checkPERF1, checkIV2: checkIV2, checkIA2: checkIA2, checkIP2: checkIP2, checkPERF2: checkPERF2, checkIV3: checkIV3, checkIA3: checkIA3, checkIP3: checkIP3, checkPERF3: checkPERF3, checkIV4: checkIV4, checkIA4: checkIA4, checkIP4: checkIP4, checkPERF4: checkPERF4, checkG1V: checkG1V, checkG1A: checkG1A, checkG1P: checkG1P, checkG2V: checkG2V, checkG2A: checkG2A, checkG2P: checkG2P, checkG3V: checkG3V, checkG3A: checkG3A, checkG3P: checkG3P, checkFRQ: checkFRQ, checkEFF: checkEFF, checkINVT: checkINVT, checkBOOT: checkBOOT, checkSR: checkSR }, function(JSONResponse)
{
options.series = JSONResponse.data;
Mychart= Highcharts.chart('container',options);
Mychart.setTitle({text: JSONResponse.title});
Mychart.hideLoading();
});

});
</script>

<div align='center'>
<div id='container' style='width: 100%; height: 400px'></div>";

$year     = substr($csvdate1, 0, 4);
$month    = substr($csvdate1, 4, 2);
$day      = substr($csvdate1, 6, 2);
$sundate1 = strtotime("$year-$month-$day");
$sun_info = date_sun_info($sundate1, $LATITUDE, $LONGITUDE);
echo "<font size='-1'>  $lgSUNRISE " . date("H:i", $sun_info['sunrise']) . " - $lgTRANSIT " . date("H:i", $sun_info['transit']) . " - $lgSUNSET " . date("H:i", $sun_info['sunset']) . "</font>
<hr>
<FORM method='POST' action='detailed.php' name='chooseDateForm' id='chooseDateForm' action='#'>
<table border=0 cellspacing=0 cellpadding=5 width='85%' align='center'>
<tr>
<td>
";

if ($NUMINV > 1) {
	echo "
<select name='invtnum' onchange='this.form.submit()'>";
	for ($i = 1; $i <= $NUMINV; $i++) {
		if ($invtnum == $i) {
			echo "<option SELECTED value=$i>";
		} else {
			echo "<option value=$i>";
		}
		echo "$lgINVT$i</option>";
	}
	echo "</select>";
}
echo "
</td><td colspan=3>
$lgCHOOSEDATE :&nbsp;
<button id='oneDayBack'> < </button>
<input name='date1' id='datepickid' value='$date1' size=7 maxlength=10>
<button id='oneDayFwd'> > </button>
</td>
<td colspan=2>&nbsp;</td>
</tr>
<tr><td>&nbsp;</td><td><input type='checkbox' name='checkavgpower' value='on'";
if ($checkavgpower == 'on') {
	echo ' checked';
}
echo ">$lgPOWERAVG <img src='images/info10.png' width=10 height=10 border=0 title='$lgPOWERAVGINFO'</td>
<td><input type='checkbox' name='checkPROD' value='on'";
if ($checkPROD == 'on') {
	echo ' checked';
}
echo ">$lgPROD</td><td><input type='checkbox' name='checkPERF' value='on'";
if ($checkPERF == 'on') {
	echo ' checked';
}
echo ">$lgPERF</td><td>&nbsp;</td></tr>
<tr><td align=left colspan=5><hr></td></tr>";

for ($stri = 1; $stri <=4; $stri++) {
	if (${'ARRAY' . $stri . "_POWER$invtnum"} > 0) {
		echo "
<tr>
<td><b>$lgARRAY $stri</b> (${'ARRAY' . $stri . "_POWER$invtnum"} W) <b>:</b></td>
<td width='20%'><input type='checkbox' name='checkIP$stri' value='on'";
		if (${'checkIP'.$stri} == 'on') {
			echo ' checked';
		}
		echo ">$lgPOWER</td>
<td width='20%'><input type='checkbox' name='checkIV$stri' value='on'";
		if (${'checkIV'.$stri} == 'on') {
			echo ' checked';
		}
		echo ">$lgVOLTAGE</td>
<td width='20%'><input type='checkbox' name='checkIA$stri' value='on'";
		if (${'checkIA'.$stri} == 'on') {
			echo ' checked';
		}
		echo ">$lgCURRENT</td>
<td width='20%'><input type='checkbox' name='checkPERF$stri' value='on'";
		if (${'checkPERF'.$stri} == 'on') {
			echo ' checked';
		}
		echo ">$lgPERF</td>
</tr>";
	}
}
echo "
<tr><td align=left colspan=5><hr></td></tr>
<tr>
  <td><b>$lgGRID $lgDPHASE 1 :</b></td>
  <td width='20%'><input type='checkbox' name='checkG1P' value='on'";
if ($checkG1P == 'on') {
	echo ' checked';
}
echo ">$lgPOWER</td>
  <td width='20%'><input type='checkbox' name='checkG1V' value='on'";
if ($checkG1V == 'on') {
	echo ' checked';
}
echo ">$lgVOLTAGE</td>
  <td width='20%'><input type='checkbox' name='checkG1A' value='on'";
if ($checkG1A == 'on') {
	echo ' checked';
}
echo ">$lgCURRENT</td>
  <td width='20%'><input type='checkbox' name='checkFRQ' value='on'";
if ($checkFRQ == 'on') {
	echo ' checked';
}
echo ">$lgFREQ</td>
</tr>";
if (${'PHASE' . $invtnum}) {
	echo "
<tr>
  <td><b>$lgGRID $lgDPHASE 2 :</b></td>
  <td width='20%'><input type='checkbox' name='checkG2P' value='on'";
	if ($checkG2P == 'on') {
		echo ' checked';
	}
	echo ">$lgPOWER</td>
  <td width='20%'><input type='checkbox' name='checkG2V' value='on'";
	if ($checkG2V == 'on') {
		echo ' checked';
	}
	echo ">$lgVOLTAGE</td>
  <td width='20%'><input type='checkbox' name='checkG2A' value='on'";
	if ($checkG2A == 'on') {
		echo ' checked';
	}
	echo ">$lgCURRENT</td>
  <td width='20%'>&nbsp;</td>
</tr>
<tr>
  <td><b>$lgGRID $lgDPHASE 3 :</b></td>
  <td width='20%'><input type='checkbox' name='checkG3P' value='on'";
	if ($checkG3P == 'on') {
		echo ' checked';
	}
	echo ">$lgPOWER</td>
  <td width='20%'><input type='checkbox' name='checkG3V' value='on'";
	if ($checkG3V == 'on') {
		echo ' checked';
	}
	echo ">$lgVOLTAGE</td>
  <td width='20%'><input type='checkbox' name='checkG3A' value='on'";
	if ($checkG3A == 'on') {
		echo ' checked';
	}
	echo ">$lgCURRENT</td>
  <td width='20%'>&nbsp;</td>
</tr>
";
}
echo "
<tr><td align=left colspan=5><hr></td></tr>
<tr>
  <td><b>$lgINVERTER :</b></td>
  <td width='20%'><input type='checkbox' name='checkINVT' value='on'";
if ($checkINVT == 'on') {
	echo ' checked';
}
echo ">$lgTEMP</td>
  <td width='20%'><input type='checkbox' name='checkBOOT' value='on'";
if ($checkBOOT == 'on') {
	echo ' checked';
}
echo ">$lgBOOSTER $lgDTEMP</td>
  <td width='20%'><input type='checkbox' name='checkEFF' value='on'";
if ($checkEFF == 'on') {
	echo ' checked';
}
echo ">$lgEFF</td>
  <td width='20%'>";
if (${'SR' . $invtnum} != 'no') {
	echo "<input type='checkbox' name='checkSR' value='on'";
	if ($checkSR == 'on') {
		echo ' checked';
	}
	echo ">$lgSENSOR";
} else {
	echo "&nbsp;";
}
echo "</td>
</tr>
<tr><td align=center colspan=5><br>&nbsp;<input type='submit' value='   $lgOK   '></td></tr>
</table>
</FORM>
</div>
<br>";
include "styles/$STYLE/footer.php";
?>
