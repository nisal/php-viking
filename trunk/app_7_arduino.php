<?
include("lib_7_arduino.php");
//========================================
// PHP-VIKING APP 7: Arduino Simulator
//========================================
define('T_UPLOAD_SKETCH','Upload sketch to account');
define('T_UPLOAD_FILE','Upload file (.ino or .scn) to your account');
define('T_DELETE','Delete');
define('T_CONFIG','Configuration');
define('T_LOOP_F','Next Loop');
define('T_LOOP_B','Prev Loop');
define('T_STEP_F','Next Step');
define('T_STEP_B','Prev Step');
define('T_CREATE','Create');
define('T_COPY','Copy');
define('T_EDIT','Edit');
define('T_EDIT_SKETCH','Edit Sketch');
define('T_EDIT_SCENARIO','Edit Scenario');
define('T_SAVE','Save');
define('T_SELECT','Select');
define('T_LOAD','Load');
define('T_VIEW','View');
define('T_RUN', 'Run');
define('T_EXAMPLE', 'Example');
define('T_TEMPLATE', 'Template');
define('T_SET', 'Set');
define('T_SET_DIG_PIN_VALUE', 'Set Digital Pin Value');
define('T_SET_ANA_PIN_VALUE', 'Set Analog Pin Value');
define('T_APPLY', 'Send Application');


// Analog Pins
define('READ',   '1');

define('S_READ',   '1');
define('S_WRITE',  '2');

// Digital Pins
define('LOW',    '0');
define('HIGH',   '1');
define('PWM',    '3');

//#define CHANGE  11
//#define RISING  12
//#define FALLING 13
// Digital Pins Mode
define('VOID',     '0');
define('I_LOW',    '0');
define('I_RISING', '12');
define('I_FALLING','13');
define('I_CHANGE', '11');
define('TX',       '5');
define('RX',       '4');
define('INPUT' ,   '0');
define('OUTPUT' ,  '1');

define('YES' , '1');
define('NO' ,  '2');



$UNO_DIG_PINS  = 14;
$UNO_ANA_PINS  =  6;
$MEGA_DIG_PINS = 54;
$MEGA_ANA_PINS = 16;

// Declarations =========================
$g_tok  = array();

$user    = $par['user'];

// Set filenames
$fn = array(); 
$fn['application'] = 'application.txt';
$fn['userstat']    = 'login.log';
$fn['start']       = 'start.htm';
$fn['about']       = 'about.htm';
$fn['news']        = 'news.htm';
$fn['register']    = 'register.htm';
$fn['help']        = 'help.htm';
$fn['faq']         = 'faq.htm';
$fn['sk_template'] = 'sk_template.pde';
$fn['sk_example']  = 'sk_example.pde';
$fn['sc_template'] = 'sc_template.scn';
$fn['sc_example']  = 'sc_example.scn';
$fn['tutorial']    = 'tutorial.htm';
$fn['setting']     = 'account/'.$user.'/setting.txt';


$userEvent = $par['user_event'];

if($userEvent == 2) // user logged out!
  {
    resetSession();
    $par['user_event'] = 0;
    $par['pv'] = 'start';
  }

$application = 0;


readLoginCounter();


