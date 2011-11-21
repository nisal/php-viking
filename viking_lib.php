<?



$g_db_dir = 'php-viking/db/';
if(!is_dir($g_db_dir))
  {
    echo("zzzDirectory missing: db "); exit();
    //     session_destroy();
    //     $info = $info.' Initiate db-directory';
    //     mkdir($g_db_dir);

    //     $file = $g_db_dir.'db.list';
    //     $fh = fopen($file, 'w');
    //     fclose($fh);

    //     $file = $g_db_dir.'ids.template';
    //     $fh = fopen($file, 'w');
    //     fwrite($fh, 1);    
    //     fwrite($fh, "\n");
    //     fclose($fh);

    //     $g_db_dir_images = 'db/images/';
    //     if(!is_dir($g_db_dir_images))
    //       {
    // 	$info = $info.' Initiate db/image directory';
    // 	mkdir($g_db_dir_images);
    //       }
  }





//=======================================
function getNextNodeId($db_name)
//=======================================
{
  $file = "php-viking/db/".$db_name.".ids";

  $fh = fopen($file, 'r') or die("getNextNodeId can't open file: $file");
  $row = fgets($fh);
  sscanf($row, "%d", $id);
  fclose($fh);

  $id++;

  $fh = fopen($file, 'w') or die("getNextNodeId can't open file: $file");
  fwrite($fh, $id);    
  fwrite($fh, "\n");
  fclose($fh);

  return($id);
}

//=======================================
function getUniNo()
//=======================================
{
  $file = 'php-viking/db/global.uni';

  $fh = fopen($file, 'r') or die("getUniNo can't open file: $file");
  $row = fgets($fh);
  sscanf($row, "%d", $id);
  fclose($fh);

  $id++;

  $fh = fopen($file, 'w') or die("getUniNo can't open file: $file");
  fwrite($fh, $id);
  fwrite($fh, "\n");
  fclose($fh);

  return($id);
}

//=======================================
function getNextLinkId($db_name)
//=======================================
{
  $file = "php-viking/db/".$db_name.".link";

  $fh = fopen($file, 'r') or die("read:getNextLinkId can't open file: $file");
  $row = fgets($fh);
  sscanf($row, "%d", $id);
  fclose($fh);

  $id++;

  $fh = fopen($file, 'w') or die("write:getNextLinkId can't open file: $file");
  fwrite($fh, $id);    
  fwrite($fh, "\n");
  fclose($fh);

  return($id);
}
//=======================================
function addDbList($db_name,$app)
//=======================================
{
  global $g_db_dir;
  $file = $g_db_dir.'db.list';
  $in = fopen($file, 'a+') or die("can't open file: $file");
  $temp = $db_name.' '.$app;
  fwrite($in, $temp);    
  fwrite($in, "\n");
  fclose($in);
}
//=======================================
function deleteDbList($db_name)
//=======================================
{
  global $g_db_dir;
  $file = $g_db_dir.'db.list';
  $in = fopen($file, "r") or die("can't open file r: $file");
  $work = $g_db_dir.'temp.work';
  $out = fopen($work,"w") or die("can't open file w: $work");
  while (!feof($in)) 
    {
      $row = fgets($in);
      sscanf($row, "%s %d", $temp1, $temp2);
      if($temp1 != $db_name && $row)
	{
	  $temp = $temp1.' '.$temp2;
	  fwrite($out,$temp);  
	  fwrite($out, "\n");
	}
    }
  fclose($in);
  fclose($out);
  copy($work,$file);
}
//=======================================
function getXmlFileName($db_name)
//=======================================
{
  global $g_db_dir;
  $file =$g_db_dir.$db_name.'.xml';
  return($file);
}
//=======================================
function getIdsFileName($db_name)
//=======================================
{
  global $g_db_dir;
  $file =$g_db_dir.$db_name.'.ids';
  return($file);
}

