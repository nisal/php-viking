<?
//======================================
// Users
//======================================

// Swedish
// define("T_4_ADD_USER","Registrera användare");
// define("T_4_DEL_USER","Avregistrera användare");
// define("T_4_LOGIN_USER","Logga in");
// define("T_4_LOGOUT_USER","Logga ut");
// define("T_4_USER_LOGGED_OUT","Utloggad");
// define("T_4_USERNAME","Användarnamn");
// define("T_4_PASSWORD","Lösenord");

define("T_4_ADD_USER","Register user");
define("T_4_DEL_USER","Unregister user");
define("T_4_LOGIN_USER","Login");
define("T_4_LOGOUT_USER","Logout");
define("T_4_USER_LOGGED_OUT","Logged Out!");
define("T_4_USERNAME","Username");
define("T_4_PASSWORD","Password");
define("T_4_USER","User");
define("T_4_PSWD","Pswd");



// Declarations =====================================

// Always use database: users
$_SESSION['a4_db'] = 'users';



// read SESSION parameters ===============================
$sel_db     = $_SESSION['a4_db'];
$sel_object = $_SESSION['a4_object_id'];
$sel_name   = $_SESSION['a4_object_name'];
$super      = $_SESSION['super'];
$par['super']  = $super;
// Initiate database if necessary
$file = getXmlFileName($sel_db);
if(!file_exists($file))
  {
    createDb($sel_db,4);
	    $sel_object = 1;
	    $_SESSION['a4_db']     = $sel_db; 
	    $_SESSION['a4_object'] = $sel_object;
	    echo("Database initiated !");

	    $user_id = getNextNodeId($sel_db);
	    createObject($sel_db,1,'admin',$user_id);
	    setObjectText($sel_db,$user_id,'admin');
  }

// GET ==============================================

$temp = $_GET['a4_object_id'];
if($temp)
  {
    $sel_object = $temp;
    $sel_name   = getObjectName($sel_db,$sel_object);
  }


// Logout
if($par['p1'] == 'a4_logout')
  {
    loginGlobalLog('logout');
    if($par['user'] == 'admin')$super = 0;
    $par['user'] ='';
    $par['user_event'] = 2;
  }

// Super Logout
if($par['p1'] == 'superzero')
  {
    $par['user'] ='admin';
    $par['user_event'] = 1;
  }
// POST =============================================
if ($_SERVER['REQUEST_METHOD'] == "POST")
  {
    $post_action = $_POST['a4_post_action'];
    

    if($post_action == 'post_login_user')
      {
	$user_name  = $_POST['a4_user_name'];
	$user_pswd  = $_POST['a4_user_pswd'];

	$id = getObjectIdbyName($sel_db,$user_name);
	if($id)
	  {
	    $temp_pswd = getObjectText($sel_db,$id);
	    $temp_name = getObjectName($sel_db,$id);

	    if($temp_pswd == $user_pswd && $temp_name = $user_name)
	      {
		$par['user'] = $user_name;
		$par['user_event'] = 1;
		loginGlobalCounter();
		loginGlobalLog('login');
                if($user_name == 'admin')$super = 1;
	      }
	  }
	else 
	  vikingError("User does not exist");	
      }

    if($post_action == 'post_login_super' && $super == 1)
      {
	echo("bennny<br>");
	$user_name  = $_POST['a4_user_name'];

	$id = getObjectIdbyName($sel_db,$user_name);
	if($id)
	  {
	    $temp_name = getObjectName($sel_db,$id);

	    if($temp_name = $user_name)
	      {
		$par['user'] = $user_name;
		$par['user_event'] = 1;
	      }
	  }
	else 
	  vikingError("User does not exist");	
      }
    
    
    if($post_action == 'post_add_user')
      {
 	$user_name  = $_POST['a4_user_name'];
 	$user_pswd1 = $_POST['a4_user_pswd1'];
 	$user_pswd2 = $_POST['a4_user_pswd2'];

	$user_id =  createUser($user_name,$user_pswd1,$user_pswd2);
	
// 	$id = getObjectIdbyName($sel_db,$user_name);
// 	if($id == 'void')
// 	  {
// 	    $father_id   = 1;
// 	    if($sel_db && $father_id && $user_name && $user_pswd1)
// 	      {
// 		if($user_pswd1 == $user_pswd2)
// 		  {
// 		    $user_id = getNextNodeId($sel_db);
// 		    createObject($sel_db,$father_id,$user_name,$user_id);
// 		    setObjectText($sel_db,$user_id,$user_pswd1);
// 		  }
// 		else
// 		  vikingError("Mismatch password");
// 	      } 
// 	  }
// 	else
// 	  vikingError("User name already exists");
      }

    if($post_action == 'post_del_user')
      {
	$user_name  = $_POST['a4_user_name'];
	
	$object_id = getObjectIdbyName($sel_db,$user_name);
	if($id != 'void')
	  {
	    $father_id = 1;
	    deleteObject($sel_db,$object_id,$father_id);
	  }
	else
	  vikingError("User name does not exists ($user_name)");
      }
  }


