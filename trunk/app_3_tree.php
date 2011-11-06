<?

//=========================
// Application Tree  (3)
//=========================
define("DBNO","4"); // Number of Databases

// Declarations =====================================
$a3pr    = array();

$expand  = array();
$expand2 = array();

// read SESSION parameters ===============================

for($ii=1;$ii<=DBNO;$ii++)
  {
    $temp = $ii.'_a3_db';         $a3pr[$ii]['a3_db']     = $_SESSION[$temp];
    $temp = $ii.'_a3_object';     $a3pr[$ii]['a3_object'] = $_SESSION[$temp];
    $temp = $ii.'_a3_name';       $a3pr[$ii]['a3_name']   = $_SESSION[$temp];
    $temp = $ii.'_a3_index';      $a3pr[$ii]['a3_index']  = $_SESSION[$temp];
    $temp = $ii.'_a3_index2';     $a3pr[$ii]['a3_index2'] = $_SESSION[$temp];
    $temp = $ii.'_a3_from';       $a3pr[$ii]['a3_from']   = $_SESSION[$temp];
    $temp = $ii.'_a3_from_father';$a3pr[$ii]['a3_from_father'] = $_SESSION[$temp];
    $temp = $ii.'_a3_show_all   ';$a3pr[$ii]['a3_show_all']    = $_SESSION[$temp];
    $temp = $a3pr[$ii]['a3_db'];    $par[$temp] = $ii;
  }

$par['a3_link_from_sid']    = $_SESSION['a3_link_from_sid'];
$par['a3_link_from_object'] = $_SESSION['a3_link_from_object'];
$par['a3_link_to_sid']      = $_SESSION['a3_link_to_sid'];
$par['a3_link_to_object']   = $_SESSION['a3_link_to_object'];


//====================================================
// GET
//====================================================
$sys_id = $_GET['a3_sid'];
$par['a3_sid'] = $_GET['a3_sid'];

$sel_db      = $a3pr[$sys_id]['a3_db'];
$father_id   = $a3pr[$sys_id]['a3_object'];
$father_name = $a3pr[$sys_id]['a3_name'];
$sel_object  = $a3pr[$sys_id]['a3_object'];
$sel_name    = $a3pr[$sys_id]['a3_name'];
$index       = $a3pr[$sys_id]['a3_index'];
$index2      = $a3pr[$sys_id]['a3_index2'];
$from        = $a3pr[$sys_id]['a3_from'];
$from_father = $a3pr[$sys_id]['a3_from_father'];

$link_from_object  = $par['a3_link_from_object'];
$link_from_sid     = $par['a3_link_from_sid'];
$link_to_object    = $par['a3_link_to_object'];
$link_to_sid       = $par['a3_link_to_sid'];


$temp = $_GET['index'];
if($temp)
  {
    $index = $temp;
    $a3pr[$sys_id]['a3_index'] = $index;
  }
//$index = $a3pr[$sys_id]['a3_index'] = $index;

if($par['p1'] == 'show_all')
  {    
    if($a3pr[$sys_id]['a3_show_all'] == '>') $a3pr[$sys_id]['a3_show_all'] = '<';
    else
      $a3pr[$sys_id]['a3_show_all'] = '>';
  }

if($par['p1'] == 'delete_link_in')
  {
    $db1     = $par['p2'];
    $object1 = $par['p3'];
    $db2     = $par['p4'];
    $object2 = $par['p5'];
    deleteNode($db1,$object1,$object2,'type','linkIn');
    deleteNode($db2,$object2,$object1,'type','linkOut');
  }
if($par['p1'] == 'delete_link_out')
  {
    $db1     = $par['p2'];
    $object1 = $par['p3'];
    $db2     = $par['p4'];
    $object2 = $par['p5'];
    deleteNode($db1,$object1,$object2,'type','linkOut');
    deleteNode($db2,$object2,$object1,'type','linkIn');
  }

$temp = $_GET['a3_object_id'];

