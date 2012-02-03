<?
//====================================================
//  Internal functions
//====================================================

function resetSession()
{

  $_SESSION['a7_cur_sim_len']       = ""; 
  $_SESSION['a7_cur_loop_len']      = "";
  $_SESSION['a7_cur_sketch']        = ""; 
  $_SESSION['a7_cur_source']        = ""; 
  $_SESSION['a7_sel_source']        = ""; 
  $_SESSION['a7_cur_step']          = 0; 
  $_SESSION['a7_cur_loop']          = 0; 
  $_SESSION['a7_cur_read']          = 0; 
  $_SESSION['a7_cur_file']          = ""; 
  $_SESSION['a7_cur_sketch_name']   = "";
  $_SESSION['a7_cur_board_type']    = "";
  $_SESSION['a7_cur_board_digpins'] = 0;
  $_SESSION['a7_cur_board_anapins'] = 0;
  $_SESSION['a7_ser_log']           = "";
  $_SESSION['a7_row_number']        = 0;
}
//==========================================
function tokString($str,$delimiter)
//==========================================
{
  global $g_tok;
  if(!$str)return;
 
  $str = trim($str);

  //echo("====== tokstr: ($str)<br>");

  $tok = strtok($str, $delimiter);
  //$g_tok[1] = $tok;

  $ix = 0;
  while ($tok !== false) {
    $ix++;
    $g_tok[$ix] = $tok;
    //echo("tok: $ix ($tok)<br>");
    $tok = strtok($delimiter);
  }
  $g_tok[0] = $ix;
  //echo("toklen: $ix<br>");
  return($ix);
}
//==========================================
function accessControlFile($file,$rw)
//==========================================
{
  $res = file_exists($file);
  if($res)
    return(YES);
  else
    return(NO);
}

// //==========================================
// function rowNumber($data)
// //==========================================
// {
//   $row = array();

//   $res = "";
//   $row = strtok($data, "\n");
//   $row[0] = $tok;

//   $ix = 0;
//   $res = "";
//   while ($tok !== false) {
//     $ix++;
//     echo "$tok<br>";
//     $tok = strtok("\n");
//     $row[$ix] = $tok;
//   }

// }