// Set par array values
$par['a4_db']     = $sel_db;
$par['a4_object'] = $sel_object;
$par['a4_name']   = $sel_name;
$par['super']     = $super;

// set SESSION parameters ===============================
$_SESSION['a4_db']          = $sel_db;
$_SESSION['a4_object_id']   = $sel_object;
$_SESSION['a4_object_name'] = $sel_name;
$_SESSION['super']          = $super;

$_SESSION['user'] = $par['user'];

//====================================================
//  Internal functions
//====================================================


//=======================================
function createUser($user,$pswd1,$pswd2)
//=======================================
{
  global $sel_db;

  $user_name  = $user;
  $user_pswd1 = $pswd1;
  $user_pswd2 = $pswd2;

  $user_id = 0;
  
  $id = getObjectIdbyName($sel_db,$user_name);
  if($id == 'void')
    {
      $father_id = 1;
      if($sel_db && $user_name && $user_pswd1)
	{
	  if($user_pswd1 == $user_pswd2)
	    {
	      $user_id = getNextNodeId($sel_db);
	      createObject($sel_db,$father_id,$user_name,$user_id);
	      setObjectText($sel_db,$user_id,$user_pswd1);
	    }
	  else
	    vikingError("Mismatch password");
	} 
    }
  else
    vikingError("User name already exists");

  return($user_id);
}

//=======================================
function loginGlobalCounter()
//=======================================
{
  $file = 'login.counter';

  $fh = fopen($file, 'r');
  $row = fgets($fh);
  sscanf($row, "%d", $id);
  fclose($fh);

  $id++;

  $fh = fopen($file, 'w');
  fwrite($fh, $id);    
  fwrite($fh, "\n");
  fclose($fh);

  return($id);
}

//==========================================
function loginGlobalLog($action)
//==========================================
{
  global $par;

  $user = $par['user'];  

  if($user)
    {
      $out = fopen('login.log',"a");
      if($out)
	{
	  $date = date("Y-m-d H:i:s");
	  $temp = $date." ".$user." ".$action."\n";
	  fwrite($out,$temp);
	}
      fclose($out);
    }
  else
    vikingError("Login logg entry without username");  
}

//====================================================
//  HTML functions
//====================================================
function viking_4_showUserLoggedIn()
{  
  global $par;
  $path  = $par['path'];
  $user  = $par['user'];
  $super = $par['super'];

  if($user)
    {
      if($user=='admin')
	echo("<a href=$path&pv=pv>$user</a>");
      else if($super == 1)
	echo("<a href=$path&p1=superzero>$user</a>");
      else
	echo("$user");
    }
  else
    echo("-");
}


function viking_4_addUser_Form()
{  
  global $par;
  $path       = $par['path'];
  $sel_name   = $par['a4_name'];
  $app_open   = $par['p1'];
  $sel_db     = $par['a4_db'];
  $user       = $par['user'];

  if($app_open == "open_4_addUser"  && $sel_db && $user == 'admin')
    {
      echo("<table><tr>");
      echo("<form name=\"form_add_user\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a4_post_action\" value=\"post_add_user\">");
      echo("<tr><td>".T_4_USER."</td><td><input type=\"text\" name=\"a4_user_name\" value=\"\"></td></tr>");
      echo("<tr><td>".T_4_PSWD."</td><td><input type=\"password\" name=\"a4_user_pswd1\" value=\"\"></td></tr>");
      echo("<tr><td>".T_4_PSWD."</td><td><input type=\"password\" name=\"a4_user_pswd2\" value=\"\"></td></tr>");
      echo("<tr><td><input type =\"submit\" name=\"form_submit\" value=\"".T_4_ADD_USER."\"></td>");
      echo("</form>");
      echo("</tr></table>");
    }
}

