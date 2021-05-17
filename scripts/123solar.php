<?php
/**
 * /srv/http/123solar/scripts/123solar.php
 *
 * @package default
 */


include 'loadcfg.php';
while (true) { // To infinity ... and beyond!
	for ($invt_num = 1; $invt_num <= $NUMINV; $invt_num++) { //Multi inverters pooling
		///// Main memory
		$data         = file_get_contents($MEMORY);
		$memarray1st  = $data;
		$memarray     = json_decode($data, true);
		///// Live memory
		$data         = file_get_contents($LIVEMEMORY);
		$livememarray = json_decode($data, true);

		// Re-initialize variables
		$RET  = '';
		$msg  = '';
		$I1V  = null;
		$I1A  = null;
		$I1P  = null;
		$I2V  = null;
		$I2A  = null;
		$I2P  = null;
		$I3V  = null;
		$I3A  = null;
		$I3P  = null;
		$I4V  = null;
		$I4A  = null;
		$I4P  = null;
		$G1V  = null;
		$G1A  = null;
		$G1P  = null;
		$G2V  = null;
		$G2A  = null;
		$G2P  = null;
		$G3V  = null;
		$G3A  = null;
		$G3P  = null;
		$FRQ  = null;
		$EFF  = null;
		$INVT = null;
		$BOOT = null;
		$SSR  = null;
		//$KWHT = null;

		$INVTDIR  = $DATADIR . "invt$invt_num";
		$nowUTCs  = strtotime(date('Ymd H:i:s'));
		$nowUTC   = strtotime(date('Ymd H:i'));
		$sun_info = date_sun_info((strtotime(date('Ymd'))), $LATITUDE, $LONGITUDE);
		$now      = date($DATEFORMAT . ' H:i:s');
		$today    = date('Ymd');

		if (!$memarray['awake'] && $nowUTC > ($sun_info['sunrise'] - 300) && $nowUTC < ($sun_info['sunset'])) { // Wake up
			$memarray['awake']  = true;
			$memarray['status'] = '123Solar awake';
			for ($i = 1; $i <= $NUMINV; $i++) {
				$memarray["invtstat$i"] = '';
				logevents($i, "#* $now\t123Solar awake\n\n");
			}
			if ($daemon && $invt_num == $GOPVO) { // Wake up once daemon(s)
				include 'daemon_start.php';
			}
		}

		if ($nowUTC > ($sun_info['sunset'] + 300) && $memarray['awake']) { // Go to bed
			$memarray['awake'] = false;
			for ($i = 1; $i <= $NUMINV; $i++) {
				logevents($i, "#* $now\t123Solar asleep\n\n");
			}
			if ($daemon && $invt_num == $GOPVO) { // Stop daemon(s)
				include 'daemon_stop.php';
			}
		}

		if ($nowUTC < ($sun_info['sunrise'] - 300) || $nowUTC > ($sun_info['sunset'] + 300)) { // Sleeping
			$memarray['status'] = '123Solar ZzzZ';
			for ($i = 1; $i <= $NUMINV; $i++) { // Reset live values
				$nowUTCs                = strtotime(date('Ymd H:i:s'));
				$livememarray["SDTE$i"] = $nowUTCs;
				$livememarray["I1V$i"]  = 0;
				$livememarray["I1A$i"]  = 0;
				$livememarray["I1P$i"]  = 0;
				$livememarray["I2V$i"]  = 0;
				$livememarray["I2A$i"]  = 0;
				$livememarray["I2P$i"]  = 0;
				$livememarray["I3V$i"]  = 0;
				$livememarray["I3A$i"]  = 0;
				$livememarray["I3P$i"]  = 0;
				$livememarray["I4V$i"]  = 0;
				$livememarray["I4A$i"]  = 0;
				$livememarray["I4P$i"]  = 0;
				$livememarray["G1V$i"]  = 0;
				$livememarray["G1A$i"]  = 0;
				$livememarray["G1P$i"]  = 0;
				$livememarray["G2V$i"]  = 0;
				$livememarray["G2A$i"]  = 0;
				$livememarray["G2P$i"]  = 0;
				$livememarray["G3V$i"]  = 0;
				$livememarray["G3A$i"]  = 0;
				$livememarray["G3P$i"]  = 0;
				$livememarray["FRQ$i"]  = 0;
				$livememarray["EFF$i"]  = 0;
				$livememarray["INVT$i"] = 0;
				$livememarray["BOOT$i"] = 0;
				$livememarray["SSR$i"]  = 0;
				$livememarray["KWHT$i"] = $livememarray["KWHT$i"];
				$memarray["invtstat$i"] = '';
			}
			if ($pvoconsu) {
				sleep(15); // Take a nap but continue to feed PVo
			} else {
				sleep(60); // Is he alive ?
			}
		}

		if ($memarray['awake'] && !${'SKIPMONITORING' . $invt_num}) {
			include "protocols/${'PROTOCOL'.$invt_num}.php"; // Main request com. app.
		}

		if (($RET == 'OK' && $memarray['awake']) && !${'SKIPMONITORING' . $invt_num}) {
			if (${'SR' . $invt_num} != 'no') { // Sensor
				include "../config/sensor/${'SR'.$invt_num}.php";
			} else {
				$SSR = null;
			}

			if (${'comlost' . $invt_num}) {
				${'comlost' . $invt_num} = false;
				logevents($invt_num, "#$invt_num $now\tConnection restored\n\n");
				if (${'NORESPM' . $invt_num}) {
					$msg = "$now\r\nConnection restored with inverter\r\n";
					file_put_contents($INVTDIR . '/msgqueue/' . $nowUTC . 'LR.txt', $msg);
				}
			}

			$livememarray["SDTE$invt_num"] = $nowUTCs;
			$livememarray["I1V$invt_num"]  = $I1V;
			$livememarray["I1A$invt_num"]  = $I1A;
			$livememarray["I1P$invt_num"]  = $I1P;
			$livememarray["I2V$invt_num"]  = $I2V;
			$livememarray["I2A$invt_num"]  = $I2A;
			$livememarray["I2P$invt_num"]  = $I2P;
			$livememarray["I3V$invt_num"]  = $I3V;
			$livememarray["I3A$invt_num"]  = $I3A;
			$livememarray["I3P$invt_num"]  = $I3P;
			$livememarray["I4V$invt_num"]  = $I4V;
			$livememarray["I4A$invt_num"]  = $I4A;
			$livememarray["I4P$invt_num"]  = $I4P;
			$livememarray["G1V$invt_num"]  = $G1V;
			$livememarray["G1A$invt_num"]  = $G1A;
			$livememarray["G1P$invt_num"]  = $G1P;
			$livememarray["G2V$invt_num"]  = $G2V;
			$livememarray["G2A$invt_num"]  = $G2A;
			$livememarray["G2P$invt_num"]  = $G2P;
			$livememarray["G3V$invt_num"]  = $G3V;
			$livememarray["G3A$invt_num"]  = $G3A;
			$livememarray["G3P$invt_num"]  = $G3P;
			$livememarray["FRQ$invt_num"]  = $FRQ;
			$livememarray["EFF$invt_num"]  = $EFF;
			$livememarray["INVT$invt_num"] = $INVT;
			$livememarray["BOOT$invt_num"] = $BOOT;
			$livememarray["SSR$invt_num"]  = $SSR;
			$livememarray["KWHT$invt_num"] = $KWHT;

			$GP2[$invt_num] = round(($G1P + $G2P + $G3P), 1);

			if ($GP2[$invt_num] > $memarray["pmotd$invt_num"]) { // Max instant power of the day
				$memarray["pmotd$invt_num"]  = $GP2[$invt_num];
				$memarray["pmotdt$invt_num"] = $nowUTC;
			}

			if ($NUMINV > 1) { // Max instant power of the day on multi
				$GP2multi = array_sum($GP2);
				if ($GP2multi > $memarray['pmotdmulti']) {
					$memarray['pmotdmulti']  = $GP2multi;
					$memarray['pmotdtmulti'] = $nowUTC;
				}
			}
		} else {
			$GP2[$invt_num] = 0;
		} // End of OK

		///// Live memory
		$data = json_encode($livememarray);
		file_put_contents($LIVEMEMORY, $data);
		/////
		$minute = date('i');
		if (in_array($minute, $minlist) && !$memarray["5minflag$invt_num"]) { // 5 min jobs
			$memarray["5minflag$invt_num"] = true;
			$giveup                        = 0;

			if ($memarray['awake'] && $RET != 'OK' && !${'SKIPMONITORING' . $invt_num}) {
				while ($RET != 'OK' && $giveup < 3) { // Insist 3 times
					include "protocols/${'PROTOCOL'.$invt_num}.php";
					sleep($giveup);
					$giveup++;
				}
				if ($giveup > 2 && $memarray['awake'] && $nowUTC < ($sun_info['sunset'] - 600)) {
					logevents($invt_num, "#$invt_num $now\tMissing 5' sample\n\n");
				}
			}

			if ($memarray['awake'] && $RET == 'OK' && !${'SKIPMONITORING' . $invt_num}) { // Log to daily csv
				if (!file_exists($INVTDIR . "/csv/$today.csv")) { // Dawn startup
					$stringData = "Time,I1V,I1A,I1P,I2V,I2A,I2P,I3V,I3A,I3P,I4V,I4A,I4P,G1V,G1A,G1P,G2V,G2A,G2P,G3V,G3A,G3P,FRQ,EFF,INVT,BOOT,SR,KWHT\r\n"; // Header
					file_put_contents($INVTDIR . "/csv/$today.csv", $stringData, FILE_APPEND);

					$memarray["First$invt_num"] = $KWHT;

					$csvlist = glob($INVTDIR . '/csv/*.csv');
					sort($csvlist);
					$xdays = count($csvlist);

					if ($xdays > 1) {
						$dir          = $INVTDIR . '/csv/';
						$yesterdaylog = $csvlist[$xdays - 2]; // Yesterday production
						$lines        = file($yesterdaylog);
						$contalines   = count($lines);
						$array        = preg_split('/,/', $lines[1]);

						$prodyesterday = (float) $array[27];
						$array         = preg_split('/,/', $lines[$contalines - 1]);
						$prodtoday     = (float) $array[27];
						if ($prodtoday >= $prodyesterday) {
							$production = round(($prodtoday - $prodyesterday), 3);
						} else { // passed over
							$production = round((($prodtoday + ${'PASSO' . $invt_num}) - $prodyesterday), 3);
						}
						$option     = $csvlist[$xdays - 2];
						$option     = str_replace($dir, '', $option);
						$date1      = substr($option, 0, 8);
						$year       = substr($option, 0, 4); // For new year
						$stringData = "$date1,$production\r\n";
						$myFile     = $INVTDIR . '/production/energy' . $year . '.csv';
						file_put_contents($myFile, $stringData, FILE_APPEND);

						// monthly report
						$day1 = date('d');
						if ($day1 == '01' && !empty(${'EMAIL' . $invt_num})) {
							$y_year     = date('Y', time() - 60 * 60 * 24); // yesterday
							$y_month    = date('m', time() - 60 * 60 * 24);
							$myFile     = file($INVTDIR . "/production/energy$y_year.csv");
							$contalines = count($myFile);
							$i          = 0;
							for ($line_num = 0; $line_num < $contalines; $line_num++) {
								$array = preg_split('/,/', $myFile[$line_num]);
								$month = substr($array[0], 4, 2);
								if ($month == $y_month) {
									$year         = substr($array[0], 0, 4);
									$month        = substr($array[0], 4, 2);
									$day          = substr($array[0], 6, 2);
									$dayname[$i]  = date($DATEFORMAT, mktime(0, 0, 0, $month, $day, $year));
									$prod_day[$i] = round($array[1], 1);
									$i++;
								}
							}
							$y_month = (int) $y_month;

							$prod_month = array_sum($prod_day) * ${'CORRECTFACTOR' . $invt_num};
							$perf       = round((($prod_month - ${'EXPECT' . $y_month . '_' . $invt_num}) / ${'EXPECT' . $y_month . '_' . $invt_num}) * 100, 1);
							$prod_month = round($prod_month, 1);
							$cnt        = count($dayname);
							for ($i = 0; $i < $cnt; $i++) {
								$prod_day[$i] = round($prod_day[$i] * ${'CORRECTFACTOR' . $invt_num}, 2);
								$kwhkc        = round(($prod_day[$i] / (${'PLANT_POWER' . $invt_num} / 1000)), 2);
								$msg .= "$dayname[$i]\t$prod_day[$i] kWh ($kwhkc kWh/kWp)\r\n";
							}
							$kwhkc = round(($prod_month / (${'PLANT_POWER' . $invt_num} / 1000)), 2);
							$msg .= "\r\n---\r\n$prod_month kWh produced on $y_month/$y_year ($kwhkc kWh/kWp)\r\nPerformance $perf% (${'EXPECT' . $y_month . '_' . $invt_num} kWh expected)\r\n";
							mail("${'EMAIL'.$invt_num}", "123Solar: Inverter #$invt_num monthly production report $y_month/$y_year", $msg, "From: \"123Solar\" <${'EMAIL'.$invt_num}>");
						}

					}

					$memarray["pmotd$invt_num"]  = 0; // Reset past pmotd
					$memarray["pmotdt$invt_num"] = $nowUTC;
					if ($NUMINV > 1) {
						$memarray['pmotdmulti']  = 0; // Reset past multi pmotd
						$memarray['pmotdtmulti'] = $nowUTC;
					}
					$memarray["AWt$invt_num"]     = $nowUTC; // Reset AW msg
					$memarray["AWriso$invt_num"]  = 0;
					$memarray["AWileak$invt_num"] = 0;
					$memarray["peakotd$invt_num"] = 0; // Peaks
					$memarray["peakoat$invt_num"] = 0;
					$flagvolt[$invt_num]          = false;
					$flagriso[$invt_num]          = false;
					$flagleak[$invt_num]          = false;

					// Morning cleanup
					$stringData = "#$invt_num $now\tClean up";
					$myFile     = $INVTDIR . '/infos/events.txt';
					if (file_exists($myFile)) {
						$lines = file($myFile);
						$cnt   = count($lines);
						if ($cnt >= $AMOUNTLOG) {
							array_splice($lines, $AMOUNTLOG);
							$file2 = fopen($myFile, 'w');
							fwrite($file2, implode('', $lines));
							fclose($file2);
							$stringData .= ' log';
						}
					}
					$myFile = $INVTDIR . '/infos/checks_log.txt';
					if (file_exists($myFile)) {
						$lines = file($myFile);
						$cnt   = count($lines);
						if ($cnt >= $AMOUNTLOG) {
							$cnt -= $AMOUNTLOG + 1;
							array_splice($lines, 1, $cnt);
							$file2 = fopen($myFile, 'w');
							fwrite($file2, implode('', $lines));
							fclose($file2);
						}
					}
					$myFile = $INVTDIR . '/infos/checks_status.txt';
					if (file_exists($myFile)) {
						$lines = file($myFile);
						$cnt   = count($lines);
						if ($cnt >= $AMOUNTLOG) {
							$cnt -= $AMOUNTLOG;
							array_splice($lines, 0, $cnt);
							$file2 = fopen($myFile, 'w');
							fwrite($file2, implode('', $lines));
							fclose($file2);
						}
					}
					if ($KEEPDDAYS != 0) {
						if ($xdays > $KEEPDDAYS) {
							$i = 0;
							while ($i < $xdays - $KEEPDDAYS) {
								unlink($csvlist[$i]);
								$i++;
							}
							$stringData .= ", purging $i csv";
						}
					}
					logevents($invt_num, $stringData . "\n\n");
					// Morning cleanup
				} // End of dawn startup

				$PCnow      = date('H:i:s'); // PC time
				$stringData = "$PCnow,$I1V,$I1A,$I1P,$I2V,$I2A,$I2P,$I3V,$I3A,$I3P,$I4V,$I4A,$I4P,$G1V,$G1A,$G1P,$G2V,$G2A,$G2P,$G3V,$G3A,$G3P,$FRQ,$EFF,$INVT,$BOOT,$SSR,$KWHT\r\n";
				$myFile     = $INVTDIR . "/csv/$today.csv";
				file_put_contents($myFile, $stringData, FILE_APPEND);

				$memarray["Last$invt_num"] = $KWHT;
				$memarray['status']        = '123Solar running';

				$lines      = file($myFile);
				$contalines = count($lines);
				if ($contalines == 14) { // Wait 60 min after startup to make sure the inverter is fully awake
					$output = array();
					$info   = '';
					include 'protocols/' . ${'PROTOCOL' . $invt_num} . '_startup.php';
					if (isset($CMD_INFO)) {
						exec($CMD_INFO, $output); // Info file
						$info = implode(PHP_EOL, $output);
					} else {
						$info = 'no infos';
					}
					if (trim($info) == true) {
						file_put_contents($INVTDIR . '/infos/infos.txt', $info);
					}
					if (${'SYNC' . $invt_num} && isset($CMD_SYNC)) {
						exec($CMD_SYNC); //Sync inverter time
						logevents($invt_num, "#$invt_num $now\tSync. inverter time\n\n");
					}
				}
			} // Awake
		} // End of 5 min jobs

		if (!in_array($minute, $minlist) && $memarray["5minflag$invt_num"]) { // Run once every 1,6,11,16,..
			if ($daemon && $memarray['awake'] && $invt_num == $GOPVO) { // Check daemon's up
				include 'daemon_start.php';
			}

			if ($NUMPVO > 0 && $invt_num == $GOPVO && ($memarray['awake'] || $pvoconsu)) { // PVoutput
				$stringData = '';
				$today2     = date('Ymd');
				$time       = date('H:i', mktime(date('H'), date('i') - 1, 0, date('m'), date('d'), date('Y')));

				for ($i = 1; $i <= $NUMPVO; $i++) { // For each SYSID
					$KWHDtot      = 0;
					$GPtot        = 0;
					$GVtot        = 0;
					$TEMP         = 0;
					$CONSUMED_WHD = null;
					$CONSUMED_W   = null;

					for ($j = 1; $j <= $NUMINV; $j++) {
						if (${'PVOUTPUT' . $i . $j} && $memarray['awake'] && !${'SKIPMONITORING' . $j}) { // PVoutput
							$dir     = $DATADIR . "invt$j/csv/";
							$csvlist = glob($dir . '*.csv');
							sort($csvlist);
							$xdays      = count($csvlist);
							$option     = $csvlist[$xdays - 1];
							$lines      = file($option);
							$option     = str_replace($dir, '', $option);
							$array      = preg_split('/,/', $lines[1]);
							$contalines = count($lines);
							if ($contalines > 2) {
								$year  = substr($option, 0, 4);
								$month = substr($option, 4, 2);
								$day   = substr($option, 6, 2);

								if ("$year" . "$month" . "$day" == date('Ymd')) { // today
									$array2      = preg_split('/,/', $lines[$contalines - 1]);
									$hour        = substr($array2[0], 0, 2);
									$minut       = substr($array2[0], 3, 2);
									$seconde     = substr($array2[0], 6, 2);
									$UTCfiledate = strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minut . ':' . $seconde);

									$KWHT_strt = (float) $array[27];
									$KWHT_stop = (float) $array2[27];
									$KWHD      = round((($KWHT_stop - $KWHT_strt) * 1000 * ${'CORRECTFACTOR' . $i}), 0); //Wh
									if ($nowUTC - $UTCfiledate < 300) {
										$GP = round(((float) $array2[15] + (float) $array2[18] + (float) $array2[21]), 0);
										$GV = round((float) $array2[13], 1);
									} else { // too old
										$KWHD = 0;
										$GP   = 0;
										$GV   = 0;
									}
								} else {
									$KWHD = 0;
									$GP   = 0;
									$GV   = 0;
								}
							} else {
								$KWHD = 0;
								$GP   = 0;
								$GV   = 0;
							}
						} else {
							$KWHD = null;
							$GP   = null;
							$GV   = null;
						}

						$KWHDtot += $KWHD;
						$GPtot += $GP;
						if ($GV > $GVtot) {
							$GVtot = $GV; // highest volt
						}
						if (${'PVOT' . $i} == 'inverter') { // Invt temp
							include '../config/pvoutput/temperature/inverter.php';
						}
						if ($DEBUG) {
							$stringData .= "#invt$j $KWHD Wh $GP W $GV V\n";
						}
					}
					if (${'PVOC' . $i} != 'no') { // Consumption
						include "../config/pvoutput/consumption/${'PVOC' . $i}.php";
					}
					if (${'PVOT' . $i} != 'inverter') {
						include "../config/pvoutput/temperature/${'PVOT' . $i}.php";
					}

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'http://pvoutput.org/service/r2/addstatus.jsp');
					curl_setopt($ch, CURLOPT_TIMEOUT, 10);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
							'X-Pvoutput-Apikey: ' . ${'APIKEY' . $i},
							'X-Pvoutput-SystemId: ' . ${'SYSID' . $i}
						));
					$params = 'd=' . $today2 . '&t=' . $time;
					if (isset($KWHDtot)) {
						$params .= '&v1=' . $KWHDtot . '&v2=' . $GPtot . '&v5=' . $TEMP . '&v6=' . $GVtot;
					}
					if (isset($CONSUMED_WHD)) {
						$params .= '&v3=' . $CONSUMED_WHD . '&v4=' . $CONSUMED_W;
					}
					if (${'PVOEXT' . $i}) {
						include "../config/pvoutput/extended/extended$i.php";
						$params .= '&v7=' . $pvo_v7 . '&v8=' . $pvo_v8 . '&v9=' . $pvo_v9 . '&v10=' . $pvo_v10 . '&v11=' . $pvo_v11 . '&v12=' . $pvo_v12;
					}

					curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
					$pvo = curl_exec($ch);
					curl_close($ch);

					if ($DEBUG) {
						$stringData .= "\nSend for SYSID ${'SYSID'.$i} : $today2 $time - $KWHDtot Wh $GPtot W $GVtot V $TEMP C\nConsumption : daily energy $CONSUMED_WHD Wh Avg. power $CONSUMED_W W\n";
						if (${'PVOEXT' . $i}) {
							$stringData .= "Extended data : v7 $pvo_v7 v8 $pvo_v8 v9 $pvo_v9 v10 $pvo_v10 v11 $pvo_v11 v12 $pvo_v12\n";
						}
						$stringData .= "\nPVoutput returned: $pvo";
					}
				} // For each SYSID
				if ($DEBUG) {
					$myFile = $DATADIR . 'pvoutput_return.txt';
					file_put_contents($myFile, $stringData);
				}
			} // End of once PVoutput feed
			$memarray["5minflag$invt_num"] = false; // Reset 5minflag
		} // 1,6,11,..

		if ($memarray['awake'] && !${'SKIPMONITORING' . $invt_num}) { // Test com., Alarms and Warnings
			if ($RET != 'OK' && trim($CMD_RETURN) && ($nowUTCs > ($sun_info['sunrise'] + 1800) && $nowUTCs < ($sun_info['sunset'] - 1800))) { //NOK
				if (${'LOGCOM' . $invt_num}) {
					logevents($invt_num, "#$invt_num $now\tCommunication error\n\n");
				}
			} // End of NOK

			if (($nowUTC - $memarray["AWt$invt_num"]) >= (${'AWPOOLING' . $invt_num} * 60)) { // Checks pooling
				$memarray["AWt$invt_num"] = $nowUTC;
				$ILEAK                    = 0;
				$RISO                     = 0;
				$PPEAK                    = 0;
				$PPEAKOTD                 = 0;
				$STATE                    = null;
				$ALARM                    = null;
				$MESSAGE                  = null;

				if ($RET == 'OK') {
					for ($i = 1; $i <= 3; $i++) { // Grid voltage
						if (isset(${'G' . $i . 'V'})) {
							if (((${'G' . $i . 'V'} > ${'VGRIDT' . $invt_num}) || (${'G' . $i . 'V'} < ${'VGRIDUT' . $invt_num})) && ${'G' . $i . 'V'} != 0) { // Avoid 0 values at early wake up
								$flagvolt[$invt_num] = true;
								${'G' . $i . 'V'}    = round(${'G' . $i . 'V'}, 2);
								if (${'G' . $i . 'V'} > ${'VGRIDT' . $invt_num}) {
									$msg        = "VGrid: ${'G'.$i.'V'} V (Exceeded threshold ${'VGRIDT'.$invt_num} V)\r\n";
									$stringData = "#$invt_num $now\tVGrid high warning ${'G'.$i.'V'} V on phase $i\n\n";
								}
								if (${'G' . $i . 'V'} < ${'VGRIDUT' . $invt_num}) {
									$msg        = "VGrid: ${'G'.$i.'V'} V (Below threshold ${'VGRIDUT'.$invt_num} V)\r\n";
									$stringData = "#$invt_num $now\tVGrid low warning ${'G'.$i.'V'} V on phase $i\n\n";
								}
								logevents($invt_num, $stringData);
								file_put_contents($INVTDIR . '/msgqueue/' . $nowUTC . 'G' . $i . 'V.txt', $msg);
							}

							if ($flagvolt[$invt_num] && ${'G' . $i . 'V'} < ${'VGRIDT' . $invt_num} && ${'G' . $i . 'V'} > ${'VGRIDUT' . $invt_num} && ${'G' . $i . 'V'} != 0) {
								$flagvolt[$invt_num] = false;
								logevents($invt_num, "#$invt_num $now\tVGrid OK ${'G'.$i.'V'} V on phase $i\n\n");
							}
						}
					}
				}
				$RET = '';

				include 'protocols/' . ${'PROTOCOL' . $invt_num} . '_checks.php'; // Req. checks
				if ($RET == 'OK') {
					if (($RISO < ${'RISOT' . $invt_num} && $RISO > 0 && $RISO != $memarray["AWriso$invt_num"])) { // Riso
						$flagriso[$invt_num] = true;
						logevents($invt_num, "#$invt_num $now\tRiso warning $RISO Mohm\n\n");
						file_put_contents($INVTDIR . '/msgqueue/' . $nowUTC . 'R.txt', "Riso: $RISO Mohm (Below threshold ${'RISOT'.$invt_num} Mohm)\r\n");
					}
					if ($flagriso[$invt_num] && $RISO >= ${'RISOT' . $invt_num} && $RISO > 0) {
						$flagriso[$invt_num] = false;
						logevents($invt_num, "#$invt_num $now\tRiso OK $RISO Mohm\n\n");
					}

					if ($ILEAK > ${'ILEAKT' . $invt_num} && $ILEAK > 0 && $ILEAK != $memarray["AWileak$invt_num"]) { // iLeak
						$flagleak[$invt_num] = true;
						logevents($invt_num, "#$invt_num $now\tiLeak warning $ILEAK mA\n\n");
						$msg = "iLeak: $ILEAK mA (Exceeded threshold ${'ILEAKT'.$invt_num} mA)\r\n";
						file_put_contents($INVTDIR . '/msgqueue/' . $nowUTC . 'I.txt', $msg);
					}
					if ($flagleak[$invt_num] && $ILEAK <= ${'ILEAKT' . $invt_num}) {
						$flagleak[$invt_num] = false;
						logevents($invt_num, "#$invt_num $now\tiLeak OK $ILEAK mA\n\n");
					}
					$memarray["AWriso$invt_num"]  = $RISO;
					$memarray["AWileak$invt_num"] = $ILEAK;
					$memarray["peakotd$invt_num"] = $PPEAKOTD;
					$memarray["peakoat$invt_num"] = $PPEAK;
				}

				if (trim($STATE)) {
					$memarray["invtstat$invt_num"] = $STATE; // Inverter status
				}
				if (isset($ALARM)) { // Alarms
					$found = false;
					logevents($invt_num, "#$invt_num $now $ALARM" . PHP_EOL);
					$filter     = ${'FILTER' . $invt_num};
					$stringData = preg_split('/,/', $filter);
					foreach ($stringData as $word) { // filter
						if (strstr($ALARM, $word)) {
							$found = true;
						}
					}
					if (${'SENDALARMS' . $invt_num} && !$found) {
						file_put_contents($INVTDIR . '/msgqueue/' . $nowUTC . 'A.txt', $ALARM);
					}
				} // if alarm

				if (isset($MESSAGE)) { // Messages
					$found = false;
					logevents($invt_num, "#$invt_num $now $MESSAGE" . PHP_EOL);
					$filter     = ${'FILTER' . $invt_num};
					$stringData = preg_split('/,/', $filter);
					foreach ($stringData as $word) { // filter
						if (strstr($MESSAGE, $word)) {
							$found = true;
						}
					}
					if (${'SENDMSGS' . $invt_num} && !$found) {
						file_put_contents($INVTDIR . '/msgqueue/' . $nowUTC . 'M.txt', $MESSAGE);
					}
				} // if message

				if ($nowUTCs > ($sun_info['sunrise'] + 1800) && $nowUTCs < ($sun_info['sunset'] - 1800) && $nowUTCs - $livememarray["SDTE$invt_num"] > 60 && !${'SKIPMONITORING' . $invt_num}) { // Lost of connection
					logevents($invt_num, "#$invt_num $now\tConnection lost\n\n");
					if (${'NORESPM' . $invt_num} && !${'comlost' . $invt_num}) {
						${'comlost' . $invt_num} = true;
						$msg                     = "Connection lost with inverter\r\n";
						file_put_contents($INVTDIR . '/msgqueue/' . $nowUTC . 'L.txt', $msg);
					}
				}

				if (${'LOGMAW' . $invt_num} && $RET == 'OK') { // Log all measures & alarms/warnings
					if (!file_exists($INVTDIR . '/infos/checks_log.txt')) {
						$stringData = "Time,Riso,iLeak,G1V,G2V,G3V\r\n";
						file_put_contents($INVTDIR . '/infos/checks_log.txt', $stringData, FILE_APPEND);
					}
					$stringData = "$now,$RISO,$ILEAK";
					for ($i = 1; $i <= 3; $i++) { // Grid voltage
						if (isset(${'G' . $i . 'V'})) {
							$stringData .= ",${'G' . $i . 'V'}";
						}
					}
					file_put_contents($INVTDIR . '/infos/checks_log.txt', $stringData . PHP_EOL, FILE_APPEND);
					$stringData = "$now $ALARM $MESSAGE" . PHP_EOL;
					file_put_contents($INVTDIR . '/infos/checks_status.txt', $stringData, FILE_APPEND);
				}

				if (${'DIGESTMAIL' . $invt_num} == 0 || (($nowUTC - $memarray["msgq$invt_num"]) >= (${'DIGESTMAIL' . $invt_num} * 60)) || $nowUTC > $sun_info['sunset']) { // Checking queue and sending last msgs before going to bed
					$output = array();
					$output = glob($INVTDIR . '/msgqueue/*.txt');
					if (isset($output[0])) {
						$memarray["msgq$invt_num"] = $nowUTC;
						sort($output);
						$cnt = count($output);

						if ($cnt > 0) { // Will send the first msg as it arrive, the following will be digested
							$contents = '';
							$msg      = '';

							for ($i = 0; $i < $cnt; $i++) {
								$filename = $output[$i];
								$handle   = fopen($filename, 'r');
								$contents = fread($handle, filesize($filename));
								$msg      = $contents . $msg;
								fclose($handle);
							}
							$i = 0;
							while ($i < $cnt) {
								unlink($output[$i]);
								$i++;
							}
							if ($prevmsg[$invt_num] != $msg) { // skip same messages
								$prevmsg[$invt_num] = $msg;
								if (${'MAILW' . $invt_num}) {
									mail("${'EMAIL'.$invt_num}", "123Solar: #$invt_num ${'INVNAME' . $invt_num} Alarms Warnings digest", $msg, "From: \"123Solar\" <${'EMAIL'.$invt_num}>");
								}
								if (!empty(${'TLGRTOK' . $invt_num}) && !empty(${'TLGRCID' . $invt_num})) {
									$telegram = telegram(${'TLGRTOK' . $invt_num}, ${'TLGRCID' . $invt_num}, "123Solar #$invt_num ${'INVNAME' . $invt_num} - $msg");
								}
								if (strlen($msg) > 485) { // Limited to 512 characters, including title
									$msg = substr($msg, 0, 482);
									$msg .= '...';
								}
								if (!empty(${'POAKEY' . $invt_num}) && !empty(${'POUKEY' . $invt_num})) {
									$pushover = pushover(${'POAKEY' . $invt_num}, ${'POUKEY' . $invt_num}, "#$invt_num ${'INVNAME' . $invt_num}", $msg);
								}
							}
						}
					}
				} // Checking queue
			} // Alarms pooling
		} // If awake
		///// Main memory
		$data = json_encode($memarray);
		if ($data != $memarray1st) { // Reduce write
			file_put_contents($MEMORY, $data);
		}
		/////
	} // Multi inverters pooling
} // infinity
?>
