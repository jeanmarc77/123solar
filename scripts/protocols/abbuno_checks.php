<?php
/**
 * /srv/http/123solar/scripts/protocols/abbuno_checks.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// For TCP Modbus https://www.modbusdriver.com/modpoll.html
// Riso iLeak test & Peak Powers

$CMD_RISOLEAK 	= "modpoll -m tcp -r 40226 -c 1 -0 -a 1 -i -1 ${'ADR'.$invt_num}";
$CMD_STATE 		= "modpoll -m tcp -r 40108 -c 1 -0 -a 1 -i -1 ${'ADR'.$invt_num}";
$CMD_ALARM 		= "modpoll -m tcp -r 41110 -c 1 -t4:int -0 -a 1 -i -1 ${'ADR'.$invt_num}";
$CMD_MESSAGE 	= "modpoll -m tcp -r 40114 -c 3 -t4:int -0 -a 1 -i -1 ${'ADR'.$invt_num}";
$RET          	= 'NOK';
$CMD_RETURN	  	= '';
$ILEAK			= 0;
$register		= '';			

$PPEAK 			= 0;
$PPEAKOTD 		= 0;

exec($CMD_RISOLEAK, $CMD_RETURN);
if (isset($CMD_RETURN[11]))
	{
	$RISO = (float)(substr ($CMD_RETURN[11], 9) / 1000);
	
	unset ($CMD_RETURN);
	exec($CMD_STATE, $CMD_RETURN);
	
	if (isset($CMD_RETURN[11]))
		{
		
		$register = (int)(substr ($CMD_RETURN[11], 9));
		/*
			1	Off (OFF)
			2	Sleeping (SLEEPING)
			3	Starting (STARTING)
			4	MPPT
			5	Throttled (THROTTLED)
			6	Shutting down (SHUTTING_DOWN)
			7	Fault (FAULT)
			8	Standby (STANDBY)
		*/
		
		switch ($register)
			{
			case 0:
				break;
			case 1:
				$STATE = 'Off (OFF)';
				break;
			case 2:
				$STATE = 'Sleeping (SLEEPING)';
				break;
			case 3:
				$STATE = 'Starting (STARTING)';
				break;
			case 4:
				$STATE = 'MPPT';
				break;
			case 5:
				$STATE = 'Throttled (THROTTLED)';
				break;
			case 6:
				$STATE = 'Shutting down (SHUTTING_DOWN)';
				break;
			case 7:
				$STATE = 'Fault (FAULT)';
				break;
			case 8:
				$STATE = 'Standby (STANDBY)';
				break;
			default:
				break;
			}
		
		
		$register 	= 0;
		unset ($CMD_RETURN);
		exec($CMD_ALARM, $CMD_RETURN);
		if (isset($CMD_RETURN[11]))
			{
			$register = (int)(substr ($CMD_RETURN[11], 9));
			/*
			Bit 0	"1 =Ground Fault 
						(GROUND_FAULT)"			"This state corresponds to the following internal alarms:
												- E018: Leakage current fault
												- E025: Low R-iso
												- E037: Grounding interface fault"
			Bit 1	1 =Input Over Voltage (INPUT_OVER_VOLTAGE)			"This state corresponds to the following internal alarms:
												- E002: Input OV"
			unimplemented	Reserved (RESERVED)			
			unimplemented	DC Disconnect (DC_DISCONNECT)			
			unimplemented	Cabinet Open (CABINET_OPEN)			
			Bit 6	1 =Manual Shutdown (MANUAL_SHUTDOWN)			"This state corresponds to the following internal alarms:
												- E035: Remote off"
			Bit 7	"1 =Over Temperature 
							(OVER_TEMP)"			"This state corresponds to the following internal alarms:
												- E014: OTH"
			unimplemented	Blown Fuse (BLOWN_FUSE)			
			Bit 13	1 = Under Temperature (UNDER_TEMP)			"This state corresponds to the following internal alarms:
												- E033: UTH"
			Bit 14	"1 = Memory Loss 
							(MEMORY_LOSS)"			This state corresponds to statistics memory fault.
			Bit 15	"1 = Arc Detection 
							(ARC_DETECTION)"			"This state corresponds to the following internal alarms:
												- E050: Arc-Fault"
			Bit 20	"1 = Test Failed 
							(TEST_FAILED)"			"This state corresponds to the following internal alarms:
												- E005: Internal bus fault
												- E013: Wrong DC input mode
												- E019: Lekage current self-test fault
												- E020: Grid interface self-test fault
												- E021: Grid interface self-test fault
												- E053: Arc-fault detector self-test fault
												- E078: R-iso self-test fault"
			Bit 21	1 = Under Voltage (INPUT_UNDER_VOLTAGE)			"This state corresponds to the following internal alarms:
												- W002: Input UV"
			Bit 22	1 = Over Current (INPUT_OVER_CURRENT)			"This state corresponds to the following internal alarms:
												- E001: Input OC"

			*/
			
			$ALARM = Null;
			if($register & 0x0001) 
				{
				$ALARM = "Ground Fault";
				}
			if($register & 0x0002) 
				{
				if ($ALARM != Null)
					{
					$ALARM = $ALARM.','."Input Over Voltage";
					}
				else
					{
					$ALARM = "Input Over Voltage";
					}
				}
			if($register & 0x0040) 
				{
				if ($ALARM != Null)
					{
					$ALARM = $ALARM.','."Manual Shutdown";
					}
				else
					{
					$ALARM = "Manual Shutdown";
					}
				}
			if($register & 0x0080) 
				{
				if ($ALARM != Null)
					{
					$ALARM = $ALARM.','."Over Temperature";
					}
				else
					{
					$ALARM = "Over Temperature";
					}
				}
			if($register & 0x2000) 
				{
				if ($ALARM != Null)
					{
					$ALARM = $ALARM.','."Under Temperature";
					}
				else
					{
					$ALARM = "Under Temperature";
					}
				}
			if($register & 0x4000) 
				{
				if ($ALARM != Null)
					{
					$ALARM = $ALARM.','."Memory Loss";
					}
				else
					{
					$ALARM = "Memory Loss";
					}
				}
			if($register & 0x8000) 
				{
				if ($ALARM != Null)
					{
					$ALARM = $ALARM.','."Arc Detection";
					}
				else
					{
					$ALARM = "Arc Detection";
					}
				}
			if($register & 0x100000) 
				{
				if ($ALARM != Null)
					{
					$ALARM = $ALARM.','."Test Failed";
					}
				else
					{
					$ALARM = "Test Failed";
					}
				}
			if($register & 0x200000) 
				{
				if ($ALARM != Null)
					{
					$ALARM = $ALARM.','."Under Voltage";
					}
				else
					{
					$ALARM = "Under Voltage";
					}
				}
			if($register & 0x400000) 
				{
				if ($ALARM != Null)
					{
					$ALARM = $ALARM.','."Over Current";
					}
				else
					{
					$ALARM = "Over Current";
					}
				}
			
			unset ($CMD_RETURN);
			$MESSAGE 	= Null;
			$register	= 0;
			exec($CMD_MESSAGE, $CMD_RETURN);
			if (isset($CMD_RETURN[11]))
				{
				$register = (int)(substr ($CMD_RETURN[11], 9));
				/*
				"This register provides status of active power derating:
					- Bit0: Power curtailment from external command
					- Bit1: Power limitation from Frequency-Watt
					- Bit2: Power limitation from high average grid voltage
					- Bit3: Power limitation from anti-islanding protection
					- Bit4: Power limitation from grid current rating limitation
					- Bit5: Power limitation for high temperature
					- Bit6: Power limitation for high DC voltage
					- Bit7: Reserved
					- Bit8: Power limitation from ramp-rate (connection ramp)
					- Bit9: Power limitation from momentary cessation
					- Bit10: Power limitation from ramp-rate (normal ramp-up)
					- Bit11: Power limitation from Volt-Watt
					- Bit12-31: Reserved"
				*/
				if($register & 0x0001) 
					{
					$MESSAGE = "Power curtailment from external command";
					}
				if($register & 0x0002) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation from Frequency-Watt";
						}
					else
						{
						$MESSAGE = "Power limitation from Frequency-Watt";
						}
					}
				if($register & 0x0004) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation from high average grid voltage";
						}
					else
						{
						$MESSAGE = "Power limitation from high average grid voltage";
						}
					}
				if($register & 0x0008) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation from anti-islanding protection";
						}
					else
						{
						$MESSAGE = "Power limitation from anti-islanding protection";
						}
					}
				if($register & 0x0010) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation from grid current rating limitation";
						}
					else
						{
						$MESSAGE = "Power limitation from grid current rating limitation";
						}
					}
				if($register & 0x0020) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation for high temperature";
						}
					else
						{
						$MESSAGE = "Power limitation for high temperature";
						}
					}
				if($register & 0x0040) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation for high DC voltage";
						}
					else
						{
						$MESSAGE = "Power limitation for high DC voltage";
						}
					}
				if($register & 0x0100) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation from ramp-rate (connection ramp)";
						}
					else
						{
						$MESSAGE = "Power limitation from ramp-rate (connection ramp)";
						}
					}
				if($register & 0x0200) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation from momentary cessation";
						}
					else
						{
						$MESSAGE = "Power limitation from momentary cessation";
						}
					}
				if($register & 0x0400) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation from ramp-rate (normal ramp-up)";
						}
					else
						{
						$MESSAGE = "Power limitation from ramp-rate (normal ramp-up)";
						}
					}
				if($register & 0x0800) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power limitation from Volt-Watt";
						}
					else
						{
						$MESSAGE = "Power limitation from Volt-Watt";
						}
					}
					
				$register = (int)(substr ($CMD_RETURN[12], 9));
				/*
				This register provides status of apparent power derating:
					- Bit0: Power limitation for high temperature
					- Bit1: Power limitation for high DC voltage
					- Bit2-31: Reserved
				*/
				if($register & 0x0001) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power derating for high temperature";
						}
					else
						{
						$MESSAGE = "Power derating for high temperature";
						}
					}
				if($register & 0x0002) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Power derating for high DC voltage";
						}
					else
						{
						$MESSAGE = "Power derating for high DC voltage";
						}
					}
					
				$register = (int)(substr ($CMD_RETURN[13], 9));
				/*
				"This register provides status of auxiliary devices:
					- Bit0: Fan fault
					- Bit1: Statistics memory fault
					- Bit2: RTC clock not set
					- Bit3: Reserved
					- Bit4: RTC low battery fault
					- Bit5: RTC quartz fault
					- Bit6-31: Reserved"
				*/
				if($register & 0x0001) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Fan fault";
						}
					else
						{
						$MESSAGE = "Power derating for high temperature";
						}
					}
				if($register & 0x0002) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."Statistics memory fault";
						}
					else
						{
						$MESSAGE = "Statistics memory fault";
						}
					}
				if($register & 0x0004) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."RTC clock not set";
						}
					else
						{
						$MESSAGE = "RTC clock not set";
						}
					}
				if($register & 0x0010) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."RTC low battery fault";
						}
					else
						{
						$MESSAGE = "RTC low battery fault";
						}
					}
				if($register & 0x0020) 
					{
					if ($MESSAGE != Null)
						{
						$MESSAGE = $MESSAGE.','."RTC quartz fault";
						}
					else
						{
						$MESSAGE = "RTC quartz fault";
						}
					}
				$RET = 'OK';
				}
			}
		}
	}

?>