if($temp)
  {
    $sel_object = $temp;
    $a3pr[$sys_id]['a3_object'] = $sel_object;
    $sel_name   = getObjectName($sel_db,$sel_object);
    $a3pr[$sys_id]['a3_name'] = $sel_name;

    //     if($par['p1'] == 'delete' && $from && $from_father)
    //       {
    // 	deleteObject($sel_db,$from,$from_father);
    // 	$a3pr[$sys_id]['a3_index2']      = '';
    // 	$a3pr[$sys_id]['a3_from']        = '';
    // 	$a3pr[$sys_id]['a3_from_father'] = '';
    //         $a3pr[$sys_id]['a3_object']      = 1;
    //       }


    if($par['p1'] == 'select')
      {
	$a3pr[$sys_id]['a3_index2']      = $index;
	$a3pr[$sys_id]['a3_from']        = $sel_object;
	$a3pr[$sys_id]['a3_from_father'] = $par['p2'];
	echo("select:save index: $index<br>");
      }

    if($par['p1'] == 'cancel')
      {
	$a3pr[$sys_id]['a3_index2']      = '';
	$a3pr[$sys_id]['a3_from']        = '';
	$a3pr[$sys_id]['a3_from_father'] = '';
        $a3pr[$sys_id]['a3_link']        = '';
      }
    if($par['p1'] == 'to' && $from && $from_father)
      {
	$to = $par['p2'];
	moveObject($sel_db,$from,$from_father,$to);
	$a3pr[$sys_id]['a3_index2']      = '';
	$a3pr[$sys_id]['a3_from']        = '';
	$a3pr[$sys_id]['a3_from_father'] = '';
	echo("TO $sel_db,$from,$from_father,$to");
      }
  }

//====================================================
// POST
//====================================================
if ($_SERVER['REQUEST_METHOD'] == "POST")
  {
    $post_action = $_POST['a3_post_action'];
    $sys_id      = $_POST['a3_sid'];

    $sel_db      = $a3pr[$sys_id]['a3_db'];
    $father_id   = $a3pr[$sys_id]['a3_object'];
    $father_name = $a3pr[$sys_id]['a3_name'];
    $sel_object  = $a3pr[$sys_id]['a3_object'];
    $sel_name    = $a3pr[$sys_id]['a3_name'];
    $index       = $a3pr[$sys_id]['a3_index'];
    $index2      = $a3pr[$sys_id]['a3_index2'];
    $from        = $a3pr[$sys_id]['a3_from'];
    $from_father = $a3pr[$sys_id]['a3_from_father'];
    
    $link_from_sid    = $par['a3_link_from_sid'];
    $link_from_object = $par['a3_link_from_object'];
    $link_to_sid      = $par['a3_link_to_sid'];
    $link_to_object   = $par['a3_link_to_object'];

    if($post_action == 'post_select_db')
      {
	$a3pr[$sys_id]['a3_index2']      = '';
	$a3pr[$sys_id]['a3_from']        = '';
	$a3pr[$sys_id]['a3_from_father'] = '';

	$a3pr[$sys_id]['a3_db'] = $_POST['a3_database'];
	$a3pr[$sys_id]['a3_object'] = 1;
        $a3pr[$sys_id]['a3_name']   = $a3pr[$sys_id]['a3_db'];
	$a3pr[$sys_id]['a3_index']  = '1.'; 
      }

    if($post_action == 'post_delete_db')
      {
	$temp_db  = $_POST['a3_database'];
	if($temp_db)
	  {
	    deleteDb($temp_db);
	    $a3pr[$sys_id]['a3_db']     = 0;
	    $a3pr[$sys_id]['a3_object'] = 0;
	  }
      }

    if($post_action == 'post_add_object')
      {

	$object_name = $_POST['a3_object_name'];
  
	if($sel_db && $father_id && $object_name)
	  {
	    $object_id = getNextNodeId($sel_db);
	    createObject($sel_db,$father_id,$object_name,$object_id);
	  }
      } 

    if($post_action == 'post_rename_object')
      {
	
	$object_name = $_POST['a3_object_name'];
  
	if($sel_db && $sel_object>1 && $object_name)
	  {
	    renameNode($sel_db,$sel_object,$object_name);
	    $a3pr[$sys_id]['a3_name'] = $object_name;
	  }
      } 

    if($post_action == 'post_delete_object')
      {
	if($sel_db && $sel_object>1)
	  {
	    $father = $par['p2'];
	    deleteObject($sel_db,$sel_object,$father);
	    $a3pr[$sys_id]['a3_index2']      = '';
	    $a3pr[$sys_id]['a3_from']        = '';
	    $a3pr[$sys_id]['a3_from_father'] = '';
	    $a3pr[$sys_id]['a3_object']      = 1;
	  }
      } 
    
    if($post_action == 'post_create_db')
      {
	
	$sel_db     = $_POST['a3_db'];
	$a3pr[$sys_id]['a3_db'] = $sel_db;
	if($sel_db)
	  {
	    createDb($sel_db,3);
	    //$sel_object = 1;
	    $a3pr[$sys_id]['a3_object']=1;
	  }
	else
	  $g_error = 10;
      }   
 
    if($post_action == 'post_set_text')
      {
	$value   = $_POST['a3_object_text'];
	if($sel_db && $sel_object)
	  setObjectText($sel_db,$sel_object,$value);
      }

    if($post_action == 'post_add_link_from')
      {
	echo("dummy linking from $sel_object ($sys_id)<br>");
	$par['a3_link_from_object'] = $sel_object;
	$par['a3_link_from_sid']    = $sys_id;
	$link_from_object = $sel_object;
	$link_from_sid    = $sys_id;
      }   

    if($post_action == 'post_add_link_to')
      {
	$link_from_object = $par['a3_link_from_object'];
	$link_from_sid    = $par['a3_link_from_sid'];	
	// 	$link_to_object   = $par['a3_link_to_object'];
	// 	$link_to_sid      = $par['a3_link_to_sid'];	
	$link_to_object   = $sel_object;
	$link_to_sid      = $sys_id;	

	//echo("from_sid=	$link_from_sid to_sid= $link_to_sid <br>"); 
	$from_struct = $a3pr[$link_from_sid]['a3_db'];
	// 	$to_struct   = $a3pr[$link_to_sid]['a3_db'];
	$to_struct   = $sel_db;

	//echo("addLink($from_struct,$link_from_object,$to_struct,$link_to_object)");
	addLink($from_struct,$link_from_object,$to_struct,$link_to_object);

	//$par['a3_link_to_object']   = $sel_object;
	
	// Clear from destination
	$par['a3_link_from_object'] = 0;	
	$par['a3_link_from_sid']    = 0;	
	$par['a3_link_to_object']   = 0;	
	$par['a3_link_to_sid']      = 0;	
      }   

    if($post_action == 'post_add_link_cancel')
      {
        $par['a3_link_from_object'] = 0;
        $par['a3_link_from_sid']    = 0;
        $link_from_object = 0;
        $link_from_sid    = 0;
      }
 
    if($post_action == 'post_set_image')
      {
	if($sel_db && $sel_object)
	  {
	    $image_name = uploadImage($sel_db,$sel_object);
	    if($image_name)
	      setObjectImage($sel_db,$sel_object,$image_name);
	  }
      } 
   
  }