//========================
function createDb($db_name,$app)
//========================
{
  $db_name = str_replace(" ", "-", $db_name);
  echo("createDb $db_name app=$app<br>");
  global $g_db_dir;
  $file_tmp =$g_db_dir.'ids.template';
  $file_db = getXmlFileName($db_name);
  $file_ids = getIdsFileName($db_name);
  //$file_link = getLinkFileName($db_name);
  echo("createDb $file_tmp $file_db $file_ids<br>");
  if(!copy($file_tmp,$file_ids)) echo("Copy failed=copy($file_tmp,$file_ids)");
  //if(!copy($file_tmp,$file_link)) echo("Copy failed=copy($file_tmp,$file_link)");
  addDbList($db_name,$app);

  // create doctype
  $dom = new DOMDocument("1.0");

  // Create the root node
  $root = $dom->createElement("object",""); //database
  $dom->appendChild($root);
  
  $root->setAttribute('name',$db_name);
  $root->setAttribute('id','1');
  $root->setAttribute('type','node');
  //$root->setAttribute('lid','void');

  // Create the subnode: text
  $subchild = $dom->createElement("object","");
  $temp = $root->appendChild($subchild);	  
  $temp->setAttribute('name',$db_name);
  $temp->setAttribute('id','1');
  $temp->setAttribute('type','text');
  //$temp->setAttribute('lid','void');	  
  
  // Create the subnode: image
  $subchild = $dom->createElement("object","");
  $temp = $root->appendChild($subchild);	  
  $temp->setAttribute('name',$db_name);
  $temp->setAttribute('id','1');
  $temp->setAttribute('type','image');	  
  //$temp->setAttribute('lid','void'); 

  // Create the subnode: file
  $subchild = $dom->createElement("object","");
  $temp = $root->appendChild($subchild);	  
  $temp->setAttribute('name',$db_name);
  $temp->setAttribute('id','1');
  $temp->setAttribute('type','file');	  
  //$temp->setAttribute('lid','void'); 
  
  // save tree to file
  $dom->save($file_db);

  chmod($file_db,0666);
  chmod($file_ids,0666);
}

//========================
function deleteDb($db_name)
//========================
{
  echo("deleteDb");

  // Remove xml file
  $file = getXmlFileName($db_name);
  unlink($file);

  // Remove ids file
  $file = getIdsFileName($db_name);
  unlink($file);

  // Remove entry in db.list
  deleteDbList($db_name);

  // Remove all images
  $sys = 'rm php-viking/db/images/'.$db_name.'*';
  system($sys);
}

//========================
function renameDb($db_name,$new_db_name)
//========================
{
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object"; //database
  $objects = $xpath->query($question);

  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 

      if($id == 1)
	{     
	  $object->setAttribute('name',$new_db_name); 
	}
    }
  $dom->save($file);

  // TODO  rename files !!!
}

//========================
function createNode($db_name,$father_id,$node_name,$node_id,$node_type)
//                   given     given     attr:name  attr:id  attr:type      
//========================
{
  //if($node_lid == 'generate')
  //  $node_lid = getUniNo();

  //echo("CreateNode - $db_name,$father_id,$node_name,$node_id,$node_type");
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  if($father_id == 1)$question = "//object"; // database
  else
    $question = "//object";

  $objects = $xpath->query($question);
  foreach($objects as $object)
    {
      $f_id   = $object->getAttribute('id'); 
      $f_type = $object->getAttribute('type'); 

      //echo("  $f_id == $father_id type=$f_type<br>");
      if($f_id == $father_id && $f_type == 'node')
	{
	  //echo("Match  $f_id == $father_id type=$f_type<br>");
	  // Create the node
	  $child = $dom->createElement("object","");
	  $new_object = $object->appendChild($child);
	  
	  $new_object->setAttribute('name',$node_name);
	  $new_object->setAttribute('id',$node_id);
	  $new_object->setAttribute('type',$node_type);
          //$new_object->setAttribute('lid',$node_lid);	  
	}
    }
  
  $dom->save($file);
  //return($node_lid;
}

//========================
function renameNode($db_name,$object_id,$new_object_name)
//========================
{
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);
  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 
      if($id == $object_id)
	{
	  $object->setAttribute('name',$new_object_name);

	}
    }
  $dom->save($file);
}



//========================
function getNodeHandle($db_name,$object_id,$attr_type)
//========================
{

  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);

  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 
      $type = $object->getAttribute($attr_name); 
      if($id == $object_id && $type == $attr_value)
	{     
	  $res = $object;
	}
    }
  return($res);
}

