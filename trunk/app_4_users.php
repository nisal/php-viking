<?
//======================================
// Users

// function viking_4_showUserLoggedIn()
// function viking_4_addUser()
// function viking_4_delUser()
// function viking_4_editUser()
// function viking_4_loginUser()
// function viking_4_logoutUser()
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



// Declarations =====================================

// Always use database: shop
$_SESSION['a4_db'] = 'users';



// read SESSION parameters ===============================
$sel_db     = $_SESSION['a4_db'];
$sel_object = $_SESSION['a4_object_id'];
$sel_name   = $_SESSION['a4_object_name'];

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

// PAR 1,2,3,4,5

// Logout
if($par['p1'] == 'a4_logout') $par['user'] ='';

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
	      $par['user'] = $user_name;;
	  }
	else 
	  echo("User does not exist");	
      }
    
    
    if($post_action == 'post_add_user')
      {
	$user_name  = $_POST['a4_user_name'];
	$user_pswd1 = $_POST['a4_user_pswd1'];
	$user_pswd2 = $_POST['a4_user_pswd2'];
	
	$id = getObjectIdbyName($sel_db,$user_name);
	if($id == 'void')
	  {
	    $father_id   = 1;
	    if($sel_db && $father_id && $user_name && $user_pswd1)
	      {
		if($user_pswd1 == $user_pswd2)
		  {
		    $user_id = getNextNodeId($sel_db);
		    createObject($sel_db,$father_id,$user_name,$user_id);
		    setObjectText($sel_db,$user_id,$user_pswd1);
		  }
		else
		  echo("Mismatch  password ");
	      } 
	  }
	else
	  echo("User name already exists");
      }
  }


// Set par array values
$par['a4_db']     = $sel_db;
$par['a4_object'] = $sel_object;
$par['a4_name']   = $sel_name;


// set SESSION parameters ===============================
$_SESSION['a4_db']          = $sel_db;
$_SESSION['a4_object_id']   = $sel_object;
$_SESSION['a4_object_name'] = $sel_name;

$_SESSION['user'] = $par['user'];

//====================================================
//  Internal functions
//====================================================


//====================================================
//  HTML functions
//====================================================
function viking_4_showUserLoggedIn()
{  
  global $par;
  $user  = $par['user'];
  
  if($user)
    {
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
      echo("<form name=\"form_add_user\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a4_post_action\" value=\"post_add_user\">");
      echo(T_4_USERNAME."<input type=\"text\" name=\"a4_user_name\" value=\"\"><br>");
      echo(T_4_PASSWORD."<input type=\"password\" name=\"a4_user_pswd1\" value=\"\"><br>");
      echo(T_4_PASSWORD."<input type=\"password\" name=\"a4_user_pswd2\" value=\"\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".T_4_ADD_USER."\">");
      echo("</form>");
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

function viking_4_login_Form()
{  
  global $par;
  $path       = $par['path'];
  $sel_name   = $par['a4_name'];
  $app_open   = $par['p1'];
  $sel_db     = $par['a4_db'];

  if($app_open == "open_4_login"  && $sel_db)
    {
      echo("<form name=\"form_login_user\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a4_post_action\" value=\"post_login_user\">");
      echo(T_4_USERNAME."<input type=\"text\" name=\"a4_user_name\" value=\"\"><br>");
      echo(T_4_PASSWORD."<input type=\"password\" name=\"a4_user_pswd\" value=\"\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".T_4_LOGIN_USER."\">");
      echo("</form>");
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