// End of POST ==================================



for($ii=1;$ii<=DBNO;$ii++)
  {
    $temp = $ii.'_a3_db';          $_SESSION[$temp]=$a3pr[$ii]['a3_db']  ;
    $temp = $ii.'_a3_object';      $_SESSION[$temp]=$a3pr[$ii]['a3_object'];
    $temp = $ii.'_a3_name';        $_SESSION[$temp]=$a3pr[$ii]['a3_name'];
    $temp = $ii.'_a3_index';       $_SESSION[$temp]=$a3pr[$ii]['a3_index'];
    $temp = $ii.'_a3_index2';      $_SESSION[$temp]=$a3pr[$ii]['a3_index2'];
    $temp = $ii.'_a3_from';        $_SESSION[$temp]=$a3pr[$ii]['a3_from'] ;
    $temp = $ii.'_a3_from_father'; $_SESSION[$temp]=$a3pr[$ii]['a3_from_father'];
    $temp = $ii.'_a3_show_all   '; $_SESSION[$temp]=$a3pr[$ii]['a3_show_all'];
  }

$_SESSION['a3_link_from_sid']   =$par['a3_link_from_sid'];
$_SESSION['a3_link_from_object']=$par['a3_link_from_object'];
$_SESSION['a3_link_to_sid']     =$par['a3_link_to_sid'];
$_SESSION['a3_link_to_object']  =$par['a3_link_to_object'];


//====================================================
//  Internal functions
//====================================================


