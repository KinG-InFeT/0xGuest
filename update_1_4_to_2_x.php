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
		Upgrade v1.4 to 2.x
		<br />
		  
<form method="POST" action="?send=1" />
Prefix: <input type="text" name="prefix" value="guestbook_"/><br />
Upgrade? <input type="radio" name="check" value="yes"/>Yes  <input type="radio" name="check" value="no"/>No
<input type="submit" value="Send" />
</form>
<?php
if((@$_GET['send'] == 1) && !empty($_POST['check']) && !empty($_POST['prefix'])) {
	
	if(@$POST['check'] == 'no')
		die(header('Location: index.php'));
	
	  mysql_connect($db_host, $db_uname, $db_pass) or die(mysql_error());
	mysql_select_db($db_name) or die(mysql_error());	
	
	$prefix = mysql_real_escape_string( htmlspecialchars( stripslashes( $_POST['prefix'] )));
	
	//rinomino la tabella dei sorgenti pastati
	mysql_query("RENAME TABLE `".$db_name."`.`0xGuest` TO `".$db_name."`.`".$prefix."signatures` ;") or die(mysql_error());
	
	//creo la tabella users
	mysql_query("CREATE TABLE `".$prefix."users` (
	  `id` int(11) NOT NULL auto_increment,
	  `password` text NOT NULL,
	  KEY `id` (`id`)
	) TYPE=MyISAM AUTO_INCREMENT=1 ;") or die(mysql_error());
	
	//popolo le tabelle
	mysql_query("INSERT INTO ".$prefix."users (password) VALUES ('".$password."');") or die(mysql_error());
 	
	//tabella config
	mysql_query("CREATE TABLE `".$prefix."config` (
	  `title` text NOT NULL,
	  `inserit_smile` INT NOT NULL,
	  `limit` INT NOT NULL
	) TYPE=MyISAM AUTO_INCREMENT=1 ;") or die(mysql_error());
	
	//popolazione tabella config
	mysql_query("INSERT INTO `".$prefix."config` (`title`, `inserit_smile`, `limit`) VALUES ('Upgraded 2.x Version', 1, ".$limit.");") or die(mysql_error());
		
	//creo il file config.php ;)
	$config = '<?php
/*
 *
 * @project 0xGuest
 * @author KinG-InFeT
 * @licence GNU/GPL
 *
 * @file config.php
 *
 * @link http://0xproject.hellospace.net#0xGuest
 *
 */

@define("__INSTALLED__", 1);

@define("__PREFIX__","'.$prefix.'");

$db_host = "'.$db_host.'";
$db_user = "'.$db_uname.'";
$db_pass = "'.$db_pass.'";
$db_name = "'.$db_name.'";
?>';
	
		// Scriviamo sul config.php i dati che ci occorrono
		if(!($open = fopen( "config.php", "w" )))
			die("Errore durante l'apertura sul file config.php<br /> Prego di controllare i permessi sul file!");
			
		fwrite ($open, $config);//Scrivo sul file config.php
		
		fclose ($open); // chiudo il file
		print "<script>alert(\"Upgrade System with success\");</script>";
		header('Location: index.php');
}
	
?>
</body>
</html>