//=================================================
//+++++++++++++++++++++++++++++++++++++++++++++++++
//=================================================
if($user)
  {
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

    // 2 dim (pin,step)
    $valueInPinD   = array();
    $valueOutPinD  = array();
    $valueInPinA   = array();

    $g_readValue   = array();
    $g_readPin     = array();
    $g_readType    = array();

    $par['a7_cur_sim_len']       = $_SESSION['a7_cur_sim_len'];
    init($par['a7_cur_sim_len']);
    $par['a7_cur_loop_len']      = $_SESSION['a7_cur_loop_len'];
    $par['a7_cur_read_len']      = $_SESSION['a7_cur_read_len'];
    $par['a7_sel_source']        = $_SESSION['a7_sel_source'];
    $par['a7_cur_source']        = $_SESSION['a7_cur_source'];
    $par['a7_sel_scenario']      = $_SESSION['a7_sel_scenario'];
    $par['a7_cur_scenario']      = $_SESSION['a7_cur_scenario'];
    $par['a7_cur_step']          = $_SESSION['a7_cur_step'];
    $par['a7_cur_loop']          = $_SESSION['a7_cur_loop'];
    $par['a7_cur_read']          = $_SESSION['a7_cur_read'];
    $par['a7_cur_sketch_name']   = $_SESSION['a7_cur_sketch_name'];
    $par['a7_cur_board_type']    = $_SESSION['a7_cur_board_type'];
    $par['a7_cur_board_digpins'] = $_SESSION['a7_cur_board_digpins'];
    $par['a7_cur_board_anapins'] = $_SESSION['a7_cur_board_anapins'];
    $par['a7_ser_log']           = $_SESSION['a7_ser_log'];
    $par['a7_row_number']        = $_SESSION['a7_row_number'];
    $par['a7_cur_file']          = $_SESSION['a7_cur_file'];

    if($userEvent == 1) // user logged in!
      {
	$fn['setting'] = 'account/'.$user.'/setting.txt';
	resetSession();
	readUserSetting();
	$par['user_event'] = 0;
      }

    $account = $par['user'];

    $tDir = 'account/'.$account;

    $upload   = $tDir.'/upload/';


    $fn['serial']      = 'account/'.$account.'/serv.serial';
    $fn['custom']      = 'account/'.$account.'/serv.cust';
    $fn['event']       = 'account/'.$account.'/serv.event';
    $fn['error']       = 'account/'.$account.'/serv.error';
    $fn['exec']        = 'account/'.$account.'/exec.error';
    $fn['g++']         = 'account/'.$account.'/g++.error';

    $fn['status']      = 'account/'.$account.'/data.status';
    $fn['debug_ino']   = 'account/'.$account.'/ino.debug';

    $fn['pinmod']      = 'account/'.$account.'/serv.pinmod';
    $fn['pinrw']       = 'account/'.$account.'/serv.pinrw';
    $fn['digval']      = 'account/'.$account.'/serv.digval';
    $fn['anaval']      = 'account/'.$account.'/serv.anaval';

    $fn['code']        = 'account/'.$account.'/data.code';
    $fn['sketch']      = 'account/'.$account.'/sketch.ino';
    $fn['scenario']    = 'account/'.$account.'/data.scen';
    $fn['scenexp']     = 'account/'.$account.'/data.scenario';
    $fn['time']        = 'account/'.$account.'/serv.time';
    $fn['list']        = 'account/'.$account.'/list.txt';


    //accessControl(); // Keep this until debugged

    
    if($tDir && !is_dir($tDir))
      {
	vikingWarning("Create account");
	if(!mkdir($tDir,0777))vikingError("Not possible to create account");
	$tDir2 = $tDir.'/upload';
	if(!mkdir($tDir2,0777))vikingError("Not possible to create upload in account");
	
	$syscom = "cd $tDir;touch index.htm;";
	system($syscom);
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
	$syscom = "cd $tDir;touch data.serial data.custom data.arduino data.error data.time data.status data.code data.scen data.scenario sketch.ino;";
	system($syscom);
	resetSession();
      }
    
    
    $hBoard = 300; // 300 240
    $wBoard = 500; // 500 400

    readSketchInfo();
    canvasPos();

    //$tFile = $fn['custom'];
    readSimulation();

    $curSimLen  = $par['a7_cur_sim_len'] ;
    $curLoopLen = $par['a7_cur_loop_len'] ;
    $curReadLen = $par['a7_cur_read_len'] ;

    $curSource  = $par['a7_cur_source'] ;
    $selSource  = $par['a7_sel_source'] ;
    $curScenario  = $par['a7_cur_scenario'] ;
    $selScenario  = $par['a7_sel_scenario'] ;
    $curFile    = $par['a7_cur_file'] ;

    $curLoop    = $par['a7_cur_loop'] ;
    $curRead    = $par['a7_cur_read'] ;
    $curStep    = $par['a7_cur_step'];
    //$submenu    = $par['a7_submenu'];

    // GET ==============================================

    //$input  = array_keys($_GET); 
    //$coords = explode(',', $input[0]); 
    //print("X coordinate : ".$coords[0]."<br/> Y Coordinate : ".$coords[1]); 

    $action  = $_GET['ac'];
    $alt     = $_GET['x'];

    if($action == 'access_control')
      {
	accessControl();
      }

    if($action == 'delete_file' && $user != 'guest')
      {
	$tFile = $upload.$alt;
	if (file_exists($tFile)) 
	  {
	    unlink($tFile);
	    //$par['a7_sel_source'] = '-';
	  }
      }

    if($action == 'run' && $curSimLen > 0)
      {
	execSketch($curSimLen,0);
      }

    if($action == 'rownumber')
      {
	$par['a7_row_number'] = $alt;
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

    if($action == 'edit_sketch')
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

    if($action == 'setDpin')
      {
	readScenario();
	$pin   = $alt;
	$value = $valueInPinD[$pin][$curStep];

	if($value==0)$value = "1";
	else if($value==1)$value = "0";

        if($pin != "-")
	  {
	    $pinType = 2;//DIG
	    $do = 10; // ADD=10, DELETE = 20
	    $syscom = "cd account/$user;./servuino $curSimLen 1 $pinType $pin $value $curStep $do >exec.error 2>&1;chmod 777 data.*;";
	    //echo("$syscom<br/>");
	    system($syscom);
	    init($curSimLen);
	    readSketchInfo();
	    //$tFile = $fn['arduino'];
	    readSimulation();
	    readStatus();
	  }	
      }

    // POST =============================================

    if (!isset($_POST['action']))$_POST['action'] = "undefined"; 

    $action = $_POST['action'];

    // Data Form
    if($action == 'select_file' )
      {
	$par['a7_cur_file'] = $_POST['file'];
	$curFile = $par['a7_cur_file'];
	if($user == 'admin')
	  {
	    if($curFile == $fn['news'] || $curFile == $fn['faq'] || $curFile == $fn['tutorial'] || $curFile == $fn['start'] || $curFile == $fn['help'] || $curFile == $fn['about'] || $curFile == $fn['register'])$par['tinyMCE'] = 1;
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
	if(strlen($data) == 0)$data = "empty file";
	$what = $_POST['submit_edit'];
	$curSimLen = $par['a7_cur_sim_len'];
	if($tempFile)
	  {
	    
	    
	    // Always save file
	    $res = evilCode($data);
	    if($res == 0 && $user != 'guest')
	      {
		$fp = fopen($tempFile, 'w')or die("Could not open file ($tempFile) (write)!");;
		fwrite($fp,$data) or die("Could not write to file ($tempFile) !");
		fclose($fp);
	      }
	    else
	      $par['a7_ready'] = "File did not pass check for evil code!";
	    
	    if($what == T_SAVE) 
	      {
		$par['a7_cur_file'] = $tempFile;
		$curFile = $par['a7_cur_file'];
		$curEditFlag = 1;
		if($curFile == $fn['news'] || $curFile == $fn['faq'] || $curFile == $fn['start'] || $curFile == $fn['help'] || $curFile == $fn['about'] || $curFile == $fn['register'] || $curFile == $fn['tutorial'])$par['tinyMCE'] = 1;
		else
		  $par['tinyMCE'] = 0;
	      }
	  }
	
      }
    if($action == 'edit_sketch')
      {
	$tempFile = $_POST['file_name'];
	$data     = $_POST['file_data'];
	$what     = $_POST['submit_edit'];

	if($tempFile)
	  {
	    
	    // Always save file
	    $res = evilCode($data);
	    if($res == 0 && $user != 'guest')
	      {
		$fp = fopen($tempFile, 'w')or die("Could not open sketch ($tempFile) (write)!");;
		fwrite($fp,$data) or die("Could not write to sketch ($tempFile) !");
		fclose($fp);
	      }
	    else
	      $par['a7_ready'] = "Sketch did not pass check for evil code!";
	    
	    if($what == T_SAVE) 
	      {
		$par['a7_sel_source'] = $tempFile;
		$curEditFlag = 1;
	      }
	  }
	else
	  $par['a7_ready'] = "No Sketch specified!";
      }

    if($action == 'edit_scenario')
      {
	$tempFile = $_POST['file_name'];
	$data     = $_POST['file_data'];
	$what     = $_POST['submit_edit'];

	if($tempFile)
	  {
	    
	    // Always save file
	    $res = evilCode($data);
	    if($res == 0 && $user != 'guest')
	      {
		$fp = fopen($tempFile, 'w')or die("Could not open scenario ($tempFile) (write)!");;
		fwrite($fp,$data) or die("Could not write to scenario ($tempFile) !");
		fclose($fp);
	      }
	    else
	      $par['a7_ready'] = "Scenario did not pass check for evil code!";
	    
	    if($what == T_SAVE) 
	      {
		$par['a7_sel_scenario'] = $tempFile;
		$curEditFlag = 1;
	      }
	  }
	else
	  $par['a7_ready'] = "No Scenario specified!";
      }


    if($action == 'upload_source' && $user != 'guest')
      {
	$dummy = uploadFile2();
      }

    if($action == 'set_new_sketch' && $user != 'guest' )
      {
	$what = $_POST['submit_new_sketch'];
	if($what==T_TEMPLATE)$temp = $fn['sk_template'];
	if($what==T_EXAMPLE)$temp = $fn['sk_example'];
	$newName = $_POST['new_sketch_name'];
	$dest = $upload.$newName;
	if (!copy($temp,$dest)) {
	  vikingError("Failed to copy ($temp -> $dest)");
	}
	else
	  {
	    $par['a7_sel_source'] = $dest;
	    $selSource = $par['a7_sel_source'];
	  }
      }

    if($action == 'set_new_scenario' && $user != 'guest' )
      {
	$what = $_POST['submit_new_scenario'];
	if($what==T_TEMPLATE)$temp = $fn['sc_template'];
	if($what==T_EXAMPLE)$temp = $fn['sc_example'];
	$newName = $_POST['new_scenario_name'];
	$dest = $upload.$newName;
	if (!copy($temp,$dest)) {
	  vikingError("Failed to copy ($temp -> $dest)");
	}
	else
	  {
	    $par['a7_sel_scenario'] = $dest;
	    $selSource = $par['a7_sel_scenario'];
	  }
      }

    if($action == 'copy_file' && $user != 'guest' )
      {
	$temp = $_POST['copy_source'];
	$newName = $_POST['copy_file_name'];
	$dest = $upload.$newName;
	$temp = $upload.$temp;
	if (!copy($temp,$dest)) {
	  vikingError("Failed to copy ($temp -> $dest)");
	}
      }

    if($action == 'manage_sketch' )
      {
	$selSource   = $_POST['sketch'];
	$par['a7_sel_source']   =  $selSource;
	$curEditFlag = 1;
      }

    if($action == 'manage_scenario' )
      {
	$selScenario = $_POST['scenario'];
	$par['a7_sel_scenario']   =  $selScenario;
	$curEditFlag = 1;
      }

    if($action == 'set_load' )
      {
	$selSource   = $_POST['sketch'];
	$selScenario = $_POST['scenario'];
	$scenSource  = $_POST['scenario_source'];

	$what = $_POST['submit_load_del'];
	if($what == T_LOAD && $selSource)
	  {
       
	    $par['a7_sel_source']   =  $selSource;
	    $par['a7_sel_scenario'] =  $selScenario;

	    $curSimLen = $_POST['sim_len'];
	    if($curSimLen > 2000)$curSimLen = 2000;
	    $res = copySketchScenario($selSource,$selScenario);
	    if($res == 0)
	      {
		$curSource = $selSource;
		$par['a7_cur_sim_len']  =  $curSimLen;
		$par['a7_cur_source']   =  $selSource;
		compileSketch();
		if($scenSource == 'internal') 
		  {
		    $par['a7_cur_scenario'] = 'sketch';
		    execSketch($curSimLen,0);
		  }
		if($scenSource == 'external' )
		  {
		    $par['a7_cur_scenario'] = $selScenario;
		    execSketch($curSimLen,1);
		  }
		$par['a7_cur_step'] = 1;
		$par['a7_cur_loop'] = 0;
		$par['a7_cur_read'] = 1;
		init($curSimLen);
		readSketchInfo();
		readSimulation();
		readStatus();
		$par['a7_ready'] = "Sketch loaded!";
	      }
	    else
	      $par['a7_ready'] = "Sketch not loaded!";
	  }
	else
	  vikingWarning("No sketch selected !");
      }

    if($action == 'run_target' )
      {
        $targetStep = $_POST['target_step'];
        runTarget($targetStep);
      }

    if($action == 'set_dig_scenario' )
      {
	$pin   = $_POST['pin_value'];
	if($pin==0)$pin = "0";
	$value = $_POST['dvalue'];
	if($value==0)$value = "0";
        if($pin != "-")
	  {
	    $pinType = 2;//DIG
	    $do = 10; // ADD=10, DELETE = 20
	    $syscom = "cd account/$user;./servuino $curSimLen 1 $pinType $pin $value $curStep $do >exec.error 2>&1;chmod 777 data.*;";
	    //echo("$syscom<br/>");
	    system($syscom);
	    init($curSimLen);
	    readSketchInfo();
	    readSimulation();
	    readStatus();
	  }
      }

    if($action == 'set_ana_scenario' )
      {
	$pin   = $_POST['pin_value'];
	if($pin==0)$pin = "0";
	$value = $_POST['dvalue'];
	if($value==0)$value = "0";
        if($pin != "-")
	  {
	    $pinType = 1;//DIG=2 ANA=1
	    $do = 10;    // ADD=10, DELETE=20
	    $syscom = "cd account/$user;./servuino $curSimLen 1 $pinType $pin $value $curStep $do >exec.error 2>&1;chmod 777 data.*;";
	    //echo("$syscom<br/>");
	    system($syscom);
	    init($curSimLen);
	    readSketchInfo();
	    readSimulation();
	    readStatus();
	  }
      }

    $curStep = $par['a7_cur_step'];
    $curLoop = $par['a7_cur_loop'];
    readStatus();
    decodeStatus($status[$curStep]);

    writeUserSetting();

    // set SESSION parameters ===============================
    $_SESSION['a7_cur_sim_len']        = $par['a7_cur_sim_len'];
    $_SESSION['a7_cur_loop_len']       = $par['a7_cur_loop_len'];
    $_SESSION['a7_cur_read_len']       = $par['a7_cur_read_len'];
    $_SESSION['a7_sel_source']         = $par['a7_sel_source'];
    $_SESSION['a7_cur_scenario']       = $par['a7_cur_scenario'];
    $_SESSION['a7_sel_scenario']       = $par['a7_sel_scenario'];
    $_SESSION['a7_cur_source']         = $par['a7_cur_source'];
    $_SESSION['a7_cur_step']           = $par['a7_cur_step'];
    $_SESSION['a7_cur_loop']           = $par['a7_cur_loop'];
    $_SESSION['a7_cur_read']           = $par['a7_cur_read'];
    $_SESSION['a7_cur_sketch_name']    = $par['a7_cur_sketch_name'];
    $_SESSION['a7_cur_board_type']     = $par['a7_cur_board_type'];
    $_SESSION['a7_cur_board_digpins']  = $par['a7_cur_board_digpins'];
    $_SESSION['a7_cur_board_anapins']  = $par['a7_cur_board_anapins'];
    $_SESSION['a7_ser_log']            = $par['a7_ser_log'];
    $_SESSION['a7_row_number']         = $par['a7_row_number'];
    $_SESSION['a7_cur_file']           = $par['a7_cur_file'];
    //$_SESSION['a7_submenu']            = $par['a7_submenu'];
    
  } // end if user

//=================================================
//+++++++++++++++++++++++++++++++++++++++++++++++++
//=================================================

 else
   {
     if (!isset($_POST['action']))$_POST['action'] = "undefined"; 

     $action = $_POST['action'];

     if($action == 'apply_account' )
       {
	 $username = $_POST['username'];
	 $email    = $_POST['email'];
	 $letter   = $_POST['letter'];
	 $letter = safeText3($letter);
	 createApplication($username,$email,$letter);
       }

   }

//====================================================
//  HTML functions
//====================================================

//********************************************
function viking_7_mainmenu($sys_id)
{
  global $par;
  $path    = $par['path'];
  $user    = $par['user'];
  //$submenu = $par['a7_submenu'];

  //if(!$submenu) $submenu = 1;

  echo("<ul id=\"dropdown\">");

  echo("<li><a href=\"$path\">File</a>");
  echo("<ul>");
  echo("   <li><a href=\"index.php?pv=create\">Create</a></li>");
  echo("   <li><a href=\"index.php?pv=copy\">Copy</a></li>");
  echo("   <li><a href=\"index.php?pv=delete\">Delete</a></li>");
  echo("   <li><a href=\"index.php?pv=upload\">Upload</a></li>");
  echo("   <li>");viking_4_login_logout();echo("</li>");
  echo("</ul>");
  echo("</li>");

  echo("<li><a href=\"$path\">Edit</a>");
  echo("<ul>");
  echo("   <li><a href=\"index.php?pv=edit_sketch\">Sketch</a></li>");
  echo("   <li><a href=\"index.php?pv=edit_scenario\">Scenario</a></li>");
  echo("</ul>");
  echo("</li>");

  echo("<li><a href=\"$path\">Simulation</a>");
  echo("<ul>");
  echo("   <li><a href=\"index.php?pv=load\">Load</a></li>");
  echo("   <li><a href=\"index.php?pv=board\">Board</a></li>");
  echo("   <li><a href=\"index.php?pv=log\">Event Serial</a></li>");
  //echo("   <li><a href=\"index.php?pv=log\">SerialOut</a></li>");
  echo("   <li><a href=\"index.php?pv=graph_status\">Status</a></li>");
  echo("   <li><a href=\"index.php?pv=graph_scenario\">Scenario</a></li>");
  echo("   <li><a href=\"index.php?pv=source\">Source</a></li>");
  echo("</ul>");
  echo("</li>");

//   echo("<li><a href=\"$path\">Configuration</a>");
//   echo("<ul>");
//   echo("   <li><a href=\"index.php?pv=config\">Board HW</a></li>");
//   echo("</ul>");
//   echo("</li>");

  echo("<li><a href=\"$path\">Help</a>");
  echo("<ul>");
  echo("   <li><a href=\"index.php?pv=faq\">FAQ</a></li>");
  echo("   <li><a href=\"index.php?pv=tutorial\">Example</a></li>");
  echo("</ul>");
  echo("</li>");

  echo("<li><a href=\"$path\">About</a>");
  echo("<ul>");
  echo("   <li><a href=\"index.php?pv=start\">Simuino</a></li>");
  echo("   <li><a href=\"index.php?pv=news\">News</a></li>");
  echo("</ul>");
  echo("</li>");

  if(!$user)
    {
      echo("<li><a href=\"index.php?pv=register\">Register</a>");
      echo("</li>");
    }

  if($user == 'admin')
    {
      echo("<li><a href=\"$path\">Admin</a>");
      echo("<ul>");
      echo("   <li><a href=\"index.php?pv=list_user\">List Users</a></li>");
      echo("   <li><a href=\"index.php?pv=add_user\">Register</a></li>");
      echo("   <li><a href=\"index.php?pv=del_user\">Unregister</a></li>");
      echo("   <li><a href=\"index.php?pv=debug\">Debug</a></li>");
      echo("</ul>");
      echo("</li>");
    }

  echo("</ul>");
 
}
//********************************************
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
  echo("         <li><a href=$path&ac=read&x=$temp>read+</a></li>");

  $temp = $curRead - 1;
  if($temp < 1)$temp = 0;
  echo("         <li><a href=$path&ac=read&x=$temp>read-</a></li>");

  $temp = $curSimLen;
  echo("         <li><a href=$path&ac=step&x=$temp>last</a></li>");
  echo("         <ul><li><a href=$path&ac=step&x=1>first</a></li>");

  echo("</ul>");
}
//********************************************
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
//********************************************
function viking_7_canvas($sys_id)
{
  global $par,$wBoard,$hBoard;
  
  $board = $par['a7_cur_board_type'];

  $path = $par['path'];
  $sid  = $par['a7_sid'];
  $user = $par['user'];
  echo("<canvas id=\"$board\" width=\"$wBoard\" height=\"$hBoard\"></canvas>\n");
}
//********************************************
function viking_7_editFile($sys_id)
{
  global $par,$servuino,$fn,$upload;
  global $curEditFlag;

  $user      = $par['user'];
  $path      = $par['path'];
  $curFile   = $par['a7_cur_file'];
  $selSource = $par['a7_sel_source'];
  $ready     = $par['a7_ready'];
  $memPV     = $par['pv_mem'];
  $curPV     = $par['pv'];


  $file = $curFile;

  if(!$file)vikingWarning("editFile: No file specified");;
  
  if($curEditFlag == 0 && $file)
    {
      if($par['pv'] != 'large_file')
	echo(" (<a href=$path&pv=large_file&pv_mem=$curPV>Wide Window</a>)");
      else
	echo(" (<a href=$path&pv=$memPV>Narrow Window</a>)");

      if($par['a7_row_number']==0)echo(" (<a href=$path&ac=rownumber&x=1>Row Number ON</a>)");
      if($par['a7_row_number']==1)echo(" (<a href=$path&ac=rownumber&x=0>Row Number OFF</a>)");
      echo("<div id=\"anyFile\" style=\"font-family:Courier,monospace; font-size:11px;float:left; border : solid 1px #000000; background : #A9BCF5; color : #000000;  text-align:left; padding : 3px; width :100%; height:500px; overflow : auto; margin-left:0px; margin-bottom:10px;line-height:1.0em; \">\n");
      $len = readAnyFile(1,$file);
      showAnyFile($len);
      echo("</div>\n");
    }
  else if($curEditFlag == 1 && $user)
    {
      if(!$file)return;
      $fileSize = filesize($file);
      if($fileSize > 0)
	{
	  $fh = fopen($file, "r") or die("Could not open file ($file)!");
	  $data = fread($fh, filesize($file)) or die("Could not read file ($file)!");
	  fclose($fh);
	}
      if($par['pv'] != 'large_file')
	{
	  $ncols = 80;
	  echo(" (<a href=$path&pv=large_file&pv_mem=$curPV&ac=edit_file>Wide Window</a>)");
	}
      else
	{
	  $ncols = 120;
	  echo(" (<a href=$path&pv=$memPV&ac=edit_file>Narrow Window</a>)");
	}

      echo("<form name=\"f_edit_file\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
      echo("<input type=\"hidden\" name=\"action\" value=\"edit_file\">\n");
      echo("<input type=\"hidden\" name=\"file_name\" value=\"$file\">\n");
      echo("<table><tr><td>");
      if($file == $fn['sketch'])echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_LOAD."\">\n");
      if($file == $fn['scenario'])echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_RUN."\">\n");
      if($user == 'admin')
	{
	  if($file == $fn['news'] || $file == $fn['tutorial'] || $file == $fn['faq'] || $file == $fn['start'] || $file == $fn['help'] || $file == $fn['about'] || $file == $fn['register'] )echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_SAVE."\">\n");
	}
      echo("</td></tr><tr><td><textarea style=\"color: #0000FF; font-size: 8pt;\" name=\"file_data\" cols=$ncols rows=36>$data</textarea></td></tr></table>");  
      echo("</form><br/>");
    }
  echo("$ready");
}
//********************************************
function viking_7_editSketch($sys_id)
{
  global $par,$servuino,$fn,$upload;
  global $curEditFlag;

  $user      = $par['user'];
  $path      = $par['path'];
  //$curFile   = $par['a7_cur_file'];
  $selSource = $par['a7_sel_source'];
  $ready     = $par['a7_ready'];
  $memPV     = $par['pv_mem'];
  $curPV     = $par['pv'];


  $file = $selSource;

  //echo("file=$file<br/> curpv=$curPV<br/> mempv=$memPV<br/>");

  if(!$file)vikingWarning("editSketch: No file specified");;
  
  if($curEditFlag == 0 && $file)
    {
      if($par['pv'] != 'large_sketch')
	echo(" (<a href=$path&pv=large_sketch&pv_mem=$curPV>Wide Window</a>)");
      else
	echo(" (<a href=$path&pv=$memPV>Narrow Window</a>)");

      if($par['a7_row_number']==0)echo(" (<a href=$path&ac=rownumber&x=1>Row Number ON</a>)");
      if($par['a7_row_number']==1)echo(" (<a href=$path&ac=rownumber&x=0>Row Number OFF</a>)");
      echo("<div id=\"anyFile\" style=\"font-family:Courier,monospace; font-size:11px;float:left; border : solid 1px #000000; background : #A9BCF5; color : #000000;  text-align:left; padding : 3px; width :100%; height:500px; overflow : auto; margin-left:0px; margin-bottom:10px;line-height:1.0em; \">\n");
      $len = readAnySketch(1,$file);
      showAnyFile($len);
      echo("</div>\n");
    }
  else if($curEditFlag == 1 && $user)
    {
      if(!$file)return;
      $fileSize = filesize($file);
      if($fileSize > 0)
	{
	  $fh = fopen($file, "r") or die("Could not open file ($file)!");
	  $data = fread($fh, filesize($file)) or die("Could not read file ($file)!");
	  fclose($fh);
	}
      if($par['pv'] != 'large_sketch')
	{
	  $ncols = 80;
	  echo(" (<a href=$path&pv=large_sketch&pv_mem=$curPV&ac=edit_file>Wide Window</a>)");
	}
      else
	{
	  $ncols = 120;
	  echo(" (<a href=$path&pv=$memPV&ac=edit_file>Narrow Window</a>)");
	}

      echo("<form name=\"f_edit_sketch\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
      echo("<input type=\"hidden\" name=\"action\" value=\"edit_sketch\">\n");
      echo("<input type=\"hidden\" name=\"file_name\" value=\"$file\">\n");
      echo("<table><tr><td>");
      echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_SAVE."\">\n");
      echo("</td></tr><tr><td><textarea style=\"color: #0000FF; font-size: 8pt;\" name=\"file_data\" cols=$ncols rows=36>$data</textarea></td></tr></table>");  
      echo("</form><br/>");
    }
  //echo("$ready");
}
//********************************************
function viking_7_editScenario($sys_id)
{
  global $par,$servuino,$fn,$upload;
  global $curEditFlag;

  $user      = $par['user'];
  $path      = $par['path'];
  //$curFile   = $par['a7_cur_file'];
  $selScenario = $par['a7_sel_scenario'];
  $ready     = $par['a7_ready'];
  $memPV     = $par['pv_mem'];
  $curPV     = $par['pv'];


  $file = $selScenario;

  //echo("file=$file<br/> curpv=$curPV<br/> mempv=$memPV<br/>");

  if(!$file)vikingWarning("editScenario: No file specified");;
  
  if($curEditFlag == 0 && $file)
    {
      if($par['pv'] != 'large_sketch')
	echo(" (<a href=$path&pv=large_sketch&pv_mem=$curPV>Wide Window</a>)");
      else
	echo(" (<a href=$path&pv=$memPV>Narrow Window</a>)");

      if($par['a7_row_number']==0)echo(" (<a href=$path&ac=rownumber&x=1>Row Number ON</a>)");
      if($par['a7_row_number']==1)echo(" (<a href=$path&ac=rownumber&x=0>Row Number OFF</a>)");
      echo("<div id=\"anyFile\" style=\"font-family:Courier,monospace; font-size:11px;float:left; border : solid 1px #000000; background : #A9BCF5; color : #000000;  text-align:left; padding : 3px; width :100%; height:500px; overflow : auto; margin-left:0px; margin-bottom:10px;line-height:1.0em; \">\n");
      $len = readAnySketch(1,$file);
      showAnyFile($len);
      echo("</div>\n");
    }
  else if($curEditFlag == 1 && $user)
    {
      if(!$file)return;
      $fileSize = filesize($file);
      if($fileSize > 0)
	{
	  $fh = fopen($file, "r") or die("Could not open file ($file)!");
	  $data = fread($fh, filesize($file)) or die("Could not read file ($file)!");
	  fclose($fh);
	}
      if($par['pv'] != 'large_sketch')
	{
	  $ncols = 80;
	  echo(" (<a href=$path&pv=large_sketch&pv_mem=$curPV&ac=edit_file>Wide Window</a>)");
	}
      else
	{
	  $ncols = 120;
	  echo(" (<a href=$path&pv=$memPV&ac=edit_file>Narrow Window</a>)");
	}

      echo("<form name=\"f_edit_sketch\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">\n ");
      echo("<input type=\"hidden\" name=\"action\" value=\"edit_scenario\">\n");
      echo("<input type=\"hidden\" name=\"file_name\" value=\"$file\">\n");
      echo("<table><tr><td>");
      echo("<input type =\"submit\" name=\"submit_edit\" value=\"".T_SAVE."\">\n");
      echo("</td></tr><tr><td><textarea style=\"color: #0000FF; font-size: 8pt;\" name=\"file_data\" cols=$ncols rows=36>$data</textarea></td></tr></table>");  
      echo("</form><br/>");
    }
  echo("$ready");
}
//********************************************
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
      //echo(" (<a href=$path&ac=winserlog&x=sce>Scenario</a>)");
      echo(" Serial Interface ");
      echo("<div id=\"serWin\" style=\"margin-right:1px;margin-bottom:5px;font-family:Courier,monospace; font-size:12px;float:left; border:solid 1px #FF0000; background:#BDBDBD; color:#000000; text-align:left; padding:4px; width:100%; height:280px; overflow:auto; \">");
      readSerial();
      showSerial($curStep);
      echo("</div>\n"); 
    }
