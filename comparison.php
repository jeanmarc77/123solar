<?php
/**
 * /srv/http/123solar/comparison.php
 *
 * @package default
 */


include 'styles/globalheader.php';

if (!empty($_POST['invtnum_f']) && is_numeric($_POST['invtnum_f'])) {
	$invtnum_f = $_POST['invtnum_f'];
} else {
	if ($NUMINV > 1) {
		$invtnum_f = 0;
	} else {
		$invtnum_f = 1;
	}
}
if ($invtnum_f == 0) {
	$dir = 'data/invt1/production';
} else {
	$dir = 'data/invt' . $invtnum_f . '/production';
}

$output = glob($dir . "/*.csv");
sort($output);
$xyears = count($output);

if ($invtnum_f == 0) {
	$startinv = 1;
	$uptoinv  = $NUMINV;
} else {
	$startinv = $invtnum_f;
	$uptoinv  = $invtnum_f;
}

$PLANT_POWERtot = 0;
for ($invt_num = $startinv; $invt_num <= $uptoinv; $invt_num++) { // Multi
	include "config/config_invt$invt_num.php";
	$PLANT_POWERtot += ${'PLANT_POWER' . $invt_num};
} // multi

if (!empty($_POST['whichmonth']) && is_numeric($_POST['whichmonth'])) {
	$whichmonth = $_POST['whichmonth'];
} else {
	$whichmonth = date('n', time() - 60 * 60 * 24);
}
if (!empty($_POST['whichyear']) && is_numeric($_POST['whichyear'])) {
	$whichyear = $_POST['whichyear'];
} else {
	$whichyear = date('Y', time() - 60 * 60 * 24);
}
if (!empty($_POST['comparemonth']) && is_numeric($_POST['comparemonth'])) {
	$comparemonth = $_POST['comparemonth'];
} else {
	$comparemonth = date('n', time() - 60 * 60 * 24);
}
if ($comparemonth == 13 || $whichmonth == 13) {
	$comparemonth = $whichmonth;
}
if (!empty($_POST['compareyear']) && is_string($_POST['compareyear'])) {
	$compareyear = htmlspecialchars($_POST['compareyear'], ENT_QUOTES, "UTF-8");
} else {
	$compareyear = "expected";
}
echo "
<table width='95%' border=0 align=center cellpadding=8>
<tr><td>
<form method='POST' action='comparison.php'>";

if ($NUMINV > 1) {
	echo "<select name='invtnum_f' onchange='this.form.submit()'>";
	if ($invtnum_f == 0) {
		echo "<option SELECTED value=0>$lgALL</option>";
	} else {
		echo "<option value=0>$lgALL</option>";
	}
	for ($i = 1; $i <= $NUMINV; $i++) {
		if ($invtnum_f == $i) {
			echo "<option SELECTED value=$i>";
		} else {
			echo "<option value=$i>";
		}
		echo "$lgINVT$i</option>";
	}
	echo "</select>&nbsp;";
}
echo "$lgCHOOSEDATE :
<select name='whichmonth' onchange='this.form.submit()'>";
for ($i = 1; $i <= 13; $i++) {
	if ($whichmonth == $i) {
		echo "<option SELECTED value='$i'>";
	} else {
		echo "<option value='$i'>";
	}
	echo "$lgMONTH[$i]</option>";
}
echo "
</select>
<select name='whichyear' onchange='this.form.submit()'>";
$newy = date("dm");
if ($xyears == 0 || $newy == "0101") {
	$newy = date("Y");
	echo "<option>$newy</option>";
}
for ($i = ($xyears - 1); $i >= 0; $i--) {
	$output[$i] = str_replace("$dir", '', "$output[$i]");
	$option     = substr($output[$i], -8, 4);
	if ($whichyear == $option) {
		echo "<option SELECTED>";
	} else {
		echo "<option>";
	}
	echo "$option</option>";
}
echo "</select>
$lgCOMPAREDWITH
<select name='comparemonth' onchange='this.form.submit()'>";
if ($comparemonth != 13) {
	for ($i = 1; $i <= 12; $i++) {
		if ($comparemonth == $i) {
			echo "<option SELECTED value='$i'>";
		} else {
			echo "<option value='$i'>";
		}
		echo "$lgMONTH[$i]</option>";
	}
} else {
	echo "<option SELECTED value=13>$lgMONTH[13]</option>";
}

echo "
</select>
<select name='compareyear' onchange='this.form.submit()'>";
if ($compareyear == 'expected') {
	echo "<option SELECTED value='expected'>$lgEXPECTED";
	$compareyear2 = $lgEXPECTED;
} else {
	echo "<option value='expected'>$lgEXPECTED";
	$compareyear2 = $compareyear;
}
echo "</option>";

