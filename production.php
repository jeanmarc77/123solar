<?php
/**
 * /srv/http/123solar/production.php
 *
 * @package default
 */


include 'styles/globalheader.php';
include 'config/config_main.php';
$output = array();
$dir    = 'data/invt1/production/';
$output = glob($dir . '*.csv');
$cnt    = count($output);

if ($cnt > 0) {
	if (!empty($_POST['invtnum'])) {
		$invtnum = $_POST['invtnum'];
		$strt    = $invtnum;
		$upto    = $invtnum;
	} else {
		if ($NUMINV > 1) {
			$invtnum = 0;
		} else {
			$invtnum = 1;
			$strt    = 1;
			$upto    = 1;
		}
	}

	if ($invtnum == 0) {
		$strt = 1;
		$upto = $NUMINV;
	} else {
		$strt = $invtnum;
		$upto = $invtnum;
	}
	$PLANT_POWER = 0;
	for ($invt_num = $strt; $invt_num <= $upto; $invt_num++) {
		include "config/config_invt$invt_num.php";
		$PLANT_POWER += ${'PLANT_POWER' . $invt_num};
	}

	echo "<table width='95%' border=0 align=center cellpadding=8>
<tr><td>";

	if ($NUMINV > 1) {
		echo "
	<form method='POST' action='production.php'>
	$lgCHOOSEINVT: <select name='invtnum' onchange='this.form.submit()'>";
		if ($invtnum == 0) {
			echo "<option SELECTED value=0>$lgALL</option>";
		} else {
			echo "<option value=0>$lgALL</option>";
		}
		for ($i = 1; $i <= $NUMINV; $i++) {
			if ($invtnum == $i) {
				echo "<option SELECTED value=$i>";
			} else {
				echo "<option value=$i>";
			}
			echo "$lgINVT$i</option>";
		}
		echo "</select>
</form>
";
	}
	echo "
</td></tr>
</table>

<script type=\"text/javascript\">
var PLANT_POWER=$PLANT_POWER;
$(document).ready(function() {
Highcharts.setOptions({
global: {useUTC: true},
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

var defaultTitle = ";
	if ($invtnum != 0) {
		$INVNAME = ${'INVNAME' . $invtnum};
	} else {
		$INVNAME = "$lgALL";
	}
	echo "\"$lgPRODTITLE: $INVNAME\", prevPointTitle = null;

var Mychart, options = {
        chart: {
                type: 'column',
                backgroundColor: null,
				events: {
					drilldown: function (e) {
						this.setTitle({
							text: e.point.title
						});
						if (!prevPointTitle) {
							prevPointTitle = e.point.title;
						}
					},
					drillup: function (e) {
						if (this.series[0].options._levelNumber==1) {
							this.setTitle({
								text: defaultTitle
							});
							prevPointTitle = null;
						} else {
							this.setTitle({
								text: prevPointTitle
							});
						}
					}
				},
            },
			title: {
				text: defaultTitle,
				style: {fontSize: '1em'}
			},
            subtitle: {text: '$lgPRODSUBTITLE'},
            xAxis: {
            type: 'datetime'
            },
            yAxis: {
    		min: 0,
    		minPadding: 0,
			title: {text: 'kWh'}
            },
		plotOptions: {
			series: {
				borderWidth: 1,
				dataLabels: {
					enabled: true,
					formatter:function()
					{
					return Highcharts.numberFormat(this.y,'1') + ' kWh';
					}
				},
				point: {
					events: {
						click: function(event) {
							var point = this;
								if (point.y) {
									if (confirm('$lgPRODTOOLTIP ?')) {
									window.location = 'detailed.php?invtnum='+$invtnum+'&date2='+this.x;
									}
								}
						}
					}
				}
			},
			column: {
			  color: \"#4572A7\",
			  cursor: 'pointer',
			  minPointLength: 5
			}
		},
        tooltip: {
			formatter: function() {
				if (Mychart.series[0].options._levelNumber==1) {
				s= '<b>' + Highcharts.dateFormat('%B %Y', this.x);
				s+= ': ' + Highcharts.numberFormat(this.y,1) + ' kWh</b>';
				} else if (Mychart.series[0].options._levelNumber==2) {
				s= '<b>' + Highcharts.dateFormat('%a. %d %B %Y', this.x);
				s+= ': ' + Highcharts.numberFormat(this.y,2) + ' kWh</b>';
				} else {
				s= '<b>' + Highcharts.dateFormat('%Y', this.x);
				s+= ': ' +Highcharts.numberFormat(this.y) + ' kWh</b>';
				}
				s += '<br>$lgEFF: ' + (this.y/(PLANT_POWER/1000)).toFixed(2)+ ' kWh/kWp';
				s += '<br>$lgPRODTOOLTIP<br>';
			return s;
			}
		 },
  exporting: {
  filename: '123Solar-chart',
  width: 1200
  },
  credits: {
  enabled: false
  },
    series: [],
    drilldown: []
 };

var invtnum = '$invtnum';
Mychart= Highcharts.chart('container',options);

Mychart.showLoading();
$.getJSON('programs/programproduction.php', { invtnum: invtnum }, function(JSONResponse) {
  options.series = JSONResponse.series;
  options.drilldown = JSONResponse.drilldown;
  Mychart= Highcharts.chart('container',options);
  Mychart.hideLoading();
});
});";

	echo "
</script>";
} else {
	echo '<br>No data';
}
echo "
<table width='100%' border=0 align=center cellpadding=0>
<tr><td><div id='container' style='width: 95%; height: 450px'></div></td></tr>
</table>
";
include "styles/$STYLE/footer.php";
?>