//   else if($sl == 'sce')
//     {
//       echo(" (<a href=$path&ac=winserlog&x=log>Log Events</a>)");
//       echo(" Scenario ");
//       echo(" (<a href=$path&ac=winserlog&x=ser>Serial Interface</a>)");
//       echo("<div id=\"serWin\" style=\"margin-right:1px;margin-bottom:5px;font-family:Courier,monospace; font-size:12px;float:left; border:solid 1px #FF0000; background:#BDBDBD; color:#000000; text-align:left; padding:4px; width:100%; height:280px; overflow:auto; \">");
//       readScenario();
//       showScenario($curStep);
//       echo("</div>\n"); 
//     }
  else 
    {
      echo(" Log events ");
      //echo(" (<a href=$path&ac=winserlog&x=sce>Scenario</a>)");
      echo(" (<a href=$path&ac=winserlog&x=ser>Serial Interface</a>)");
      echo("<div id=\"logList\" style=\"margin-right:1px;margin-bottom:5px;float:left; border:solid 1px #000000; background:#AFCAE6; color:#000000; text-align:left; padding:4px; width:100%; height:280px; overflow:auto; \">");
      showStep($curStep);
      echo("</div>");
    }
}
//********************************************
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
//********************************************
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
//********************************************
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
//********************************************
function viking_7_faq($sys_id)
{
  global $par,$fn;
  $tFile = $fn['faq'];

  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}