for ($i = ($xyears - 1); $i >= 0; $i--) {
	$output[$i] = str_replace("$dir", '', "$output[$i]");
	$option     = substr($output[$i], -8, 4);
	if ($compareyear == $option) {
		echo "<option SELECTED>";
	} else {
		echo "<option>";
	}
	echo "$option</option>";
}
echo "
</select>
</form>
</td></tr>
</table>
<script type='text/javascript'>
var PLANT_POWER='$PLANT_POWERtot';

$(document).ready(function() {
Highcharts.setOptions({
global: {
useUTC: true
},
lang: {
decimalPoint: '$DPOINT',
thousandsSep: '$THSEP',
months: ['";
for ($i = 1; $i < 12; $i++) {
	echo "$lgMONTH[$i]','";
}
echo "$lgMONTH[12]'],
shortMonths: ['";
for ($i = 1; $i < 12; $i++) {
	echo "$lgSMONTH[$i]','";
}
echo "$lgSMONTH[12]'],
weekdays: ['";
for ($i = 1; $i < 7; $i++) {
	echo "$lgWEEKD[$i]','";
}
echo "$lgWEEKD[7]'],
drillUpText: '$lgDRILLUP',
loading: '$lgLOAD',
printChart: '$lgPRINT',
resetZoom: '$lgRESETZ'
}
});

var Mychart, options = {
chart: {
type: 'spline',
backgroundColor: null,
zoomType: 'xy',
resetZoomButton: {
                position: {
                    align: 'right',
                    verticalAlign: 'top'
                }
},
spaceRight:20
},
colors: [
	'#4572A7',
	'#AA4643',
	'#89A54E',
	'#80699B',
	'#3D96AE',
	'#DB843D',
	'#92A8CD',
	'#A47D7C',
	'#B5CA92'
],
credits: {
enabled: false
},
title: {
  text: '";
if ($invtnum_f == 0 || $NUMINV == 1) {
	$parttitle = '';
} else {
	$parttitle = "$lgINVT$invtnum_f - ";
}
echo "$parttitle $lgCOMPARETITLE $lgMONTH[$whichmonth] $whichyear $lgWITH $lgMONTH[$comparemonth] $compareyear2'
,style: {fontSize: '1em'}
},
subtitle: { text: '$lgCOMPARESUBTITLE' },
xAxis: [{
type: 'datetime',
maxZoom: 43200000
  }, {
type: 'datetime',
maxZoom: 43200000
}] ,
yAxis: [{
min: 0,
maxZoom: 200,
labels: { formatter: function() { return this.value + 'kWh';}},
title: { text: '$lgPRODCUM'}
},
],
tooltip: {
formatter: function() {
    if ((Mychart.series[0].name== this.series.name)&& (Mychart.series[0].name!=Mychart.series[1].name)){
  var s = '';
  s += '<b>' + Highcharts.dateFormat('%A %e %b %Y',this.x) + ' :</b> ' + Highcharts.numberFormat(this.y,'1') + ' kWh (' + Highcharts.numberFormat((this.y/(PLANT_POWER/1000)).toFixed(2))+ ' kWh/kWp)<br/>';
  var secondSeriesLen =  Mychart.series[1].data.length;
  var daynum = ((this.x-Mychart.series[0].data[0].x)/86400000)+1;
  if(daynum<=secondSeriesLen) {
	var perf = (((this.y * 100)/(Mychart.series[1].data[daynum-1].y))-100).toFixed(1);
  } else {
    var firstSeriesLen = Mychart.series[0].data.length;
    var secondSeriesMax = Mychart.series[1].data[secondSeriesLen-1].y;
	var perf = (((this.y * 100 *firstSeriesLen)/(secondSeriesMax*daynum))-100).toFixed(1);
	perf = '~' + perf;
  }
  s += '$lgGLOBPERF: '+perf+ '%';

  return s;
   } else {
      return '<b>' + Highcharts.dateFormat('%A %e %b %Y',this.x) + ' :</b> ' + Highcharts.numberFormat(this.y,'1') + ' kWh (' + Highcharts.numberFormat((this.y/(PLANT_POWER/1000)).toFixed(2))+ ' kWh/kWp)<br/>';
   }
},
crosshairs: true
},
legend: {
layout: 'horizontal',
align: 'center',
floating: false,
backgroundColor: '#FFFFFF'
},
exporting: {
filename: '123Solar-chart',
width: 1200
},
series: []
};
";

// transmit the value to proceed them via _GET
$destination = "programs/programcomparison.php?whichmonth=$whichmonth&whichyear=$whichyear&comparemonth=$comparemonth&compareyear=$compareyear";

echo "
Mychart= Highcharts.chart('container',options);
Mychart.showLoading();
var invtnum_f = $invtnum_f;
$.getJSON('$destination', { invtnum_f: $invtnum_f }, function(data)
{
options.series = data;
Mychart = Highcharts.chart('container',options);
Mychart.hideLoading();
});
});
</script>
"; //End of echo
?>

<table width="100%" border=0 align=center cellpadding="0">
<tr><td><div id="container" style="width: 95%; height: 450px"></div></td></tr>
</table>
<?php
include "styles/$STYLE/footer.php";
?>