//========================
function copyNode($db_name,$object_id,$new_father_id,$attr_name,$attr_value)
//========================
{

  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);


  // Get new father handle
  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 
      $type = $object->getAttribute($attr_name); 
      if($id == $new_father_id && $type == $attr_value)
	{     
	  $h_new_father = $object;
	}
    }

  // Get node handle
  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 
      $type = $object->getAttribute($attr_name); 
      if($id == $object_id && $type == $attr_value)
	{     
	  $h_node = $object;
	}
    }
  
  if($h_new_father && $h_node)
    {
      echo("copy node: $db_name,$object_id,$new_father_id,$attr_name,$attr_value");
      $h_new_father->appendChild($h_node->cloneNode(true));
    }
  else
    echo("unable to copy node:$db_name,$object_id,$new_father_id,$attr_type"); 
  
  $dom->save($file);
}
//========================
function deleteNode($db_name,$father_id,$object_id,$attr_name,$attr_value)
//========================
{
  //echo("function deleteNode($db_name,$father_id,$object_id,$attr_name,$attr_value)");
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);
  
  // Get old father handle
  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 
      $type = $object->getAttribute($attr_name); 
      //echo("$db_name,a1 ($id) == ($father_id) && ($type) == ($attr_value) <br>");
      //if($id == $father_id && $type == $attr_value)
      if($id == $father_id)
	{  
	  //echo("a2 $id == $father_id && $type == $attr_value <br>");   
	  $h_father = $object;
	  if($object->hasChildNodes())
	    {
	      $childs = $object->childNodes;
	      foreach($childs as $child)
		{
		  $id   = $child->getAttribute('id'); 
		  $type = $child->getAttribute($attr_name);
		  //echo("a3 $type == $attr_value<br>");
		  if($id == $object_id && $type == $attr_value)
		    {     
		     // echo("xxxdelete node: struct=$db_name, father=$father_id,object=$object_id,attr=$attr_name,attr_value=$attr_value");
		      $object->removeChild($child);
		    }
		}
	    }
	}
    } 
  
  $dom->save($file);
}
//========================
function setNodeValue($db_name,$object_id,$attr_name,$attr_value,$new_value)
//========================
{

  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);

  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 
      $type = $object->getAttribute($attr_name); 
      echo("$id == $object_id && $type == $attr_value<br>");
      if($id == $object_id && $type == $attr_value)
	{     
	  $object->nodeValue = $new_value;
	}
    }
  $dom->save($file);
}

//========================
function getNodeValue($db_name,$node_id,$attr_name,$attr_value)
//========================
{
  $res = 'void';

  if(!$db_name) return($res);
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $nodes = $xpath->query($question);
  
  foreach($nodes as $node)
    {
      $id   = $node->getAttribute('id'); 
      $attr = $node->getAttribute($attr_name); 
      //$lid  = $node->getAttribute('lid');

      if($id == $node_id && $attr == $attr_value)
	{
	  $res = $node->nodeValue;
	  // 	  $res2 = $object->nodeName;
	  // 	  $res3 = $object->nodeType;
	  // 	  $res4 = $object->textContent;
	  // echo("value=$res Name=$res2 Type=$res3 text=$res4<br>");
	}
    }
  return($res);
}


//========================
function createAttribute($db_name,$object_id,$attribute_name,$attribute_value)
//========================
{

  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);

  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 
      if($id == $object_id)
	{
	  $attr = $dom->createAttribute($attribute_name);
	  $object->appendChild($attr);
	  
	  $temp = $dom->createTextNode($attribute_value);
	  $attr->appendChild($temp);
	  
	  $dom->save($file);
	}
    }
}
//========================
function deleteAttribute($db_name,$element_name,$attribute_name,$attribute_value)
//========================
{
}

//========================
function setNodeAttr($db_name,$object_id,$attribute_name,$new_attribute_value)
//========================
{
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);

  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 
      if($id == $object_id)
	{
	  $object->setAttribute($attribute_name,$new_attribute_value);	  
	  $dom->save($file);
	}
    }
}