//********************************************
function viking_7_help($sys_id)
{
  global $par,$fn;
  $tFile = $fn['help'];
  $curStep   = $par['a7_cur_step'];

  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}
//********************************************
function viking_7_about($sys_id)
{
  global $par,$fn;
  $tFile = $fn['about'];

  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}
//********************************************
function viking_7_register($sys_id)
{
  global $par,$fn;
  $tFile = $fn['register'];

  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}
//********************************************
function viking_7_start($sys_id)
{
  global $par,$fn;
  $tFile = $fn['start'];

  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}
//********************************************
function viking_7_news($sys_id)
{
  global $par,$fn;
  $tFile = $fn['news'];

  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}

//********************************************
function viking_7_tutorial($sys_id)
{
  global $par,$fn;
  $tFile = $fn['tutorial'];

  $len = readAnyFile(1,$tFile);
  showAnyFile($len);
}
//********************************************
function viking_7_loginCounter($sys_id)
{
  global $par;
  $temp = $par['login_counter'];
  echo("[$temp]");
}
//********************************************
function viking_7_accessControl($sys_id)
{
  global $par;
  $path    = $par['path'];
  $user    = $par['user'];

  if($user == 'admin')
    echo(" <a href=$path&ac=access_control>Access Control</a>");
}
//********************************************
function viking_7_error($sys_id)
{
  global $par,$fn;
  $path   = $par['path'];
  $user   = $par['user'];
  $rowNumber = $par['a7_row_number'];

  //if($user)echo("[Webuino Version 2011-12-22] Any errors (compile,exec and servuino) will be shown here<br/>");
  if($user)
    {
      echo("<div id=\"anyFile\" style=\"font-family:Courier,monospace; font-size:11px;float:left; border : solid 1px #000000; background : #F3F781; color : #000000;  text-align:left; padding : 3px; width :100%; height:250px; overflow : auto; margin-left:0px; margin-bottom:10px;line-height:1.0em; \">\n");

      $par['a7_row_number'] = 0;

      $file = $fn['g++'];
      $len = readAnyFile(2,$file);
      if($len)showAnyFile($len);
      else
	echo("No compilation errors<br/>");

      $file = $fn['exec'];
      $len = readAnyFile(2,$file);
      if($len)showAnyFile($len);
      else
	echo("No execution errors<br/>");

      $file = $fn['error'];
      $len = readAnyFile(2,$file);
      if($len)showAnyFile($len);
      else
	echo("No servuino errors<br/>");

      $par['a7_row_number'] = $rowNumber;

      $temp = $par['a7_ready'];
      echo($temp);
      echo("</div>\n");
    }
}

//********************************************
function viking_7_source($sys_id)
{
  global $par,$fn,$content,$stepLine;
  $path   = $par['path'];
  $user   = $par['user'];

  $curSketch = $par['a7_cur_source'];
  $curStep   = $par['a7_cur_step'];
  //$curSimLength = $par['a7_cur_sim_len'];

  if($user)
    {
      $nrows = readAnyFile(1,$curSketch);
      readSourceLine();
      $curLine = $stepLine[$curStep];

      $from = $curLine-12;
      $to = $curLine+12;
      if($from < 0)$from = 1;
      if($to > $nrows) $to = $nrows;

      echo("<b>Source Debug Information ($curLine)</b>");
      echo("<div id=\"anyFile\" style=\"font-family:Courier,monospace; font-size:11px;float:left; border : solid 1px #000000; background : #F3F781; color : #000000;  text-align:left; padding : 3px; width :100%; height:280px; overflow : auto; margin-left:0px; margin-bottom:10px;line-height:1.0em; \">\n");

      for($ii=$from;$ii<=$to;$ii++)
	{
	  if($ii==$curLine)echo("<b>");
	  echo("$ii $content[$ii]<br/>");
	  if($ii==$curLine)echo("</b>");
	}
      
      echo("</div>\n");
    }
  else
    echo("$notLoggedIn");
}