//==========================================
function accessControl()
//==========================================
{
  global $par,$fn;
  
  $file = $fn['serial'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['custom'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['event'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['error'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['exec'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['g++'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['status'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['code'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['application'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['sketch'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['scenario'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['scenexp'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['list'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['setting'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file");

  $file = $fn['start'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['faq'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['about'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 

  $file = $fn['register'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file");  

  $file = $fn['help'];
  $res = accessControlFile($file,"r");
  if($res == NO)vikingError("Read Access: $file"); 
}
//==========================================
function writeUserSetting()
//==========================================
{
  global $par,$fn;

  $file = $fn['setting'];
  if(!$file)
    {
      vikingWarning("writeUserSetting: no file ($file)");
      return;
    }
  $out = fopen($file,"w");
  if($out)
    {
      $temp = "CUR_SOURCE: ".$par['a7_cur_source']."\n";
      fwrite($out,$temp);
      $temp = "CUR_FILE: ".$par['a7_cur_file']."\n";
      fwrite($out,$temp);
      $temp = "SEL_SOURCE: ".$par['a7_sel_source']."\n";
      fwrite($out,$temp);
    }
  else
    vikingError("Not able to open user setting file write ($file)");  

  fclose($out);
}

//==========================================
function readUserSetting()
//==========================================
{
  global $par,$fn;

  $file = $fn['setting'];
  if(!$file)
    {
      vikingWarning("readUserSetting: no file ($file)");
      return;
    }

  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  if(strstr($row,"CUR_SOURCE"))
	    {
	      if($pp = strstr($row,":"))
		{
		  sscanf($pp,"%s%s",$junk,$curSource);
		  $par['a7_cur_source'] = $curSource;
		  $_SESSION['a7_cur_source'] = $curSource;
		}
	    }
	  if(strstr($row,"CUR_FILE"))
	    {
	      if($pp = strstr($row,":"))
		{
		  sscanf($pp,"%s%s",$junk,$curFile);
		  $par['a7_cur_file'] = $curFile;
		  $_SESSION['a7_cur_file'] = $curFile;
		}
	    }
	  if(strstr($row,"SEL_SOURCE"))
	    {
	      if($pp = strstr($row,":"))
		{
		  sscanf($pp,"%s%s",$junk,$selSource);
		  $par['a7_sel_source'] = $selSource;
		  $_SESSION['a7_sel_source'] = $selSource;
		}
	    }
	}
    }
  else
    vikingError("Not able to open user setting file read ($file)");  
  
  fclose($in);
}


//==========================================
function check_email_address($email) 
//==========================================
{

  // First, we check that there's one @ symbol, 
  // and that the lengths are right.
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    // Email invalid because wrong number of characters 
    // in one section or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
    if
      (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
	     $local_array[$i])) {
      return false;
    }
  }
  // Check if domain is IP. If not, 
  // it should be valid domain name
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
      return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if
	(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
↪([A-Za-z0-9]+))$",
	       $domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}

//==========================================
function createApplication($username,$email,$letter)
//==========================================
{
  global $application,$fn;
  
  if($email && $username)
    {
      $res = check_email_address($email);
      if($res)
	{
	  $file = $fn['application'];
	  $fileSize = filesize($file);
	  if($fileSize < 900000)
	    {
	      $out = fopen($file,"a");
	      if($out)
		{
		  $date = date("Y-m-d H:i:s");
		  $temp = $date." ".$username."   ".$email." [".$letter."]"."\n";
		  fwrite($out,$temp);
		  $application = 1;
		}
	      fclose($out);
	    }
	  else
	    vikingError("Application rejected due to overload");  
	}
      else
	vikingError("Non valid E-mail Address");
    }
  else
    vikingError("Application rejected");  
}

//==========================================
function canvasPos()
//==========================================
{
  global $par,$pinModeD,$pinStatusD,$pinStatusA;
  global $UNO_DIG_PINS,$UNO_ANA_PINS,$MEGA_DIG_PINS,$MEGA_ANA_PINS;
  global $digX,$digY,$anaX,$anaY,$resetX,$resetY;
  global $TXledX,$TXledY,$onOffX,$onOffY,$led13X,$led13Y;
  global $sketchNameX,$sketchNameY,$helpX,$helpY,$help2X,$help2Y;
  $user = $par['user'];

  $board   = $par['a7_cur_board_type'];

  //$input  = array_keys($_GET);
  //$coords = explode(',', $input[0]);
  //$bb = $coords[0]; $aa=$coords[1];

  if($board == 'boardUno')
    {
      // Digital Pin Positions
      $yy = 220;
      for($ii=0; $ii<$UNO_DIG_PINS; $ii++)
	{
	  $xx = 17; $yy = $yy+17;
	  if($ii == 6) $yy = $yy+10;
	  $ix = $UNO_DIG_PINS - 1 - $ii;
	  $digX[$ix] = $xx;
	  $digY[$ix] = $yy;
	  $pinModeD[$ii] = 0;
	  $pinStatusD[$ii] = 0;
	}
      
      // Analog Pin Positions
      $yy = 363;
      for($ii=0; $ii<$UNO_ANA_PINS; $ii++)
	{
	  $xx = 288; $yy = $yy+17;
	  $anaX[$ii] = $xx;
	  $anaY[$ii] = $yy;
	  $pinStatusA[$ii] = 0;
	}
      
      // Step + - positions
      $stepForwardY  = 312;
      $stepForwardX  = 80;
      $stepBackwardY = 273;
      $stepBackwardX = 80;
      
      $resetY = 411;
      $resetX = 150;
      
      $uploadY = 434;
      $uploadX = 212;
      
      $TXledY = 230;
      $TXledX =  93;
      
      $RXledY = 230;
      $RXledX = 107;
      
      $led13Y = 230;
      $led13X =  63;
      
      $onOffY = 431;
      $onOffX = 94;
      
      $sketchNameY = 280;
      $sketchNameX = 220;
      
      $configY = 360;
      $configX = 78;   

      $helpY  =  10;
      $helpX  =  69; 
      $help2Y =   5;
      $help2X = 140; 
    }

  if($board == 'boardMega')
    {
      // Digital Pin Positions
      $yy = 150;
      for($ii=0; $ii<$UNO_DIG_PINS; $ii++)
	{
	  $xx = 19; $yy = $yy+12;
	  if($ii == 6) $yy = $yy+8;
	  $ix = $UNO_DIG_PINS - 1 - $ii;
	  $digX[$ix] = $xx;
	  $digY[$ix] = $yy;
	  $pinModeD[$ix] = 0;
	  $pinStatusD[$ix] = 0;
	}

      for($ii=$UNO_DIG_PINS; $ii<22; $ii++)
	{
	  $xx = 19; $yy = $yy+12;
	  if($ii == $UNO_DIG_PINS) $yy = $yy+10;
	  $digX[$ii] = $xx;
	  $digY[$ii] = $yy;
	  $pinModeD[$ii] = 0;
	  $pinStatusD[$ii] = 0;
	}

      // 22 column
      $xx = 32;$yy = 466;
      for($ii=22; $ii<$MEGA_DIG_PINS; $ii=$ii+2)
	{
	  if($ii == 30) $xx = $xx+2;
	  if($ii == 34) $xx = $xx+2;
	  $digX[$ii] = $xx;
	  $digY[$ii] = $yy;
	  $pinModeD[$ii] = 0;
	  $pinStatusD[$ii] = 0;
	  $xx = $xx + 14;
	}
      // 23 column
      $xx = 32;$yy = 478;
      for($ii=23; $ii<$MEGA_DIG_PINS; $ii=$ii+2)
	{
	  if($ii == 31) $xx = $xx+2;
	  if($ii == 35) $xx = $xx+2;
	  $digX[$ii] = $xx;
	  $digY[$ii] = $yy;
	  $pinModeD[$ii] = 0;
	  $pinStatusD[$ii] = 0;
	  $xx = $xx + 14;
	}
      
      // Analog Pin Positions
      $yy = 253;
      for($ii=0; $ii<$UNO_ANA_PINS+2; $ii++)
	{
	  $xx = 291; $yy = $yy+12;
	  $anaX[$ii] = $xx;
	  $anaY[$ii] = $yy;
	  $pinStatusA[$ii] = 0;
	}
      for($ii=$UNO_ANA_PINS+2; $ii<$MEGA_ANA_PINS; $ii++)
	{
	  $xx = 291; $yy = $yy+12;
	  if($ii == $UNO_ANA_PINS+2) $yy = $yy+10;
	  $anaX[$ii] = $xx;
	  $anaY[$ii] = $yy;
	  $pinStatusA[$ii] = 0;
	}
      
      // Step + - positions
      $stepForwardY  = 312;
      $stepForwardX  = 80;
      $stepBackwardY = 273;
      $stepBackwardX = 80;
      
      $resetY = 410;
      $resetX = 149;
      
      $uploadY = 434;
      $uploadX = 212;
      
      $TXledY = 163;
      $TXledX = 97;
      
      $RXledY = 163;
      $RXledX = 111;
      
      $led13Y = 163;
      $led13X =  67;
      
      $onOffY = 384;
      $onOffX = 95;
      
      $sketchNameY = 175;
      $sketchNameX =  80;
      
      $configY = 360;
      $configX = 78;

      $helpY  =   5;
      $helpX  =  69; 
      $help2Y =   0;
      $help2X = 140;      
    }
}


//==========================================
function copySketch($sketch)
//==========================================
{
  global $par,$fn;
  $user = $par['user'];
  global $upload;

  //$sketch = $upload.$sketch;
  
  $fTemp = $fn['sketch'];
  
  if($sketch && $fTemp)
    {
      $res = checkSketch($sketch);
      if($res == 0)
	{
	  if (!copy($sketch,$fTemp)) {
	    vikingError("Failed to copy ($sketch)");
	  }
	}
      else
	vikingError("Failed to copy ($sketch) due to sketch check");
    }
  else
    {
      $res = 1;
      vikingError("Unable to copy sketch: ($sketch) to ($fTemp)");
    }
  return($res);
}

//==========================================
function compileSketch()
//==========================================
{
  global $par;
  $user = $par['user'];
  $syscom ="cd account/$user;g++ -o servuino servuino.c > g++.error 2>&1;";
  //echo("$syscom<br>");
  system($syscom);
}

//==========================================
function execSketch($steps,$source)
//==========================================
{
  global $par;
  $user = $par['user'];
  if($steps < 1)vikingWarning("Simulation length < 0");

  $syscom = "cd account/$user;./servuino $steps $source >exec.error 2>&1;chmod 777 data.*;";
  //echo("$syscom<br>");
  system($syscom);
}

//==========================================
function decodeAllStatus($step,$code)
//==========================================
{
  //global $par;
  //$user = $par['user'];
  //global $pinValueA,$pinValueD,$pinStatusA,$pinStatusD,$pinModeD;
  global $valueInPinA,$valueOutPinD;

  //$curStep = $par['a7_cur_step'];

  if(!$code)return;

  $xpar = array();
  $tok = strtok($code, ",");
  $xpar[0] = $tok;
//   if($tok != $curStep)
//     {
//       vikingError("Sync Error Step: $step - $curStep");
//       return;
//     }
  $ix = 0;
  while ($tok !== false) {
    $ix++;
    $tok = strtok(",");
    $xpar[$ix] = $tok;
  }

  // Mode Digital Pin
//   $temp = $xpar[1];
//   $bb = strlen($temp);
//   for($ii=0;$ii<strlen($temp);$ii++)
//     {
//       if($temp[$ii]=='-')$pinModeD[$ii] = VOID;
//       if($temp[$ii]=='O')$pinModeD[$ii] = OUTPUT;
//       if($temp[$ii]=='I')$pinModeD[$ii] = INPUT;
//       if($temp[$ii]=='X')$pinModeD[$ii] = TX;
//       if($temp[$ii]=='Y')$pinModeD[$ii] = RX;
//       if($temp[$ii]=='C')$pinModeD[$ii] = I_CHANGE;
//       if($temp[$ii]=='R')$pinModeD[$ii] = I_RISING;
//       if($temp[$ii]=='F')$pinModeD[$ii] = I_FALLING;
//     }

  // Status Analog Pin
  $tempA = $xpar[2]; // Number of Analog Values
  if($tempA > 0)
    {
      for($ii=0;$ii<$tempA;$ii++)
	{
	  $ix = 4+$ii*2;
	  //$pinValueA[$xpar[$ix]] = $xpar[$ix+1];
	  $aw = $xpar[$ix]; 
          $qq = $xpar[$ix+1];
	  //if($pinValueA[$xpar[$ix]]> 0)$pinStatusA[$xpar[$ix]] = READ;
	  //echo("$tempA Analog $ii $aw $qq<br>");
	  $valueInPinA[$aw][$step] = $qq;
	}
    }
  $tempD = $xpar[3]; // Number of Digital Values
  if($tempD > 0)
    {
      for($ii=0;$ii<$tempD;$ii++)
	{
	  $ix = 4+$ii*2+2*$tempA;
	  //$pinValueD[$xpar[$ix]] = $xpar[$ix+1];
	  $aw = $xpar[$ix]; 
          $qq = $xpar[$ix+1];
	  if($qq == 0)$tmp = LOW;
	  if($qq == 1)$tmp = HIGH;
	  if($qq  > 1)$tmp = PWM;

	  $valueOutPinD[$aw][$step] = $tmp;

	  //$temp = $valueOutPinD[$aw][$step];
	  //echo("Value=$temp Digital $ii $aw $qq step=$step<br>");
	}
    }
}

//==========================================
function decodeStatus($code)
//==========================================
{
  global $par;
  $user = $par['user'];
  global $pinValueA,$pinValueD,$pinStatusA,$pinStatusD,$pinModeD;
  global $valueInPinA,$valueOutPinD;

  $curStep = $par['a7_cur_step'];

  if(!$code)return;

  $xpar = array();
  $tok = strtok($code, ",");
  $xpar[0] = $tok;
  if($tok != $curStep)
    {
      vikingError("Sync Error Step: $tok - $curStep");
      return;
    }
  $ix = 0;
  while ($tok !== false) {
    $ix++;
    $tok = strtok(",");
    $xpar[$ix] = $tok;
  }

  // Mode Digital Pin
  $temp = $xpar[1];
  $bb = strlen($temp);
  for($ii=0;$ii<strlen($temp);$ii++)
    {
      if($temp[$ii]=='-')$pinModeD[$ii] = VOID;
      if($temp[$ii]=='O')$pinModeD[$ii] = OUTPUT;
      if($temp[$ii]=='I')$pinModeD[$ii] = INPUT;
      if($temp[$ii]=='X')$pinModeD[$ii] = TX;
      if($temp[$ii]=='Y')$pinModeD[$ii] = RX;
      if($temp[$ii]=='C')$pinModeD[$ii] = I_CHANGE;
      if($temp[$ii]=='R')$pinModeD[$ii] = I_RISING;
      if($temp[$ii]=='F')$pinModeD[$ii] = I_FALLING;
    }

  // Status Analog Pin
  $tempA = $xpar[2]; // Number of Analog Values
  if($tempA > 0)
    {
      for($ii=0;$ii<$tempA;$ii++)
	{
	  $ix = 4+$ii*2;
	  $pinValueA[$xpar[$ix]] = $xpar[$ix+1];
	  $aw = $xpar[$ix]; 
          $qq = $xpar[$ix+1];
	  if($pinValueA[$xpar[$ix]]> 0)$pinStatusA[$xpar[$ix]] = READ;
	  //echo("$tempA Analog $ii $aw $qq<br>");
	}
    }
  $tempD = $xpar[3]; // Number of Digital Values
  if($tempD > 0)
    {
      for($ii=0;$ii<$tempD;$ii++)
	{
	  $ix = 4+$ii*2+2*$tempA;
	  $pinValueD[$xpar[$ix]] = $xpar[$ix+1];
	  $aw = $xpar[$ix]; 
          $qq = $xpar[$ix+1];
	  if($pinValueD[$aw] == 0)$pinStatusD[$aw] = LOW;
	  if($pinValueD[$aw] == 1)$pinStatusD[$aw] = HIGH;
	  if($pinValueD[$aw]  > 1)$pinStatusD[$aw] = PWM;
	}
    }
}


//==========================================
function init($steps)
//==========================================
{
  global $par,$simulation,$serial,$serialL;

  $user  = $par['user'];
  for($ii=0;$ii<=$steps;$ii++)
    {
      $simulation[$ii] = "";
      $scenario[$ii] = "";
      $serial[$ii] = "";
      $serialL[$ii] = "";
    }
}

//==========================================
function showStep($target)
//==========================================
{
  global $par;
  $path   = $par['path'];
  $user   = $par['user'];
  global $curStep,$simulation,$curSimLen;


  for($ii=$target+1;$ii>0;$ii--)
    {
      if($ii==$target+1)
	echo("next> $simulation[$ii]<br>");
      else if($ii==$target)
	echo("now> $simulation[$ii]<br>");
      else
	echo("<a href=\"$path&ac=step&x=$ii\">$simulation[$ii]</a><br>");
    }
}

//==========================================
function showScenario($target)
//==========================================
{
  global $par;
  $path   = $par['path'];
  $user   = $par['user'];
  global $scenario;

  echo("$scenario[0]<br>");
  for($ii=$target+1;$ii>0;$ii--)
    {
      if($ii==$target+1)
	echo("next> $scenario[$ii]<br>");
      else if($ii==$target)
	echo("now> $scenario[$ii]<br>");
      else
	echo("<a href=\"$path&ac=step&x=$ii\">$scenario[$ii]</a><br>");
    }
}


//==========================================
function showSerial($target)
//==========================================
{
  global $par;
  global $curStep,$serial,$serialL;

  $path   = $par['path'];
  $user   = $par['user'];

  $stemp = array();
 
  $jj = 1;
  //for($ii=$target;$ii>0;$ii--)
  for($ii=1;$ii<=$target;$ii++)
    {
      $temp = $serial[$ii];
      if($serialL[$ii] == 'NL')
	{
	  $stemp[$jj] = $stemp[$jj]."<a href=\"$path&ac=step&x=$ii\">$temp</a><br>";
	  $jj++;
	  $flag = 0;
	  //echo("<a href=\"index.php?ac=step&x=$ii\">$temp</a><br>");
	}
      if($serialL[$ii] == 'SL')
	{
	  $stemp[$jj] = $stemp[$jj]."<a href=\"$path&ac=step&x=$ii\">$temp</a>";
	  $flag = 1;
	  //echo("<a href=\"index.php?ac=step&x=$ii\">$temp</a>");
	}
    }


  //for($ii=1;$ii<=$jj;$ii++)
 
  for($ii=$jj;$ii>0;$ii--)
    {
      $temp = $stemp[$ii];
      echo("$temp");
      if($flag==1 && $ii == $jj)echo("<br>");
    }

}

//==========================================
function showSimulation($target)
//==========================================
{
  global $par;
  global $simulation;

  $path  = $par['path'];
  $user  = $par['user'];
  
  $curStep   = $par['a7_cur_step'];
  $curSimLen = $par['a7_cur_sim_len'];
  
  for($ii=1;$ii<=$curSimLen;$ii++)
    {
      if($ii==$target)
	echo("now> $simulation[$ii]<br>");
      else
	echo("<a href=\"$path&ac=step&x=$ii\">$simulation[$ii]</a><br>");
    }
}

//==========================================
function showAnyFile($target)
//==========================================
{
  global $par;
  global $content;

  $rowNumber = $par['a7_row_number'];
  $user      = $par['user'];

  for($ii=1;$ii<=$target;$ii++)
    {
      if($rowNumber == 1)
	echo("$ii: $content[$ii]<br>");
      else
	echo("$content[$ii]<br>");
    }
}

//==========================================
function readSimulation()
//==========================================
{
  global $par,$fn;
  $user  = $par['user'];
  global $simulation;
  global $loopStep,$stepLoop,$readStep,$stepRead;
  //$servuino,$loopStep,$stepLoop,$readStep,$stepRead;

  //$file = $servuino.$file;
  $step = 0;
  $loop = 0;
  $read = 0;

  $file = $fn['event'];
  if(!$file)
    {
      vikingWarning("readSimulation: no file ($file)");
      return;
    }

  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  //$row = trim($row);
	  //$row = safeText($row);
	  //echo("$row<br>");
	  if($row[0]=='+')
	    {
	      $pp = strpos($row,"?");
	      $row[$pp] = ' ';
	      $step++;
              $row[0] = ' ';
	      $simulation[$step] = $row;

              if(strstr($row,"digitalRead ") || strstr($row,"analogRead ") && !strstr($row,"Serial:"))
		{
		  $read++;
		  $readStep[$read] = $step;
		  if(strstr($row,"gRead"))$type = 1;//Analog
		  if(strstr($row,"lRead"))$type = 2;//Digital
		  $pp = strstr($row,"Read");
		  $pp = strstr($pp," ");
		  tokString($pp," ");
		  $pin   = $g_tok[1];
		  $value = $g_tok[2];
		  $g_readType[$step]  = $type;
                  $g_readPin[$step]   = $pin;
		  $g_readValue[$step] = $value;
		  //echo("$step pin=$pin value=$value type=$type<br>");
		}
              $stepRead[$step] = $read;

              if(strstr($row,"servuinoLoop "))
		{
		  $loop++;
		  $loopStep[$loop] = $step;
		}
              $stepLoop[$step] = $loop;
	    }
	}
      $par['a7_cur_sim_len']  = $step;
      $par['a7_cur_loop_len'] = $loop;
      $par['a7_cur_read_len'] = $read;
      fclose($in);
    }
  else
    {
      $temp = "readSimulation: Fail to open ($file)";
      vikingError($temp);
    }

  //readArduino();

  return($step);
}

//==========================================
function readArduino()
//==========================================
{
  global $par,$fn,$g_tok,$g_readValue,$g_readPin,$g_readType;
  $user  = $par['user'];
  //global $simulation,$servuino,
  global $loopStep,$stepLoop,$readStep,$stepRead;


  $step = 0;
  $loop = 0;
  $read = 0;
  $file = $fn['event'];
  if(!$file)
    {
      vikingWarning("readArduino: no file ($file)");
      return;
    }

  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  //$row = trim($row);
	  //$row = safeText($row);
	  //echo("$row<br>");
	  if($row[0]=='+')
	    {
	      $step++;
              if(strstr($row,"digitalRead ") || strstr($row,"analogRead ") && !strstr($row,"Serial:"))
		{
		  $read++;
		  $readStep[$read] = $step;
		  if(strstr($row,"gRead"))$type = 1;//Analog
		  if(strstr($row,"lRead"))$type = 2;//Digital
		  $pp = strstr($row,"Read");
		  $pp = strstr($pp," ");
		  tokString($pp," ");
		  $pin   = $g_tok[1];
		  $value = $g_tok[2];
		  $g_readType[$step]  = $type;
                  $g_readPin[$step]   = $pin;
		  $g_readValue[$step] = $value;
		  //echo("$step pin=$pin value=$value type=$type<br>");
		}
              $stepRead[$step] = $read;
              if(strstr($row,"servuinoLoop "))
		{
		  $loop++;
		  $loopStep[$loop] = $step;
		}
              $stepLoop[$step] = $loop;
	    }
	}
      $par['a7_cur_sim_len'] = $step;
      $par['a7_cur_loop_len'] = $loop;
      $par['a7_cur_read_len'] = $read;
      fclose($in);
    }
  else
    {
      $temp = "readArduino: Fail to open ($file)";
      vikingError($temp);
    }
  return($step);
}


//==========================================
function readSerial()
//==========================================
{
  global $par,$fn;
  $user  = $par['user'];
  global $serial,$servuino,$serialL;

  $file = $fn['serial'];
  $step = 0;
  if(!$file)
    {
      vikingWarning("readSerial: no file ($file)");
      return;
    }

  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  $row = trim($row);
	  sscanf($row,"%d %s %s",$step,$line,$value);
	  $serialL[$step] = $line;
	  $left  = strpos($row,"[");
	  $right = strpos($row,"]");
	  if($right && $left){$left++;$value = substr($row,$left,$right-$left);}
	  $value = safeText2($value);
	  //echo("$step $line $value<br>");
	  $serial[$step] = $value;
	}
      fclose($in);
    }
  else
    {
      $temp = "readSerial: Fail to open ($file)";
      vikingError($temp);
    }
  return($step);
}

//==========================================
function readxStatus()
//==========================================
{
  global $par,$fn;
  $user = $par['user'];
  global $status,$servuino;

  $file = $fn['status'];
  $step = 0;
  if(!$file)
    {
      vikingWarning("readStatus: no file ($file)");
      return;
    }


  $in = fopen($file,"r");
  if($in)
    {
      $row = fgets($in);
      $row = fgets($in);
      while (!feof($in))
	{
	  $row = fgets($in);
	  $step++;
	  $row = trim($row);
	  $status[$step] = $row;
          //decodeStatus($status[$step]);
	}
      fclose($in);
    }
  else
    {
      $temp = "readStatus: Fail to open ($file)";
      vikingError($temp);
    }

  return($step);
}

//==========================================
function readStatus()
//==========================================
{
// int x_pinMode[MAX_TOTAL_PINS];
// int x_pinScenario[MAX_TOTAL_PINS][SCEN_MAX];
// int x_pinDigValue[MAX_TOTAL_PINS];
// int x_pinAnaValue[MAX_TOTAL_PINS];
// int x_pinRW[MAX_TOTAL_PINS];

  global $par,$fn;
  //$user = $par['user'];
  //global $status,$servuino;
  global $x_pinMode,$x_pinDigValue,$x_pinAnaValue,$x_pinRW;
  global $g_tok;

  // ========= Read Pin Mode
  $file = $fn['pinmod'];
  $step = 0;
  if(!$file)
    {
      vikingWarning("readStatus: no file ($file)");
      return;
    }
  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  if($row[0] == '+')
	    {
	      sscanf($row,"%s %d",$junk,$step);
	      $pp = strstr($row,"? ");
	      $qq = strstr($pp," ");
	      $nn = tokString($qq,",");
	      //echo("$nn benny: ($qq) <br>");
	      for($ii=1;$ii<=$nn;$ii++)
		{
		  $pin = $ii-1;
		  $x_pinMode[$pin][$step] = $g_tok[$ii];
		  //$temp = $x_pinMode[$pin][$step];
		  //echo("benny step=$step pin=$pin mode=$temp<br>");
		}
	    }
	}
      fclose($in);
    }
  else
    {
      $temp = "readStatus: Fail to open ($file)";
      vikingError($temp);
    }

  // ========= Read Dig Pin Value
  $file = $fn['digval'];
  $step = 0;
  if(!$file)
    {
      vikingWarning("readStatus: no file ($file)");
      return;
    }
  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  if($row[0] == '+')
	    {
	      sscanf($row,"%s %d",$junk,$step);
	      $pp = strstr($row,"? ");
	      $qq = strstr($pp," ");
	      $nn = tokString($qq,",");
	      //echo("$nn benny: ($qq) <br>");
	      for($ii=1;$ii<=$nn;$ii++)
		{
		  $pin = $ii-1;
		  $x_pinDigValue[$pin][$step] = $g_tok[$ii];
		  //$temp = $x_pinMode[$pin][$step];
		  //echo("benny step=$step pin=$pin mode=$temp<br>");
		}
	    }
	}
      fclose($in);
    }
  else
    {
      $temp = "readStatus: Fail to open ($file)";
      vikingError($temp);
    }

  // ========= Read Ana Pin Value
  $file = $fn['anaval'];
  $step = 0;
  if(!$file)
    {
      vikingWarning("readStatus: no file ($file)");
      return;
    }
  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  if($row[0] == '+')
	    {
	      sscanf($row,"%s %d",$junk,$step);
	      $pp = strstr($row,"? ");
	      $qq = strstr($pp," ");
	      $nn = tokString($qq,",");
	      //echo("$nn benny: ($qq) <br>");
	      for($ii=1;$ii<=$nn;$ii++)
		{
		  $pin = $ii-1;
		  $x_pinAnaValue[$pin][$step] = $g_tok[$ii];
		  //$temp = $x_pinAnaValue[$pin][$step];
		  //echo("benny step=$step pin=$pin val=$temp<br>");
		}
	    }
	}
      fclose($in);
    }
  else
    {
      $temp = "readStatus: Fail to open ($file)";
      vikingError($temp);
    }

  // ========= Read pin RW
  $file = $fn['pinrw'];
  $step = 0;
  if(!$file)
    {
      vikingWarning("readStatus: no file ($file)");
      return;
    }
  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  if($row[0] == '+')
	    {
	      sscanf($row,"%s %d",$junk,$step);
	      $pp = strstr($row,"? ");
	      $qq = strstr($pp," ");
	      $nn = tokString($qq,",");
	      //echo("$nn $step benny: ($qq) <br>");
	      for($ii=1;$ii<=$nn;$ii++)
		{
		  $pin = $ii-1;
		  $x_pinRW[$pin][$step] = $g_tok[$ii];
		  //$temp = $x_pinRW[$pin][$step];
		  //echo("benny step=$step pin=$pin val=$temp<br>");
		}
	    }
	}
      fclose($in);
    }
  else
    {
      $temp = "readStatus: Fail to open ($file)";
      vikingError($temp);
    }

  return($step);
}