//========================
function showXmlTree($sys_id)// TODO move to library
//========================
{
  global $par,$a3pr;
  global $expand;
  global $expand2;

  $g_path = $par['path'];

  $link_from_sid    = $par['a3_link_from_sid'];
  $link_from_object = $par['a3_link_from_object'];

  $index  = $a3pr[$sys_id]['a3_index'];
  $index2 = $a3pr[$sys_id]['a3_index2'];
  $show_all = $a3pr[$sys_id]['a3_show_all'];
  if(!$show_all) $show_all = '<';

  // Set expand path
  $tok = strtok($index, ".");
  $expand[1] = $tok;
  $ii = 2;
  while ($tok !== false) 
    {
      $tok = strtok(".");
      $expand[$ii] = $tok;
      $ii++;
    }
  
  $tok = strtok($index2, ".");
  $expand2[1] = $tok;
  $ii = 2;
  while ($tok !== false) 
    {
      $tok = strtok(".");
      $expand2[$ii] = $tok;
      $ii++;
    }
  
  $sel  = $a3pr[$sys_id]['a3_object'];
  $from = $a3pr[$sys_id]['a3_from'];
  $from_father = $a3pr[$sys_id]['a3_from_father'];
  $to   = $a3pr[$sys_id]['a3_to'];
  $user = $par['user'];

  $db_name = $a3pr[$sys_id]['a3_db'];

  $ix = array();
  $level = 0;
  if($db_name)
    {
      $file = getXmlFileName($db_name);
    }
  else
    {
      echo("No database selected");
      //exit();
    }

  $dom = new DOMDocument();
  $dom->load($file);

  $xpath = new DOMXPath($dom);

  $question = "//object"; //database
  $elements = $xpath->query($question);

  foreach($elements as $element)
    {

      $attr_name = $element->attributes->getNamedItem("name")->nodeValue;
      $attr_id   = $element->attributes->getNamedItem("id")->nodeValue;
      $attr_type = $element->attributes->getNamedItem("type")->nodeValue;
      if($attr_id == 1 && $attr_type == 'node')
	{
	  echo("<a href=\"$g_path&a3_sid=$sys_id&a3_object_id=$attr_id&a3_object_name=$attr_name\">$attr_name 1 </a>");
	  if($link_from_object == 1 && $link_from_sid == $sys_id)
	    echo(" F");
	  echo("<a href=\"$g_path&a3_sid=$sys_id&p1=show_all\"> $show_all</a>");

	  if($user && $sel == $attr_id && $from && $from != $sel)
	    echo("<a href=\"$g_path&a3_sid=$sys_id&a3_object_id=$attr_id&index=$index&p1=to&p2=$attr_id\"> M</a>");

	  echo("<br>");

	  if($show_all == '<')
	    {
	      $image = getNodeValue($db_name,$attr_id,'void','type','image');
	      if($image != 'void')echo("<br><img src=\"$image\" height=\"50\" alt=\"image error\"/>");
	      $text = getNodeValue($db_name,$attr_id,'void','type','text');
	      if($text != 'void')echo("$text");
	    }

	  traverse($sys_id,$g_path,$element,$level,1,$ix,$expand,$expand2,$attr_id);
	}
    }
}

//========================// TODO move to library
function traverse($sys_id,$path,$node,$level,$id,$ix,$expand,$expand2,$father)
//========================
{
  global $par,$a3pr;

  $sel  = $a3pr[$sys_id]['a3_object'];
  $from = $a3pr[$sys_id]['a3_from'];
  $from_father = $a3pr[$sys_id]['a3_from_father'];
  $to   = $a3pr[$sys_id]['a3_to'];
  $user = $par['user'];
  $link_from_sid    = $par['a3_link_from_sid'];
  $link_from_object = $par['a3_link_from_object'];

  $db_name  = $a3pr[$sys_id]['a3_db'];
  $show_all = $a3pr[$sys_id]['a3_show_all'];

  if($node->hasChildNodes())
    {
      $level++; $id = 0;
      $childs = $node->childNodes;
      foreach($childs as $child)
	{
	  $attr_type = $child->attributes->getNamedItem("type")->nodeValue;
	  if($attr_type == 'node')
	    {
	      //if($child->hasChildNodes())echo("...");
	      for($ii=1;$ii<$level;$ii++)echo("-");
	      
	      $attr_name = $child->attributes->getNamedItem("name")->nodeValue;
	      $attr_id   = $child->attributes->getNamedItem("id")->nodeValue;
	      $id++;
	      $ix[$level]=$id;
	      $index = "";
	      for($ii=1;$ii<=$level;$ii++)$index=$index.$ix[$ii].".";
	     
	      echo("$index <a href=\"$path&a3_sid=$sys_id&a3_object_id=$attr_id&index=$index&p2=$father\">$attr_name $attr_id</a>");
	      if($user && $sel == $attr_id)
		{
		  if(!$from)echo("<a href=\"$path&a3_sid=$sys_id&a3_object_id=$attr_id&index=$index&p1=select&p2=$father\"> ?</a>");
		}

	      if($link_from_object == $attr_id && $link_from_sid == $sys_id)
		echo(" F");

	      if($user && $sel == $attr_id && $from && $from != $sel)
		{
		  echo("<a href=\"$path&a3_sid=$sys_id&a3_object_id=$attr_id&index=$index&p1=to&p2=$attr_id\"> M</a>");
		}
	      if($from == $attr_id || $link == $attr_id)
		{
		  echo("<a href=\"$path&a3_sid=$sys_id&a3_object_id=$attr_id&index=$index&p1=cancel\"> *</a>");
		  //if($from == $attr_id)echo("<a href=\"$path&sid=$sys_id&a3_object_id=$attr_id&index=$index&p1=delete\"> D</a>");
		}
	      if($show_all == '<')
		{
		  $image = getNodeValue($db_name,$attr_id,'type','image');
		  if($image)echo("<br><img src=\"$image\" height=\"50\" alt=\"image error\"/>");
		  $text = getNodeValue($db_name,$attr_id,'type','text');
		  if($text)echo("$text");
		}
		
	      echo("<br>");
		
	      if($expand[$level] == $ix[$level] || $expand2[$level] == $ix[$level]  )
		traverse($sys_id,$path,$child,$level,$id,$ix,$expand,$expand2,$attr_id);
	    }
	  
	}
      $level--;
    }
}  

