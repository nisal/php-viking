<?
//======================================
// Arduino

// function viking_7_function1()
//======================================
define('T_UPLOAD_SKETCH','Upload Sketch to Library');
define('T_CONFIG','Configuration');
define('T_SELECT','Select');
define('T_LOOP_F','Next Loop');
define('T_LOOP_B','Prev Loop');
define('T_STEP_F','Next Step');
define('T_STEP_B','Prev Step');
define('T_EDIT','Edit');
define('T_SAVE','Save');
define('T_LOAD','Load');
define('T_RUN', 'Run');

define('BLACK',  '0');
define('YELLOW', '1');
define('WHITE',  '2');
define('RED',    '3');
define('GREEN',  '4');
define('BLUE',   '5');
define('FUCHSIA','6');
define('AQUA' ,  '7');

// Declarations =====================================

$upload   = 'upload/';
$servuino = 'servuino/';

$simulation = array();
$content    = array();
$status     = array();
$serial     = array();
$serialL    = array();

$pinValueA  = array();
$pinValueD  = array();
$pinStatusA = array();
$pinStatusD = array();
$pinModeD   = array();


$hBoard = 300; // 300
$wBoard = 500; // 500
canvasPos();

// read SESSION parameters ===============================
if (!isset($_SESSION['a7_cur_sim_len']))$_SESSION['a7_cur_sim_len'] = "undefine"; 
if (!isset($_SESSION['a7_cur_sketch']))$_SESSION['a7_cur_sketch'] = "undefine"; 
if (!isset($_SESSION['a7_cur_step']))$_SESSION['a7_cur_step'] = "undefine"; 
if (!isset($_SESSION['a7_cur_loop']))$_SESSION['a7_cur_loop'] = "undefine"; 
if (!isset($_SESSION['a7_cur_log']))$_SESSION['a7_cur_log'] = "undefine"; 
if (!isset($_SESSION['a7_cur_menu']))$_SESSION['a7_cur_menu'] = "undefine"; 
if (!isset($_SESSION['a7_cur_file']))$_SESSION['a7_cur_file'] = "undefine"; 
if (!isset($_SESSION['a7_cur_sketch_name']))$_SESSION['a7_cur_sketch_name'] = "undefine"; 



$par['a7_cur_sim_len'] = $_SESSION['a7_cur_sim_len'];
init($par['a7_cur_sim_len']);

$par['a7_cur_sketch']  = $_SESSION['a7_cur_sketch'];
$par['a7_cur_step']    = $_SESSION['a7_cur_step'];
$par['a7_cur_loop']    = $_SESSION['a7_cur_loop'];
$par['a7_cur_log']     = $_SESSION['a7_cur_log'];
$par['a7_cur_menu']    = $_SESSION['a7_cur_menu'];
$par['a7_cur_file']    = $_SESSION['a7_cur_file'];
$par['a7_cur_sketch_name'] = $_SESSION['a7_cur_sketch_name'];


readSketchInfo();
readSimulation('data.custom');
readSerial('data.serial');


// GET ==============================================
$sys_id = $_GET['a7_sid'];
$par['a7_sid'] = $_GET['a7_sid'];

//$input  = array_keys($_GET); 
//$coords = explode(',', $input[0]); 
//print("X coordinate : ".$coords[0]."<br> Y Coordinate : ".$coords[1]); 

$action  = $_GET['ac'];
$alt     = $_GET['x'];

if($action == 'load')
  {
    $alt = $_GET['x'];
    if($alt == 'CGE')
      {
	$file = $upload.$curSketch;
	copySketch($file);// C
	compileSketch(); // G
	execSketch($curSimLen,0); // E
      }
    if($alt == 'GE')
      {
	compileSketch(); // G
	execSketch($curSimLen,0); // E
      }
    if($alt == 'E')
      {
	execSketch($curSimLen,0); // E
      }

    $curStep = 0;
    init($curSimLen);
    readSketchInfo();
    readSimulation('data.custom');
    readSerial('data.serial');
    readStatus();
    $par['a7_ready'] = "Sketch loaded!";
  }

if($action == 'menu')
  {
    $curMenu = $_GET['x'];
    $_SESSION['cur_menu'] = $curMenu; 
  }


