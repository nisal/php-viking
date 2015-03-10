<?php

$info    = '';
$warning = '';
$error   = '';

$par = array();
$vikingError   = array();
$vikingWarning = array();
$vikingError[0]   = 0;
$vikingWarning[0] = 0;

$errIx = 0;
$warIx = 0;

//  pv-support
$par['inc'] = 0;


$par['pv']      = $_SESSION['pv'];
$par['prev_pv'] = $_SESSION['pv']; // Always previous
$par['pv_mem']  = $_SESSION['pv_mem'];

if($_GET['pv'])$par['pv']         = $_GET['pv'];
if($_GET['pv_mem'])$par['pv_mem'] = $_GET['pv_mem'];

$_SESSION['pv']      = $par['pv'];
$_SESSION['pv_mem']  = $par['pv_mem'];
$_SESSION['pv_prev'] = $par['pv_prev'];

//$par['admin'] = $_SESSION['admin'];
$par['user']  = $_SESSION['user'];

$g_self = $_SERVER['PHP_SELF'];
$par['path'] = $g_self.'?a=b';

$path = $par['path'];


$par['p1'] = $_GET['p1'];
$par['p2'] = $_GET['p2'];
$par['p3'] = $_GET['p3'];
$par['p4'] = $_GET['p4'];
$par['p5'] = $_GET['p5'];
$par['p6'] = $_GET['p6'];

//=======================================
function vikingError($msg)
//=======================================
{
  global $vikingError,$errIx;
  $errIx++;
  //echo("Error found: ($errIx) $msg");
  $vikingError[0] = $errIx;
  $vikingError[$errIx] = $msg;
  return;
}

//=======================================
function vikingWarning($msg)
//=======================================
{
  global $vikingWarning,$warIx;
  $warIx++;
  //echo("Warning found: ($warIx) $msg");
  $vikingWarning[0] = $warIx;
  $vikingWarning[$warIx] = $msg;
  return;
}


//=======================================
function displayLinkIn($app,$structFromName,$objectFromId,$structToName,$objectToId,$objectToName,$structToSid)
//=======================================
{
  global $par;

  $app_sid = $app.'_sid';
  echo("<br/><a href=index.php?a=b&$app_sid=$structToSid&a3_object_id=$objectToId> in </a>");
  echo("<z2>$objectToName ($structToName)</z2>");
  if($par['user'])echo("<a href=index.php?p1=delete_link_in&p2=$structFromName&p3=$objectFromId&p4=$structToName&p5=$objectToId> D </a>");

}

//=======================================
function displayLinkOut($app,$structFromName,$objectFromId,$structToName,$objectToId,$objectToName,$structToSid)
//=======================================
{
  global $par;

  $app_sid = $app.'_sid';
  echo("<br/><a href=index.php?a=b&$app_sid=$structToSid&a3_object_id=$objectToId> out </a>");
  echo("<z2>$objectToName ($structToName)</z2>");
  if($par['user'])echo("<a href=index.php?p1=delete_link_out&p2=$structFromName&p3=$objectFromId&p4=$structToName&p5=$objectToId> D </a>");

}

//=======================================
function displayObjectText($objectText)
//=======================================
{
  echo("$objectText<br/>");
}

//=======================================
function displayObjectImage($imageName)
//=======================================
{
  echo("<br/><img src=\"$imageName\" height=\"200\" alt=\"no image available\"/><br/>");
}

//=======================================
function displayObjectFile($fileName)
//=======================================
{
  // echo("$fileName<br/>");
  
  $in = fopen($fileName, "r") or die("can't open file r: $fileName");
  while (!feof($in)) 
    {
      $row = fgets($in);
      echo("$row<br/>");
    }
  fclose($in);
}


//=======================================
function getExtension($str)
//======================================= 
{
  $i = strrpos($str,".");
  if (!$i) { return ""; }
  $l = strlen($str) - $i;
  $ext = substr($str,$i+1,$l);
  return $ext;
}

//=======================================
function safeText($text)
//=======================================
{
   $text = str_replace("#", "No.", $text); 
   $text = str_replace("$", "Dollar", $text); 
   $text = str_replace("%", "Percent", $text); 
   $text = str_replace("^", "", $text); 
   $text = str_replace("&", "and", $text); 
   $text = str_replace("*", "", $text); 
   $text = str_replace("?", "", $text); 
   return($text);
}