//========================
function getNodeAttr($db_name,$object_id,$attribute_name)
//========================
{

  $res = 'void';
  if(!$db_name) return($res);
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);

  foreach($objects as $object)
    {
      $id   = $object->getAttribute('id'); 
      $type = $object->getAttribute('type'); 
      if($id == $object_id && $type == 'node')
	{
	  $res = $object->getAttribute($attribute_name);	  
	}
    }
  return($res);
}


//========================
function getNodeIdbyAttr($db_name,$attribute_name,$attribute_value)
//========================
{
  $nn = 0;
  $res = 'void';
  if(!$db_name) return($res);
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);

  foreach($objects as $object)
    {
      $type = $object->getAttribute('type'); 
      $temp = $object->getAttribute($attribute_name);
      $string1 = implode(str_split($temp));
      $string2 = implode(str_split($attribute_value));
//echo("($temp) == ($attribute_value) && ($type) <br> ");
      if($temp == $attribute_value && $type == "node")
	{
          $nn++;
//echo("***hit $nn<br>");
	  $res = $object->getAttribute('id');	  
	}
    }
  if($nn > 1) $res='multiple';
  return($res);
}

//========================
function getObjectName($db,$object_id)
//========================
{

  if($object_id == 1)
    $res = $db;
  else
    {
      $res = "void";
      $res = getNodeAttr($db,$object_id,'name');
    }
  return($res);
}

//========================
function setObjectName($db,$object_d,$value)
//========================
{
  $res = "void";
  setNodeAttr($db,$object_id,'name',$value);
  return($res);
}


//========================
function getObjectId($db,$object_id)
//========================
{
  $res = "void";
  $res = getNodeAttr($db,$object_id,'id');
  return($res);
}


//========================
function getObjectText($db,$object_id)
//========================
{
  $res = "void";
  $res = getNodeValue($db,$object_id,'type','text');
  return($res);
}

//========================
function getObjectImage($db,$object_id)
//========================
{
  $res = "void";
  $res = getNodeValue($db,$object_id,'type','image');
  return($res);
}

//========================
function getObjectFile($db,$object_id)
//========================
{
  $res = "void";
  $res = getNodeValue($db,$object_id,'type','file');
  return($res);
}

//========================
function getObjectImageName($db,$object_id)
//========================
{
  $res = "void";
  $res = 'php-viking/db/images/'.$db.'-'.$object_id.'.jpg';
  return($res);
}


//========================
function setObjectText($db,$object_id,$value)
//========================
{
  setNodeValue($db,$object_id,'type','text',$value);
}

//========================
function setObjectImage($db,$object_id,$value)
//========================
{
  setNodeValue($db,$object_id,'type','image',$value);
}

//========================
function setObjectFile($db,$object_id,$value)
//========================
{
  echo("$db,$object_id,'type','file',$value");
  setNodeValue($db,$object_id,'type','file',$value);
}


//========================
function listAllObjects($db)
//========================
{
  echo("Not implemented");
}

//========================
function moveObject($db,$object_id,$old_father_id,$new_father_id)
//========================
{
  copyNode($db,$object_id,$new_father_id,'type','node');
  deleteNode($db,$old_father_id,$object_id,'type','node');
}

//========================
function deleteObject($db,$object_id,$father_id)
//========================
{
  deleteNode($db,$father_id,$object_id,'type','node');
}

//========================
function getObjectIdbyName($db,$object_name)
//========================
{
  $res = "void";
  $res = getNodeIdbyAttr($db,'name',$object_name);
  return($res);
}

