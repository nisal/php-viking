<?
//========================================
// PHP-VIKING APP 7: Arduino
//========================================
define('T_UPLOAD_SKETCH','Upload sketch to account');
define('T_DELETE','Delete');
define('T_CONFIG','Configuration');
define('T_SELECT','Select');
define('T_LOOP_F','Next Loop');
define('T_LOOP_B','Prev Loop');
define('T_STEP_F','Next Step');
define('T_STEP_B','Prev Step');
define('T_CREATE','Create');
define('T_EDIT','Edit');
define('T_SAVE','Save');
define('T_LOAD','Load');
define('T_RUN', 'Run');
define('T_SET', 'Set');
define('T_SET_DIG_PIN_VALUE', 'Set Digital Pin Value');
define('T_SET_ANA_PIN_VALUE', 'Set Analog Pin Value');
define('T_APPLY', 'Send Application');


// Analog Pins
define('READ',   '1');

// Digital Pins
define('LOW',    '1');
define('HIGH',   '2');
define('PWM',    '3');

// Digital Pins Mode
define('VOID',     '0');
define('I_LOW',    '5');
define('I_RISING', '6');
define('I_FALLING','7');
define('I_CHANGE', '8');
define('TX',       '9');
define('RX',       '10');
define('INPUT' ,   '11');
define('OUTPUT' ,  '12');

define('YES' , '1');
define('NO' ,  '2');



$UNO_DIG_PINS  = 14;
$UNO_ANA_PINS  =  6;
$MEGA_DIG_PINS = 54;
$MEGA_ANA_PINS = 16;

// Declarations =========================

$simulation = array();
$content    = array();
$status     = array();
$serial     = array();
$serialL    = array();
$scenario   = array();

$stepLoop   = array();
$loopStep   = array();
$readStep   = array();
$stepRead   = array();

$pinValueA  = array();
$pinValueD  = array();
$pinStatusA = array();
$pinStatusD = array();
$pinModeD   = array();

$user    = $par['user'];

// Set filenames
$fn = array(); 
$fn['application'] = 'application.txt';
$fn['start']       = 'start.htm';
$fn['about']       = 'about.htm';
$fn['register']    = 'register.htm';
$fn['help']        = 'help.htm';
$fn['new_sketch']  = 'new_sketch.pde';

$userEvent = $par['user_event'];

if($userEvent == 1) // user logged in!
  {
    $fn['setting'] = 'account/'.$user.'/setting.txt';
    //accessControl();
    resetSession();
    readUserSetting();
    $par['user_event'] = 0;
  }

if($userEvent == 2) // user logged out!
  {
    $fn['setting'] = 'account/'.$user.'/setting.txt';
    writeUserSetting();
    resetSession();
    $par['user_event'] = 0;
    $par['pv'] = 'start';
  }

$application = 0;

readLoginCounter();

$par['a7_cur_sim_len']       = $_SESSION['a7_cur_sim_len'];
init($par['a7_cur_sim_len']);
$par['a7_cur_loop_len']      = $_SESSION['a7_cur_loop_len'];
$par['a7_cur_read_len']      = $_SESSION['a7_cur_read_len'];
$par['a7_cur_source']        = $_SESSION['a7_cur_source'];
$par['a7_cur_step']          = $_SESSION['a7_cur_step'];
$par['a7_cur_loop']          = $_SESSION['a7_cur_loop'];
$par['a7_cur_read']          = $_SESSION['a7_cur_read'];
$par['a7_cur_menu']          = $_SESSION['a7_cur_menu'];
$par['a7_cur_file']          = $_SESSION['a7_cur_file'];
$par['a7_cur_sketch_name']   = $_SESSION['a7_cur_sketch_name'];
$par['a7_cur_board_type']    = $_SESSION['a7_cur_board_type'];
$par['a7_cur_board_digpins'] = $_SESSION['a7_cur_board_digpins'];
$par['a7_cur_board_anapins'] = $_SESSION['a7_cur_board_anapins'];
$par['a7_ser_log']           = $_SESSION['a7_ser_log'];