function addLink($from_struct,$from_object,$to_struct,$to_object)
{
  // <object name="Heater control" id="3" type="linkFrom">T24 45</object>
  global $error;

  // Not possible to link to smae structure
  if($from_struct == $to_struct) return; 

  //echo("Create link: $from_struct,$from_object,$to_struct,$to_object<br>");
  if($from_struct && $from_object && $to_struct && $to_object)
    {
      $object_name_from = getObjectName($from_struct,$from_object);
      $object_name_to   = getObjectName($to_struct,$to_object);

      //$lid = createNode('generate',$from_struct,$from_object,$object_name_from,$from_object,'linkFrom');

      createNode($from_struct,$from_object,$to_struct,$to_object,'linkOut');

      $value = $to_struct." ".$object_name_to." ".$to_object;
      setNodeValue($from_struct,$from_object,'type','linkFrom',$value);
      // echo("new lid = $lid<br>"); 
      createNode($to_struct,$to_object,$from_struct,$from_object,'linkIn');
      $value = $from_struct." ".$object_name_from." ".$from_object;
      setNodeValue($to_struct,$to_object,'type','linkTo',$value);
    }
  else
    echo("Unable to create link: $from_struct,$from_object,$to_struct,$to_object");  
}

//====================================================
//  HTML functions
//====================================================

function viking_3_selectDb($sys_id)// TODO move to library
{
  global $par,$a3pr;
  //global $g_app;
  global $g_db_dir;
  $path   = $par['path'];
  $sel_db = $a3pr[$sys_id]['a3_db'];
  $db_list_file = $g_db_dir.'db.list';

  echo("<form name=\"form_select_db\" action=\"$path\" method=\"post\"> ");
  echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_select_db\">");
  echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
  echo("<select name=\"a3_database\">");
  //system("pwd;");
  echo("<option value=\"void\"> - </option>");
  $fh = fopen($db_list_file, 'r') or die("can't open file: $db_list_file");
  while (!feof($fh)) 
    {
      $row = fgets($fh);
      if($row)
	{
	  sscanf($row, "%s %d", $db_temp,$app_id);
	  if($app_id == 3)
	    {
	      if($sel_db == $db_temp)
		echo("<option value=\"$db_temp\" selected>$db_temp</option>");
	      else
		echo("<option value=\"$db_temp\">$db_temp</option>");
	    }
 
	}
    }
  fclose($fh);	
  echo("</select>");
  echo("<input type =\"submit\" name=\"form_submit\" value=\"".SELECT_TREE."\">");
  echo("</form>");
}


function viking_3_createDb_Form($sys_id)
{  
  global $par,$a3pr;

  $sid        = $par['a3_sid'];
  if($sid != $sys_id) return;

  $path     = $par['path'];
  $user     = $par['user'];
  $app_open = $par['p1'];

  if($app_open == "open_3_createDb" && $user == 'admin')
    {
      echo("<form name=\"form_create_db\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_create_db\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      echo("<input type=\"text\" name=\"a3_db\" value=\"\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".CREATE_TREE."\">");
      echo("</form>");
    }
}
function viking_3_createDb_Link($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];

  if($app_open == "open_3_createDb" && $sys_id == $sid)
    {
      echo(CREATE);
    }
  else if($user == 'admin')
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_createDb>".CREATE."</a>");
}