//========================
function showObject($app,$db_name,$object_id)
//========================
{
  global $par;
  $path        = $par['path'];
  $open_file   = $par['p1'];
  //echo("showObject: $db_name,$object_id<br>");
  //if($object_id == 1)echo("$db_name<br>");
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  $question = "//object";
  $objects = $xpath->query($question);
  foreach($objects as $object)
    {
      $id = $object->getAttribute('id'); 
      $name = $object->getAttribute('name'); 
      $type = $object->getAttribute('type'); 
      //echo("Aid= $id $object_id<br>");
      if($id == $object_id && $type == 'node')
	{
	  echo("<b>$name $id</b><br>");
	  $childs = $object->childNodes;
	  foreach($childs as $child)
	    {
	      $attr_name = $child->attributes->getNamedItem("name")->nodeValue;
	      $attr_id   = $child->attributes->getNamedItem("id")->nodeValue;
	      $attr_type = $child->attributes->getNamedItem("type")->nodeValue;
              //$attr_lid  = $child->attributes->getNamedItem("lid")->nodeValue;
	      //if($attr_type != 'node')echo("Show: $attr_name $attr_id $attr_type<br>");
	      
              if($attr_type == 'linkOut')
                {
		  $temp = getObjectName($attr_name,$attr_id);
                  //$temp = getNodeValue($db_name,$attr_id,$attr_lid,'type','linkFrom');
                  if($temp)
		    {
		      $sid = $par[$attr_name];
                      displayLinkOut($app,$db_name,$object_id,$attr_name,$attr_id,$temp,$sid);
		    }
                }

              if($attr_type == 'linkIn')
                {
		  $temp = getObjectName($attr_name,$attr_id);
                  //$temp = getNodeValue($db_name,$attr_id,$attr_lid,'type','linkTo');
                  if($temp)
		    {
                      $sid = $par[$attr_name];
                      displayLinkIn($app,$db_name,$object_id,$attr_name,$attr_id,$temp,$sid);
		    }
                }

	      if($attr_type == 'text')
	      	{
		  $temp = getNodeValue($db_name,$attr_id,'type','text');
		  if($temp)displayObjectText($temp);
		}
	      if($attr_type == 'image')
	      	{
		  $temp = getNodeValue($db_name,$attr_id,'type','image');
		  //if($temp)echo("Image: $temp<br>");
		  //$image_name = getObjectImageName($db_name,$object_id);
		  //if($temp == $image_name)
		  $image_name = $temp;
		  if($temp)displayObjectImage($image_name);
		}
	      if($attr_type == 'file')
	      	{
		  $temp = getNodeValue($db_name,$attr_id,'type','file');
		  $file_name = $temp;

		  if($temp)
		    {
		      if($open_file == "open_file")
			{
			  echo("<a href=$path&a3_sid=$sys_id&p1=close_file>".FILE_CLOSE."</a><br>");
			  displayObjectFile($file_name);
			}
		      else
			echo("<a href=$path&a3_sid=$sys_id&p1=open_file>".FILE_OPEN."</a><br>");
		    }
		}
	    }  
	} 
    }
  echo("<hr>");
}

//========================
function createObject($db_name,$father_id,$object_name,$object_id)
//========================
{

  //echo("CreateObject - $db_name,$father_id,$object_name");
  $file = getXmlFileName($db_name);
  $dom = new DOMDocument();
  $dom->load($file);
  $xpath = new DOMXPath($dom);
  if($father_id == 1)$question = "//object"; // database
  else
    $question = "//object";

  $objects = $xpath->query($question);
  foreach($objects as $object)
    {
      $f_id   = $object->getAttribute('id');
      $f_type = $object->getAttribute('type'); 

      //echo("  $f_id == $father_id");
      if($f_id == $father_id && $f_type == 'node')
	{
	  // Create the node
	  $child = $dom->createElement("object","");
	  $new_object = $object->appendChild($child);
	  
	  $new_object->setAttribute('name',$object_name);
	  $new_object->setAttribute('id',$object_id);
	  $new_object->setAttribute('type','node');

	  // Create the subnode: text
	  $subchild = $dom->createElement("object","");
	  $temp = $child->appendChild($subchild);	  
	  $temp->setAttribute('name',$object_name);
	  $temp->setAttribute('id',$object_id);
	  $temp->setAttribute('type','text');

	  // Create the subnode: image
	  $subchild = $dom->createElement("object","");
	  $temp = $child->appendChild($subchild);	  
	  $temp->setAttribute('name',$object_name);
	  $temp->setAttribute('id',$object_id);
	  $temp->setAttribute('type','image');	  

	  // Create the subnode: file
	  $subchild = $dom->createElement("object","");
	  $temp = $child->appendChild($subchild);	  
	  $temp->setAttribute('name',$object_name);
	  $temp->setAttribute('id',$object_id);
	  $temp->setAttribute('type','file');	  
	}
    }
  
  $dom->save($file);
}

?>