//=================================================
//+++++++++++++++++++++++++++++++++++++++++++++++++
//=================================================
if($user)
  {
    $account = $par['user'];

    $tDir = 'account/'.$account;

    $upload   = $tDir.'/upload/';


    $fn['serial']      = 'account/'.$account.'/data.serial';
    $fn['custom']      = 'account/'.$account.'/data.custom';
    $fn['arduino']     = 'account/'.$account.'/data.arduino';
    $fn['error']       = 'account/'.$account.'/data.error';
    $fn['exec']        = 'account/'.$account.'/exec.error';
    $fn['g++']         = 'account/'.$account.'/g++.error';
    $fn['status']      = 'account/'.$account.'/data.status';
    $fn['code']        = 'account/'.$account.'/data.code';
    $fn['sketch']      = 'account/'.$account.'/sketch.pde';
    $fn['scenario']    = 'account/'.$account.'/data.scen';
    $fn['scenexp']     = 'account/'.$account.'/data.scenario';
    $fn['list']        = 'account/'.$account.'/list.txt';
    $fn['setting']     = 'account/'.$account.'/setting.txt';

    accessControl(); // Benny. Keep this until debugged


    //if(!$account)$account = 'public';
    
    if($tDir && !is_dir($tDir))
      {
	vikingWarning("Create account");
	if(!mkdir($tDir,0777))vikingError("Not possible to create account");
	$tDir2 = $tDir.'/upload';
	if(!mkdir($tDir2,0777))vikingError("Not possible to create upload in account");
	
	$syscom = "cd $tDir;ln -s ../../servuino/servuino.c servuino.c;";
	system($syscom);
	$syscom = "cd $tDir;ln -s ../../servuino/servuino_lib.c servuino_lib.c;";
	system($syscom);
	$syscom = "cd $tDir;ln -s ../../servuino/servuino.h servuino.h;";
	system($syscom);
	$syscom = "cd $tDir;ln -s ../../servuino/arduino_lib.c arduino_lib.c;";
	system($syscom);
	$syscom = "cd $tDir;ln -s ../../servuino/common.h common.h;";
	system($syscom);
	$syscom = "cd $tDir;ln -s ../../servuino/common_lib.c common_lib.c;";
	system($syscom);
	$syscom = "cd $tDir;ln -s ../../servuino/code.h code.h;";
	system($syscom);
	
	$syscom = "cd $tDir;touch g++.error exec.error setting.txt;";
	system($syscom);
	$syscom = "cd $tDir;touch data.serial data.custom data.arduino data.error data.status data.code data.scen sketch.pde;";
	system($syscom);
	resetSession();
      }
    
    
    $hBoard = 300; // 300 240
    $wBoard = 500; // 500 400

    readSketchInfo();
    canvasPos();

    $tFile = $fn['custom'];
    readSimulation($tFile);

    $curSimLen  = $par['a7_cur_sim_len'] ;
    $curLoopLen = $par['a7_cur_loop_len'] ;
    $curReadLen = $par['a7_cur_read_len'] ;
    $curSource  = $par['a7_cur_source'] ;
    $curLoop    = $par['a7_cur_loop'] ;
    $curRead    = $par['a7_cur_read'] ;
    $curStep    = $par['a7_cur_step'];

    // GET ==============================================

    //$input  = array_keys($_GET); 
    //$coords = explode(',', $input[0]); 
    //print("X coordinate : ".$coords[0]."<br> Y Coordinate : ".$coords[1]); 

    $action  = $_GET['ac'];
    $alt     = $_GET['x'];

    if($action == 'access_control')
      {
	accessControl();
      }

    if($action == 'run' && $curSimLen > 0)
      {
	execSketch($curSimLen,0);
      }

    if($action == 'step')
      {
	$par['a7_cur_step'] = $alt;
	$curStep = $alt;
	$par['a7_cur_loop'] = $stepLoop[$curStep];
	$par['a7_cur_read'] = $stepRead[$curStep];
      }

    if($action == 'loop')
      {
	$par['a7_cur_loop'] = $alt;
	$loop = $alt;
	$par['a7_cur_step'] = $loopStep[$loop];
	$curStep = $par['a7_cur_step'];
	$par['a7_cur_read'] = $stepRead[$curStep];
      }

    if($action == 'read')
      {
	$par['a7_cur_read'] = $alt;
	$read = $alt;
	$par['a7_cur_step'] = $readStep[$read];
	$curStep = $par['a7_cur_step'];
	$par['a7_cur_loop'] = $stepLoop[$curStep];
      }

    if($action == 'edit_file')
      {
	$curEditFlag = 1;
      }

    if($action == 'winserlog')
      {
	$par['a7_ser_log'] = $alt;
      }

    if($action == 'reset')
      {
	$par['a7_cur_step'] = 1;
      }

    // POST =============================================

    if (!isset($_POST['action']))$_POST['action'] = "undefined"; 

    $action = $_POST['action'];

    //echo("$action");
    
    if($action == 'select_file' )
      {
	$par['a7_cur_file'] = $_POST['file'];
	$curFile = $par['a7_cur_file'];
	if($user == 'admin')
	  {
	    if($curFile == 'start.htm' || $curFile == 'help.htm' || $curFile == 'about.htm' || $curFile == 'register.htm')$par['tinyMCE'] = 1;
	    else
	      $par['tinyMCE'] = 0;
	  }
	$what = $_POST['submit_select'];
	if($what == T_EDIT) $curEditFlag = 1;
      }

    if($action == 'edit_file')
      {
	$tempFile = $_POST['file_name'];
	$data = $_POST['file_data'];
	$what = $_POST['submit_edit'];
	$curSimLen = $par['a7_cur_sim_len'];
	if(!$tempFile)return;


	// Always save file
	$res = evilCode($data);
	if($res == 0)
	  {
	    $fp = fopen($tempFile, 'w')or die("Could not open file ($tempFile) (write)!");;
	    fwrite($fp,$data) or die("Could not write to file ($tempFile) !");
	    fclose($fp);
	  }
	else
	  $par['a7_ready'] = "File did not pass check for evil code!";
	
	if($what == T_SAVE) 
	  {
	    $par['a7_sel_source'] = $tempFile;
	    $curEditFlag = 1;
	  }

	if($res == 0)
	  {
	    if($what == T_LOAD)
	      {
		compileSketch();
		execSketch($curSimLen,1);
		$par['a7_cur_step'] = 0;
		init($curSimLen);
		readSketchInfo();
		$tFile = $fn['custom'];
		readSimulation($tFile);
		readStatus();
	    //readSerial();
		$par['a7_ready'] = "Sketch loaded!";
	      }
	    if($what == T_RUN)
	      {
		execSketch($curSimLen,1);
		$par['a7_cur_step'] = 0;
		init($curSimLen);
		readSketchInfo();
		$tFile = $fn['custom'];
		readSimulation($tFile);
		readStatus();
		//readSerial();
		$par['a7_ready'] = "Sketch Executed!";
	      }
	  }
	
      }
    if($action == 'upload_source' )
      {
	$par['a7_cur_source'] = uploadFile2();
	$curSource = $par['a7_cur_source'];
      }

    if($action == 'set_load_new_sketch' )
      {
	$temp = $fn['new_sketch'];
	$newSketchName = $_POST['new_sketch_name'];
	$dest = $upload.$newSketchName;
	if (!copy($temp,$dest)) {
	  vikingError("Failed to copy ($sketch)");
	}
	else
	  {
	    $par['a7_cur_source'] = $fn['new_sketch'];
	    $curSource = $par['a7_cur_source'];
	  }
      }


    if($action == 'set_load_delete' )
      {
	$curSource = $_POST['source'];
	$par['a7_sel_source'] =  $curSource;
	$what = $_POST['submit_load_del'];
	if($what == T_EDIT) $curEditFlag = 1;
	if($what == T_LOAD && $curSource)
	  {
	    $curSimLen = $_POST['sim_len'];
	    if($curSimLen > 2000)$curSimLen = 2000;
	    $par['a7_cur_sim_len'] =  $curSimLen;
	    $par['a7_cur_source']  =  $curSource;
	    $res = copySketch($curSource);
	    if($res == 0)
	      {
		compileSketch();
		execSketch($curSimLen,0);
		$par['a7_cur_step'] = 0;
		$par['a7_cur_loop'] = 0;
		init($curSimLen);
		readSketchInfo();
		$tFile = $fn['custom'];
		readSimulation($tFile);
		readStatus();
		//readSerial();
		writeUserSetting();
		$par['a7_ready'] = "Sketch loaded!";
	      }
	    else
	      $par['a7_ready'] = "Sketch not loaded!";
	  }
	else
	  vikingWarning("No sketch selected !");
	if($what == T_DELETE)
	  {
	    unlink($curSource);
	  }

      }

    if($action == 'run_target' )
      {
        $targetStep = $_POST['target_step'];
        runTarget($targetStep);
      }

    if($action == 'apply_account' )
      {
        $username = $_POST['username'];
        $email    = $_POST['email'];
        createApplication($username,$email);
      }

    if($action == 'set_dig_scenario' )
      {
	$pin   = $_POST['pin_value'];
	if(!$pin)$pin = "0";
	$value = $_POST['dvalue'];
	if(!$value)$value = "0";
        if($pin != "-")
	  {
	    $pinType = 2;//DIG
	    $do = 10; // ADD=10, DELETE = 20
	    $syscom = "cd account/$user;./servuino $curSimLen 1 $pinType $pin $value $curStep $do >exec.error 2>&1;chmod 777 data.*;";
	    echo("$syscom<br>");
	    system($syscom);
	    init($curSimLen);
	    readSketchInfo();
	    $tFile = $fn['custom'];
	    readSimulation($tFile);
	    readStatus();
	  }
      }

    if($action == 'set_ana_scenario' )
      {
	$pin   = $_POST['pin_value'];
	if(!$pin)$pin = "0";
	$value = $_POST['dvalue'];
	if(!$value)$value = "0";
        if($pin != "-")
	  {
	    $pinType = 1;//DIG=2 ANA=1
	    $do = 10;    // ADD=10, DELETE=20
	    $syscom = "cd account/$user;./servuino $curSimLen 1 $pinType $pin $value $curStep $do >exec.error 2>&1;chmod 777 data.*;";
	    echo("$syscom<br>");
	    system($syscom);
	    init($curSimLen);
	    readSketchInfo();
	    $tFile = $fn['custom'];
	    readSimulation($tFile);
	    readStatus();
	  }
      }


    $curStep = $par['a7_cur_step'];
    readStatus();
    decodeStatus($status[$curStep]);


  } // end if user