function viking_3_deleteDb_Form($sys_id) // TODO move to library
{  

  global $par,$a3pr;
  //   global $g_app;
  global $g_db_dir;

  $path      = $par['path'];
  $user      = $par['user'];
  $sel_db    = $a3pr[$sys_id]['a3_db'];
  $app_open  = $par['p1'];
  $db_list_file = $g_db_dir.'db.list';

  if($app_open == 'open_3_deleteDb' && $user == 'admin')
    {
      echo("<form name=\"form_delete_db\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_delete_db\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      echo("<select name=\"a3_database\">");
      $fh = fopen($db_list_file, 'r') or die("can't open file: $db_list_file");
      while (!feof($fh)) 
	{
	  $row = fgets($fh);
	  if($row)
	    {
	      sscanf($row, "%s %d", $db_temp,$app_id);
	      if($app_id == 3)
		{
		  if($sel_db == $db_temp)
		    echo("<option value=\"$db_temp\" selected>$db_temp</option>");
		  else
		    echo("<option value=\"$db_temp\">$db_temp</option>");
		}
	      
	    }
	}
      fclose($fh);	
      echo("</select>");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".DELETE_TREE."\">");
      echo("</form>");
    }
}
function viking_3_deleteDb_Link($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];

  if($app_open == "open_3_deleteDb")
    {
      echo(" ".DELETE);
    }
  else if($user == 'admin')
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_deleteDb> ".DELETE."</a>");
}

function viking_3_addObject_Form($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  if($sid != $sys_id) return;
  $path       = $par['path'];
  $user       = $par['user'];

  $sel_name   = $a3pr[$sys_id]['a3_name'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];

  if($app_open == "open_3_addObject"  && $sel_db && $user)
    {
      echo("<form name=\"form_add_node\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_add_object\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      echo("$sel_name <input type=\"text\" name=\"a3_object_name\" value=\"\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".CREATE_OBJECT."\">");
      echo("</form>");
    }
}
function viking_3_addObject_Link($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];

  if($app_open == "open_3_addObject" && $sys_id == $sid)
    {
      echo(" ".CREATE);
    }
  else  if($sel_db && $user )
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_addObject> ".CREATE."</a>");
}

function viking_3_renameObject_Form($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  if($sid != $sys_id) return;
  $path       = $par['path'];
  $user       = $par['user'];
  $sel_name   = $a3pr[$sys_id]['a3_name'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];

  if($app_open == "open_3_renameObject"  && $sel_db && $user)
    {
      echo("<form name=\"form_rename_node\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_rename_object\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      echo(NAME_OBJECT." <input type=\"text\" name=\"a3_object_name\" value=\"$sel_name\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".RENAME_OBJECT."\">");
      echo("</form>");
    }
}
function viking_3_renameObject_Link($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];

  if($app_open == "open_3_renameObject" && $sys_id == $sid)
    {
      echo(" ".RENAME);
    }
  else  if($sel_db && $user)
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_renameObject> ".RENAME."</a>");
}

function viking_3_deleteObject_Form($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  if($sid != $sys_id) return;
  $path       = $par['path'];
  $user       = $par['user'];
  $sel_name   = $a3pr[$sys_id]['a3_name'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $father     = $par['p2'];

  if($app_open == "open_3_deleteObject"  && $sel_db && $user)
    {
      echo("<form name=\"form_delete_object\" action=\"$path&p2=$father\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_delete_object\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      //echo(OBJECT_NAME." <input type=\"text\" name=\"a3_object_name\" value=\"$sel_name\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".DELETE_OBJECT." $sel_name\">");
      echo("</form>");
    }
}
function viking_3_deleteObject_Link($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];
  $father     = $par['p2'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];

  if($app_open == "open_3_deleteObject" && $sys_id == $sid)
    {
      echo(" ".DELETE);
    }
  else  if($sel_db && $user)
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_deleteObject&p2=$father> ".DELETE."</a>");
}


function viking_3_setText_Form($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  if($sid != $sys_id) return;
  $path       = $par['path'];
  $user       = $par['user'];
  $sel_name   = $a3pr[$sys_id]['3_name'];
  $app_open   = $par['p1'];

  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  $temp = getObjectText($sel_db,$sel_object);

  if($app_open == 'open_3_setText' && $sel_db && $user)
    {
      echo("<form name=\"form_set_text\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_set_text\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      echo("<textarea name=\"a3_object_text\" cols=40 rows=6>$temp</textarea>");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".UPDATE."\">");
      echo("</form>");
    }
}