//==========================================
function readScenario()
//==========================================
{
  global $par,$fn,$g_tok,$valueInPinA,$valueInPinD;
  $apin = array();
  $dpin = array();
  $user  = $par['user'];
  global $scenario,$servuino;

  $file = $fn['scenexp'];
  $step = 0;
  if(!$file)
    {
      vikingWarning("readScenario: no file ($file)");
      return;
    }

  $in = fopen($file,"r");
  if($in)
    {
      $temp = "No Pin definition";
      $row  = fgets($in);
      if(strstr($row,"Digital:"))
	{
	  $temp = $row;
	  $pp = strstr($row,":");
	  $pp = strstr($pp," ");
	  tokString($pp," ");
	  $Dpins = $g_tok[0];
	  if($Dpins > 0)
	    {
	      for($ii=1;$ii<=$Dpins;$ii++)
		{
		  $dpin[$ii] = $g_tok[$ii];
		  $tmp = $dpin[$ii];
		  //echo("($row)PinNo $ii=($tmp)<br>");
		}
	    }
	}
      $row  = fgets($in);
      if(strstr($row,"Analog:"))
	{
	  $temp = $temp.$row;
	  $pp = strstr($row,":");
	  $pp = strstr($pp," ");
	  tokString($pp," ");
	  $Apins = $g_tok[0];
	  if($Apins > 0)
	    {
	      for($ii=1;$ii<=$Apins;$ii++)
		$apin[$ii] = $g_tok[$ii];
	    }
	}
      $scenario[0] = $temp;

      while (!feof($in))
	{
	  $row = fgets($in);
	  if($row[0] != '#')
	    {
	      $step++;
	      $scenario[$step] = $row;
	      tokString($row," ");
	      $temp1 = $g_tok[0];
	      $ix = 0;
	      //echo("$Dpins+$Apins+1 == $temp1<br>");
	      if($Dpins+$Apins+1 == $temp1)
		{
		  for($ii=1;$ii<=$Dpins;$ii++)
		    {
		      $ix++;
		      $pin = $dpin[$ii];
		      $valueInPinD[$pin][$step] = $g_tok[$ix+1];
		      $val = $g_tok[$ix+1];
		      //echo("Dig $step pin=$pin value=$val ix=$ix<br>");
		    }
		  for($ii=1;$ii<=$Apins;$ii++)
		    {
		      $ix++;
		      $pin = $apin[$ii];
		      $valueInPinA[$pin][$step] = $g_tok[$ix+1];
		      $val = $g_tok[$ix+1];
		      //echo("Ana $step pin=$pin value=$val<br>");
		    }
		}
	    }
	}
      fclose($in);
    }
  else
    {
      $temp = "readScenario: Fail to open ($file)";
      vikingError($temp);
    }

  return($step);
}