//=======================================
function safeUserName($name)
//=======================================
{
   $name = str_replace("#", "_", $name); 
   $name = str_replace("$", "_", $name); 
   $name = str_replace("%", "_", $name); 
   $name = str_replace("^", "_", $name); 
   $name = str_replace("&", "_", $name); 
   $name = str_replace("*", "_", $name); 
   $name = str_replace("?", "_", $name); 
   $name = str_replace(" ", "_", $name); 
   $name = str_replace("+", "_", $name); 
   $name = str_replace("!", "_", $name); 
   $name = str_replace(".", "_", $name); 
   return($name);
}


//=======================================
function uploadImage($db,$id)
//=======================================
{

  define ("MAX_SIZE","300");
  $errors=0;
  $newname = '';

  if(isset($_POST['submit_image']))
    {

      $image=$_FILES['image']['name'];
      if ($image)
        {
          $filename = stripslashes($_FILES['image']['name']);
          $filename = safeText($filename);
          $extension = getExtension($filename);
          $extension = strtolower($extension);
          if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "txt"))
            {
              echo '<h1>Unknown extension!</h1>';
              $errors=1;
            }
          else
            {
              $size=filesize($_FILES['image']['tmp_name']);
              if ($size > MAX_SIZE*1024)
                {
                  echo "<h1>You have exceeded the size limit! $size</h1>";
                  $errors=1;
                }
              //$image_name=time().'.'.$extension;
              $image_name = $db.'-'.$id.'.'.$extension;
              $newname="php-viking/db/images/".$image_name;
              $copied = copy($_FILES['image']['tmp_name'], $newname);
              if (!$copied)
                {
                  echo '<h1>Copy unsuccessfull!</h1>';
                  $errors=1;
                }
            }
        }
    }

  if(isset($_POST['submit_image']) && !$errors)
    {
      chmod($newname,0666);
      return($newname);
      //echo "<h1>File Uploaded Successfully!</h1>";
    }
  return($newname);
}
//=======================================
function uploadFile()
//=======================================
{

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
              $newname="php-viking/import/".$file_name;
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

//=====================================================
// HTML
//=====================================================

function viking_lib_showError()
{
  global $vikingError;
  
  $temp = $vikingError[0];
  //echo("Errors:$temp ");
  if($vikingError[0] < 1)return;
  for ($ii=1; $ii<=$vikingError[0];$ii++)
  {
    $temp = $vikingError[$ii];
    echo("<br/>$ii Error: $temp <br/>");
  }
}

function viking_lib_showWarning()
{
  global $vikingWarning;

  $temp = $vikingWarning[0];
  //echo("Warnings: $temp");
  if($vikingWarning[0] < 1)return;
  for ($ii=1; $ii<=$vikingWarning[0];$ii++)
  {
    $temp = $vikingWarning[$ii];
    echo("<br/>$ii Warning: $temp <br/>");
  }
}

//========================================
function viking_lib_editInfoText_Link()
//========================================
{
  global $par;
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];

  if($app_open == "open_lib_editTextInfo")
    {
      echo("Edit text");
    }
  else  if($user == 'admin' )
    echo("<a href=$path&p1=open_lib_editTextInfo>Edit text</a>");
}

//========================================
function viking_lib_editInfoText_Form()
//========================================
{
  global $par,$a3pr;
  $path       = $par['path'];
  $user       = $par['user'];
  $app_open   = $par['p1'];

  if($app_open == "open_lib_editTextInfo"  &&  $user == 'admin')
    {
      echo("<form name=\"form_edit_text_info\" action=\"$path\" method=\"post\"> ");
      echo("<input type=\"hidden\" name=\"a3_post_action\" value=\"post_edit_text_info\">");
      echo("<textarea name=\"lib_text_info\" cols=10 rows=6>$data</textarea>");
      echo("<input type =\"submit\" name=\"form_submit\" value=\"".T_SAVE."\">");
      echo("</form>");
    }
}

//========================================
function viking_lib_tinyMCE()
//========================================
{
  global $par;
  $tinyMCE = $par['tinyMCE'];
 
  if($tinyMCE)
    {
      echo("<!-- TinyMCE -->");
      echo("<script type=\"text/javascript\" src=\"tinymce/jscripts/tiny_mce/tiny_mce.js\"></script>");
      echo("<script type=\"text/javascript\">");
      echo("tinyMCE.init({");
      echo("mode : \"textareas\",");
      //echo("theme : \"simple\"");
      echo("theme : \"advanced\"");
      echo("   });");
      echo("</script>");
      echo("<!-- /TinyMCE -->");
    }
}

?>