function viking_3_setText_Link($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  if($app_open == "open_3_setText" && $sys_id == $sid)
    {
      echo(" ".SET_TEXT);
    }
  else if($sel_db && $user)
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_setText> ".SET_TEXT."</a>");
}

function viking_3_addLinkFrom_Form($sys_id)
{
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  if($sid != $sys_id) return;
  $path       = $par['path'];
  $user       = $par['user'];
  $sel_name   = $a3pr[$sys_id]['a3_name'];
  $app_open   = $par['p1'];

  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  if($app_open == 'open_3_addLinkFrom' && $sel_db && $user)
    {
      echo("<form name=\"form_add_link_from\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_add_link_from\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".LINK_FROM." $sel_name\">");
      echo("</form>");
    }

}



function viking_3_addLinkFrom_Link($sys_id)
{
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  if($app_open == "open_3_addLinkFrom"  && $sys_id == $sid)
    {
      echo(" ".LINK_FROM);
    }
  else if($sel_db && $user)
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_addLinkFrom> ".LINK_FROM."</a>");
}

function viking_3_addLinkTo_Form($sys_id)
{
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  if($sid != $sys_id) return;
  $path       = $par['path'];
  $user       = $par['user'];
  $sel_name   = $a3pr[$sys_id]['a3_name'];
  $app_open   = $par['p1'];

  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  if($app_open == 'open_3_addLinkTo' && $sel_db && $user)
    {
      echo("<form name=\"form_add_link_to\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_add_link_to\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".LINK_TO." $sel_name\">");
      echo("</form>");
    }
}


function viking_3_addLinkTo_Link($sys_id)
{
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  $from_sid   = $par['a3_link_from_sid'];

  // Not possible to link to same structure
  if($a3pr[$from_sid]['a3_db'] == $sel_db)return;
 
  if($app_open == "open_3_addLinkTo"  && $sys_id == $sid)
    {
      echo(" ".LINK_TO);
    }
  else if($sel_db && $user)
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_addLinkTo> ".LINK_TO."</a>");
}

function viking_3_addLinkCancel_Form($sys_id)
{
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  if($sid != $sys_id) return;
  $path       = $par['path'];
  $user       = $par['user'];
  $sel_name   = $a3pr[$sys_id]['a3_name'];
  $app_open   = $par['p1'];

  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  if($app_open == 'open_3_addLinkCancel' && $sel_db && $user)
    {
      echo("<form name=\"form_add_link_cancel\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_add_link_cancel\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".LINK_CANCEL." $sel_name\">");
      echo("</form>");
    }
}


function viking_3_addLinkCancel_Link($sys_id)
{
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  if($app_open == "open_3_addLinkCancel"  && $sys_id == $sid)
    {
      echo(" ".LINK_CANCEL);
    }
  else if($sel_db && $user)
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_addLinkCancel> ".LINK_CANCEL."</a>");
}


function viking_3_setImage_Form($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  if($sid != $sys_id) return;
  $path       = $par['path'];
  $user       = $par['user'];
  $sel_name   = $a3pr[$sys_id]['a3_name'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  $image_name = getObjectImage($sel_db,$sel_object);



  if($app_open == 'open_3_setImage' && $sel_db && $user)
    {
      if($image_name)echo(" <img src=\"$image_name\" height=\"50\" alt=\"blabla\"/>");
      echo("<form name=\"form_set_image\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_set_image\">");
      echo("<input type=\"hidden\" name=\"a3_sid\" value=\"$sys_id\">");
      echo("<input type=\"file\" name=\"image\">");
      echo("<input type =\"submit\" name=\"submit_image\" value=\"".UPLOAD."\">");
      echo("</form>");
    }
}

function viking_3_setImage_Link($sys_id)
{  
  global $par,$a3pr;
  $sid        = $par['a3_sid'];
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];
  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];

  if($app_open == "open_3_setImage" && $sys_id == $sid)
    {
      echo(" ".SET_IMAGE);
    }
  else  if($sel_db && $user)
    echo("<a href=$path&a3_sid=$sys_id&p1=open_3_setImage> ".SET_IMAGE."</a>");
}


function viking_3_showDb($sys_id)
{ 
  showXmlTree($sys_id);
  echo("<hr>");
}

function viking_3_showDbName($sys_id)
{ 
  global $par,$a3pr;
  $sel_db  = $a3pr[$sys_id]['a3_db'];
  echo("$sel_db");
}

