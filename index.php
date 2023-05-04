<?php
/**
 * /srv/http/123solar/index.php
 *
 * @package default
 */


include 'styles/globalheader.php';
include 'config/memory.php';
if (!file_exists($MEMORY)) {
	header('Location: admin/');
}
if (!empty($_GET['selectinvt']) && is_numeric($_GET['selectinvt'])) {
	$selectinvt = $_GET['selectinvt'];
	$strto      = $selectinvt;
	$upto       = $selectinvt;
} else {
	if ($NUMINV > 1) {
		$selectinvt = 0;
		$strto      = 1;
		$upto       = $NUMINV;
	} else {
		$selectinvt = 1;
		$strto      = 1;
		$upto       = 1;
	}
}
echo '
<script type="text/javascript">
var myPLANT_POWER=new Array();';

$PLANT_POWERtot = 0;
$YMAXtot        = 0;
$YINTERVALtot   = 0;

for ($i = $strto; $i <= $upto; $i++) {
	include "config/config_invt$i.php";
	$PLANTPOWER[$i] = ${'PLANT_POWER' . $i};
	$PLANT_POWERtot += ${'PLANT_POWER' . $i};
	$YMAXtot += ${'YMAX' . $i};
	$YINTERVALtot += ${'YINTERVAL' . $i};

	echo "myPLANT_POWER[$i] ='$PLANTPOWER[$i]';\n";
}
echo "var PLANT_POWERtot='$PLANT_POWERtot';