//********************************************
function viking_7_data($sys_id)
{
  global $par,$fn;
  $path    = $par['path'];
  $sid     = $par['a7_sid'];
  $user    = $par['user'];
  $curFile = $par['a7_cur_file'];
  $curPV   = $par['pv'];

  echo("<div><table><tr><td>");
  echo("<form name=\"f_sel_win\" action=\"$path&pv_mem=$curPV\" method=\"post\" enctype=\"multipart/form-data\">\n ");
  echo("<input type=\"hidden\" name=\"action\" value=\"select_file\">\n");
  echo("<select name=\"file\">");
  if($user == 'admin')
    {
      $selected = "";$temp = $fn['application'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Application</option>");

      $selected = "";$temp = $fn['userstat'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>User Stat</option>");

      $selected = "";$temp = $fn['start'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Start</option>");

      $selected = "";$temp = $fn['help'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Help</option>");

      $selected = "";$temp = $fn['news'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>News</option>");

      $selected = "";$temp = $fn['about'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>About</option>");

      $selected = "";$temp = $fn['tutorial'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Tutorial</option>");

      $selected = "";$temp = $fn['faq'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>FAQ</option>");

      $selected = "";$temp = $fn['pinmod'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Pin Mode</option>");

      $selected = "";$temp = $fn['custom'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Custom Log</option>");

      $selected = "";$temp = $fn['event'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"  $selected>Event Log</option>");

      $selected = "";$temp = $fn['pinrw'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Pin RW</option>");

      $selected = "";$temp = $fn['digval'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Digital Value</option>");

      $selected = "";$temp = $fn['anaval'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Analog Value</option>");

      $selected = "";$temp = $fn['serial'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Serial Log</option>");

      $selected = "";$temp = $fn['code'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Code Log</option>");

      $selected = "";$temp = $fn['debug_ino'];if($curFile == $temp)$selected = 'selected';
      echo("<option value=\"$temp\"   $selected>Debug Ino</option>");
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
      if($curFile == $fn['news'] || $curFile == $fn['tutorial'] || $curFile == $fn['faq'] || $curFile == $fn['start'] || $curFile == $fn['help'] || $curFile == $fn['about'] || $curFile == $fn['register'] )echo("<input type =\"submit\" name=\"submit_select\" value=\"".T_EDIT."\">\n");
    }

  echo("</form></td>");
  echo("</table></div>");
}

//********************************************
function viking_7_only_load($sys_id)
{
  global $par,$fn,$upload, $editSFlag,$curEditFlag;
  $path      = $par['path'];
  $sid       = $par['a7_sid'];
  $user      = $par['user'];

  $curSource = $par['a7_cur_source'];
  $selSource = $par['a7_sel_source'];

  $curScenario = $par['a7_cur_scenario'];
  $selScenario = $par['a7_sel_scenario'];

  $curSimLen = $par['a7_cur_sim_len'];
  $ready     = $par['a7_ready'];
  
  echo("<div style=\"float:left; width : 100%; background :white; text-align: left;margin-left:20px; margin-bottom:20px;\">");
  if($user)
    {
      $fTemp = basename($curSource);
      echo("<hr><b>Loaded Sketch:</b> $fTemp<br/>");
      $fTemp = basename($curScenario);
      echo("<b>Loaded Scenario:</b> $fTemp<br/>");


      $fTemp = basename($selSource);
      echo("<b>Selected Sketch:</b> $fTemp<br/>");
      $fTemp = basename($selScenario);
      echo("<b>Selected Scenario:</b> $fTemp<hr>");

      echo("<table border=\"0\"><tr>");      
      echo("<form name=\"f_load\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
      echo("<input type=\"hidden\" name=\"action\" value=\"set_load\">\n");

      $tFile = $fn['list'];
      $syscom = "ls $upload/*pde $upload/*ino> $tFile;";
      system($syscom);
      echo("<td>Sketch</td>");
      $nSketches = formSelectFile("Sketch ","sketch",$tFile,$selSource,$upload);
      echo("</tr>");

      $tFile = $fn['list'];
      $syscom = "ls $upload/*.scn > $tFile;";
      system($syscom);
      echo("<tr><td>Scenario</td>");
      $nSketches = formSelectFile("Scenario File ","scenario",$tFile,$selSource,$upload);
      echo("</tr>");
      echo("<input type=\"radio\" name=\"scenario_source\" value=\"internal\" /> Scenario from Sketch");
      echo("<br/><input type=\"radio\" name=\"scenario_source\" value=\"external\" checked /> Scenario from Scenario File");
      echo("<tr><td>Simulation Length</td><td><input type=\"text\" name=\"sim_len\" value=\"$curSimLen\" size=\"5\"></td></tr>");

      echo("</table><br/>");

      echo("<input type =\"submit\" name=\"submit_load_del\" value=\"".T_LOAD."\">");
      //echo("<input type =\"submit\" name=\"submit_load_del\" value=\"".T_SELECT."\">");
      //echo("<input type =\"submit\" name=\"submit_load_del\" value=\"".T_EDIT."\">");
      
      echo("</form>");
    }
  echo("</div>");
}

//********************************************
function viking_7_create($sys_id)
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
      $tFile = $fn['list'];
      echo("<h4>Create new sketch</h4>");

      $syscom = "ls $upload/*.pde $upload/*.ino  > $tFile;";
      system($syscom);
      $nSketches = listFiles("Sketches ",$tFile);
      echo("<hr>");
      echo("<table border=\"0\"><tr>");      
      echo("<form name=\"f_load_new_sketch\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
      echo("<input type=\"hidden\" name=\"action\" value=\"set_new_sketch\">");
      echo("<td>New Sketch Name</td><td><input type=\"text\" name=\"new sketch_name\" value=\"$user.ino\" size=\"20\"></td>");
      echo("<td><input type =\"submit\" name=\"submit_new_sketch\" value=\"".T_TEMPLATE."\"></td>");
      echo("<td><input type =\"submit\" name=\"submit_new_sketch\" value=\"".T_EXAMPLE."\"></td>");
      echo("</tr></table><br/>");
      echo("</form>");



      echo("<h4>Create new scenario</h4>");

      $syscom = "ls $upload/*.scn > $tFile;";
      system($syscom);
      $nScenarios = listFiles("Scenarios ",$tFile);
      echo("<hr>");
      echo("<table border=\"0\"><tr>");      
      echo("<form name=\"f_load_new_scenario\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
      echo("<input type=\"hidden\" name=\"action\" value=\"set_new_scenario\">");
      echo("<td>New Scenario Name</td><td><input type=\"text\" name=\"new scenario_name\" value=\"$user.scn\" size=\"20\"></td>");
      echo("<td><input type =\"submit\" name=\"submit_new_scenario\" value=\"".T_TEMPLATE."\"></td>");
      echo("<td><input type =\"submit\" name=\"submit_new_scenario\" value=\"".T_EXAMPLE."\"></td>");
      echo("</tr></table><br/>");
      echo("</form>");

      //echo("<hr>");
    }
  echo("</div>");
}
//********************************************
function viking_7_copy($sys_id)
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
      $tFile = $fn['list'];
      $syscom = "ls $upload > $tFile;";
      system($syscom);
      echo("<h4>Copy file</h4>");
      echo("<table border=\"0\"><tr>");      
      echo("<form name=\"f_copy_file\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
      echo("<input type=\"hidden\" name=\"action\" value=\"copy_file\">");
      echo("<td>Source File");
      $nSketches = formSelectFile("Source File","copy_source",$tFile,$selSource,$upload);
      echo("</td></tr><tr>");
      echo("<td>New File Name(.ino or .scn) </td><td><input type=\"text\" name=\"copy_file_name\" value=\"$user.ino\" size=\"20\"></td>");
      echo("<tr><td><input type =\"submit\" name=\"submit_copy_sketch\" value=\"".T_COPY."\"></td>");
      echo("</tr></table><br/>");
      echo("</form>");


      //echo("<hr>");
    }
  echo("</div>");
}
//********************************************
function viking_7_delete($sys_id)
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
      $tFile = $fn['list'];
      $syscom = "ls $upload/*.pde  $upload/*.ino > $tFile;";
      system($syscom);
      deleteFiles("Sketches:",$tFile);

      $tFile = $fn['list'];
      $syscom = "ls $upload/*.scn > $tFile;";
      system($syscom);
      deleteFiles("Scenarios:",$tFile);
    }
  echo("</div>");
}
//********************************************
function viking_7_upload($sys_id)
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
      $tFile = $fn['list'];
      $syscom = "ls $upload/*.pde  $upload/*.ino> $tFile;";
      system($syscom);
      $nSketches = listFiles("Account Sketches ",$tFile);

      $syscom = "ls $upload/*.scn > $tFile;";
      system($syscom);
      $nScenarios = listFiles("Account Scenarios ",$tFile);

      if($nSketches >= 10 || $nSketches >= 10 )
	{
	  echo("<h2>You have reached the limit of sketches/scenarios stored. Delete some of them.</h2>");
	}
      else
	{
	  echo("<hr><h4>You have $nSketches sketches and $nScenarios scenarios stored. Limit is 10 + 10.</h4><br/><form name=\"f_upload_source\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\"> ");
	  echo("<input type=\"hidden\" name=\"action\" value=\"upload_source\">");
	  echo("<input type=\"file\" name=\"import_file\" value=\"\">\n");
	  echo("<input type =\"submit\" name=\"submit_file\" value=\"".T_UPLOAD_FILE."\">");
	  echo("</form><br/>");
	}
      

    }
  echo("</div>");
}
//********************************************
function viking_7_edit_sketch($sys_id)
{
  global $par,$fn,$upload, $editSFlag,$curEditFlag;
  $path      = $par['path'];
  $sid       = $par['a7_sid'];
  $user      = $par['user'];
  $curSource = $par['a7_cur_source'];
  $selSource = $par['a7_sel_source'];
  $selScenario = $par['a7_sel_scenario'];
  $curSimLen = $par['a7_cur_sim_len'];
  $ready     = $par['a7_ready'];
  
  echo("<div style=\"float:left; width : 100%; background :white; text-align: left;margin-left:20px; margin-bottom:20px;\">");
  if($user)
    {
      $fTemp = basename($curSource);
      echo("<hr><b>Loaded Sketch:</b> $fTemp<br/>");
      $fTemp = basename($curScenario);
      echo("<b>Loaded Scenario:</b> $fTemp<hr>");

      $fTemp = basename($selSource);
      echo("<b>Selected Sketch:</b> $fTemp<br/>");
      $fTemp = basename($selScenario);
      echo("<b>Selected Scenario:</b> $fTemp<hr>");
      
      echo("<table border=\"0\"><tr>");      
      echo("<form name=\"f_load\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
      echo("<input type=\"hidden\" name=\"action\" value=\"manage_sketch\">\n");

      $tFile = $fn['list'];
      $syscom = "ls $upload/*.pde  $upload/*.ino> $tFile;";
      system($syscom);
      echo("<td>");
      $nSketches = formSelectFile("Sketches ","sketch",$tFile,$selSource,$upload);
      //echo("<input type =\"submit\" name=\"submit_load_del\" value=\"".T_SELECT."\">");
      echo("</td><td><input type =\"submit\" name=\"submit_order\" value=\"".T_EDIT_SKETCH."\">");
      echo("</td></tr></table><br/>");
      echo("</form>");

    }  
  echo("</div>");
}
//********************************************
function viking_7_edit_scenario($sys_id)
{
  global $par,$fn,$upload, $editSFlag,$curEditFlag;
  $path      = $par['path'];
  $sid       = $par['a7_sid'];
  $user      = $par['user'];
  $curSource = $par['a7_cur_source'];
  $selSource = $par['a7_sel_source'];
  $selScenario = $par['a7_sel_scenario'];
  $curSimLen = $par['a7_cur_sim_len'];
  $ready     = $par['a7_ready'];
  
  echo("<div style=\"float:left; width : 100%; background :white; text-align: left;margin-left:20px; margin-bottom:20px;\">");
  if($user)
    {
      $fTemp = basename($curSource);
      echo("<hr><b>Loaded Sketch:</b> $fTemp<br/>");
      $fTemp = basename($curScenario);
      echo("<b>Loaded Scenario:</b> $fTemp<hr>");

      $fTemp = basename($selSource);
      echo("<b>Selected Sketch:</b> $fTemp<br/>");
      $fTemp = basename($selScenario);
      echo("<b>Selected Scenario:</b> $fTemp<hr>");
      
      echo("<table border=\"0\"><tr>");      
      echo("<form name=\"f_load\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
      echo("<input type=\"hidden\" name=\"action\" value=\"manage_scenario\">\n");

      $tFile = $fn['list'];
      $syscom = "ls $upload/*.scn > $tFile;";
      system($syscom);
      echo("<td>");
      $nFiles = formSelectFile("Scenarios ","scenario",$tFile,$selScenario,$upload);
      //echo("<input type =\"submit\" name=\"submit_load_del\" value=\"".T_SELECT."\">");
      echo("</td><td><input type =\"submit\" name=\"submit_order\" value=\"".T_EDIT_SCENARIO."\">");
      echo("</td></tr></table><br/>");
      echo("</form>");

    }  
  echo("</div>");
}

//********************************************
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

  echo("<br/>Set Scenario Breakpoints at step: $curStep<br/><br/>");
  echo("<form name=\"f_set_dig_scenario\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\">");
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
  //echo("The value is valid from step $curStep to next breakpoint<br/>"); 

//   echo("<hr><b>Analog Pin Values at step $curStep</b>");
//   echo("<table border=0><tr>");
//   $count = 0;
//   for($ii=0;$ii<$aPins;$ii++)
//     {
//       if($pinValueA[$ii])
// 	{
// 	  $count++;
// 	  echo("<td>$ii =</td>");
// 	  echo("<td>$pinValueA[$ii]|</td>");
// 	  if($count == 8)echo("</tr><tr>");
// 	}
//     }
//   echo("</tr></table>");
    
//   echo("<br/><b>Digital Pin Values at step $curStep</b>");
//   echo("<table border=0><tr>");
//   $count = 0;
//   for($ii=0;$ii<$dPins;$ii++)
//     {
//       if($pinValueD[$ii])
// 	{
// 	  $count++;
// 	  echo("<td>$ii=</td>");
// 	  echo("<td>$pinValueD[$ii]</td>");
// 	  if($count == 8)echo("</tr><tr>");
// 	}
//     }
//   echo("</tr>");
//   echo("</table>");
    
  echo("</div>");
}

//********************************************
function viking_7_downloadSketch($sys_id)
{
  global $fn,$upload;

  $tFile = $fn['list'];
  $syscom = "ls $upload > $tFile;";
  system($syscom);
  $nSketches = linkFile($tFile,$selSource,$upload);
}

//********************************************
function viking_7_graph_status($sys_id)
{
  global $par,$scenario,$g_readValue,$g_readPin,$g_readType;
  global $valueInPinD, $valueOutPinD, $valueInPinA,$pinModeD,$stepRead, $stepLoop;
  global $x_pinMode,$x_pinDigValue,$x_pinAnaValue,$x_pinRW;
  global $simulation;

  $path       = $par['path'];
  $curStep    = $par['a7_cur_step'];
  $curSimLen  = $par['a7_cur_sim_len'];
  $curPinNo   = $par['a7_cur_pin_no'];
  $curPinType = $par['a7_cur_pin_type'];
  $curBoardType = $par['a7_cur_board_type'];
  $memPV      = $par['pv_mem'];
  $curPV      = $par['pv'];

  $dPins      = $par['a7_cur_board_digpins'];
  $aPins      = $par['a7_cur_board_anapins'];
  $tPins      = $dPins + $aPins;

  $x_max = $curSimLen;

  $y_minD = 0;
  $y_maxD = $dPins;

  $y_minA = 0;
  $y_maxA = $aPins;


  readScenario();

  $x_min = $curStep-50;
  if($x_min<=0)$x_min = 1;
  //echo(" (<a href=$path&pv=large_dig_graph&pv_mem=$curPV>Wide Read Digital Graph</a>)");
//   if($par['a7_cur_board_type']=="boardMEGA")
//     $winSize = "500px";
//   else
  $winSize = "300px";
  

  echo("<div>");
  //echo("$curPV  && $curBoardType");
  if($curPV != 'large_graph' && $curBoardType=="boardMega")
    {
      echo(" (<a href=$path&pv=large_graph&pv_mem=$curPV>Large Window</a>)");
      $winSize = "300px";
    }
  else if($curBoardType=="boardMega")
    {
      echo("<a href=$path&pv=$memPV>Small Window</a>");
      $winSize = "600px";
    }
   echo("</div>");

  //echo("<a href=\"JavaScript:newPopup($path);\">Open a popup window</a>");

  echo("<div id=\"graph\" style=\"font-family:Courier,monospace; font-size:11px;float:left; border : solid 1px #000000; background : #A9BCF5; color : #000000;  text-align:left; padding : 3px; width :100%; height:$winSize; overflow : auto; margin-left:0px; margin-bottom:10px;line-height:1.0em; \">");
  
  $event = $simulation[$curStep];
  echo("<b>Status Graph </b>Step: $event<br/><br/>");
  $pin   = $g_readPin[$curStep];
  $value = $g_readValue[$curStep];
  $type  = $g_readType[$curStep];
  if($type==1)$anadig = "Analog";
  else
    $anadig = "Digital";
  if($pin || $value)echo("Step:$curStep $anadig Pin:$pin Value:$value<br/>");
  echo("&nbsp;&nbsp;&nbsp;&nbsp;");
  for($xx=$x_min;$xx<=$x_max;$xx++)
    {
      $done = 0;
      for($pin = 0;$pin < $tPins; $pin++)
	{
	  if($x_pinRW[$pin][$xx]==S_READ)
	    {
	      echo("<a href=$path&ac=step&x=$xx>R</a>");
	      $done = 1;
	    }
	  if($x_pinRW[$pin][$xx]==S_WRITE)
	    {
	      echo("<a href=$path&ac=step&x=$xx>W</a>");
	      $done = 1;
	    }
	}

       if($stepLoop[$xx] != $stepLoop[$xx-1])
	 {
	   echo("<a href=$path&ac=step&x=$xx>+</a>");
	   $done = 1;
	 }
       if($done == 0)echo("&nbsp;");
    }
  echo("<br/>");
  
  // Analog Pins
  for($yy=$y_maxA-1;$yy>=$y_minA;$yy--)
    {

      $star = "*";
      vprintf("A%02s:",$yy);
      //vprintf("<a href=$path&ac=setApin&x=$yy>A%02s</a>:",$yy);
      for($xx=$x_min;$xx<=$x_max;$xx++)
	{

	  if($xx==$curStep)
	    echo($star);
	  else if($valueInPinA[$yy][$xx] == 1)
	    echo("*");
	  else
	    {
	      if($xx%10 == 0)echo("|");
	    else
	      echo("&nbsp;");
	    }
	}
      echo("<br/>");
    }
  for($xx=$x_min;$xx<=$x_max;$xx++)echo("_");echo("<br/>");
  // Digital Pins
  for($yy=$y_maxD-1;$yy>=$y_minD;$yy--)
    {
      if($x_pinMode[$yy][$curStep]      == INPUT)    $star = "I";
      else if($x_pinMode[$yy][$curStep] == OUTPUT)   $star = "O";
      else if($x_pinMode[$yy][$curStep] == I_FALLING)$star = "F";
      else if($x_pinMode[$yy][$curStep] == I_RISING) $star = "R";
      else if($x_pinMode[$yy][$curStep] == I_CHANGE) $star = "C";
      else if($x_pinMode[$yy][$curStep] == I_LOW)    $star = "L";
      else
	$star = "&nbsp;";

      //$benny = $valuePinD[$yy][$curStep];

      //if($star == "O" || $star == "&nbsp;")
	vprintf("D%02s:",$yy);
	//else
	//vprintf("<a href=$path&ac=setDpin&x=$yy>D%02s</a>:",$yy);

      for($xx=$x_min;$xx<=$x_max;$xx++)
	{
	  if($xx==$curStep)
	    echo($star);
	  else if($x_pinDigValue[$yy][$xx] == HIGH)
	    echo("o");
	  else if($x_pinDigValue[$yy][$xx] == LOW)
	    echo(".");


// 	  else if($valueInPinD[$yy][$xx] == 1)
// 	    echo("o");
// 	  else if($valueOutPinD[$yy][$xx] == HIGH)
// 	    echo(".");
	  else
	    {
	      if($xx%10 == 0)echo("|");
	      else
		echo("&nbsp;");
	    }
	}
      echo("<br/>");
    }

  echo("&nbsp;&nbsp;&nbsp;&nbsp;");
  for($xx=$x_min;$xx<=$x_max;$xx++)
    {
      if($curStep==$xx)
	echo("^");
      else if($xx%10 == 0)
	echo("|");
      else
	//echo("_");
	vprintf("<a href=$path&ac=step&x=$xx>=</a>",$xx);
    }
  echo("<br/>");
  echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  for($xx=$x_min;$xx<=$x_max;$xx++)
    {
      if($xx%10 == 0 && $xx-$x_min > 2)
	vprintf("<a href=$path&ac=step&x=$xx>%04s</a>",$xx);
      else if($xx%10 < 7)
	echo("&nbsp;");
    }
  echo("<br/>");

  echo("</div>");
}
//********************************************
function viking_7_graph_scenario($sys_id)
{
  global $par,$scenario,$g_readValue,$g_readPin,$g_readType;
  global $valueInPinD, $valueOutPinD, $valueInPinA,$pinModeD,$stepRead, $stepLoop;
  global $x_pinMode,$x_pinDigValue,$x_pinAnaValue,$x_pinRW;
  global $simulation;

  $path       = $par['path'];
  $curStep    = $par['a7_cur_step'];
  $curSimLen  = $par['a7_cur_sim_len'];
  $curPinNo   = $par['a7_cur_pin_no'];
  $curPinType = $par['a7_cur_pin_type'];
  $curBoardType = $par['a7_cur_board_type'];
  $memPV      = $par['pv_mem'];
  $curPV      = $par['pv'];

  $dPins      = $par['a7_cur_board_digpins'];
  $aPins      = $par['a7_cur_board_anapins'];
  $tPins      = $dPins + $aPins;

  $x_max  = $curSimLen;

  $y_minD = 0;
  $y_maxD = $dPins;

  $y_minA = 0;
  $y_maxA = $aPins;


  readScenario();

  $x_min = $curStep-50;
  if($x_min<=0)$x_min = 1;

  $winSize = "300px";
  

  echo("<div>");
  //echo("$curPV  && $curBoardType");
  if($curPV != 'large_graph' && $curBoardType=="boardMega")
    {
      echo(" (<a href=$path&pv=large_graph&pv_mem=$curPV>Large Window</a>)");
      $winSize = "300px";
    }
  else if($curBoardType=="boardMega")
    {
      echo("<a href=$path&pv=$memPV>Small Window</a>");
      $winSize = "600px";
    }
   echo("</div>");

  //echo("<a href=\"JavaScript:newPopup($path);\">Open a popup window</a>");

  echo("<div id=\"graph\" style=\"font-family:Courier,monospace; font-size:11px;float:left; border : solid 1px #000000; background : #A9BCF5; color : #000000;  text-align:left; padding : 3px; width :100%; height:$winSize; overflow : auto; margin-left:0px; margin-bottom:10px;line-height:1.0em; \">");
  
  $event = $simulation[$curStep];
  echo("<b>Scenario Graph </b>Step:$event<br/><br/>");
  $pin   = $g_readPin[$curStep];
  $value = $g_readValue[$curStep];
  $type  = $g_readType[$curStep];
  if($type==1)$anadig = "Analog";
  else
    $anadig = "Digital";
  if($pin || $value)echo("Step:$curStep $anadig Pin:$pin Value:$value<br/>");
  echo("&nbsp;&nbsp;&nbsp;&nbsp;");
  for($xx=$x_min;$xx<=$x_max;$xx++)
    {
      $done = 0;
      for($pin = 0;$pin < $tPins; $pin++)
	{
	  if($x_pinRW[$pin][$xx]==S_READ)
	    {
	      echo("<a href=$path&ac=step&x=$xx>R</a>");
	      $done = 1;
	    }
	  if($x_pinRW[$pin][$xx]==S_WRITE)
	    {
	      echo("<a href=$path&ac=step&x=$xx>W</a>");
	      $done = 1;
	    }
	}

       if($stepLoop[$xx] != $stepLoop[$xx-1])
	 {
	   echo("<a href=$path&ac=step&x=$xx>+</a>");
	   $done = 1;
	 }
       if($done == 0)echo("&nbsp;");
    }
  echo("<br/>");
  
  // Analog Pins
  for($yy=$y_maxA-1;$yy>=$y_minA;$yy--)
    {

      $star = "*";
      vprintf("A%02s:",$yy);
      //vprintf("<a href=$path&ac=setApin&x=$yy>A%02s</a>:",$yy);
      for($xx=$x_min;$xx<=$x_max;$xx++)
	{
	  if($valueInPinA[$yy][$xx] > 0)
	    echo("o");
	  else
	    {
	      if($xx%10 == 0)echo("|");
	    else
	      echo("&nbsp;");
	    }
	}
      echo("<br/>");
    }
  for($xx=$x_min;$xx<=$x_max;$xx++)
    {
      if($xx-4==$curStep)
	echo("^");
      else
	echo("_");
    }
  echo("<br/>");

  // Digital Pins
  for($yy=$y_maxD-1;$yy>=$y_minD;$yy--)
    {
      	vprintf("D%02s:",$yy);

      for($xx=$x_min;$xx<=$x_max;$xx++)
	{
	  if($valueInPinD[$yy][$xx] == HIGH)
	    echo("o");
	  else
	    {
	      if($xx%10 == 0)echo("|");
	      else
		echo("&nbsp;");
	    }
	}
      echo("<br/>");
    }

  echo("&nbsp;&nbsp;&nbsp;&nbsp;");
  for($xx=$x_min;$xx<=$x_max;$xx++)
    {
      if($curStep==$xx)
	echo("^");
      else if($xx%10 == 0)
	echo("|");
      else
	vprintf("<a href=$path&ac=step&x=$xx>=</a>",$xx);
    }
  echo("<br/>");
  echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  for($xx=$x_min;$xx<=$x_max;$xx++)
    {
      if($xx%10 == 0 && $xx-$x_min > 2)
	vprintf("<a href=$path&ac=step&x=$xx>%04s</a>",$xx);
      else if($xx%10 < 7)
	echo("&nbsp;");
    }
  echo("<br/>");

  echo("</div>");
}

//********************************************
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
      echo("<td>");
      echo("<input type =\"submit\" name=\"submit_file\" value=\"".T_APPLY."\"></td>\n");
      echo("How are you going to use Simuino?<br/><textarea name=\"letter\" cols=50 rows=3>(Very short description here)</textarea>");
      echo("</form></tr>");
      echo("</table>");
    }
  else if($application == 1)
    {
      echo("<h2>Thank you for your interest!<br/>Your account information will be sent to you within 24 hours.</h2>");  
    }
      
  echo("</div>");

}

//********************************************
function viking_7_draw_void($sys_id)
{
  echo("<script type= \"text/javascript\">");
  echo("function draw(){}");
  echo("</script>");
}
//********************************************
function viking_7_draw($sys_id)
{
  global $par,$coords;
  global $wBoard,$hBoard;
  global $boardId,$boardDigPins,$boardAnaPins,$boardTotPins;
  global $digX,$digY,$anaX,$anaY,$resetX,$resetY;
  global $TXledX,$TXledY,$onOffX,$onOffY,$led13X,$led13Y;
  global $sketchNameX,$sketchNameY,$helpX,$helpY,$help2X,$help2Y;
  global $pinModeD,$pinStatusD,$pinStatusA,$serial;

  global $x_pinMode,$x_pinDigValue,$x_pinAnaValue,$x_pinRW;

  $path   = $par['path'];
  $sid    = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user   = $par['user'];

  $curSketchName = $par['a7_cur_sketch_name'];
  $curStep       = $par['a7_cur_step'];
  $board         = $par['a7_cur_board_type'];
  $boardDigPins  = $par['a7_cur_board_digpins'];
  $boardAnaPins  = $par['a7_cur_board_anapins'];
  $boardTotPins  = $boardAnaPins + $boardDigPins;

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
      

  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      $pinModeD[$ii]   = $x_pinMode[$ii][$curStep];
      $pinStatusD[$ii] = $x_pinDigValue[$ii][$curStep];
      $pinStatusA[$ii] = $x_pinAnaValue[$ii][$curStep];
    }

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
      print("ctx.closePath();");
      print("ctx.fill();");
    }

  // Digital Pins Mode
  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      //if($pinModeD[$ii]!=0)
      //{
	  if($pinModeD[$ii]==OUTPUT)print($green);  //OUTPUT
	  if($pinModeD[$ii]==INPUT)print($red);     //INPUT
	  if($pinModeD[$ii]==RX)print($white);      // RX
	  if($pinModeD[$ii]==TX)print($grey);       // TX
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
	  //}
    }

  // Digital Pins Status
  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      if($pinStatusD[$ii]!=0)
	{
	  $dotSize = 4;
// 	  if($x_pinRW[$ii][$curStep] == S_READ)$dotSize = 2;
// 	  if($x_pinRW[$ii][$curStep] == S_WRITE)$dotSize = 6;
	  if($pinStatusD[$ii]==HIGH)print($yellow);  // HIGH
	  if($pinStatusD[$ii]==LOW)print($black);
	  if($pinStatusD[$ii]==PWM)print($green);
	  print("ctx.beginPath();");
	  print("ctx.arc($digY[$ii], $digX[$ii], $dotSize, 0, Math.PI*2, true);");
	  if($ii == 13 && $pinStatusD[13]>0)
	    print("ctx.rect($led13Y-4, $led13X-3,8, 5);");
	  print("ctx.closePath();");
	  print("ctx.fill();");
	}
    }

  // Analog Pins Status
  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      if($pinStatusA[$ii]!=0)
	{
	  $dotSize = 4;
// 	  if($x_pinRW[$ii][$curStep] == S_READ)$dotSize = 2;
// 	  if($x_pinRW[$ii][$curStep] == S_WRITE)$dotSize = 6;
	  $jj = $ii-$boardDigPins;
	  print($red); // reading
	  print("ctx.beginPath();");
	  print("ctx.arc($anaY[$jj], $anaX[$jj], $dotSize, 0, Math.PI*2, true);");
	  print("ctx.closePath();");
	  print("ctx.fill();");
	}
    }

  // RW Pins Status
  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      if($x_pinRW[$ii][$curStep]!=0)
	{
	  $dotSize = 4;
	  if($x_pinRW[$ii][$curStep] == S_READ)$dotSize = 2;
	  if($x_pinRW[$ii][$curStep] == S_WRITE)$dotSize = 6;
	  print($green); // reading
	  print("ctx.beginPath();");
	  if($ii < $boardDigPins)
	    print("ctx.arc($digY[$ii], $digX[$ii], $dotSize, 0, Math.PI*2, true);");
	  else
	    {
	      $jj = $ii-$boardDigPins;
	      print("ctx.arc($anaY[$jj], $anaX[$jj], $dotSize, 0, Math.PI*2, true);");
	    }

	  print("ctx.closePath();");
	  print("ctx.fill();");
	}
    }

  // Write Sketch Name on IC
  print("ctx.font = \"15pt Calibri\";");
  print("ctx.fillStyle = \"#FF0000\";");
  print("ctx.fillText(\"$curSketchName\",$sketchNameY,$sketchNameX);");

  // Help
  print($yellow);  // HIGH
  print("ctx.beginPath();");
  print("ctx.arc($helpY, $helpX, 4, 0, Math.PI*2, true);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"HIGH\",$helpY+7,$helpX+4);");
  print($black);  // LOW
  $helpX = $helpX + 10;
  print("ctx.beginPath();");
  print("ctx.arc($helpY, $helpX, 4, 0, Math.PI*2, true);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"LOW\",$helpY+7,$helpX+4);");
  print($green);  // PWM, Analog Write
  $helpX = $helpX + 10;
  print("ctx.beginPath();");
  print("ctx.arc($helpY, $helpX, 4, 0, Math.PI*2, true);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"Write,PWM\",$helpY+7,$helpX+4);");
  print($red);  // ANALOG READ
  $helpX = $helpX + 10;
  print("ctx.beginPath();");
  print("ctx.arc($helpY, $helpX, 4, 0, Math.PI*2, true);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"Analog Read\",$helpY+7,$helpX+4);");
  

  print($green);  // OUTPUT
  print("ctx.beginPath();");
  print("ctx.rect($help2Y, $help2X,8, 4);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"Output\",$help2Y+10,$help2X+5);");
  $help2X = $help2X + 10;
  print($red);  // INPUT
  print("ctx.beginPath();");
  print("ctx.rect($help2Y, $help2X,8, 4);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"Input\",$help2Y+10,$help2X+5);");
  $help2X = $help2X + 10;
  print($white);  // RX
  print("ctx.beginPath();");
  print("ctx.rect($help2Y, $help2X,8, 4);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"RX\",$help2Y+10,$help2X+5);");
  $help2X = $help2X + 10;
  print($grey);  // TX
  print("ctx.beginPath();");
  print("ctx.rect($help2Y, $help2X,8, 4);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"TX\",$help2Y+10,$help2X+5);");
  $help2X = $help2X + 10;
  print($blue);  // CHANGE
  print("ctx.beginPath();");
  print("ctx.rect($help2Y, $help2X,8, 4);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"Change\",$help2Y+10,$help2X+5);");
  $help2X = $help2X + 10;
  print($orange);  // RISING
  print("ctx.beginPath();");
  print("ctx.rect($help2Y, $help2X,8, 4);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"Rising\",$help2Y+10,$help2X+5);");
  $help2X = $help2X + 10;
  print($yellow);  // FALLING
  print("ctx.beginPath();");
  print("ctx.rect($help2Y, $help2X,8, 4);");
  print("ctx.closePath();");
  print("ctx.fill();");
  print("ctx.font = \"8pt Calibri\";");
  print("ctx.fillStyle = \"#000000\";");
  print("ctx.fillText(\"Falling\",$help2Y+10,$help2X+5);");


  echo(" }");
  echo(" }");
  
  echo("function ajaxArduino(str)\n");
  echo("{\n");
  echo("  if (str.length==0)\n");
  echo("  {\n");
  echo("    document.getElementById(\"ajax_7\").innerHTML=\"\";\n");
  echo("    return;\n");
  echo("  }\n");
  echo("  if (window.XMLHttpRequest)\n");
  echo("  {");
  echo("    xmlhttp=new XMLHttpRequest();\n");
  echo("  }\n");
  echo("  else\n");
  echo("  {\n");
  echo("    xmlhttp=new ActiveXObject(\"Microsoft.XMLHTTP\");\n");
  echo("  }\n");
  echo("  xmlhttp.onreadystatechange=function()\n");
  echo("  {\n");
  echo("    if (xmlhttp.readyState==4 && xmlhttp.status==200)\n");
  echo("    {\n");
  echo("      document.getElementById(\"ajax_7\").innerHTML=xmlhttp.responseText;\n");
  echo("    }\n");
  echo("  }\n");
  echo("  xmlhttp.open(\"GET\",\"ajax_7_arduino.php?q=\"+str,true);\n");
  echo("  xmlhttp.send();\n");
  echo("}\n");


//   echo("function newPopup(url) {");
//   echo("	popupWindow = window.open(");
//   echo("		url,'popUpWindow','height=700,width=800,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes')");
// echo("}");

   echo("</script>");

  //Ajax: <span id="ajax_7"></span>
}
//********************************************
function viking_7_pinScenario($sys_id)
{
  global $par;
  global $boardId,$boardDigPins,$boardAnaPins,$boardTotPins;
  global $pinModeD,$pinStatusD,$pinStatusA,$serial;
  global $x_pinMode,$x_pinDigValue,$x_pinAnaValue,$x_pinRW;
  global $valueInPinD, $valueOutPinD, $valueInPinA,$pinModeD,$stepRead, $stepLoop;

  $path   = $par['path'];
  $sid    = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user   = $par['user'];

  $curSketchName = $par['a7_cur_sketch_name'];
  $curStep       = $par['a7_cur_step'];
  $board         = $par['a7_cur_board_type'];
  $boardDigPins  = $par['a7_cur_board_digpins'];
  $boardAnaPins  = $par['a7_cur_board_anapins'];
  $boardTotPins  = $boardAnaPins + $boardDigPins;

  readScenario();

  echo("<table border=1>");
  echo("<tr><td>Pin</td>");
  // Digital Pins Status
  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      echo("<td><b>");
      if($ii < $boardDigPins)
	vprintf("D%02s ",$ii);
      else
	vprintf("A%02s ",$ii-$boardDigPins);
      echo("</b></td>");
    }
  echo("</tr>");

  echo("<tr><td>Digital</td>");
  // Pins Status
  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      echo("<td>");
      if($ii < $boardDigPins)
	$status = $valueInPinD[$ii][$curStep];
      else
	$status = $valueInPinA[$ii-$boardDigPins][$curStep];
      echo("$status");
      echo("</td>");
    }

  echo("</tr>");
  echo("</table>");

}
//********************************************
function viking_7_pinStatus($sys_id)
{
  global $par;
  global $boardId,$boardDigPins,$boardAnaPins,$boardTotPins;
  global $pinModeD,$pinStatusD,$pinStatusA,$serial;
  global $x_pinMode,$x_pinDigValue,$x_pinAnaValue,$x_pinRW;

  $path   = $par['path'];
  $sid    = $par['a7_sid'];
  //if($sid != $sys_id) return;
  $user   = $par['user'];

  $curSketchName = $par['a7_cur_sketch_name'];
  $curStep       = $par['a7_cur_step'];
  $board         = $par['a7_cur_board_type'];
  $boardDigPins  = $par['a7_cur_board_digpins'];
  $boardAnaPins  = $par['a7_cur_board_anapins'];
  $boardTotPins  = $boardAnaPins + $boardDigPins;

  readScenario();

  echo("<table border=1>");
  echo("<tr><td>Pin</td>");
  // Digital Pins Status
  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      echo("<td><b>");
      if($ii < $boardDigPins)
	vprintf("D%02s ",$ii);
      else
	vprintf("A%02s ",$ii-$boardDigPins);
      echo("</b></td>");
    }
  echo("</tr>");

  echo("<tr><td>Digital</td>");
  // Digital Pins Status
  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      echo("<td>");
      $status = $x_pinDigValue[$ii][$curStep];
      //echo("$status ");
      //echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("$status");
      echo("</td>");
    }
  echo("</tr>");

  echo("<tr><td>Analog</td>");
  // Analog Pins Status
  for($ii=0; $ii<$boardTotPins; $ii++)
    {
      echo("<td>");
      $status = $x_pinAnaValue[$ii][$curStep];
      echo("$status ");
      echo("</td>");
    }
  echo("</tr>");
  echo("</table>");
}

//********************************************
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