if($action == 'run' && $curSimLen > 0)
  {
    execSketch($curSimLen,0);
  }

if($action == 'step')
  {
    $par['a7_cur_step'] = $_GET['x'];
  }

if($action == 'edit_file')
  {
    $curEditFlag = 1;
  }

if($action == 'reset')
  {
    $par['a7_cur_step'] = 1;
  }

if($action == 'log')
  {
    $source = $_GET['x'];
    if($source == 'code')   $curLog = 'data.code';
    if($source == 'error')  $curLog = 'data.error';
    if($source == 'custom') $curLog = 'data.custom';
    if($source == 'arduino')$curLog = 'data.arduino';
    if($source == 'scen')   $curLog = 'data.scen';
    if($source == 'status') $curLog = 'data.status';
    if($source == 'serial') $curLog = 'data.serial';
    $par['a7_cur_log'] = $curLog;
    //$_SESSION['cur_log'] = $curLog;
    $logLen = readAnyFile(1,$curLog);
  }


// POST =============================================

if (!isset($_POST['action']))$_POST['action'] = "undefine"; 

    $action = $_POST['action'];
    
    if($action == 'select_file' )
      {
	$par['a7_cur_file'] = $_POST['file'];
	$what = $_POST['submit_select'];
	if($what == T_EDIT) $curEditFlag = 1;
      }

    if($action == 'edit_file')
      {
	$tempFile = $_POST['file_name'];
	$data = $_POST['file_data'];
	$what = $_POST['submit_edit'];
	$curSimLen = $par['a7_cur_sim_len'];
	$fp = fopen($tempFile, 'w')or die("Could not open file ($tempFile) (write)!");;
	fwrite($fp,$data) or die("Could not write to file ($tempFile) !");
	fclose($fp);

	if($what == T_LOAD)
	  {
	    compileSketch();
	    execSketch($curSimLen,0);
	    $par['a7_cur_step'] = 0;
	    init($curSimLen);
	    readSketchInfo();
	    readSimulation('data.custom');
	    readStatus();
	    readSerial('data.serial');
	    $par['a7_ready'] = "Sketch loaded!";
	  }
	if($what == T_RUN)
	  {
	    execSketch($curSimLen,1);
	    $par['a7_cur_step'] = 0;
	    init($curSimLen);
	    readSketchInfo();
	    readSimulation('data.custom');
	    readStatus();
	    readSerial('data.serial');
	    $par['a7_ready'] = "Sketch Executed!";
	  }


      }
    if($action == 'upload_sketch' )
      {
	$fil = uploadFile2();
      }

    if($action == 'set_configuration' )
      {
        $curSimLen = $_POST['sim_len'];
	$par['a7_cur_sim_len'] =  $curSimLen;
        $curSketch = $_POST['sketch'];
	$par['a7_cur_sketch'] =  $curSketch;
        copySketch($curSketch);
        compileSketch();
        execSketch($curSimLen,0);
        $par['a7_cur_step'] = 0;
	init($curSimLen);
	readSketchInfo();
	readSimulation('data.custom');
	readStatus();
	readSerial('data.serial');
	$par['a7_ready'] = "Sketch loaded!";
      }

    if($action == 'run_target' )
      {
        $targetStep = $_POST['target_step'];
        runTarget($targetStep);
      }


 
//  }

$curStep = $par['a7_cur_step'];
readStatus();
decodeStatus($status[$curStep]);

// set SESSION parameters ===============================


// $par['a7_cur_step']    = $curStep;
// $par['a7_cur_sim_len'] = $curSimLen;
// $par['a7_cur_sketch']  = $curSketch;
// $par['a7_cur_log']     = $curLog;
// $par['a7_cur_menu']    = $curMenu; 
// $par['a7_cur_file']    = $curFile; 
// $par['a7_cur_sketch_name'] = $curSketchName;

$_SESSION['a7_cur_sim_len'] = $par['a7_cur_sim_len'];
$_SESSION['a7_cur_sketch']  = $par['a7_cur_sketch'];
$_SESSION['a7_cur_step']    = $par['a7_cur_step'];
$_SESSION['a7_cur_loop']    = $par['a7_cur_loop'];
$_SESSION['a7_cur_log']     = $par['a7_cur_log'];
$_SESSION['a7_cur_menu']    = $par['a7_cur_menu'];
$_SESSION['a7_cur_file']    = $par['a7_cur_file'];
$_SESSION['a7_cur_sketch_name'] = $par['a7_cur_sketch_name'];


