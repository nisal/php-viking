<?
//======================================
// Object Manager
//======================================


//====================================================
// PxHmPl functions
//====================================================

function pxhmpl_1_createObject($par)
{
  $db_id = $par['om_db_id'];
  echo("Object Manager: createObject");
  $path        = $par['path'];

  echo("<form name=\"form_\" action=\"$path\" method=\"post\"> ");
  echo("<input type=\"hidden\" name=\"post_action\" value=\"post_add_db\">");
  echo("<input type=\"text\" name=\"db\" value=\"\">");
  echo("<input type =\"submit\" name=\"form_submit\" value=\"Create Database\">");
  echo("</form>");

}
function pxhmpl_1_deleteObject($par)
{
  $db_id     = $par['om_db_id'];
  $object_id = $par['om_object_id'];

  echo("Object Manager: deleteObject");
}
function pxhmpl_1_editObject($par)
{
  $db_id     = $par['om_db_id'];
  $object_id = $par['om_object_id'];

  echo("Object Manager: editObject");
}
function pxhmpl_1_showObject($par)
{
  $db_id     = $par['om_db_id'];
  $object_id = $par['om_object_id'];

  echo("Object Manager: showObject");
}
function pxhmpl_1_listObjects($par)
{
  $db_id = $par['om_db_id'];
  echo("Object Manager: listObject");
}

?>