function viking_3_showObject($sys_id)
{ 
  global $par,$a3pr;
  $sel_db     = $a3pr[$sys_id]['a3_db'];
  $sel_object = $a3pr[$sys_id]['a3_object'];
  $link_from_sid     = $par['a3_link_from_sid'];
  $link_from_object  = $par['a3_link_from_object'];
  
  //echo("db=$sel_db object=$sel_object <br>");

  if($sel_db == 'void' || !$sel_object)return;

  showObject($sel_db,$sel_object);

  viking_3_setText_Link($sys_id);
  viking_3_setImage_Link($sys_id);
  viking_3_renameObject_Link($sys_id);
  viking_3_addObject_Link($sys_id);
  viking_3_deleteObject_Link($sys_id);
  if(!$link_from_object)viking_3_addLinkFrom_Link($sys_id);
  if($link_from_object && $link_from_sid != $sys_id)viking_3_addLinkTo_Link($sys_id);
  if($link_from_object && $link_from_sid == $sys_id)viking_3_addLinkCancel_Link($sys_id);

  viking_3_setText_Form($sys_id);
  viking_3_setImage_Form($sys_id);
  viking_3_renameObject_Form($sys_id);
  viking_3_addObject_Form($sys_id);
  viking_3_deleteObject_Form($sys_id);
  viking_3_addLinkFrom_Form($sys_id);
  viking_3_addLinkTo_Form($sys_id);
  viking_3_addLinkCancel_Form($sys_id);
}

function viking_3_showObjectName($sys_id)
{ 
  global $par,$a3pr;
  $name  = $a3pr[$sys_id]['a3_name'];
  echo(" $name");
}

function viking_3_showFunctions($sys_id)
{
  //echo("<h2>Structure</h2>");
  echo("<table border=1>");

  echo("<tr><td>");

  echo("<b>showDb</b><br>");viking_3_showDb($sys_id);
  echo("</td><td>");
  echo("<b>showObject</b><br>");viking_3_showObject($sys_id);
  echo("</td></tr><tr><td>");

  echo("<b>selectDb</b><br>");viking_3_selectDb($sys_id);
  echo("</td><td>");
  echo("<b>showDbName</b><br>");viking_3_showDbName($sys_id);
  echo("</td><td>");
  echo("<b>showObjectName</b><br>");viking_3_showObjectName($sys_id);  
  echo("</td></tr><tr><td>");

  echo("<b>createDbLink</b><br>");viking_3_createDb_Link($sys_id);
  echo("</td><td>");
  echo("<b>createDbForm</b><br>");viking_3_createDb_Form($sys_id);
  echo("</td><td>");
  echo("</td></tr><tr><td>");

  echo("<b>deleteDbLink</b><br>");viking_3_deleteDb_Link($sys_id);
  echo("</td><td>");
  echo("<b>deleteDbForm</b><br>");viking_3_deleteDb_Form($sys_id);
  echo("</td><td>");
  echo("</td></tr><tr><td>");

  echo("<b>addObjectLink</b><br>");viking_3_addObject_Link($sys_id);
  echo("</td><td>");
  echo("<b>addObjectForm</b><br>");viking_3_addObject_Form($sys_id);
  echo("</td><td>");
  echo("</td></tr><tr><td>");

  echo("<b>renameObjectLink</b><br>");viking_3_renameObject_Link($sys_id);
  echo("</td><td>");
  echo("<b>renameObjectForm</b><br>");viking_3_renameObject_Form($sys_id);
  echo("</td><td>");
  echo("</td></tr><tr><td>");

  echo("<b>setTextLink</b><br>");viking_3_setText_Link($sys_id);
  echo("</td><td>");
  echo("<b>setTextForm</b><br>");viking_3_setText_Form($sys_id);
  echo("</td><td>");
  echo("</td></tr><tr><td>");

  echo("<b>addLinkLink</b><br>");viking_3_addLinkTo_Link($sys_id);
  echo("</td><td>");
  echo("<b>addLinkForm</b><br>");viking_3_addLinkTo_Form($sys_id);
  echo("</td><td>");
  echo("</td></tr><tr><td>");

  echo("<b>setImageLink</b><br>");viking_3_setImage_Link($sys_id);
  echo("</td><td>");
  echo("<b>setIMageForm</b><br>");viking_3_setImage_Form($sys_id);
  echo("</td><td>");
  echo("</td></tr>");
 
  echo("</table>");
 
}
?>