//====================================================
//  Internal functions
//====================================================

//====================================================================
// Calulate positions in image
//====================================================================

function canvasPos()
{
  global $par,$pinModeD,$pinStatusD,$pinStatusA;
  global $digX,$digY,$anaX,$anaY,$resetX,$resetY;
  global $TXledX,$TXledY,$onOffX,$onOffY,$led13X,$led13Y;
  global $sketchNameX,$sketchNameY;
  $user       = $par['user'];

  //$input  = array_keys($_GET);
  //$coords = explode(',', $input[0]);
  //$bb = $coords[0]; $aa=$coords[1];

  // Digital Pin Positions
  $yy = 220;
  for($ii=0; $ii<14; $ii++)
    {
      $xx = 17; $yy = $yy+17;
      if($ii == 6) $yy = $yy+10;
      $digX[13-$ii] = $xx;
      $digY[13-$ii] = $yy;
      $pinModeD[$ii] = 0;
      $pinStatusD[$ii] = 0;
    }

  // Analog Pin Positions
  $yy = 363;
  for($ii=0; $ii<6; $ii++)
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

  $resetY = 410;
  $resetX = 149;

  $uploadY = 434;
  $uploadX = 212;

  $TXledY = 230;
  $TXledX = 103;

  $RXledY = 230;
  $RXledX = 106;

  $led13Y = 230;
  $led13X =  72;

  $onOffY = 427;
  $onOffX = 91;

  $sketchNameY = 280;
  $sketchNameX = 220;

  $configY = 360;
  $configX = 78;

  $sens = 5;
}


//==========================================
function copySketch($sketch)
//==========================================
{
  global $par;
  $user = $par['user'];
  global $upload;

  $sketch = $upload.$sketch;
  if (!copy($sketch,"servuino/sketch.pde")) {
    echo "failed to copy ($sketch)...<br>";
  }
}

//==========================================
function compileSketch()
//==========================================
{
  global $par;
  $user = $par['user'];
  system("cd servuino;g++ -o servuino servuino.c > g++.error 2>&1;");
}

//==========================================
function execSketch($steps,$source)
//==========================================
{
  global $par;
  $user = $par['user'];
  system("cd servuino;./servuino $steps $source >exec.error 2>&1;");
}

