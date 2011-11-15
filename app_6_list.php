<?

//=========================
// Application List  (6)
//=========================

$a6pr  = array();

//====================================================
// GET
//====================================================
$par['a6_sid'] = $_GET['sid'];

//====================================================
// POST
//====================================================
if ($_SERVER['REQUEST_METHOD'] == "POST")
  {
    $post_action = $_POST['a6_post_action'];
    $sid         = $_POST['a6_sid'];
 
    if($post_action == 'a6_post_create_list')
      {
        $sel_db     = $_POST['a6_db'];
        $a6pr[$sys_id]['a6_db'] = $sel_db;
        if($sel_db)
          {
            createDb($sel_db,6);
            //$sel_object = 1;
            $a6pr[$sys_id]['a6_object']=1;
          }
        else
          $g_error = 10;
      }   

  }
//====================================================
// HTMLfunctions
//====================================================

function viking_6_createList_Link($sys_id)
{
  global $par;
  $sid        = $par['a6_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];

  if($app_open == "open_6_createList" && $sys_id == $sid)
    {
      echo(CREATE_LIST);
    }
  else if($user == 'admin')
    echo("<a href=$path&sid=$sys_id&p1=open_6_createList>".CREATE_LIST."</a>");
}


function viking_6_createList_Form($sys_id)
{
  global $par;

  $app_open = $par['p1'];
  $user     = $par['user'];

  if($app_open == "open_6_createList" && $user)
    {
      echo("<form name=\"form_6_createList\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a6_post_action\" value=\"a6_post_create_list\">");
      echo("<input type=\"hidden\" name=\"a6_sid\" value=\"$sys_id\">");
      echo("<input type=\"text\" name=\"a6_db\" value=\"\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".CREATE_LIST."\">");
      echo("</form>");
    }
}

function viking_6_createObject_Link($sys_id)
{
  global $par;
  $sid        = $par['sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];

  if($app_open == "open_6_createObject" && $sys_id == $sid)
    {
      echo(CREATE);
    }
  else if($user == 'admin')
    echo("<a href=$path&sid=$sys_id&p1=open_6_createObject>".CREATE."</a>");
}

function viking_6_createObject_Form($sys_id)
{
  global $par,$app_6;

  $app_open = $par['p1'];

  if($par['debug'])
    {
      $appName = $app_6['app_name'];
      echo("$appName createObject <br>");
    }

  if($app_open == "open_6_createObject"  && $sel_list && $user)
    {
      echo("<form name=\"a6_form_createObject\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a6_post_action\" value=\"a6_post_create_object\">");
      echo("<input type=\"text\" name=\"a6_list_name\" value=\"\">");
      echo("<input type=\"hidden\" name=\"a6_sid\" value=\"$sys_id\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".CREATE_OBJECT."\">");
      echo("</form>");
    }
}

function viking_6_deleteObject($sys_id)
{
  global $par;

  if($par['debug'])echo("$app createObject <br>");

  $db_id     = $par['om_db_id'];
  $object_id = $par['om_object_id'];
}
function viking_6_editObject($sys_id)
{
  global $par;

  if($par['debug'])echo("$app editObject <br>");

  $db_id     = $par['om_db_id'];
  $object_id = $par['om_object_id'];
}
function viking_6_showObject($sys_id)
{
  global $par;

  if($par['debug'])echo("$app showObject <br>");

  $db_id     = $par['om_db_id'];
  $object_id = $par['om_object_id'];
}
function viking_6_listObjects($sys_id)
{
  global $par;

  if($par['debug'])echo("$app listObjects <br>");

  $db_id = $par['om_db_id'];
}

?>
