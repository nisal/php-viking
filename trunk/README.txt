README

Install also tinymce (latest version used is 3.4.6)

Directory structure:

your application directory:

index.php  subdir: php-viking    tinymce

===============================================================================
This is a library of PHP-funtions to be used to add dynamic to your HTML-page(s).

Follow the steps below to get started:

1. Download php_viking.zip from ???

2. Unzip the file at the same directory as your HTML-page. 
   Maybe you need to chmod 777 the sub-directories

3. Edit your HTML-page:
   - change the file extension to .php
   - add the line at the top of the file:  
		  <? include("php-viking/viking.php") ?>
   
   Now you are ready to add the viking functions you need!

4. Add viking functions

   You can insert a viking functions at suitable locations in your HTML-code, by adding
                  <? viking_<N>_<function(M)> ?>
   where N is the number of the application (see list below) and <function(M)> is a 
   function available for this application. Example:
      <? viking_3_addObject

   The following applications are available:

        1 object_manager

        2 gallery

        3 structure

           Functions 
              phpsax_3_selectDb(id)
              phpsax_3_createDb(id)
              phpsax_3_createDbLink(id)
              phpsax_3_addObject(id)
              phpsax_3_addObjectLink(id)
              phpsax_3_setText(id)
              phpsax_3_setTextLink(id)
              phpsax_3_setImage(id)
              phpsax_3_setImageLink(id)
              phpsax_3_showDb(id)
              phpsax_3_showDbName(id)
              phpsax_3_showObject(id)
              phpsax_3_showObjectName(id)
           where id (value 1,2,3,4) is used to seperate between different instances 
           of the application.
           

        4 users

           Functions
              phpsax_4_showUserLoggedIn()
              phpsax_4_addUser()
              phpsax_4_delUser()
              phpsax_4_editUser()
              phpsax_4_loginUser()
              phpsax_4_logoutUser()

        5 shop



========================================================================
Example of html-code in a viking-file.
========================================================================
<?include("php-viking/viking.php") ?>
<html>
<head>
<title>Site Name</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="870">
  <tr>
    <td colspan="2" width="870">
    <img border="0" src="images/k_01.jpg" width="870" height="168"></td>
  </tr>
  <tr>
    <td colspan="2" class="nav" style="background-image: url('images/k_02.jpg')" width="870">
    <? phpsax_3_addObjectLink(1); ?>
	<a href="#">&nbsp;Link</a>1&nbsp;&nbsp;
	<a href="#">Link</a>2&nbsp;&nbsp;
	<a href="#">Link</a>3&nbsp;&nbsp;
	<a href="#">&nbsp;Link</a>4&nbsp;&nbsp;
	<a href="#">Link</a>5
    </td>
  </tr>
    <td width="205" class="leftm">
      <table border="0" cellpadding="0" cellspacing="0">
        <tr>
	  <td class="lefth" style="background-image: url('images/k_03.jpg')"><b>Menu</b></td>
        </tr>
        <tr>
          <td>
<? viking_3_selectDb(1); ?>
<? viking_3_selectDb(2); ?>
<? viking_3_selectDb(3); ?>
<? viking_3_selectDb(4); ?>

	  </td>
	</tr>

	<tr>
	  <td class="lefth" style="border-top:1px solid #000000; background-image:url('images/k_03.jpg')"><b>
	  <?viking_3_showDbName(1);?></b></td>
	</tr>
	<tr>
	  <td>
	<? viking_3_showDb(1); ?>
	  </td>
	</tr>

	<tr>
	  <td class="lefth" style="border-top:1px solid #000000; background-image:url('images/k_03.jpg')"><b>
      <?viking_3_showDbName(2);?></b></td>
	</tr>
	<tr>
	  <td>
	<? viking_3_showDb(2); ?>
	  </td>
	</tr>

	<tr>
	  <td class="lefth" style="border-top:1px solid #000000; background-image:url('images/k_03.jpg')"><b>
      <?viking_3_showDbName(3);?></b></td>
	</tr>
	<tr>
	  <td>
	<? phpsax_3_showDb(3); ?>
	  </td>
	</tr>

	<tr>
	  <td class="lefth" style="border-top:1px solid #000000; background-image:url('images/k_03.jpg')"><b>
      <?viking_3_showDbName(4);?></b></td>
	</tr>
	<tr>
	  <td>
	<? viking_3_showDb(4); ?>
	  </td>
	</tr>

        <tr>
	  <td class="lefth" style="border-top:1px solid #000000; background-image:url('images/k_03.jpg')">
      <b>Ads</b></td>
	</tr>
	<tr>
	  <td>
		&nbsp;<p align="center">Your ads goes here</p>
        <p><br>
	  </td>
	</tr>
      </table>
    </td>
    <td width="660" class="rightm">
	<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
	<p style="margin-top: 0; margin-bottom: 0">
	<? viking_3_showObject(1); ?>
	<? viking_3_showObject(2); ?>
	<? viking_3_showObject(3); ?>
	<? viking_3_showObject(4); ?>
    </td>
  </tr>
</table>
</body>
</html>
