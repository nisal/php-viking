<?php
//======================================
// 5 Shop
//
// viking_5_createProduct()
// viking_5_showProduct()
// viking_5_listProducts()
// viking_5_editProduct()
//======================================
define("T_PRODUCT_NAME","Produktnamn");
define("T_PRODUCT_PRICE","Produktpris");
define("T_PRODUCT_IMAGE","Produktbild");
define("T_PRODUCT_STATUS","Produktstatus");
define("T_PRODUCT_DESCRIPTION","Produktbeskrivning");
define("T_CREATE_PRODUCT","Skapa produkt");
define("T_EDIT_PRODUCT","Editera produkt");

// Always use database: shop
$_SESSION['a5_db'] = 'shop';

// SESSION parameters ===============================
$sel_db     = $_SESSION['a5_db'];
$sel_id     = $_SESSION['a5_object_id'];
$sel_name   = $_SESSION['a5_object_name'];

// Initiate database if necessary
$file = getXmlFileName($sel_db);
if(!file_exists($file))
  {
    createDb($sel_db,5);
	    $sel_object = 1;
	    $_SESSION['a5_db']     = $sel_db; 
	    $_SESSION['a5_object'] = $sel_object;
	    echo("Database initiated !");
  }


// GET ==============================================

$temp = $_GET['a5_object_id'];
if($temp)
  {
    $sel_object = $temp;
    $_SESSION['a5_object_id'] = $temp;
    $_SESSION['a5_object_name'] = getObjectName($sel_db,$sel_object);
  }

// POST =============================================
if ($_SERVER['REQUEST_METHOD'] == "POST")
  {
    $post_action = $_POST['a5_post_action'];
    
    if($post_action == 'post_create_product')
      {
	$sel_name   = $_POST['a5_product_name'];
	$sel_proce  = $_POST['a5_product_price'];
	$sel_status = $_POST['a5_product_status'];
	$sel_text   = $_POST['a5_product_text'];
	$sel_id     = getNextNodeId($sel_db);

	$father_id = 1;
	createObject($sel_db,$father_id,$sel_name,$sel_id);

	if($sel_db && $sel_id)
	  {
	    $image_name = uploadImage($sel_db,$sel_id);
	    if($image_name)
	      setObjectImage($sel_db,$sel_id,$image_name);
	  }
      }
  }


// Set par array values
$par['a5_db']     = $_SESSION['a5_db'];
$par['a5_object'] = $_SESSION['a5_object_id'];
$par['a5_name']   = $_SESSION['a5_object_name'];


//====================================================
//  Internal functions
//====================================================


//====================================================
//  HTML functions
//====================================================

function viking_5_createProductLink()
{
  global $par;
  $path       = $par['path'];
  $app_open   = $par['p1'];

  if($app_open == "open_5_createProduct")
    {
      echo(T_CREATE_PRODUCT);
    }
  else
    echo("<a href=$path&p1=open_5_createProduct>".T_CREATE_PRODUCT."</a>");
}

function viking_5_createProductForm()
{
  global $par;

  $path   = $par['path'];
  $name   = $par['a5_name'];
  $price  = $par['a5_price'];
  $descr  = $par['a5_text'];
  $status = $par['a5_status'];

  $app_open = $par['p1'];

  if($app_open == "open_5_createProduct")
    {
      echo("<table border=1>");
      echo("<form name=\"form_create_product\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\> ");
      echo("<input type=\"hidden\" name=\"a5_post_action\" value=\"post_create_product\">");
      echo("<tr><td>");
      echo(T_PRODUCT_NAME."</td><td><input type=\"text\" name=\"a5_product_name\" value=\"$name\">");
      echo("</td></tr><tr><td>");
      echo(T_PRODUCT_PRICE."</td><td><input type=\"text\" name=\"a5_product_price\" value=\"$price\">");
      echo("</td></tr><tr><td>");
      echo(T_PRODUCT_STATUS."</td><td><input type=\"text\" name=\"a5_product_status\" value=\"$status\">");
      echo("</td></tr><tr><td>");
      echo(T_PRODUCT_DESCRIPTION."</td><td><textarea name=\"a5_product_text\" cols=40 rows=6>$descr</textarea>");
      echo("</td></tr><tr><td>");
      echo(T_PRODUCT_IMAGE."</td><td><input type=\"file\" name=\"image\">");
      echo("</td></tr><tr><td>");
      echo("</td><td><input type =\"submit\" name=\"form_submit\" value=\"".T_CREATE_PRODUCT."\">");
      echo("</td></tr>");
      echo("</form>");
      echo("</table>");
    }
}