function viking_4_addUser_Link()
{  
  global $par;
  $path       = $par['path'];
  $app_open   = $par['p1'];
  $sel_db     = $par['a4_db'];
  $user       = $par['user'];

  if($app_open == "open_4_addUser")
    {
      echo(T_4_ADD_USER);
    }
  else  if($sel_db && $user == 'admin')
    echo("<a href=$path&p1=open_4_addUser>".T_4_ADD_USER."</a>");
}

function viking_4_delUser_Form()
{  
  global $par;
  $path       = $par['path'];
  $sel_name   = $par['a4_name'];
  $app_open   = $par['p1'];
  $sel_db     = $par['a4_db'];
  $user       = $par['user'];

  if($app_open == "open_4_delUser"  && $sel_db && $user == 'admin')
    {
      echo("<table><tr>");
      echo("<form name=\"form_del_user\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a4_post_action\" value=\"post_del_user\">");
      echo("<tr><td>".T_4_USER."</td><td><input type=\"text\" name=\"a4_user_name\" value=\"\"></td></tr>");
      echo("<tr><td><input type =\"submit\" name=\"form_submit\" value=\"".T_4_DEL_USER."\"></td>");
      echo("</form>");
      echo("</tr></table>");
    }
}

function viking_4_delUser_Link()
{  
  global $par;
  $path       = $par['path'];
  $app_open   = $par['p1'];
  $sel_db     = $par['a4_db'];
  $user       = $par['user'];

  if($app_open == "open_4_delUser")
    {
      echo(T_4_DEL_USER);
    }
  else  if($sel_db && $user == 'admin')
    echo("<a href=$path&p1=open_4_delUser>".T_4_DEL_USER."</a>");
}

function viking_4_login_Form()
{  
  global $par;
  $path       = $par['path'];
  $sel_name   = $par['a4_name'];
  $app_open   = $par['p1'];
  $sel_db     = $par['a4_db'];

  if($app_open == "open_4_login"  && $sel_db)
    {
      echo("<table><tr>");
      echo("<form name=\"form_login_user\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a4_post_action\" value=\"post_login_user\">");
      echo("<td>".T_4_USER."</td><td><input type=\"text\" name=\"a4_user_name\" value=\"\" size=\"14\"></td></tr>");
      echo("<tr><td>".T_4_PSWD."</td><td><input type=\"password\" name=\"a4_user_pswd\" value=\"\" size=\"14\"></td>");
      echo("<td><input type =\"submit\" name=\"form_submit\" value=\"".T_4_LOGIN_USER."\"></td>");
      echo("</form>");
      echo("</tr></table>");
    }
}

function viking_4_super_Form()
{  
  global $par;
  $path       = $par['path'];
  $sel_name   = $par['a4_name'];
  $app_open   = $par['p1'];
  $sel_db     = $par['a4_db'];

  if($sel_db)
    {
      echo("<table><tr>");
      echo("<form name=\"form_login_user\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a4_post_action\" value=\"post_login_super\">");
      echo("<td>".T_4_USER."</td><td><input type=\"text\" name=\"a4_user_name\" value=\"\" size=\"14\"></td></tr>");
      echo("<td><input type =\"submit\" name=\"form_submit\" value=\"".T_4_LOGIN_USER."\"></td>");
      echo("</form>");
      echo("</tr></table>");
    }
}

function viking_4_login_logout()
{  
  global $par;
  $path   = $par['path'];
  $user   = $par['user'];

  if($user)
    echo("<a href=$path&p1=a4_logout>".T_4_LOGOUT_USER."</a>"); 
  else 
    echo("<a href=$path&p1=open_4_login>".T_4_LOGIN_USER."</a>");
   
}

function viking_4_showUserByName()
{  
  global $par;
  $path   = $par['path'];
  $user   = $par['user'];
  
  $db = 'users';
  listAllObjects($db);   
}



function viking_4_showFunctions()
{
  echo("<h2>Users</h2>");
  echo("<table border=1>");
  echo("<tr><td>");



  echo("<b>addUserLink</b><br>");viking_4_addUser_Link();
  echo("</td><td>");
  echo("<b>addUserForm</b><br>");viking_4_addUser_Form();
  echo("</td><td>");
  echo("</td></tr><tr><td>");


  echo("<b>loginLink</b><br>");viking_4_login_logout();
  echo("</td><td>");
  echo("<b>loginForm</b><br>");viking_4_login_Form();
  echo("</td><td>");
  echo("<b>showUserLoggedIn</b><br>");viking_4_showUserLoggedIn();
  echo("</td></tr><tr><td>");


  echo("</td><td>");
  echo("</td><td>");
  echo("</td></tr>");
 
  echo("</table>");
 
}

?>