$(document).ready(function()
{
Highcharts.setOptions({
global: {useUTC: true},
lang: {
thousandsSep: '$THSEP',
decimalPoint: '$DPOINT',
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

/// Main Day prod ///
var Mychartmain, options1 = {
chart: {
backgroundColor: null,
         events: {
            load: function() {
              setInterval(function() {
			$.getJSON('programs/programday.php', { invtnum: $selectinvt }, function(JSONResponse) {
			Mychartmain.setTitle({text: JSONResponse.title});
			for (var annotation in Mychartmain.annotations) {
				Mychartmain.annotations[annotation].destroy();
			}
			Mychartmain.annotations.length = 0;
			Mychartmain.addAnnotation(JSONResponse.annotations[0]);
			if (typeof JSONResponse.annotations[1] !== 'undefined') {
				Mychartmain.addAnnotation(JSONResponse.annotations[1]);
			}
			";
$j = 0;
for ($i = $strto; $i <= $upto; $i++) {
	echo "Mychartmain.series[$j].update(JSONResponse.data[$j], true);";
	$j++;
}
echo "
			});
			Mychartmain.redraw();
		  }, 30000);
         }
    }
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
loading: {
   labelStyle: { top: '45%'  },
   style: { backgroundColor: null }
},
title: {
text: '$lgTODAYTITLE (... kWh)',
style: {fontSize: '1em'}
},
subtitle: {
text: '$lgSUNRISE ..... - $lgTRANSIT ..... - $lgSUNSET .....'
},
credits: {enabled: false},
legend: { enabled:";
if ($selectinvt == 0) {
	echo 'true';
} else {
	echo 'false';
}
echo " },
plotOptions: {
series: {
stacking: 'normal'
},
areaspline: {
   marker: {
   enabled: false,
   symbol: 'circle',
   radius: 2,
   states: { hover: { enabled: true } }
   }
}
},
xAxis: {type: 'datetime'},
yAxis: {
title: { text: '$lgAVGP (W)'},
gridLineColor: '#BDBDBD',
minorGridLineColor: '#c3c3c3',
minorGridLineWidth: 0.5,
max: $YMAXtot,
min: 0,
endOnTick: false,
tickInterval: $YINTERVALtot,
minorTickInterval: 'auto'
},
tooltip: {
formatter: function() { return '<b>' + Highcharts.numberFormat(this.y,'0') + ' W' + '</b><br/>' + Highcharts.dateFormat('%H:%M', this.x)}
},
exporting: {enabled: false},
series: []
};

/// Yesterday prod ///
var Mychart2, options2 = {
chart: {
backgroundColor: null
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
loading: {
   labelStyle: { top: '45%'  },
   style: { backgroundColor: null }
},
title: {
text: '$lgYESTERDAYTITLE (... kWh)',
style: {fontSize: '1em'}
},
credits: {enabled: false},
legend: {enabled: false},
plotOptions: {
areaspline: {
  marker: {
    enabled: false,
    symbol: 'circle',
    radius: 2,
    states: {hover: {enabled: true}}
  }
}
},
xAxis: {type: 'datetime'},
yAxis: {
max: $YMAXtot,
title: {text: '$lgAVGP (W)'},
gridLineColor: '#BDBDBD',
minorGridLineColor: '#c3c3c3',
minorGridLineWidth: 0.5,
endOnTick: false,
minorTickInterval: 'auto',
tickInterval: $YINTERVALtot,
min: 0
},
tooltip: {
formatter: function() {
return '<b>' + Highcharts.numberFormat(this.y,'0') + ' W' + '</b><br/>' + Highcharts.dateFormat('%H:%M', this.x)
}
},
exporting: {enabled: false},
series: [],
annotations: []
};

/// Last days prod ///
var Mychart3, options3 = {
chart: {
type: 'column',
backgroundColor: null,
defaultSeriesType: 'column'
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
loading: {
   labelStyle: { top: '45%'  },
   style: { backgroundColor: null }
},
credits: {enabled: false},
title: {
text: '$lgLASTPRODTITLE',
style: {fontSize: '1em'}
},
subtitle: {text: '$lgLASTPRODSUBTITLE'},
xAxis: {
type: 'datetime',
tickmarkPlacement: 'on',
dateTimeLabelFormats: {day: '%e %b'}
},
yAxis: {
title: {text: '$lgENERGY (kWh)'},
gridLineColor: '#BDBDBD',
minorGridLineDashStyle: 'longdash',
minorGridLineColor: '#c3c3c3',
minorGridLineWidth: 0.5,
stackLabels: {
enabled: true,
formatter: function() { return Highcharts.numberFormat(this.total,'1')}
},
minorGridLineWidth: 1,
minorTickInterval: 'auto'
},
min: 0,
legend: {enabled:";
if ($selectinvt == 0) {
	echo 'true';
} else {
	echo 'false';
}
echo " },
tooltip: {
formatter: function() {
var point = this.point,
s = '<b>'+Highcharts.dateFormat('%a %e %b', this.x) + ': '+ Highcharts.numberFormat((this.point.stackTotal).toFixed(1),'1') +' kWh</b><br>';
s += '$lgEFF: ' + Highcharts.numberFormat((this.point.stackTotal/(PLANT_POWERtot/1000)).toFixed(2),'2')+ ' kWh/kWp<br>';";
if ($selectinvt == 0) {
	echo "s +=  this.series.name+': '+ Highcharts.numberFormat(this.y,'1') + ' kWh ('+ Highcharts.numberFormat((this.y/(myPLANT_POWER[this.series.index+1]/1000)).toFixed(2),'2')+ ' kWh/kWp)';";
}
echo "
return s;
}
},
plotOptions: {
    series: {
    shadow: false,
    minPointLength: 3,
    point:{
      events: {
        click: function(event) {";
if ($selectinvt == 0) {
	echo "window.location = 'detailed.php?invtnum='+[this.series.index+1]+'&date2='+this.x;";
} else {
	echo "window.location = 'detailed.php?invtnum=$selectinvt&date2='+this.x;";
}
echo "
        }
      }
    }
  },
column: {
stacking: 'normal',
minPointLength: 5
}
},
exporting: {enabled: false},
series: [
";
for ($i = $strto; $i <= $upto; $i++) {
	echo "{
name: '$lgINVT$i',
animation: false,
dataLabels: {
    enabled: false
}
}";
	if ($i != $NUMINV) {
		echo ",";
	}
}
echo "
]
};";

$Y9  = round(($YMAXtot / 9), 0);
$Y92 = $Y9 * 2;
$Y93 = $Y9 * 3;
$Y94 = $Y9 * 4;
$Y95 = $Y9 * 5;
$Y96 = $Y9 * 6;
$Y97 = $Y9 * 7;
$Y98 = $Y9 * 8;

echo "
/// Live gauge ///
var Mygauge, options4 = {
  chart: {
    type: 'gauge',
    backgroundColor: null,
    plotBackgroundColor: null,
    plotBackgroundImage: null,
    plotBorderWidth: 0,
    plotShadow: false,
    height: 250,
          events: {
        load: function() {
        var series = this.series[0];
        setInterval(function () {";
if ($selectinvt == 0) {
	echo "
		$.getJSON('programs/programmultilive.php', function(JSONResponse){
		var point = Mygauge.series[0].points[0];
		point.update(JSONResponse.GPTOT);
		document.getElementById('PMAXOTD').innerHTML = JSONResponse.PMAXOTD;
		document.getElementById('PMAXOTDTIME').innerHTML = JSONResponse.PMAXOTDTIME;
		});";
	for ($i = $strto; $i <= $upto; $i++) {
		echo "
		$.getJSON('programs/programlive.php', { invtnum: $i }, function(JSONResponse){
		GPTOT = JSONResponse.G1P+JSONResponse.G2P+JSONResponse.G3P;
			  if(isNaN(GPTOT)){
			  document.getElementById('GPTOT$i').innerHTML = '...';
			  } else {
			  document.getElementById('GPTOT$i').innerHTML = Highcharts.numberFormat(GPTOT,1);
			  }
		});";
	}
} else {
	echo "
		$.getJSON('programs/programlive.php', { invtnum: $selectinvt }, function(JSONResponse){
		var point = Mygauge.series[0].points[0];
		point.update(JSONResponse.G1P+JSONResponse.G2P+JSONResponse.G3P);
		document.getElementById('PMAXOTD').innerHTML = JSONResponse.PMAXOTD;
		document.getElementById('PMAXOTDTIME').innerHTML = JSONResponse.PMAXOTDTIME;
		});";
}
echo "
        }, 1000);
            }
         }
  },
  title: {
    text: ''
  },
  plotOptions: {
  gauge: {
    pivot: {
      radius: 8,
      borderWidth: 1,
      borderColor: '#303030',
      backgroundColor: {
        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
        stops: [
          [0, '#AAA'],
          [1, '#333']
        ]
      }
    },
    dial: {
      baseLength : 10,
      baseWidth: 8,
      backgroundColor: '#666',
      radius : 70,
      rearLength: 40
    }
  }},
  pane: {
    startAngle: -150,
    endAngle: 150,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 1, x2: 0, y2: 0 },
                    stops: [
                        [0, '#333'],
                        [1, '#AAA']
                    ]
                },
                borderWidth: 0,
                outerRadius: '115%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 1, x2: 0, y2:0 },
                    stops: [
                        [0, '#AAA'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '113%'
            },{
                // default background
            }, {
                backgroundColor: Highcharts.svg ? {
                    radialGradient: {
                        cx: 0.5,
                        cy: -0.6,
                        r: 1.6
                    },
                    stops: [
                        [0.5, 'rgba(255, 255, 255, 0.1)'],
                        [0.3, 'rgba(200, 200, 200, 0.1)']
                    ]
                } : null },{
                backgroundColor: Highcharts.svg ? {
                    radialGradient: {
                        cx: 0.5,
                        cy: -0.9,
                        r: 2.6
                    },
                    stops: [
                        [0.5, 'rgba(255, 255, 255, 0.1)'],
                        [0.3, 'rgba(200, 200, 200, 0.1)']
                    ]
                } : null }
                        ]
  },
  yAxis: {
    min: 0,
    max: $YMAXtot,
    minorTickInterval: 'auto',
    minorTickWidth: 1,
    minorTickLength: 5,
    minorTickPosition: 'inside',
    minorTickColor: '#666',
    tickPixelInterval: 50,
    tickWidth: 2,
    tickPosition: 'inside',
    tickLength: 15,
    tickColor: '#666',
    labels: {
      step: 2,
      rotation: 'auto'
    },
    title: {
      style: {
        color: '#555',
        fontSize: '18px',
	fontWeight: 'bold'
      },
      y: 125,
      text: 'W'
    },
    plotBands: [{
      from: 0,
      to: $Y9,
      color: '#F10D17'
    }, {
      from: $Y9,
      to: $Y92,
      color: '#F76415'
    }, {
      from: $Y92,
      to: $Y93,
      color: '#F29D16'
    }, {
      from: $Y93,
      to: $Y94,
      color: '#FFEA32'
    }, {
      from: $Y94,
      to: $Y95,
      color: '#FFFF45'
    }, {
      from: $Y95,
      to: $Y96,
      color: '#ECFF31'
    }, {
      from: $Y96,
      to: $Y97,
      color: '#94DE40'
    }, {
      from: $Y97,
      to: $Y98,
      color: '#2EC846'
    }, {
      from: $Y98,
      to: $YMAXtot,
      color: '#0DB44C'
    }]
  },
  exporting: {enabled: false},
  credits: {enabled: false},
  series: [{
    name: 'power',
    data: [0],
    tooltip: {
      valueSuffix: ' W'
    },
    overshoot: 5,
    dataLabels: {
      enabled: true,
	  allowOverlap: true,
  formatter: function() {
    if (this.y>=1000) {
    return Highcharts.numberFormat(this.y,'0');
    } else {
    return Highcharts.numberFormat(this.y,'1');
    }
  },
      color: '#666',
      x: 0,
      y: 40,
      style: {
      fontSize: '14px',
      fontWeight:'bold'
      }
    }
  }]
};

Mychartmain= Highcharts.chart('container1',options1);
Mychartmain.showLoading();
Mychart2 = Highcharts.chart('container2',options2);
Mychart2.showLoading();
Mychart3 = Highcharts.chart('container3',options3);
Mychart3.showLoading();

$.getJSON('programs/programday.php', { invtnum: $selectinvt }, function(JSONResponse) {
options1.series = JSONResponse.data;
options1.annotations = JSONResponse.annotations;
Mychartmain.hideLoading();
Mychartmain= Highcharts.chart('container1',options1);
Mychartmain.setTitle({text: JSONResponse.title}, {text: JSONResponse.subtitle});
});

$.getJSON('programs/programyesterday.php', { invtnum: $selectinvt }, function(JSONResponse) {
options2.series = JSONResponse.data;
options2.annotations = JSONResponse.annotations;
Mychart2.hideLoading();
Mychart2= Highcharts.chart('container2',options2);
Mychart2.setTitle({text: JSONResponse.title});
});
";
$j = 0;
for ($i = $strto; $i <= $upto; $i++) {
	echo "
$.getJSON('programs/programlastdays.php', { invtnum: $i} ,function(JSONResponse) {
Mychart3.series[$j].setData(JSONResponse);
});
";
	$j++;
}
echo "
Mychart3.hideLoading();

Mygauge = Highcharts.chart('container4',options4);
Mygauge.series[0].data[0].dataLabel.box.hide();
});
</script>
<table width='100%' border=0 align=center cellpadding=0>
<tr><td width='80%' valign='top'><b>";
if ($selectinvt == 0) {
	echo "$lgPOWERPLANT";
} else {
	echo "${'INVNAME'.$selectinvt}";
}
echo "</b><div id='container1' style='height: 420px'></div></td>
<td width='20%' rowspan=2 valign='top'><div id='container4' align='center' valign='MIDDLE'></div>
<div align=center><font size=-1>$lgPMAX<br><b id='PMAXOTD'>--</b> W @ <b id='PMAXOTDTIME'>--</b><br></font></div>";
if ($selectinvt == 0) {
	echo "<br><table width='80%' border=1 align=center cellpadding=0 cellspacing=0>";
	for ($i = $strto; $i <= $upto; $i++) {
		if (!${'SKIPMONITORING' . $i}) {
			echo "<tr align='center'><td><a href='dashboard.php?invtnum=$i'>${'INVNAME'.$i}</a></td><td><span id='GPTOT$i'>--</span> W</td></tr>";
		} else {
			echo "<tr align='center'><td><img src='images/24/sign-error.png' width=24 height=24 border=0> <a href='dashboard.php?invtnum=$i'>${'INVNAME'.$i} skipped</a></td><td><span id='GPTOT$i'>--</span> W</td></tr>";
		}
	}
	echo "</table>";
} else {
	if (${'SKIPMONITORING' . $selectinvt}) {
		echo "<div align=center><img src='images/24/sign-error.png' width=24 height=24 border=0> <b>${'INVNAME'.$selectinvt} skipped</div>";
	}
	echo "<div align=center><a href='dashboard.php?invtnum=$selectinvt'>$lgDASHBOARD</a></div>";
}
echo "
</td></tr>
</table>
<table width='100%' border=0 align=center cellpadding=0>
<tr><td width='50%'><div id='container2' style='height: 300px'></div></td>
<td width='50%'><div id='container3' style='height: 300px'></div></td></tr>
</table>
";
include "styles/$STYLE/footer.php";
?>