//==========================================
function readAnyFile($check,$file)
//==========================================
{
  global $par;
  global $content,$servuino;
  $user = $par['user'];

  $step = 0;
  $content[0] = 0;

  if($file == '-')return(0);

  if(!$file)
    {
      vikingWarning("readAnyFile: no file ($file)");
      return;
    }
  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  //echo("($row)");
	  $row = trim($row);
	  //$row = str_replace(" ","&nbsp;",$row);
	  $step++;
	  $content[$step] = $row;
	  $content[0] = $step;
	  //echo("$step($row)");
	}
      fclose($in);
    }
  else if($check == 1)
    {
      $temp = "readAnyFile: Fail to open ($file)";
      vikingError($temp);
    }
  if($step==1)$step = strlen($content[1]); // return zero if line empty
  return($step);
}

//==========================================
function readAnySketch($check,$file)
//==========================================
{
  global $par;
  global $content,$servuino;
  $user = $par['user'];

  $step = 0;
  $content[0] = 0;

  if($file == '-')return(0);

  if(!$file)
    {
      vikingWarning("readAnyFile: no file ($file)");
      return;
    }
  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  $row = trim($row);
	  $row = str_replace(" ","&nbsp;",$row);
	  $step++;
	  $content[$step] = $row;
	  $content[0] = $step;
	}
      fclose($in);
    }
  else if($check == 1)
    {
      $temp = "readAnyFile: Fail to open ($file)";
      vikingError($temp);
    }
  return($step);
}