//==========================================
function decodeStatus($code)
//==========================================
{
  global $par;
  $user = $par['user'];
  global $pinValueA,$pinValueD,$pinStatusA,$pinStatusD,$pinModeD;

  $curStep = $par['a7_cur_step'];

  $xpar = array();
  $tok = strtok($code, ",");
  $xpar[0] = $tok;
  if($tok != $curStep)
    {
      echo("Sync Error Step: $step - $currentStep<br>");
      return;
    }
  $ix = 0;
  while ($tok !== false) {
    $ix++;
    //echo "Word=$tok<br />";
    $tok = strtok(",");
    $par[$ix] = $tok;
  }

  // Mode Digital Pin
  $temp = $par[1];
  $bb = strlen($temp);
  for($ii=0;$ii<strlen($temp);$ii++)
    {
      if($temp[$ii]=='-')$pinModeD[$ii] = BLACK;
      if($temp[$ii]=='O')$pinModeD[$ii] = YELLOW;
      if($temp[$ii]=='I')$pinModeD[$ii] = WHITE;
      if($temp[$ii]=='X')$pinModeD[$ii] = RED;
      if($temp[$ii]=='Y')$pinModeD[$ii] = GREEN;
      if($temp[$ii]=='C')$pinModeD[$ii] = BLUE;
      if($temp[$ii]=='R')$pinModeD[$ii] = FUCHSIA;
      if($temp[$ii]=='F')$pinModeD[$ii] = AQUA;
    }

  // Status Digital Pin
  $temp = $xpar[2];

  $ii = 0;
  $values = array(1,2,4,8,16,32,64,128,256,512,1024,2048,4096,8192);
  foreach ($values as $value) 
    {
      $result = $value & $temp;
      //print("$result, $value, '&', $temp<br>");
      if($result != 0) 
	$pinStatusD[$ii] = YELLOW;
      else
	$pinStatusD[$ii] = BLACK;
      $ii++;	   
    }

  // Status Analog Pin
  $tempA = $xpar[3]; // Number of Analog Values
  if($tempA > 0)
    {
      for($ii=0;$ii<$tempA;$ii++)
	{
	  $ix = 5+$ii*2;
	  $pinValueA[$xpar[$ix]] = $xpar[$ix+1];
	  $aw = $xpar[$ix]; 
          $qq = $xpar[$ix+1];
	  if($pinValueA[$xpar[$ix]]> 0)$pinStatusA[$xpar[$ix]] = YELLOW;
	  //echo("$tempA Analog $ii $aw $qq<br>");
	}
    }
  $tempD = $xpar[4]; // Number of Digital Values
  if($tempD > 0)
    {
      for($ii=0;$ii<$tempD;$ii++)
	{
	  $ix = 5+$ii*2+2*$tempA;
	  $pinValuesD[$xpar[$ix]] = $xpar[$ix+1];
	  $aw = $xpar[$ix]; 
          $qq = $xpar[$ix+1];
	  if($pinValueA[$xpar[$ix]]> 0)$pinStatusD[$ix] = RED;
	  //echo("$tempD Digital $ii $aw $qq<br>");
	}
    }
}
//==========================================
function show($step)
//==========================================
{
  global $par;
  $user = $par['user'];
  global $simulation;

  echo("$simulation[$step]<br>");
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
function showSerial($target)
//==========================================
{
  global $par;
  $path   = $par['path'];
  $user       = $par['user'];
  global $curStep,$serial,$serialL;

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
  $path  = $par['path'];
  $user  = $par['user'];
  global $simulation;
  
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
  $user       = $par['user'];
  global $content;

  for($ii=1;$ii<=$target;$ii++)
    {
      echo("$content[$ii]<br>");
    }
}

//==========================================
function readSimulation($file)
//==========================================
{
  global $par;
  $user       = $par['user'];
  global $simulation,$servuino;

  $file = $servuino.$file;
  $step = 0;
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
              $row[0] = ' ';
	      $simulation[$step] = $row;
	      //echo("$row<br>");
	    }
	}
      $par['a7_cur_sim_len'] = $step;
      fclose($in);
    }
  else
    echo("Fail to open $file<br>");
  return($step);
}

//==========================================
function readSerial($file)
//==========================================
{
  global $par;
  $user  = $par['user'];
  global $serial,$servuino,$serialL;

  $file = $servuino.$file;
  $step = 0;
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
	  $value = safeText($value);
	  //echo("$step $line $value<br>");
	  $serial[$step] = $value;
	}
      fclose($in);
    }
  else
    echo("Fail to open $file<br>");
  return($step);
}

//==========================================
function readStatus()
//==========================================
{
  global $par;
  $user       = $par['user'];
  global $status,$servuino;

  $file = $servuino.'data.status';
  $step = 0;
  $in = fopen($file,"r");
  if($in)
    {
      $row = fgets($in);
      $row = fgets($in);
      while (!feof($in))
	{
	  $step++;
	  $row = fgets($in);
	  $row = trim($row);
	  //$row = safeText($row);
	  $status[$step] = $row;
	}
      fclose($in);
    }
  else
    echo("Fail to open data.status<br>");
  return($step);
}

//==========================================
function readAnyFile($serv,$file)
//==========================================
{
  global $par;
  $user       = $par['user'];
  global $content,$servuino;

  if($serv == 1)$file = $servuino.$file;
  $step = 0;
  $in = fopen($file,"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  $row = trim($row);
	  $row = safeText($row);
	  $step++;
	  $content[$step] = $row;
	}
      fclose($in);
    }
  else
    echo("Fail to open $file<br>");
  return($step);
}

