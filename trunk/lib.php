<style type="text/css">
   z1 {text-decoration:overline;}
   z2 {color:red;}
</style>

<!-- TinyMCE -->
<script type="text/javascript" src="tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
     tinyMCE.init({
       mode : "textareas",
	   theme : "simple"
	   });
</script>
<!-- /TinyMCE -->



<?

$info    = '';
$warning = '';
$error   = '';

$par = array();


$par['admin'] = $_SESSION['admin'];
$par['user']  = $_SESSION['user'];

$g_self = $_SERVER['PHP_SELF'];
$par['path'] = $g_self.'?a=b';


$par['p1'] = $_GET['p1'];
$par['p2'] = $_GET['p2'];
$par['p3'] = $_GET['p3'];
$par['p4'] = $_GET['p4'];
$par['p5'] = $_GET['p5'];
$par['p6'] = $_GET['p6'];


//=======================================
function displayLinkIn($structFromName,$objectFromId,$structToName,$objectToId,$objectToName,$structToSid)
//=======================================
{
  global $par;
  echo("<br><a href=index.php?a=b&sid=$structToSid&a3_object_id=$objectToId> in </a>");
  echo("<z2>$objectToName ($structToName)</z2>");
  if($par['user'])echo("<a href=index.php?p1=delete_link_in&p2=$structFromName&p3=$objectFromId&p4=$structToName&p5=$objectToId> D </a>");

}

//=======================================
function displayLinkOut($structFromName,$objectFromId,$structToName,$objectToId,$objectToName,$structToSid)
//=======================================
{
  global $par;
  echo("<br><a href=index.php?a=b&sid=$structToSid&a3_object_id=$objectToId> out </a>");
  echo("<z2>$objectToName ($structToName)</z2>");
  if($par['user'])echo("<a href=index.php?p1=delete_link_out&p2=$structFromName&p3=$objectFromId&p4=$structToName&p5=$objectToId> D </a>");

}

//=======================================
function displayObjectText($objectText)
//=======================================
{
  echo("$objectText<br>");
}

//=======================================
function displayObjectImage($imageName)
//=======================================
{
  echo("<img src=\"$imageName\" height=\"200\" alt=\"no image available\"/>");
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
          $extension = getExtension($filename);
          $extension = strtolower($extension);
          if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
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

?>