//==========================================
function readLoginCounter()
//==========================================
{
  global $par;

  $file = 'login.counter';
  if(!$file)
    {
      vikingWarning("readLoginCounter: no file ($file)");
      return;
    }
  $in = fopen($file,"r");
  if($in)
    {
      $row = fgets($in);
      $par['login_counter'] = $row;
      fclose($in);
    }
  else
    {
      $temp = "readLoginCounter: Fail to open ($file)";
      vikingError($temp);
    }
  return;
}

//==========================================
function formSelectFile($name,$fname,$file,$sel,$dir)
//==========================================
{
  global $par;
  $user = $par['user'];

  $res = 0;
  if(!$file)
    {
      vikingWarning("formSelectFile: no file ($file)");
      return;
    }

  $in = fopen($file,"r");
  if($in)
    {
      echo("$name <select name=\"$fname\">");
      while (!feof($in))
	{
	  $row = fgets($in);
	  $row = trim($row);
	  //$row = safeText($row);
	  if($row)
	    {
	      $dirrow = $dir.$row;
	      $selected = "";if($sel == $dirrow)$selected = 'selected';
	      echo("<option value=\"$dirrow\" $selected>$row</option>");
              $res++;
	    }
	}
      echo("</select>");
      fclose($in);
    }
  else
    {
      $temp = "formSelectFile:Fail to open ($file)";
      vikingError($temp);
    }
  return($res);
}