//=================================================
//+++++++++++++++++++++++++++++++++++++++++++++++++
//=================================================

// set SESSION parameters ===============================

$_SESSION['a7_cur_sim_len']        = $par['a7_cur_sim_len'];
$_SESSION['a7_cur_loop_len']       = $par['a7_cur_loop_len'];
$_SESSION['a7_cur_read_len']       = $par['a7_cur_read_len'];
$_SESSION['a7_cur_source']         = $par['a7_cur_source'];
$_SESSION['a7_cur_step']           = $par['a7_cur_step'];
$_SESSION['a7_cur_loop']           = $par['a7_cur_loop'];
$_SESSION['a7_cur_read']           = $par['a7_cur_read'];
$_SESSION['a7_cur_menu']           = $par['a7_cur_menu'];
$_SESSION['a7_cur_file']           = $par['a7_cur_file'];
$_SESSION['a7_cur_sketch_name']    = $par['a7_cur_sketch_name'];
$_SESSION['a7_cur_board_type']     = $par['a7_cur_board_type'];
$_SESSION['a7_cur_board_digpins']  = $par['a7_cur_board_digpins'];
$_SESSION['a7_cur_board_anapins']  = $par['a7_cur_board_anapins'];
$_SESSION['a7_ser_log']            = $par['a7_ser_log'];


//====================================================
//  Internal functions
//====================================================

