<?
//======================================
// Template

// function viking_0_function1()
//======================================
define("TEMPLATE","Mall");



// Declarations =====================================


// read SESSION parameters ===============================
$sel_db     = $_SESSION['0_db'];
$sel_object = $_SESSION['0_object_id'];
$sel_name   = $_SESSION['0_object_name'];


// GET ==============================================

$temp = $_GET['object_id'];
if($temp)
  {
    $sel_object = $temp;
    $sel_name   = getObjectName($sel_db,$sel_object);
  }

// POST =============================================
if ($_SERVER['REQUEST_METHOD'] == "POST")
  {
    $post_action = $_POST['post_action'];
    

    if($post_action == 'post_select_db')
      {
	$sel_db  = $_POST['database'];
	$sel_object = 1;
	$index= '1.'; 
      }
  }


// Set par array values
$par['3_db']     = $sel_db;
$par['3_object'] = $sel_object;
$par['3_name']   = $sel_name;


// set SESSION parameters ===============================
$_SESSION['3_db']          = $sel_db;
$_SESSION['3_object_id']   = $sel_object;
$_SESSION['3_object_name'] = $sel_name;


//====================================================
//  Internal functions
//====================================================


//====================================================
//  HTML functions
//====================================================

?>