//==========================================
function linkFile($file,$sel,$dir)
//==========================================
{
  global $par;
  $user = $par['user'];

  $res = 0;
  if(!$file)
    {
      vikingWarning("formSelectFile: no file ($file)");
      return;
    }

  $in = fopen($file,"r");
  if($in)
    {
      echo("<hr>Download Area<br>");
      while (!feof($in))
	{
	  $row = fgets($in);
	  $row = trim($row);
	  //$row = safeText($row);
	  if($row)
	    {
	      $dirrow = $dir.$row;
	      echo("<a href=\"$dirrow\">$row</a> |");
              $res++;
	    }
	}
      echo("<hr>");
      fclose($in);
    }
  else
    {
      $temp = "linkFile:Fail to open ($file)";
      vikingError($temp);
    }
  return($res);
}
//==========================================
function readSketchInfo()
//==========================================
{
  global $par,$fn;
  global $UNO_DIG_PINS,$UNO_ANA_PINS,$MEGA_DIG_PINS,$MEGA_ANA_PINS;
  //global $curSketchName;


  // Default Values
  $name    = 'No_Sketch_Name';
  $boardId = 'boardUno';
  $boardDigPins = $UNO_DIG_PINS;
  $boardAnaPins = $UNO_ANA_PINS;

  $user = $par['user'];

  $tFile = $fn['sketch'];

  if(!$tFile)
    {
      vikingWarning("readSketchInfo: no file ($file)");
      return;
    }

  $in = fopen($tFile,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  $row = trim($row);
	  if(strstr($row,"SKETCH_NAME:"))
	    {
	      if($pp = strstr($row,":"))
		{
		  sscanf($pp,"%s%s",$junk,$name);
		}
	    }
	  if(strstr($row,"BOARD_TYPE:"))
	    {
	      if($pp = strstr($row,":"))
		{
		  sscanf($pp,"%s%s",$junk,$board);
		  $par['a7_cur_board_type'] = $board;
		  if(strstr($board,"MEGA"))
		    {
		      //echo("MEGA");
		      $boardId = 'boardMega';
		      $boardDigPins = $MEGA_DIG_PINS;
		      $boardAnaPins = $MEGA_ANA_PINS;

		    }
		  else
		    {
		      //echo("UNO");
		      $boardId = 'boardUno';
		      $boardDigPins = $UNO_DIG_PINS;
		      $boardAnaPins = $UNO_ANA_PINS;
		    }
		}
	    }
	}
      fclose($in);
    }
  else
    {
      $temp = "readSketchInfo: Fail to open ($tFile)";
      vikingError($temp);
    }
  $par['a7_cur_board_type']    = $boardId;
  $par['a7_cur_board_digpins'] = $boardDigPins;
  $par['a7_cur_board_anapins'] = $boardAnaPins;
  $par['a7_cur_sketch_name']   = $name;
  //echo("$boardId, $boardDigPins, $boardAnaPins $name");
}

