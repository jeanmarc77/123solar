<?php
/**
 * /srv/http/123solar/dashboard.php
 *
 * @package default
 */


include "styles/globalheader.php";
$currentFile = $_SERVER["PHP_SELF"];
if (!empty($_GET['invtnum']) && is_numeric($_GET['invtnum'])) {
	$invtnum = $_GET['invtnum'];
} else {
	$invtnum = 1;
}
include "config/config_invt$invtnum.php";

if (!empty($_POST['pool']) && is_numeric($_POST['pool'])) {
	$pool = ($_POST['pool']);
} else {
	$pool = 1000;
}

$poollst = array(
	'5000',
	'2000',
	'1000',
	'500',
	'200',
	'100'
);

if (${'PHASE' . $invtnum}) {
	$IMAX = round((${'YMAX' . $invtnum} * 2.1 / 3 / (${'VGRIDT' . $invtnum} + ${'VGRIDUT' . $invtnum})), 0, PHP_ROUND_HALF_UP);
} else {
	$IMAX = round((${'YMAX' . $invtnum} * 2.1 / (${'VGRIDT' . $invtnum} + ${'VGRIDUT' . $invtnum})), 0, PHP_ROUND_HALF_UP);
}
$VGRIDMin = round((${'VGRIDUT' . $invtnum} * 0.99), 0, PHP_ROUND_HALF_DOWN);
$VGRIDMax = round((${'VGRIDT' . $invtnum} * 1.01), 0, PHP_ROUND_HALF_UP);