//==========================================
function formSelectFile($name,$fname,$file,$sel)
//==========================================
{
  global $par;
  $user       = $par['user'];
  $in = fopen($file,"r");
  if($in)
    {
      echo("$name<select name=\"$fname\">");
      while (!feof($in))
	{
	  $row = fgets($in);
	  $row = trim($row);
	  //$row = safeText($row);
	  if($row)
	    {
	      $selected = "";if($sel == $row)$selected = 'selected';
	      echo("<option value=\"$row\" $selected>$row</option>");
	    }
	}
      echo("</select>");
      fclose($in);
    }
  else
    echo("Fail to open $file <br>");
}

//==========================================
function readSketchInfo()
//==========================================
{
  global $par;
  $user       = $par['user'];
  global $curSketchName;
  $in = fopen('servuino/sketch.pde',"r");
  if($in)
    {
      while (!feof($in))
	{
	  $row = fgets($in);
	  $row = trim($row);
	  if(strstr($row,"SKETCH_NAME"))
	    {
	      //echo("$row");
	      if($pp = strstr($row,":"))
		{
		  sscanf($pp,"%s%s",$junk,$name);
		  //echo("[$curSketchName]");
		}
	    }
	}
      echo("</select>");
      fclose($in);
      $par['a7_cur_sketch_name']  = $name;
    }
  else
    echo("Fail to open $file <br>");
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
function getExtension2($str)
//==========================================
{
  global $par;
  $user       = $par['user'];
  $i = strrpos($str,".");
  if (!$i) { return ""; }
  $l = strlen($str) - $i;
  $ext = substr($str,$i+1,$l);
  return $ext;
}

//==========================================
function showFile($title,$file)
//==========================================
{
  global $par;
  $user       = $par['user'];
  //echo("===== $title ======<br>");
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
    echo("Fail to open $file<br>");
  return;
}

//==========================================
function uploadFile2()
//==========================================
{
  global $par;
  $user       = $par['user'];
  define ("MAX_SIZE","300");
  $errors=0;
  $newname = '';

  if(isset($_POST['submit_file']))
    {

      $import=$_FILES['import_file']['name'];
      if ($import)
        {
          $file_name = stripslashes($_FILES['import_file']['name']);
          $file_name = safeText($file_name);
          $extension = getExtension($file_name);
          $extension = strtolower($extension);
          if (($extension != "txt") && ($extension != "pde") && ($extension != "c"))
            {
              echo "<h1>Unknown Import file Extension: $extension</h1>";
              $errors=1;
            }
          else
            {
              $size=filesize($_FILES['import_file']['tmp_name']);
              if ($size > MAX_SIZE*1024)
                {
                  echo "<h1>You have exceeded the size limit! $size</h1>";
                  $errors=1;
                }
              //$image_name=time().'.'.$extension;
              //$file_name = $db.'-'.$id.'.'.$extension;
              $newname="upload/".$file_name;
              $copied = move_uploaded_file($_FILES['import_file']['tmp_name'], $newname);
              if (!$copied)
                {
                  echo "<h1>Import Copy unsuccessfull! $size</h1>";
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

//====================================================
//  HTML functions
//====================================================
function viking_7_menu($sys_id)
{
  global $par;
  $path = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  $curStep = $par['a7_cur_step'];

  echo("         <a href=$path&ac=step&x=1>");
  echo("         <img border=\"0\" src=\"reset.gif\" alt=\"Reset\" width=\"50\" height=\"32\"></a>\n");
  $temp = $curStep - 1;
  echo("         <a href=$path&ac=step&x=$temp>");
  echo("         <img border=\"0\" src=\"backward.gif\" alt=\"Backward\" width=\"50\" height=\"32\"></a>\n");
  $temp = $curStep + 1;
  echo("         <a href=$path&ac=step&x=$temp>");
  echo("         <img border=\"0\" src=\"forward.gif\" alt=\"Forward\" width=\"50\" height=\"32\"></a>\n");
  echo("      <a href=$path&ac=menu&x=logA>\n");
  echo("         <img border=\"0\" src=\"logA.gif\" alt=\"LogA\" width=\"50\" height=\"32\"></a>\n");
  echo("      <a href=$path&ac=menu&x=logB>\n");
  echo("         <img border=\"0\" src=\"logB.gif\" alt=\"LogB\" width=\"50\" height=\"32\"></a>\n");
  echo("      <a href=$path&ac=menu&x=config>");
  echo("         <img border=\"0\" src=\"library.gif\" alt=\"Library\" width=\"50\" height=\"32\"></a>\n");
  echo("      <a href=$path&ac=menu&x=file>");
  echo("         <img border=\"0\" src=\"data.gif\" alt=\"Data\" width=\"50\" height=\"32\"></a>\n");
  echo("      <a href=$path&ac=menu&x=help>");
  echo("         <img border=\"0\" src=\"help.gif\" alt=\"Help\" width=\"50\" height=\"32\"></a>\n");
}

function viking_7_current($sys_id)
{
  global $par;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];

  $sketch = $par['a7_cur_sketch'];
  $step   = $par['a7_cur_step'];
  $length = $par['a7_cur_sim_len'];

  echo("Sketch: $sketch <br>Current Step: $step ($length)");
}

function viking_7_canvas($sys_id)
{
  global $par,$wBoard,$hBoard;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  echo("      <canvas id=\"boardUno\" width=\"$wBoard\" height=\"$hBoard\"></canvas>\n");
}

function viking_7_anyFile($sys_id)
{
  global $par;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  $curFile = $par['a7_cur_file'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  echo("<div id=\"anyFile\" style=\"float:left; border : solid 1px #000000; background : #A9BCF5; color : #000000; padding : 4px; width : 98%; height:514px; overflow : auto; \">\n");
  $len = readAnyFile(1,$curFile);
  showAnyFile($len);
  echo("</div>\n");
}


function viking_7_winSerial($sys_id)
{
  global $par;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  $curStep = $par['a7_cur_step'];
  echo("<div id=\"serWin\"t style=\"font-family: Courier,monospace;float:left; border : solid 2px #FF0000; background :#BDBDBD; color:#FF0000; padding : 4px; width : 97%; height:250px; overflow : auto; \">\n");
  showSerial($curStep);
  echo("</div>\n"); 
}

function viking_7_winLog($sys_id)
{
  global $par;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  $curStep = $par['a7_cur_step'];
  echo("<div id=\"simList\" style=\"float:left; border : solid 1px #000000; background : #FFFFFF; color : #000000; padding : 4px; width : 98%; height:250px; overflow : auto; \">\n");
  showStep($curStep);
  echo("</div>\n");
}

function viking_7_winSim($sys_id)
{
  global $par;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  $curStep = $par['a7_cur_step'];
  echo("<div id=\"serLis\"t style=\"float:right; border : solid 1px #000000; background : #FFFFFF; color : #000000; padding : 4px; width : 98%; height:250px; overflow : auto; \">\n");
  showSimulation($curStep);
  echo("</div>\n"); 
}

function viking_7_help($sys_id)
{
  global $par;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  $len = readAnyFile(0,'help.txt');
  showAnyFile($len);
}

function viking_7_error($sys_id)
{
  global $par;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  echo("[Webuino Version 2011-12-17] Any errors will be shown here<br>");
  $file = $servuino.'g++.error';
  showAnyFile($file);
  $file = $servuino.'exec.error';
  showAnyFile($file);
  $file = $servuino.'data.error';
  showAnyFile($file);
}


function viking_7_editFile($sys_id)
{
  global $par,$servuino;
  global $curEditFlag;
  if($curEditFlag == 1)
    {
      $curFile = $par['a7_cur_file'];
      $path   = $par['path'];
      $sid        = $par['a7_sid'];
      //if($sid != $sys_id) return;
      $user       = $par['user'];
      // open file
      $tempFile = $servuino.$curFile;
      $fh = fopen($tempFile, "r") or die("Could not open file ($tempFile)!");
      // read file contents
      $data = fread($fh, filesize($tempFile)) or die("Could not read file ($tempFile)!");
      // close file
      fclose($fh);
      echo("<form name=\"f_edit_file\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
      echo("<input type=\"hidden\" name=\"action\" value=\"edit_file\">\n");
      echo("<input type=\"hidden\" name=\"file_name\" value=\"$tempFile\">\n");
      echo("<table><tr><td>");
      if($curFile == 'sketch.pde')echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_LOAD."\">\n");
      if($curFile == 'data.scen')echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_RUN."\">\n");
      echo("</td></tr><tr><td><textarea name=\"file_data\" cols=64 rows=34>$data</textarea></td></tr></table>");  
      echo("</form><br>");
    }
}

function viking_7_data($sys_id)
{
  global $par;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  $curFile = $par['a7_cur_file'];

  echo("<table><tr><td>");
  echo("<form name=\"f_sel_win\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
  echo("<input type=\"hidden\" name=\"action\" value=\"select_file\">\n");
  echo("<select name=\"file\">");
  $selected = "";$temp = 'data.custom';if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Custom Log</option>");
  $selected = "";$temp = 'data.arduino';if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"  $selected>Arduino Log</option>");
  $selected = "";$temp = 'data.status';if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Status Log</option>");
  $selected = "";$temp = 'data.serial';if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Serial Log</option>");
  $selected = "";$temp = 'data.code';if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Code Log</option>");
  $selected = "";$temp = 'data.error';if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Error Log</option>");
  $selected = "";$temp = 'sketch.pde';if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Sketch</option>");
  $selected = "";$temp = 'data.scen';if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Scenario</option>");
  echo("</select>");
  echo("<input type =\"submit\" name=\"submit_select\" value=\"".T_SELECT."\">\n");
  if($curFile == 'sketch.pde')echo("<input type =\"submit\" name=\"submit_select\" value=\"".T_EDIT."\">\n");
  if($curFile == 'data.scen')echo("<input type =\"submit\" name=\"submit_select\" value=\"".T_EDIT."\">\n");
  echo("</form></td>");
  echo("</table>");
}

function viking_7_library($sys_id)
{
  global $par;
  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];
  $curSketch = $par['a7_cur_sketch'];
  $curSimLen = $par['a7_cur_sim_len'];

  echo("<hr><form name=\"upload_sketch\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
  echo("<input type=\"hidden\" name=\"action\" value=\"upload_sketch\">\n");
  echo("<input type=\"file\" name=\"import_file\" value=\"\">\n");
  echo("<input type =\"submit\" name=\"submit_file\" value=\"".T_UPLOAD_SKETCH."\">\n");
  echo("</form><br<br>");
  echo("<hr>");
  echo("<table border=\"0\"><tr><td>");
  echo("<form name=\"configuration\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
  echo("<input type=\"hidden\" name=\"action\" value=\"set_configuration\">\n");
  echo("Simulation Length <input type=\"text\" name=\"sim_len\" value=\"$curSimLen\" size=\"5\"></td>\n");
  system("ls upload > list.txt;");
  //system("pwd;ls upload;");
  echo("<td>");
  formSelectFile("Sketch Library","sketch","list.txt",$curSketch);
  echo("<input type =\"submit\" name=\"submit_file\" value=\"".T_LOAD."\"></td>\n");
  echo("</form></tr>");
  if($ready)echo("<tr><td>$ready</td></tr>");
  echo("</table><hr>");

  echo("Analog Pin Settings at step: $curStep<br>");
  echo("<table><tr>");
  echo("<form name=\"f_set_scenario\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
  echo("<input type=\"hidden\" name=\"action\" value=\"set_scenario\">\n");
  for($ii=0;$ii<6;$ii++)
    {
      echo("<td>Pin $ii<input type=\"text\" name=\"pin_$ii\" value=\"$pinValueA[$ii]\" size=\"3\"></td>");
    }
  echo("<td><input type =\"submit\" name=\"submit_scenario\" value=\"".T_LOAD."\"></td>\n");
  echo("</tr></form>");
  echo("</table><hr>");
}

function viking_7_script($sys_id)
{
  global $par;
  global $digX,$digY,$anaX,$anaY,$resetX,$resetY;
  global $TXledX,$TXledY,$onOffX,$onOffY,$led13X,$led13Y;
  global $sketchNameX,$sketchNameY;
  global $pinModeD,$pinStatusD,$pinStatusA;

  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];

  $curSketchName = $par['a7_cur_sketch_name'];
  $curStep = $par['a7_cur_step'];

  echo("<script type= \"text/javascript\">");

  echo("function draw(){");
  echo("var canvas = document.getElementById('boardUno');");
  echo("if (canvas.getContext){var ctx = canvas.getContext('2d');var imageObj = new Image();imageObj.src = \"arduino_uno.jpg\";ctx.drawImage(imageObj, 0, 0,500,300);");

  $black  = "ctx.fillStyle = \"#000000\";";
  $yellow = "ctx.fillStyle = \"#FFFF00\";";
  $white  = "ctx.fillStyle = \"#FFFFFF\";";
  $red    = "ctx.fillStyle = \"#FF0000\";";
  $green  = "ctx.fillStyle = \"#00FF00\";";
  $blue   = "ctx.fillStyle = \"#0000FF\";";
  $fuchsia= "ctx.fillStyle = \"#FF00FF\";";
  $aqua   = "ctx.fillStyle = \"#00FFFF\";";
      

  // On OFF led
  print("ctx.fillStyle = \"#FFFF00\";");
  print("ctx.beginPath();");
  print("ctx.rect($onOffY, $onOffX,8, 5);");
  //print("ctx.arc($onOffY, $onOffX, 4, 0, Math.PI*2, true);");
  print("ctx.closePath();");
  print("ctx.fill();");

  // TX led when Serial Output
  if(strlen($serial[$curStep]))
    {
      print("ctx.fillStyle = \"#FFFF00\";");
      print("ctx.beginPath();");
      print("ctx.rect($TXledY-4, $TXledX-12,8, 5);");
      //print("ctx.arc($onOffY, $onOffX, 4, 0, Math.PI*2, true);");
      print("ctx.closePath();");
      print("ctx.fill();");
    }

  // Digital Pins Mode
  for($ii=0; $ii<14; $ii++)
    {
      if($pinModeD[$ii]==0)print($black);
      if($pinModeD[$ii]==1)print($yellow);
      if($pinModeD[$ii]==2)print($white);
      if($pinModeD[$ii]==3)print($red);
      if($pinModeD[$ii]==4)print($green);
      if($pinModeD[$ii]==5)print($blue);
      if($pinModeD[$ii]==6)print($fuchsia);
      if($pinModeD[$ii]==7)print($aqua);
      print("ctx.beginPath();");
      //print("ctx.arc($digY[$ii], $digX[$ii], 5, 0, Math.PI*2, true);");
      print("ctx.rect($digY[$ii]-4, $digX[$ii]-12,8, 5);");
      print("ctx.closePath();");
      print("ctx.fill();");
    }

  // Digital Pins Status
  for($ii=0; $ii<14; $ii++)
    {
      if($pinStatusD[$ii]==0)print($black);
      if($pinStatusD[$ii]==1)print($yellow);
      if($pinStatusD[$ii]==2)print($white);
      if($pinStatusD[$ii]==3)print($red);
      if($pinStatusD[$ii]==4)print($green);
      if($pinStatusD[$ii]==5)print($blue);
      if($pinStatusD[$ii]==6)print($fuchsia);
      if($pinStatusD[$ii]==7)print($aqua);
      print("ctx.beginPath();");
      print("ctx.arc($digY[$ii], $digX[$ii], 5, 0, Math.PI*2, true);");
      if($ii == 13 && $pinStatusD[13]>0)
	print("ctx.rect($led13Y-4, $led13X-12,8, 5);");
      print("ctx.closePath();");
      print("ctx.fill();");
    }

  // Analog Pins Status
  for($ii=0; $ii<6; $ii++)
    {
      if($pinStatusA[$ii]==0)print($black);
      if($pinStatusA[$ii]==1)print($yellow);
      if($pinStatusA[$ii]==2)print($white);
      if($pinStatusA[$ii]==3)print($red);
      if($pinStatusA[$ii]==4)print($green);
      if($pinStatusA[$ii]==5)print($blue);
      if($pinStatusA[$ii]==6)print($fuchsia);
      if($pinStatusA[$ii]==7)print($aqua);
      print("ctx.beginPath();");
      print("ctx.arc($anaY[$ii], $anaX[$ii], 5, 0, Math.PI*2, true);");
      print("ctx.closePath();");
      print("ctx.fill();");
    }

  // Write Sketch Name on IC
  print("ctx.font = \"15pt Calibri\";");
  print("ctx.fillStyle = \"#FFFFFF\";");
  print("ctx.fillText(\"$curSketchName\",$sketchNameY,$sketchNameX);");
  
  echo(" }");
  echo(" }");
  
  echo("</script>");
}
?>