//==========================================
function safeText2($text)
//==========================================
{
  global $par;
  $user       = $par['user'];
  $text = str_replace("#", "No.", $text); 
  $text = str_replace("$", "Dollar", $text); 
  $text = str_replace("%", "Percent", $text); 
  $text = str_replace("^", "", $text); 
  //$text = str_replace("&", "and", $text); 
  //$text = str_replace("*", "", $text); 
  //$text = str_replace("?", "", $text); 
  $text = str_replace("<", "R", $text); 
  //$text = str_replace(">", "T", $text); 
  $text = str_replace(" ", "&nbsp;", $text); 
  return($text);
}

//==========================================
function safeText3($text)
//==========================================
{
  $text = str_replace("#", "No.", $text); 
  $text = str_replace("$", "Dollar", $text); 
  $text = str_replace("%", "Percent", $text); 
  $text = str_replace("^", "HAT", $text); 
  $text = str_replace("&", "AND", $text); 
  $text = str_replace("*", "STAR", $text); 
  $text = str_replace("?", "QM", $text); 
  $text = str_replace("<", "LEFT", $text); 
  $text = str_replace(">", "RIGHT", $text); 
  $text = str_replace(" ", "&nbsp;", $text); 
  return($text);
}

//==========================================
function getExtension2($str)
//==========================================
{
  global $par;
  $user       = $par['user'];
  $ii = strrpos($str,".");
  if (!$ii) { return ""; }
  $ll = strlen($str) - $ii;
  $ext = substr($str,$ii+1,$ll);
  return $ext;
}