echo "
<script type='text/javascript'>
var invtnum = $invtnum;
$(document).ready(function()
{
Highcharts.setOptions({
global: {useUTC: true},
lang: {
decimalPoint: '$DPOINT',
thousandsSep: '$THSEP'
}
});
";

$uptostring = 0;
for ($stri = 1; $stri <=4; $stri++) {
	if (${'ARRAY' . $stri . "_POWER$invtnum"} > 0) {
		$uptostring ++;
		$strlist[$uptostring] = $stri;
	}
}

for ($stri = 1; $stri <= $uptostring; $stri++) {
	$Y9  = round((${'ARRAY' . $strlist[$stri] . "_POWER$invtnum"} / 9), 0);
	$Y92 = round(($Y9 * 2), 0);
	$Y93 = round(($Y9 * 3), 0);
	$Y94 = round(($Y9 * 4), 0);
	$Y95 = round(($Y9 * 5), 0);
	$Y96 = round(($Y9 * 6), 0);
	$Y97 = round(($Y9 * 7), 0);
	$Y98 = round(($Y9 * 8), 0);
	echo "
/// Array $stri gauge ///
var gauge$stri, options$stri = {
  chart: {
    type: 'gauge',
    backgroundColor: null,
    plotBackgroundColor: null,
    plotBackgroundImage: null,
    plotBorderWidth: 0,
    plotShadow: false,
    height: 230
  },
  loading: {
  labelStyle: { top: '45%'  },
  style: { backgroundColor: null }
  },
  title: {
    text: '--'
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
    max: ${'ARRAY' . $strlist[$stri] . '_POWER'.$invtnum},
    labels: {
        distance: 50,
        rotation: 'auto'
    },
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
      y: 105,
      text: 'W'
    },
    plotBands: [{
      from: 0,
      to: $Y9,
      color: '#F10D17'
    }, {
      from: $Y9,
	  to : $Y92,
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
      to: ${'ARRAY' . $strlist[$stri] . '_POWER'.$invtnum},
      color: '#0DB44C'
    }]
  },
  exporting: {enabled: false},
  credits: {enabled: false},
  series: [{
    name: 'power',
    data: [0],
    tooltip: {
      valueSuffix: 'W'
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
      y: 30,
      style: {
      fontSize: '14px',
      fontWeight:'bold'
      }
    }
  }]
};
";
}

if (${'PHASE' . $invtnum}) {
	$upto = 6 + $stri;
} else {
	$upto = 2 + $stri;
}

$ph = 1;
for ($j = $stri; $j < $upto; $j++) {
	echo "
/// Live gauge GV ph. $ph ///
var gauge$j, options$j = {
  chart: {
    type: 'gauge',
    backgroundColor: null,
    plotBackgroundImage: null,
    plotBorderWidth: 1,
    plotShadow: true,
    width: 230,
    plotBackgroundColor: {
      linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
      stops: [
        [0, '#FFF'],
        [0.9, '#DDD'],
        [1, '#CCC']
      ]
    },
    plotBackgroundImage: null,
    height: 170
        },
    exporting: {enabled: false},
        credits: {enabled: false},
        title: {
        text: '-- V'
        },
        pane: {
            startAngle: -45,
            endAngle: 45,
            background: null,
            center: ['50%', '145%'],
            size: 220
        },
        yAxis: {
            min: $VGRIDMin,
            max: $VGRIDMax,
            minorTickPosition: 'outside',
            tickPosition: 'outside',
      plotBands: [{
                from: ${'VGRIDT'.$invtnum},
                to: $VGRIDMax,
                color: '#C02316',
                innerRadius: '100%',
                outerRadius: '105%'
            },{
                from: $VGRIDMin,
                to: ${'VGRIDUT'.$invtnum},
                color: '#C02316',
                innerRadius: '100%',
                outerRadius: '105%'
            }
			],
           endOnTick: false,
            title: {
              style: {
                color: '#555',
                fontSize: '18px',
		fontWeight: 'bold'
              },
            text: 'V~',
            y: -20
            },
            labels: {
                rotation: 'auto',
                distance: 20
            }
        },
        plotOptions: {
            gauge: {
                dataLabels: {
                    enabled: false
                },
                dial: {
                    radius: '100%'
                }
            }
        },
        series: [{
      name: 'Grid V',
      data: [0],
      tooltip: {
      valueSuffix: 'V'
      },
      overshoot: 5,
      dataLabels: {
      enabled: false
      }
    }
               ]
    };";
	$j++;
	echo "
/// Live gauge GA ph. $ph ///
var gauge$j, options$j = {
  chart: {
    type: 'gauge',
    backgroundColor: null,
    plotBackgroundImage: null,
    plotBorderWidth: 1,
    plotShadow: true,
    width: 230,
    plotBackgroundColor: {
      linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
      stops: [
        [0, '#FFF'],
        [0.9, '#DDD'],
        [1, '#CCC']
      ]
    },
    plotBackgroundImage: null,
    height: 170
        },
    exporting: {enabled: false},
        credits: {enabled: false},
        title: {
        text: '-- A'
        },
        pane: {
            startAngle: -45,
            endAngle: 45,
            background: null,
            center: ['50%', '145%'],
            size: 220
        },
        yAxis: {
            min: 0,
            max: $IMAX,
            minorTickPosition: 'outside',
            tickPosition: 'outside',
            title: {
              style: {
                color: '#555',
                fontSize: '18px',
		fontWeight: 'bold'
              },
            text: 'A',
            y: -20
            },
            labels: {
                rotation: 'auto',
                distance: 20
            }
        },
        plotOptions: {
            gauge: {
                dataLabels: {
                    enabled: false
                },
                dial: {
                    radius: '100%'
                }
            }
        },
        series: [{
      name: 'Grid A',
      data: [0],
      tooltip: {
      valueSuffix: 'A'
      },
    overshoot: 5,
      dataLabels: {
      enabled: false
      }
    }
               ]
    };";
	$ph++;
}

for ($i = 1; $i <= $uptostring; $i++) {
	echo "
  gauge$i = Highcharts.chart('container$i',options$i);
  gauge$i.series[0].data[0].dataLabel.box.hide();
";
}

for ($j = $stri; $j < $upto; $j++) {
	echo "
gauge$j = Highcharts.chart('container$j',options$j);
  gauge$j.renderer.text('<div id=\'hz$j\'></div>', 185, 143,true)
    .css({
      color: '#555',
      fontSize: '11px'
    })
    .add();";
	$j++;
	echo "
gauge$j = Highcharts.chart('container$j',options$j);";
}

echo "
  function updateit() {
  var invtnum = $invtnum;
  $.getJSON('programs/programlive.php', { invtnum: invtnum }, function(json){
  document.getElementById('stamp').innerHTML = json.timestamp;
  ";
for ($i = 1; $i <= $uptostring; $i++) {
	$j=$strlist[$i];
	echo "
  var point = gauge$i.series[0].points[0];
  point.update(json.I";
	echo $j;
	echo "P);
  gauge$i.setTitle({ text: Highcharts.numberFormat(json.I";
	echo $j;
	echo "V,'1') +' VDC  '+ Highcharts.numberFormat(json.I";
	echo $j;
	echo "A,'1') + ' A'});
  ";
}

$ph = 1;
for ($j = $stri; $j < $upto; $j++) {
	echo "
	if (json.G";
	echo $ph;
	echo "P>=1000) {
  	document.getElementById('G";
	echo $ph;
	echo "P').innerHTML = Highcharts.numberFormat(json.G";
	echo $ph;
	echo "P,'0');
	} else {
  	document.getElementById('G";
	echo $ph;
	echo "P').innerHTML = Highcharts.numberFormat(json.G";
	echo $ph;
	echo "P,'1');
	}
  var point = gauge$j.series[0].points[0];
  point.update(json.G";
	echo $ph;
	echo "V);
  gauge$j.setTitle({ text: Highcharts.numberFormat(json.G";
	echo $ph;
	echo "V,'1') + ' V'});
  $('#hz$j').html(json.FRQ + 'Hz');";
	$j++;
	echo "
  var point = gauge$j.series[0].points[0];
  point.update(json.G";
	echo $ph;
	echo "A);
  gauge$j.setTitle({ text: Highcharts.numberFormat(json.G";
	echo $ph;
	echo "A,'1') +' A'});
  ";
	$ph++;
}
echo "
  document.getElementById('EFF').innerHTML = Highcharts.numberFormat(json.EFF,'1');
  document.getElementById('BOOT').innerHTML = Highcharts.numberFormat(json.BOOT,'1');
  document.getElementById('INVT').innerHTML = Highcharts.numberFormat(json.INVT,'1');";
if (${"SR$invtnum"} != 'no') {
	echo "document.getElementById('SSR').innerHTML = Highcharts.numberFormat(json.SSR,'2');";
}
echo "
  document.getElementById('RISO').innerHTML = Highcharts.numberFormat(json.riso,'2');
  document.getElementById('ILEAK').innerHTML = Highcharts.numberFormat(json.ileak,'1');
  document.getElementById('AWT').innerHTML = json.awdate;

  })
  }
updateit();
setInterval(updateit, $pool);

  function updateit2() {
  var invtnum = $invtnum;
  $.getJSON('programs/programdashboard.php', { invtnum: invtnum }, function(json){
	document.getElementById('STATE').innerHTML = json['STATE'].replace(/\\n/g, '<br/>').replace(/[ ]/g, '&nbsp;');
  	document.getElementById('PPEAK').innerHTML = Highcharts.numberFormat(json['PPEAK'],'1');
  	document.getElementById('PPEAKOTD').innerHTML = Highcharts.numberFormat(json['PPEAKOTD'],'1');
	document.getElementById('STATUS').innerHTML = json['STATUS'];
  })

  $.getJSON('programs/programday.php', { invtnum: invtnum }, function(json){
	document.getElementById('TITLE').innerHTML = json.title;
  })
  }
updateit2();
setInterval(updateit2, 30000);
});


</script>";

if (${"SKIPMONITORING$invtnum"}) {
	echo "<img src='images/24/sign-error.png' width=24 height=24 border=0> <b>${'INVNAME'.$invtnum} is down for maintenance</b><br>";
}
echo "
<table width='80%' border=0 align=left cellpadding=0>
<tr>
<td align='left'><b>$lgDASHBOARD - <span id='TITLE'>--</span>";
if ($NUMINV > 1) {
	echo " $lgINVT $invtnum";
}
echo "
</b>
</td>
<td align='right'><span id='stamp'>--</span></td>
<td align='right'>
<form method=\"POST\" action=\"$currentFile\">
<select name='pool' onchange='this.form.submit()'>
";
$cnt = count($poollst);
for ($i = 0; $i < $cnt; $i++) {
	if ($pool == $poollst[$i]) {
		echo "<option SELECTED value='$poollst[$i]'>$poollst[$i]</option>";
	} else {
		echo "<option value='$poollst[$i]'>$poollst[$i]</option>";
	}
}
echo "</select> ms
</form></td>
</tr>
</table>
<br><br><hr width='80%' align='left'>
<table width='80%' border=0 align=left cellpadding=0>
<tr valign='top'><td align=left>
	<table border=0 align=center cellpadding=0>
	";
for ($i = 1; $i <= 4; $i++) {
	if ($i == 1 || $i == 3) {
		echo "<tr>";
	}
	echo "
		<td align=left width='250'> ";
	if ($i <= $uptostring) {
		$j = $strlist[$i];
		echo "		<b>$lgARRAY $j :</b>
		<br><div id='container$i' align='center'></div>
		";
	}
	echo "
		</td>";
	if ($i == 2 || $i == 4) {
		echo "</tr>";
	}
}
echo "
	</table>
</td>
<td></td>
<td align=left>
";
$ph = 1;
for ($j = $stri; $j < $upto; $j++) {
	echo "
	<table width='100%' border=0 align=left cellpadding=0>
	<tr><td><b>$lgGRID $lgDPHASE $ph : <span id='G";
	echo $ph;
	echo "P'>--</span> W</b>
	</td></tr>
	</table>
	<br>
	<table border=0 align=left cellpadding=0>
	<tr align='center'>
	<td width=250>
	<div id='container$j'></div>
	</td>";
	$j++;
	echo "<td width=250>
	<div id='container$j'></div>
	</td>
	</tr>
	</table>
	<br>";
	$ph++;
}


echo "
</td></tr>
</table>
<br>
<br>
<table width='98%' border=0 align=center cellpadding=0>
<tr>
<td COLSPAN=2>
<br>
$lgEFF <span id='EFF'>--</span>%<br>
$lgDBOOSTER $lgDTEMP <span id='BOOT'>--</span> °c<br>
$lgDINVERTER $lgDTEMP <span id='INVT'>--</span> °c<br>";
if (${"SR$invtnum"} != 'no') {
	echo "
$lgSENSOR <span id='SSR'>--</span> W/m²<br>
";

}
echo "
<br>
<b>$lgAWCHK <span id='AWT'>--</span> :</b>
<br><br>R.iso. <span id='RISO'>--</span> Mohm - iLeak <span id='ILEAK'>--</span> mA
<br>
<br>$lgPPEAK <span id='PPEAK'>--</span> W, $lgPPEAKOTD <span id='PPEAKOTD'>--</span> W
<br><span id='STATE'>--</span>
<br><span id='STATUS'>--</span>
<br><br>
</td>
</tr>
</table>";
include "styles/$STYLE/footer.php";
?>