function resetSession()
{

  $_SESSION['a7_cur_sim_len']       = ""; 
  $_SESSION['a7_cur_loop_len']      = "";
  $_SESSION['a7_cur_sketch']        = ""; 
  $_SESSION['a7_cur_source']        = ""; 
  $_SESSION['a7_cur_step']          = 0; 
  $_SESSION['a7_cur_loop']          = 0; 
  $_SESSION['a7_cur_read']          = 0; 
  $_SESSION['a7_cur_menu']          = "start"; 
  $_SESSION['a7_cur_file']          = ""; 
  $_SESSION['a7_cur_sketch_name']   = "";
  $_SESSION['a7_cur_board_type']    = "";
  $_SESSION['a7_cur_board_digpins'] = 0;
  $_SESSION['a7_cur_board_anapins'] = 0;
  $_SESSION['a7_ser_log']           = "";
}
//==========================================
function accessControlFile($file,$rw)
//==========================================
{
  $out = fopen($file,"$rw");
  if($out)
    return(OK);
  else
    return(NOK);
}
//==========================================
function accessControl()
//==========================================
{
  global $par,$fn;
  
  $file = $fn['serial'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['custom'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['arduino'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['error'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['exec'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['g++'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['status'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['code'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['application'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['sketch'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['scenario'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['scenexp'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['list'];
  $res = accessControlFile($file,"w");
  if($res == NOK)vikingError("Write Access: $file"); 

  $file = $fn['setting'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file");

  $file = $fn['start'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['about'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 

  $file = $fn['register'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file");  

  $file = $fn['help'];
  $res = accessControlFile($file,"r");
  if($res == NOK)vikingError("Read Access: $file"); 
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
      $temp = "SOURCE: ".$par['a7_cur_source']."\n";
      fwrite($out,$temp);
      $temp = "FILE: ".$par['a7_cur_file']."\n";
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
	  if(strstr($row,"SOURCE"))
	    {
	      if($pp = strstr($row,":"))
		{
		  sscanf($pp,"%s%s",$junk,$curSource);
		  $par['a7_cur_source'] = $curSource;
		  $_SESSION['a7_cur_source'] = $curSource;
		}
	    }
	  if(strstr($row,"FILE"))
	    {
	      if($pp = strstr($row,":"))
		{
		  sscanf($pp,"%s%s",$junk,$curFile);
		  $par['a7_cur_file'] = $curFile;
		  $_SESSION['a7_cur_file'] = $curFile;
		}
	    }
	}
    }
  else
    vikingError("Not able to open user setting file read ($file)");  
  
  fclose($in);
}

//==========================================
function createApplication($username,$email)
//==========================================
{
  global $application,$fn;

  if($email && $username)
    {
      $file = $fn['application'];
      $fileSize = filesize($file);
      if($fileSize < 900000)
	{
	  $out = fopen($file,"a");
	  if($out)
	    {
	      $temp = $fileSize." ".$username."   ".$email."\n";
	      //echo("$temp<br>");
	      fwrite($out,$temp);
	      $application = 1;
	    }
	  fclose($out);
	}
      else
	vikingError("Application rejected due to overload");  
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
  global $sketchNameX,$sketchNameY;
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
function decodeStatus($code)
//==========================================
{
  global $par;
  $user = $par['user'];
  global $pinValueA,$pinValueD,$pinStatusA,$pinStatusD,$pinModeD;


  $curStep = $par['a7_cur_step'];

  if(!$code)return;

  $xpar = array();
  $tok = strtok($code, ",");
  $xpar[0] = $tok;
  if($tok != $curStep)
    {
      vikingError("Sync Error Step: $step - $curStep");
      return;
    }
  $ix = 0;
  while ($tok !== false) {
    $ix++;
    //echo "Word=$tok<br />";
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
	  if($pinValueD[$xpar[$ix]] == 0)$pinStatusD[$aw] = LOW;
	  if($pinValueD[$xpar[$ix]] == 1)$pinStatusD[$aw] = HIGH;
	  if($pinValueD[$xpar[$ix]]  > 1)$pinStatusD[$aw] = PWM;
	  //echo("$tempD Digital $ii $aw $qq<br>");
	}
    }
}
// //==========================================
// function show($step)
// //==========================================
// {
//   global $par,$simulation;
//   $user = $par['user'];
//   echo("$simulation[$step]<br>");
// }

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

  $user = $par['user'];
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
  $user  = $par['user'];
  global $simulation;
  //$servuino,$loopStep,$stepLoop,$readStep,$stepRead;

  //$file = $servuino.$file;
  $step = 0;
  $loop = 0;
  $read = 0;
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
	      $step++;
              $row[0] = ' ';
	      $simulation[$step] = $row;
	    }
	}
      fclose($in);
    }
  else
    {
      $temp = "readSimulation: Fail to open ($file)";
      vikingError($temp);
    }

  readArduino();

  return($step);
}

//==========================================
function readArduino()
//==========================================
{
  global $par,$fn;
  $user  = $par['user'];
  //global $simulation,$servuino,
  global $loopStep,$stepLoop,$readStep,$stepRead;


  $step = 0;
  $loop = 0;
  $read = 0;
  $file = $fn['arduino'];
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
		}
              $stepRead[$step] = $read;
              if(strstr($row,"Loop "))
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
function readStatus()
//==========================================
{
  global $par,$fn;
  $user       = $par['user'];
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
	  $step++;
	  $row = fgets($in);
	  $row = trim($row);
	  //$row = safeText($row);
	  $status[$step] = $row;
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
  global $par,$fn;
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
	}
      $row  = fgets($in);
      if(strstr($row,"Analog:"))
	{
	  $temp = $temp.$row;
	}
      $scenario[0] = $temp;

      while (!feof($in))
	{
	  $row = fgets($in);
	  if($row[0] != '#')
	    {
	      $step++;
	      $scenario[$step] = $row;
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
function readSketchInfo()
//==========================================
{
  global $par,$fn;
  global $UNO_DIG_PINS,$UNO_ANA_PINS,$MEGA_DIG_PINS,$MEGA_ANA_PINS;
  //global $curSketchName;

  $name    = 'unknown';
  $boardId = 'unknown';

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

      if($sketch_name_ok  == NO){vikingWarning("No name in sketch")      ;$res = 1;}
      if($board_type_ok   == NO){vikingWarning("No board type in sketch");$res = 1;}
      if($no_system_calls == NO){vikingWarning("No system calls allowed");$res = 1;}
      if($no_script       == NO){vikingWarning("No script allowed")      ;$res = 1;}
      if($no_php          == NO){vikingWarning("No php allowed")         ;$res = 1;}
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
   
//====================================================
//  HTML functions
//====================================================


function viking_7_mainmenu($sys_id)
{
  global $par;
  $path = $par['path'];
  $user = $par['user'];

  echo("<ul>");
  echo("<li><a href=\"index.php?pv=start\" >Start</a></li>");
  //echo("<li><a href=\"index.php?pv=lib\"   >Library</a></li>");
  if($user)
    {
      echo("<li><a href=\"index.php?pv=load\"  >Load</a></li>");
      echo("<li><a href=\"index.php?pv=board\" >Board</a></li>");
      echo("<li><a href=\"index.php?pv=sketch\">Sketch</a></li>");
      echo("<li><a href=\"index.php?pv=log\">Log</a></li>");
    }
  if($user == 'admin')echo("<li><a href=\"index.php?pv=admin\" >Admin</a></li>");
  echo("<li><a href=\"index.php?pv=help\"  >Help</a></li>");
  echo("<li><a href=\"index.php?pv=about\" >About</a></li>");
  if(!$user)
    echo("<li><a href=\"index.php?pv=register\">Register</a></li>");
  echo("</ul>");
}

function viking_7_menu($sys_id)
{
  global $par;
  $path       = $par['path'];
  $sid        = $par['a7_sid'];
  $user       = $par['user'];
  $curStep    = $par['a7_cur_step'];
  $curLoop    = $par['a7_cur_loop'];
  $curRead    = $par['a7_cur_read'];
  $curSimLen  = $par['a7_cur_sim_len'];
  $curLoopLen = $par['a7_cur_loop_len'];
  $curReadLen = $par['a7_cur_read_len'];


  echo("<ul>");

  $temp = $curStep + 1;
  if($temp > $curSimLen)$temp = $curSimLen;
  echo("         <li><a href=$path&ac=step&x=$temp>step+</a></li>");
  $temp = $curStep - 1;
  if($temp < 1)$temp = 1;
  echo("         <li><a href=$path&ac=step&x=$temp>step-</a></li>");

  $temp = $curLoop + 1;
  if($temp > $curLoopLen)$temp = $curLoopLen;
  echo("         <li><a href=$path&ac=loop&x=$temp>loop+</a></li>");
  $temp = $curLoop - 1;
  if($temp < 1)$temp = 1;
  echo("         <li><a href=$path&ac=loop&x=$temp>loop-</a></li>");

  $temp = $curRead + 1;
  if($temp > $curReadLen)$temp = $curReadLen;
  echo("         <li><a href=$path&ac=read&x=$temp>read+</a></li></ul>");
  $temp = $curRead - 1;
  if($temp < 1)$temp = 0;
  echo("         <li><a href=$path&ac=read&x=$temp>read-</a></li>");

  echo("         <ul><li><a href=$path&ac=step&x=1>first</a></li>");
  $temp = $curSimLen;
  echo("         <li><a href=$path&ac=step&x=$temp>last</a></li>");

  echo("</ul>");
}

function viking_7_current($sys_id)
{
  global $par;

  $user = $par['user'];

  if(!$user)return;
  $path   = $par['path'];
  $sid    = $par['a7_sid'];
  $user   = $par['user'];

  $sname = $par['a7_cur_sketch_name'];
  $step   = $par['a7_cur_step'];
  $loop   = $par['a7_cur_loop'];
  $stepLength = $par['a7_cur_sim_len'];
  $loopLength = $par['a7_cur_loop_len'];

  echo("[$sname] loop=$loop($loopLength) step=$step($stepLength)");
}

function viking_7_canvas($sys_id)
{
  global $par,$wBoard,$hBoard;
  
  $board = $par['a7_cur_board_type'];

  $path = $par['path'];
  $sid  = $par['a7_sid'];
  $user = $par['user'];
  echo("<canvas id=\"$board\" width=\"$wBoard\" height=\"$hBoard\"></canvas>\n");
}

function viking_7_anyFile($sys_id)
{
  global $par,$servuino,$fn,$upload;
  global $curEditFlag;

  $user      = $par['user'];
  $path      = $par['path'];
  $curFile   = $par['a7_cur_file'];
  $selSource = $par['a7_sel_source'];
  $ready     = $par['a7_ready'];

  if($par['pv'] == 'load')$file = $selSource;
  else
    $file = $curFile;
  
  if($curEditFlag == 0 && $file)
    {
      echo("<div id=\"anyFile\" style=\"float:left; border : solid 1px #000000; background : #A9BCF5; color : #000000;  text-align:left; padding : 3px; width :100%; height:500px; overflow : auto; margin-left:0px; margin-bottom:10px;line-height:1.0em; \">\n");
      $len = readAnyFile(1,$file);
      showAnyFile($len);
      echo("</div>\n");
    }
  else if($curEditFlag == 1 && $user)
    {
      if(!$file)return;
      $fh = fopen($file, "r") or die("Could not open file ($file)!");
      $data = fread($fh, filesize($file)) or die("Could not read file ($file)!");
      fclose($fh);

      echo("<form name=\"f_edit_file\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
      echo("<input type=\"hidden\" name=\"action\" value=\"edit_file\">\n");
      echo("<input type=\"hidden\" name=\"file_name\" value=\"$file\">\n");
      echo("<table><tr><td>");
      if($par['pv'] == 'load')echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_SAVE."\">\n");
      if($file == $fn['sketch'])echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_LOAD."\">\n");
      if($file == $fn['scenario'])echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_RUN."\">\n");
      if($user == 'admin')
	{
	  if($file == $fn['start'] || $file == $fn['help'] || $file == $fn['about'] || $file == $fn['register'] )echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_SAVE."\">\n");
	}
      echo("</td></tr><tr><td><textarea name=\"file_data\" cols=50 rows=35>$data</textarea></td></tr></table>");  
      echo("</form><br>");
    }
  echo("$ready");
}


function viking_7_winSerLog($sys_id)
{
  global $par;
  $path    = $par['path'];
  $user    = $par['user'];
  $curStep = $par['a7_cur_step'];

  $sl = $par['a7_ser_log'];

  if($sl == 'ser')
    {
      echo(" (<a href=$path&ac=winserlog&x=log>Log Events</a>)");
      echo(" (<a href=$path&ac=winserlog&x=sce>Scenario</a>)");
      echo(" Serial Interface ");
      echo("<div id=\"serWin\" style=\"font-family:Courier,monospace; font-size:12px;float:left; border:solid 0px #FF0000; background:#BDBDBD; color:#000000; text-align:left; padding:4px; width:100%; height:600px; overflow:auto; \">");
      readSerial();
      showSerial($curStep);
      echo("</div>\n"); 
    }
  else if($sl == 'sce')
    {
      echo(" (<a href=$path&ac=winserlog&x=log>Log Events</a>)");
      echo(" Scenario ");
      echo(" (<a href=$path&ac=winserlog&x=ser>Serial Interface</a>)");
      echo("<div id=\"serWin\" style=\"font-family:Courier,monospace; font-size:12px;float:left; border:solid 0px #FF0000; background:#BDBDBD; color:#000000; text-align:left; padding:4px; width:100%; height:600px; overflow:auto; \">");
      readScenario();
      showScenario($curStep);
      echo("</div>\n"); 
    }
  else 
    {
      echo(" Log events ");
      echo(" (<a href=$path&ac=winserlog&x=sce>Scenario</a>)");
      echo(" (<a href=$path&ac=winserlog&x=ser>Serial Interface</a>)");
      echo("<div id=\"logList\" style=\"margin-right:1px;float:left; border:solid 0px #000000; background:#AFCAE6; color:#000000; text-align:left; padding:4px; width:100%; height:600px; overflow:auto; \">");
      showStep($curStep);
      echo("</div>");
    }
}

function viking_7_winSerial($sys_id)
{
  global $par;
  $path    = $par['path'];
  $user    = $par['user'];
  $curStep = $par['a7_cur_step'];

  echo("<div id=\"serWin\" style=\"font-family:Courier,monospace; font-size:12px;float:left; border:solid 0px #FF0000; background:#BDBDBD; color:#000000; text-align:left; padding:4px; width:100%; height:600px; overflow:auto; \">");
  readSerial();
  showSerial($curStep);
  echo("</div>\n"); 
}

function viking_7_winLog($sys_id)
{
  global $par;
  $path    = $par['path'];
  $user    = $par['user'];
  $curStep = $par['a7_cur_step'];

  echo("<div id=\"logList\" style=\"margin-right:1px;float:left; border:solid 0px #000000; background:#AFCAE6; color:#000000; text-align:left; padding:4px; width:100%; height:600px; overflow:auto; \">");
  showStep($curStep);
  echo("</div>");
}

function viking_7_winSim($sys_id)
{
  global $par;
  $path    = $par['path'];
  $user    = $par['user'];
  $curStep = $par['a7_cur_step'];
  echo("<div id=\"simLis\"t style=\"float:right; border : solid 1px #000000; background : #FFFFFF; color : #000000; text-align:left; padding : 4px; width : 98%; height:250px; overflow : auto; \">\n");
  showSimulation($curStep);
  echo("</div>"); 
}

function viking_7_help($sys_id)
{
  global $par,$fn;
  $tFile = $fn['help'];
  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}

function viking_7_about($sys_id)
{
  global $par,$fn;
  $tFile = $fn['about'];
  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}

function viking_7_register($sys_id)
{
  global $par,$fn;
  $tFile = $fn['register'];
  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}

function viking_7_start($sys_id)
{
  global $par,$fn;
  $tFile = $fn['start'];
  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}

function viking_7_loginCounter($sys_id)
{
  global $par;
  $temp = $par['login_counter'];
  echo("[$temp]");
}

function viking_7_accessControl($sys_id)
{
  global $par;
  $path    = $par['path'];
  $user    = $par['user'];

  if($user == 'admin')
    echo(" <a href=$path&ac=access_control>Access Control</a>");
}

function viking_7_error($sys_id)
{
  global $par,$fn;
  $path   = $par['path'];
  $user   = $par['user'];

  //if($user)echo("[Webuino Version 2011-12-22] Any errors (compile,exec and servuino) will be shown here<br>");
  if($user)
    {
      $file = $fn['g++'];
      $len = readAnyFile(2,$file);
      showAnyFile($len);
      $file = $fn['exec'];
      $len = readAnyFile(2,$file);
      showAnyFile($len);
      $file = $fn['error'];
      $len = readAnyFile(2,$file);
      showAnyFile($len);
    }
}


function viking_7_data($sys_id)
{
  global $par,$fn;
  $path    = $par['path'];
  $sid     = $par['a7_sid'];
  $user    = $par['user'];
  $curFile = $par['a7_cur_file'];

  echo("<div><table><tr><td>");
  echo("<form name=\"f_sel_win\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
  echo("<input type=\"hidden\" name=\"action\" value=\"select_file\">\n");
  echo("<select name=\"file\">");
  if($user == 'admin')
    {
      $selected = "";$temp = $fn['application'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Application</option>");

      $selected = "";$temp = $fn['start'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Start</option>");

      $selected = "";$temp = $fn['help'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Help</option>");

      $selected = "";$temp = $fn['about'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>About</option>");

      $selected = "";$temp = $fn['register'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Register</option>");

      $selected = "";$temp = $fn['custom'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Custom Log</option>");

      $selected = "";$temp = $fn['arduino'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"  $selected>Arduino Log</option>");

      $selected = "";$temp = $fn['status'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Status Log</option>");

      $selected = "";$temp = $fn['serial'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Serial Log</option>");

      $selected = "";$temp = $fn['code'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Code Log</option>");
    }
  $selected = "";$temp = $fn['error'];if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Error Log</option>");

  $selected = "";$temp = $fn['sketch'];if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Loaded Sketch</option>");

  $selected = "";$temp = $fn['scenario'];if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Scenario Breakpoints</option>");

  $selected = "";$temp = $fn['scenexp'];if($curFile == $temp)$selected = 'selected';
  echo("<option value=\"$temp\"   $selected>Scenario Steps</option>");

  echo("</select>");
  echo("<input type =\"submit\" name=\"submit_select\" value=\"".T_SELECT."\">\n");
  if($user)
    {
      if($curFile == $fn['sketch'])echo("<input type =\"submit\" name=\"submit_select\" value=\"".T_EDIT."\">\n");
      if($curFile == $fn['scenario'])echo("<input type =\"submit\" name=\"submit_select\" value=\"".T_EDIT."\">\n");
    }

  if($user == 'admin')
    {
      if($curFile == $fn['start'] || $curFile == $fn['help'] || $curFile == $fn['about'] || $curFile == $fn['register'] )echo("<input type =\"submit\" name=\"submit_select\" value=\"".T_EDIT."\">\n");
    }

  echo("</form></td>");
  echo("</table></div>");
}

function viking_7_load($sys_id)
{
  global $par,$fn,$upload, $editSFlag,$curEditFlag;
  $path      = $par['path'];
  $sid       = $par['a7_sid'];
  $user      = $par['user'];
  $curSource = $par['a7_cur_source'];
  $selSource = $par['a7_sel_source'];
  $curSimLen = $par['a7_cur_sim_len'];
  $ready     = $par['a7_ready'];
  
  echo("<div style=\"float:left; width : 100%; background :white; text-align: left;margin-left:20px; margin-bottom:20px;\">");
  if($user)
    {
      echo("<hr><table border=\"0\"><tr>");      
      echo("<form name=\"f_load_source\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
      echo("<input type=\"hidden\" name=\"action\" value=\"set_load_delete\">\n");
      echo("<td>Simulation Length</td><td><input type=\"text\" name=\"sim_len\" value=\"$curSimLen\" size=\"5\"></td>");
      $tFile = $fn['list'];
      $syscom = "ls $upload > $tFile;";
      system($syscom);
      echo("<td>");
      $nSketches = formSelectFile("Loaded Sketch","source",$tFile,$curSource,$upload);
      echo("</td></tr></table><br>");

      echo("<input type =\"submit\" name=\"submit_load_del\" value=\"".T_LOAD."\">");
      echo("<input type =\"submit\" name=\"submit_load_del\" value=\"".T_DELETE."\">");
      echo("<input type =\"submit\" name=\"submit_load_del\" value=\"".T_EDIT."\">");
      if($curEditFlag == 1)echo("  <b>Editing</b> <i>$selSource</i>");
      echo("</form>");

	  if($nSketches < 10)
	    {
	      echo("<hr><h4>You have $nSketches sketches stored. Limit is 10.</h4><br><form name=\"f_upload_source\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\"> ");
	      echo("<input type=\"hidden\" name=\"action\" value=\"upload_source\">");
	      echo("<input type=\"file\" name=\"import_file\" value=\"\">\n");
	      echo("<input type =\"submit\" name=\"submit_file\" value=\"".T_UPLOAD_SKETCH."\">");
	      echo("</form><br>");
	    }
	  else
	    echo("<h2>You have 10 sketches stored. Delete some of the sketches.</h2>");

      echo("<hr>");
      echo("<h4>Create a new sketch from template</h4>");
      echo("<table border=\"0\"><tr>");      
      echo("<form name=\"f_load_new_sketch\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
      echo("<input type=\"hidden\" name=\"action\" value=\"set_load_new_sketch\">\n");
      echo("<td>New Sketch Name</td><td><input type=\"text\" name=\"new sketch_name\" value=\"$user.pde\" size=\"10\"></td>");
      echo("<td><input type =\"submit\" name=\"submit_load_new_sketch\" value=\"".T_CREATE."\"></td>");
      echo("</tr></table><br>");
      echo("</form>");
      
      if($ready)echo("<br>$ready<br>");
    }  
  echo("</div>");
}


function viking_7_pinValues($sys_id)
{
  global $par,$pinValueA,$pinValueD,$pinModeD;
  $path       = $par['path'];
  $sid        = $par['a7_sid'];
  $user       = $par['user'];
  //$curSketch  = $par['a7_cur_sketch'];
  $curSimLen  = $par['a7_cur_sim_len'];
  $curStep    = $par['a7_cur_step'];

  $aPins = $par['a7_cur_board_anapins'];
  $dPins = $par['a7_cur_board_digpins'];

  echo("<div style=\"font-size:12px;float:left; width : 100%; background :white; text-align: left;margin-left:20px; margin-bottom:20px;\">");

  echo("<hr><form name=\"f_set_dig_scenario\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
  echo("<input type=\"hidden\" name=\"action\" value=\"set_dig_scenario\">");
  echo("<select name=\"pin_value\">");
  echo("<option value=\"-\">-</option>");
  for($ii=0;$ii<$dPins;$ii++)
    {
      $temp = $pinModeD[$ii];
      if($temp == INPUT || $temp == I_CHANGE || $temp == I_RISING || $temp == I_FALLING || $temp == I_LOW)     
	echo("<option value=\"$ii\">$ii</option>");
    }
  echo("</select>");
  echo("<input type=\"radio\" name=\"dvalue\" value=\"0\"> Low");
  echo("<input type=\"radio\" name=\"dvalue\" value=\"1\"> High");
  echo("<input type =\"submit\" name=\"submit_dig_scenario\" value=\"".T_SET_DIG_PIN_VALUE."\">");
  echo("</form>");

  echo("<form name=\"f_set_ana_scenario\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
  echo("<input type=\"hidden\" name=\"action\" value=\"set_ana_scenario\">");
  echo("<select name=\"pin_value\">");
  echo("<option value=\"-\">-</option>");
  for($ii=0;$ii<$aPins;$ii++)
    {    
      echo("<option value=\"$ii\">$ii</option>");
    }
  echo("</select>");
  echo("<input type=\"text\" name=\"dvalue\" value=\"\" size=\"4\">");
  echo("<input type =\"submit\" name=\"submit_ana_scenario\" value=\"".T_SET_ANA_PIN_VALUE."\">");
  echo("</form>");
  echo("The value is valid from step $curStep to next breakpoint<br>"); 

  echo("<hr><b>Analog Pin Values at step $curStep</b>");
  echo("<table border=0><tr>");
  $count = 0;
  for($ii=0;$ii<$aPins;$ii++)
    {
      if($pinValueA[$ii])
	{
	  $count++;
	  echo("<td>$ii =</td>");
	  echo("<td>$pinValueA[$ii]|</td>");
	  if($count == 8)echo("</tr><tr>");
	}
    }
  echo("</tr></table>");
    
  echo("<br><b>Digital Pin Values at step $curStep</b>");
  echo("<table border=0><tr>");
  $count = 0;
  for($ii=0;$ii<$dPins;$ii++)
    {
      if($pinValueD[$ii])
	{
	  $count++;
	  echo("<td>$ii=</td>");
	  echo("<td>$pinValueD[$ii]</td>");
	  if($count == 8)echo("</tr><tr>");
	}
    }
  echo("</tr>");
  echo("</table>");
    
  echo("</div>");
}


function viking_7_applyAccount($sys_id)
{
  global $par,$application;
  $path   = $par['path'];
  $sid    = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user   = $par['user'];
  //$curSketch = $par['a7_cur_sketch'];
  $curSimLen = $par['a7_cur_sim_len'];


  echo("<div style=\"float:left; width : 100%; background :white; text-align: left;margin-left:20px; margin-bottom:20px;\">");
  if($application == 0)
    {
	  
      echo("<table border=\"0\"><tr>");
	  
      echo("<form name=\"f_apply_account\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
      echo("<input type=\"hidden\" name=\"action\" value=\"apply_account\">\n");
      echo("<td>UserName:</td><td>Your E-mail Address:</td></tr><tr><td><input type=\"text\" name=\"username\" value=\"\" size=\"16\"></td>\n");
      echo("<td><input type=\"text\" name=\"email\" value=\"\" size=\"30\"></td>\n");
      system("ls upload > list.txt;");
      echo("<td>");
      echo("<input type =\"submit\" name=\"submit_file\" value=\"".T_APPLY."\"></td>\n");
      echo("</form></tr>");
      echo("</table>");
    }
  else if($application == 1)
    {
      echo("<h3>Thank you for your interest!<br>Your account information will be sent to you within 24 hours.</h3>");  
    }
      
  echo("</div>");

}


function viking_7_script($sys_id)
{
  global $par,$coords;
  global $wBoard,$hBoard;
  global $boardId,$boardDigPins,$boardAnaPins;
  global $digX,$digY,$anaX,$anaY,$resetX,$resetY;
  global $TXledX,$TXledY,$onOffX,$onOffY,$led13X,$led13Y;
  global $sketchNameX,$sketchNameY;
  global $pinModeD,$pinStatusD,$pinStatusA,$serial;

  $path   = $par['path'];
  $sid        = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user       = $par['user'];

  $curSketchName = $par['a7_cur_sketch_name'];
  $curStep       = $par['a7_cur_step'];
  $board         = $par['a7_cur_board_type'];
  $boardDigPins  = $par['a7_cur_board_digpins'];
  $boardAnaPins  = $par['a7_cur_board_anapins'];

  $image = 'no image';
  if($board == 'boardUno')$image  = 'arduino_uno.jpg';
  if($board == 'boardMega')$image = 'arduino_mega.jpg';

  echo("<script type= \"text/javascript\">");
  
  echo("function draw(){");
  echo("var canvas = document.getElementById('$board');");
  echo("if (canvas.getContext){var ctx = canvas.getContext('2d');var imageObj = new Image();imageObj.src = \"$image\";ctx.drawImage(imageObj, 0, 0,$wBoard,$hBoard);");

  $black  = "ctx.fillStyle = \"#000000\";";
  $yellow = "ctx.fillStyle = \"#FFFF00\";";
  $white  = "ctx.fillStyle = \"#FFFFFF\";";
  $red    = "ctx.fillStyle = \"#FF0000\";";
  $green  = "ctx.fillStyle = \"#00FF00\";";
  $orange = "ctx.fillStyle = \"#CC6600\";";
  $blue   = "ctx.fillStyle = \"#0000FF\";";
  $fuchsia= "ctx.fillStyle = \"#FF00FF\";";
  $aqua   = "ctx.fillStyle = \"#00FFFF\";";
  $grey   = "ctx.fillStyle = \"grey\";";
      

  // Test position
  $xx = $coords[0];  $yy = $coords[1];
  if($xx && $yy)
    {
      print("ctx.fillStyle = \"#FF0000\";");
      print("ctx.beginPath();");
      print("ctx.arc($xx, $yy, 2, 0, Math.PI*2, true);");
      print("ctx.closePath();");
      print("ctx.fill();");
    }

  // On OFF led
  print("ctx.fillStyle = \"#FFFF00\";");
  print("ctx.beginPath();");
  print("ctx.rect($onOffY-4, $onOffX-3,8, 5);");
  print("ctx.closePath();");
  print("ctx.fill();");

  // TX led when Serial Output
  if(strlen($serial[$curStep]))
    {
      print("ctx.fillStyle = \"#FFFF00\";");
      print("ctx.beginPath();");
      print("ctx.rect($TXledY-4, $TXledX-3,8, 5);");
      //print("ctx.arc($onOffY, $onOffX, 4, 0, Math.PI*2, true);");
      print("ctx.closePath();");
      print("ctx.fill();");
    }

  // Digital Pins Mode
  for($ii=0; $ii<$boardDigPins; $ii++)
    {
      if($pinModeD[$ii]!=0)
	{
	  if($pinModeD[$ii]==OUTPUT)print($green);  //OUTPUT
	  if($pinModeD[$ii]==INPUT)print($red);   //INPUT
	  if($pinModeD[$ii]==RX)print($white);     // RX
	  if($pinModeD[$ii]==TX)print($grey);   // TX
	  if($pinModeD[$ii]==I_CHANGE)print($blue);    // CHANGE
	  if($pinModeD[$ii]==I_FALLING)print($orange); // RISING
	  if($pinModeD[$ii]==I_RISING)print($yellow);    // FALLING
	  
	  print("ctx.beginPath();");
	  if($ii < 22)
	    print("ctx.rect($digY[$ii]-4, $digX[$ii]-12,8, 4);");
	  else if($ii%2 == 0)
	    print("ctx.rect($digY[$ii]-5, $digX[$ii]+5,8, 4);");
	  else if($ii%2 == 1)
	    print("ctx.rect($digY[$ii]-5, $digX[$ii]+5,8, 4);");
	  print("ctx.closePath();");
	  print("ctx.fill();");
	}
    }

  // Digital Pins Status
  for($ii=0; $ii<$boardDigPins; $ii++)
    {
      if($pinStatusD[$ii]!=0)
	{
	  if($pinStatusD[$ii]==HIGH)print($yellow);  // HIGH
	  if($pinStatusD[$ii]==LOW)print($black);
	  if($pinStatusD[$ii]==PWM)print($green);
	  print("ctx.beginPath();");
	  print("ctx.arc($digY[$ii], $digX[$ii], 4, 0, Math.PI*2, true);");
	  if($ii == 13 && $pinStatusD[13]>0)
	    print("ctx.rect($led13Y-4, $led13X-3,8, 5);");
	  print("ctx.closePath();");
	  print("ctx.fill();");
	}
    }

  // Analog Pins Status
  for($ii=0; $ii<$boardAnaPins; $ii++)
    {
      if($pinStatusA[$ii]!=0)
	{
	  if($pinStatusA[$ii]==READ)print($red); // reading
	  print("ctx.beginPath();");
	  print("ctx.arc($anaY[$ii], $anaX[$ii], 4, 0, Math.PI*2, true);");
	  print("ctx.closePath();");
	  print("ctx.fill();");
	}
    }

  // Write Sketch Name on IC
  print("ctx.font = \"15pt Calibri\";");
  print("ctx.fillStyle = \"#FF0000\";");
  print("ctx.fillText(\"$curSketchName\",$sketchNameY,$sketchNameX);");
  
  echo(" }");
  echo(" }");
  
  echo("</script>");
}


function viking_7_isMap($sys_id)
{
  global $par,$wBoard,$hBoard;

  $board = $par['a7_cur_board_type'];
  if($board == 'boardUno')
    echo("<a href=$path><img src=\"arduino_uno.jpg\"ismap width=$wBoard height=$hBoard></a>");
  if($board == 'boardMega')
    echo("<a href=$path><img src=\"arduino_mega.jpg\"ismap width=$wBoard height=$hBoard></a>");
}

?>
