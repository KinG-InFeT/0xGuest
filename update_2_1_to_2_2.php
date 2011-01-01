<?php
ob_start();

if(!file_exists("config.php"))
	die("File config.php does not exist!");
else
	include("config.php");

?>
<html>
<head><title>Upgrade File</title></head>
<body>		  
		<h1 align="center">Upgrade System of 0xGuest</h1><br />
		<br />
		Upgrade v2.1 to 2.2
		<br />
		  
<form method="POST" action="?send=1" />
Upgrade? <input type="radio" name="check" value="yes"/>Yes  <input type="radio" name="check" value="no"/>No
<input type="submit" value="Send" />
</form>
<?php
if(@$_GET['send'] == 1) {
	
	if(@$_POST['check'] == 'no')
		die(header('Location: index.php'));
	
	  mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
	mysql_select_db($db_name) or die(mysql_error());
	
	//aggiungo la colonna web_site
	mysql_query("ALTER TABLE `".__PREFIX__."signatures` ADD web_site TEXT") or die(mysql_error());
	
	//aggiungo la colonna email
	mysql_query("ALTER TABLE `".__PREFIX__."signatures` ADD email TEXT") or die(mysql_error());
 		
	//fixo il problema inserit smile  nella config
	mysql_query("INSERT INTO `".__PREFIX__."config` (`inserit_smile`) VALUES (1);") or die(mysql_error());

		print "<script>alert(\"Upgrade System with success\");</script>";
		header('Location: index.php');
}
	
?>
</body>
</html>