//==========================================
function showFile($title,$file)
//==========================================
{
  global $par;
  $user  = $par['user'];

  if(!$file)
    {
      vikingWarning("showFile: no file ($file)");
      return;
    }

  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in)) 
	{
	  $row = fgets($in);
	  echo($row);
	  echo("<br>");
	}
      fclose($in);
    }
  else
    {
      $temp = "showFile: Fail to open ($file)";
      vikingError($temp);
    }

  return;
}

//==========================================
function uploadFile2()
//==========================================
{
  global $par;
  $user  = $par['user'];
  define ("MAX_SIZE","300");
  $errors=0;
  $newname = '';

  if(isset($_POST['submit_file']))
    {

      $import=$_FILES['import_file']['name'];
      if ($import)
        {
          $file_name = stripslashes($_FILES['import_file']['name']);
          //$file_name = safeText($file_name);
          $extension = getExtension($file_name);
          $extension = strtolower($extension);
          if (($extension != "txt") && ($extension != "pde") && ($extension != "c"))
            {
              vikingError("Unknown Import file Extension: $extension");
              $errors=1;
            }
          else
            {
              $size=filesize($_FILES['import_file']['tmp_name']);
              if ($size > MAX_SIZE*1024)
                {
                  vikingError("You have exceeded the size limit! $size");
                  $errors=1;
                }
              //$image_name=time().'.'.$extension;
              //$file_name = $db.'-'.$id.'.'.$extension;
              $newname="account/".$user."/upload/".$file_name;
              $copied = move_uploaded_file($_FILES['import_file']['tmp_name'], $newname);
              if (!$copied)
                {
                  vikingError("Import Copy unsuccessfull! $size");;
                  $errors=1;
                }
            }
        }
    }
  if(isset($_POST['submit_file']) && !$errors)
    {
      chmod($newname,0666);
      return($newname);
      // echo "<h1>File Uploaded Successfully! $size</h1>";
    }
  return($newname);
}

//==========================================
function checkSketch($sketch)
//==========================================
{
   
  global $par;
  $res  = 0;
  $user = $par['user'];

  $sketch_name_ok  = NO;
  $board_type_ok   = NO;
  $no_system_calls = YES;
  $no_script       = YES;
  $no_php          = YES;
  
  if(!$sketch)
    {
      vikingWarning("checkSketch: no file ($file)");
      return;
    }

  $in = fopen($sketch,"r");
  if($in)
    {
      while (!feof($in))
        {
          $row = fgets($in);
          if(strstr($row,"SKETCH_NAME:"))
            {
	      $sketch_name_ok = YES;
            }
          if(strstr($row,"BOARD_TYPE:"))
            {
	      $board_type_ok = YES;
            }
          if(strstr($row,"system"))
            {
	      $no_system_calls = NO;
            }
          if(strstr($row,"script"))
            {
	      $no_script = NO;
            }
          if(strstr($row,"<?"))
            {
	      $no_php = NO;
            }
          if(strstr($row,"?>"))
            {
	      $no_php = NO;
            }
        }
      fclose($in);

      if($sketch_name_ok  == NO)
	{
	  vikingWarning("Add to sketch: // SKETCH_NAME: your_name");
	  $res = 0;
	}
      if($board_type_ok   == NO)
	{
	  vikingWarning("Default Board Type is UNO. Add to sketch: // BOARD_TYPE: UNO");
	  $res = 0;
	}
      if($no_system_calls == NO){vikingError("No system calls allowed");$res = 1;}
      if($no_script       == NO){vikingError("No script allowed")      ;$res = 1;}
      if($no_php          == NO){vikingError("No php allowed")         ;$res = 1;}
    }
  else
    {
      vikingError("checkSketch: Fail to open $file");
      $res = 1;
    }

  return($res);

}

//==========================================
function evilCode($data)
//==========================================
{ 
  global $par;
  $res  = 0;
  $user = $par['user'];
  
  $no_system_calls = YES;
  $no_script       = YES;
  $no_php          = YES;
    
  if(strstr($data,"system"))
    {
      $no_system_calls = NO;
    }
  if(strstr($data,"script"))
    {
      $no_script = NO;
    }
  if(strstr($data,"<?"))
    {
      $no_php = NO;
    }
  if(strstr($data,"?>"))
    {
      $no_php = NO;
    }

  if($no_system_calls == NO){vikingWarning("No system calls allowed");$res = 1;}
  if($no_php          == NO){vikingWarning("No php");                 $res = 1;}
  if($no_script       == NO){vikingWarning("No script allowed")      ;$res = 1;}

  return($res);
}
   
?>