function viking_5_editProductLink()
{
  global $par;
  $path       = $par['path'];
  $app_open   = $par['p1'];

  if($app_open == "open_5_editProduct")
    {
      echo(T_EDIT_PRODUCT);
    }
  else
    echo("<a href=$path&p1=open_5_editProduct>".T_EDIT_PRODUCT."</a>");
}

function viking_5_editProductForm()
{
  global $par;

  $path   = $par['path'];
  $name   = $par['a5_name'];
  $price  = $par['a5_price'];
  $text   = $par['a5_text'];
  $status = $par['a5_status'];

  $app_open = $par['p1'];

  if($app_open == "open_5_editProduct")
    {
      echo("<table border=1>");
      echo("<form name=\"form_edit_product\" action=\"$path\" method=\"post\" enctype=\"multipart/form-data\> ");
      echo("<input type=\"hidden\" name=\"a5_post_action\" value=\"post_edit_product\">");
      echo("<tr><td>");
      echo(T_PRODUCT_NAME."</td><td><input type=\"text\" name=\"a5_product_name\" value=\"$name\">");
      echo("</td></tr><tr><td>");
      echo(T_PRODUCT_PRICE."</td><td><input type=\"text\" name=\"a5_product_price\" value=\"$price\">");
      echo("</td></tr><tr><td>");
      echo(T_PRODUCT_STATUS."</td><td><input type=\"text\" name=\"a5_product_status\" value=\"$status\">");
      echo("</td></tr><tr><td>");
      echo(T_PRODUCT_TEXT."</td><td><textarea name=\"a5_product_text\" cols=40 rows=6>$text</textarea>");
      echo("</td></tr><tr><td>");
      echo(T_PRODUCT_IMAGE."</td><td><input type=\"file\" name=\"image\">");
      echo("</td></tr><tr><td>");
      echo("</td><td><input type =\"submit\" name=\"form_submit\" value=\"".T_EDIT_PRODUCT."\">");
      echo("</td></tr>");
      echo("</form>");
      echo("</table>");
    }
}

function viking_5_showProduct()
{
}

function viking_5_listProducts()
{
}


function viking_5_showFunctionsLink()
{
  global $par;
  $path       = $par['path'];
  $app_open   = $par['p1'];

  if($app_open == "open_5_showFunctions")
    {
      echo(T_SHOP);
    }
  else
    echo("<a href=$path&p1=open_5_showFunctions>".T_SHOP."</a>");
}

function viking_5_showFunctions()
{
  echo("<h2>Shop</h2>");
  echo("<table border=1>");
  echo("<tr><td>");
  echo("<b>createProductLink</b><br>");
  viking_5_createProductLink();
  echo("</td><td>");

  echo("<b>createProductForm</b><br>");
  viking_5_createProductForm();
  echo("</td><td>");

  echo("<b>showProduct</b><br>");
  viking_5_showProduct();
  echo("</td></tr><tr><td>");

  echo("<b>listProducts</b><br>");
  viking_5_listProducts();
  echo("</td><td>");

  echo("<b>editProductLink</b><br>");
  viking_5_editProductLink();
  echo("</td><td>");

  echo("<b>editProductForm</b><br>");
  viking_5_editProductForm();
  echo("</td></tr>");
  echo("</table>");

}


?>
