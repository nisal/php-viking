<? include("php-viking/viking.php") ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="description" content="Your description goes here" />
	<meta name="keywords" content="your,keywords,goes,here" />
	<meta name="author" content="Your Name" />
	<link rel="stylesheet" type="text/css" href="index.css" title="Variant Duo" media="screen,projection" />
	<title>php-viking</title>
</head>

<body>
<div id="wrap">
	<h1><a href="index.php">PHP-VIKING 0.2</a></h1>
<p class="slogan"><? viking_4_login_Form(); viking_3_createDb_Form(1); viking_3_deleteDb_Form(1);?>   </p>
		
	<div id="menu">
		<p class="menulinks">
		<strong class="hide">Main menu:</strong>
		<? viking_3_createDb_Link(1); ?>
		<? viking_3_deleteDb_Link(1); ?>
                <? viking_4_login_logout();  ?>

	</p>
	</div>
	
	<!<img class="feature" src="sample1.jpg" width="980" height="50" alt="sample image" />
	
	<div id="content">
		<div class="left">
			<h2><? viking_3_showDbName(1); viking_3_selectDb(1);?></h2>
			<p><? viking_3_showDb(1);  viking_3_showObject(1);?></p>
		</div>

		<div class="right">
			<h2><? viking_3_showDbName(2); viking_3_selectDb(2);?></h2>
			<p><? viking_3_showDb(2);  viking_3_showObject(2);?></p>
		</div>

		<div class="middle">
			<h2><? viking_3_showDbName(3); viking_3_selectDb(3); ?></h2>
			<p><? viking_3_showDb(3);  viking_3_showObject(3);?></p>
		</div>

		<hr class="clear" />
						     <p class="centered">User: <? viking_4_showUserLoggedIn(); ?> </p>
	</div>
	<p class="footer">Copyright &copy; 2011 <a href="index.php">ADCAJO</a><br />
		Template design by <a href="http://andreasviklund.com/">Andreas Viklund</a></p>

</div>

</body>
</html>


