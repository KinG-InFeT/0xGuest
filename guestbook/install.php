<?php
/*
 *
 * @project 0xGuest
 * @author KinG-InFeT
 * @licence GNU/GPL
 *
 * @file install.php
 *
 * @link http://0xproject.hellospace.net#0xGuest
 *
 */

ob_start();

if(!(file_exists('./config.php')))
	die("<b>File 'config.php' inesistente! <br />
		Creare un file vuoto chiamato config.php </b>\n");

if(!(is_writable('./install.php')))
	die ("<b>File 'install.php' non &egrave; scrivibile!<br />
		 Settare i permessi a 777 (chmod)</b>\n");		 	
		 
if(!(is_writable('./config.php')))
	die ("<b>File 'config.php' non &egrave; scrivibile!<br />
		 Settare i permessi a 777 (chmod)</b>\n");
		 
if(!(phpversion() >= '5.2.0')) {
	die('PHP version is: '.phpversion().' ERROR! Upgrade to last version > 5');
}
		 
include("config.php");

if( isSet($_GET['delete_install']) && $_GET['delete_install'] == 1 ){
	if( unlink("./install.php") == FALSE ){
		chmod("./install.php", 0777);
		die(header('Location: install.php?delete_install=1'));
	}else{
		header("location: index.php");
	}
}

if(defined("__INSTALLED__"))
	die("0xGuest already installed! <br /> Go to <a href=\"index.php\">Home Blog</a>");

function VarProtect ($content) {
	if (is_array ($content)) {
		foreach ($content as $key => $val)
			$content[$key] = mysql_real_escape_string (htmlentities (stripslashes ($content[$key])));
	}else{
		$content = mysql_real_escape_string (htmlentities ($content));
	}
	
	return (get_magic_quotes_gpc () ? stripslashes ($content) : $content);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
	<title>Welcome to 0xGuest</title>
	<style type="text/css">
	<!--
	a:link {
		color:white; 
		text-decoration: none;
	}
	
	a:hover {
		color:#66FFCC;
		text-decoration:underline;
	}
	
	a:visited {
		 color:#FF6600;
		 text-decoration: none;
	}
	
	textarea {
		 background-color: #CCFFCC;
		 color: #000033;
	}
	
	input,select ,button {
		background-color: #000000;
		color: #CC9900;
		border-style: solid;
		border-color: #4B4B4B;
	}
	
	input:hover	{
		background: #222;
	}
	
	table {
		border-collapse:collapse;
	}
	
	table,th, td {
		border: 1px solid grey;
	}
	-->
	</style>
</head>

<body bgcolor="black" text="white">
<h2><div align="center"><font color="white">Welcome to<b> 0xGuest</b> Installation</font></div></h2>
<br />
<?php

if (   !empty( $_POST['password'] )
	&& !empty( $_POST['title']    )
	&& !empty( $_POST['host']     )	
	&& !empty( $_POST['name']     )
	&& !empty( $_POST['user']     )
	&& !empty( $_POST['prefix']   )
	) {
	
	
	//dati amministrazione
	$pass_admin    = md5(VarProtect( $_POST['password'] ));
	
	//dati di configurazione
	$title   = VarProtect( $_POST['title']    );
	$prefix  = VarProtect( $_POST['prefix']   );
	$limit   = (int) $_POST['limit'];
	
	//Dati per connessione al MySQL
	$host = VarProtect( $_POST['host'] );
	$user = VarProtect( $_POST['user'] );
	$pass = VarProtect( $_POST['pass'] );
	$name = VarProtect( $_POST['name'] );
	
	//Dati Connessione MySQL e Connessione
	$db_connect = @mysql_connect  ( $host, $user, $pass );
	$db_select  = @mysql_select_db( $name );

	if(!$db_connect)
		die("<b>Errore durante la connessione al database MySQL</b><br>".mysql_errno()." : ".mysql_error());
	elseif(!$db_select)
		die("<b>Errore durante la selezione del database MySQL</b><br>".mysql_errno()." : ".mysql_error());
		
	//creo la tabella users
		
	mysql_query("CREATE TABLE `".$prefix."users` (
	  `id` int(11) NOT NULL auto_increment,
	  `password` text NOT NULL,
	  KEY `id` (`id`)
	) TYPE=MyISAM AUTO_INCREMENT=1 ;") or die(mysql_error());
	
	echo "Table <b>'".$prefix."users'</b> created with success<br />\n";
	
	mysql_query("INSERT INTO ".$prefix."users (password) VALUES ('".$pass_admin."');") or die(mysql_error());
		
	echo "User added with success<br />\n";
	
	
	//tabella config
	mysql_query("CREATE TABLE `".$prefix."config` (
	  `title` text NOT NULL,
	  `inserit_smile` INT NOT NULL,
	  `limit` INT NOT NULL
	) TYPE=MyISAM;") or die(mysql_error());
	
	echo "Table <b>'".$prefix."config'</b> created with success<br />\n";
	
	mysql_query("INSERT INTO ".$prefix."config (`title`, `inserit_smile`, `limit`) VALUES ('".$title."', 1, ".$limit.");") or die(mysql_error());
		
	echo "Table <b>'".$prefix."config'</b> populated with success<br />\n";
			
	//tabella PASTES
	mysql_query("CREATE TABLE `".$prefix."signatures` (
  `id` int(11) NOT NULL auto_increment,
  `nick` text NOT NULL,
  `commento` text NOT NULL,
  `data` text NOT NULL,
  `ip` text NOT NULL,
  `web_site` text NOT NULL,
  `email` text NOT NULL,
  KEY `id` (`id`)
) TYPE=MyISAM;") or die(mysql_error());
	
		echo "Table <b>'".$prefix."signatures'</b> created with success<br />\n";
	
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

$db_host = "'.$host.'";
$db_user = "'.$user.'";
$db_pass = "'.$pass.'";
$db_name = "'.$name.'";
?>';
	
		// Scriviamo sul config.php i dati che ci occorrono
		if(!($open = fopen( "config.php", "w" )))
			die("Errore durante l'apertura sul file config.php<br /> Prego di controllare i permessi sul file!");
			
		fwrite ($open, $config);//Scrivo sul file config.php
		
		fclose ($open); // chiudo il file

		echo "<b>config.php</b> created with success<br />\n";
		
		echo "<font color=green>Installation Complete with Success!</font>"; //stampo l'avvenuto successo di installazione
		echo '<br><br><a href="?delete_install=1">Delete file install.php</a>';//Consiglio di delettare il file install.php
}else{
?>
<div align="center">
<font color="white" face="Arial" size="5">This is your first installation!</font><br />
<br />
<form method="POST">
<table style="text-align: left;" border="0" cellpadding="2" width="100%" cellspacing="2">
<tbody>
	<tr>
		<td><font color="white">Insert your password:</font></td>
		<td><input type="password" name="password" size="40"></td>
		<td bgcolor="black"><a onclick="window.alert('Example: my_passw0rd')"><img alt="images/info.png" border="0" src="images/info.png"></a></td>
	</tr>
	<tr>
		<td><font color="white">Insert the title :</font></td>
		<td><input type="text" name="title"></td>
		<td bgcolor="black"><a onclick="window.alert('Example: Welcome to my world')"><img alt="images/info.png" border="0" src="images/info.png"></a></td>
	</tr>
	<tr>
		<td><font color="white">How many items per page? ( Recommended 6) :</font></td>
		<td><input type="text" name="limit"></td>
		<td bgcolor="black"><a onclick="window.alert('Example: 5')"><img alt="images/info.png" border="0" src="images/info.png"></a></td>
	</tr>
	</tbody>
</table>

<p><b><font color="white"><br /><br />MySQL Info</font></b></p>

<table style="text-align: left;" border="0" cellpadding="2" width="100%" cellspacing="2">
<tbody>
	<tr>
		<td><font color="white">Host Database:</font></td>
		<td><input type="text" name="host" size="30"></td>
		<td bgcolor="black"><a onclick="window.alert('The Host which is part of the Database.')"><img alt="images/info.png" border="0" src="images/info.png"></a></td>
	</tr>
	<tr>
		<td><font color="white">Username Database:</font></td>
		<td><input type="text" name="user" size="30"></td>
		<td bgcolor="black"><a onclick="window.alert('Username for Connection MySQL')"><img alt="images/info.png" border="0" src="images/info.png"></a></td>
	</tr>
	<tr>
		<td><font color="white">Password Database:</font></td>
		<td><input type="password" name="pass" size="30"></td>
		<td bgcolor="black"><a onclick="window.alert('Password for connection the database')"><img alt="images/info.png" border="0" src="images/info.png"></a></td>
	</tr>	
	<tr>
		<td><font color="white">Name Database:</font></td>
		<td><input type="text" name="name" size="30"></td>
		<td bgcolor="black"><a onclick="window.alert('Name the your Database')"><img alt="images/info.png" border="0" src="images/info.png"></a></td>
	</tr>
	<tr>
		<td><font color="white">Table Prefix:</font></td>
		<td><input type="text" name="prefix" value="guestbook_" size="15"></td>
		<td bgcolor="black"><a onclick="window.alert('Exemple: guestbook_')"><img alt="images/info.png" border="0" src="images/info.png"></a></td>
	</tr>	
	</tbody>
</table>
<br />
<div align="center"><input type="submit" value="Confirm"> <input type="reset"  value="Reset"></div>
</form>
</div>
<?php
}
?>
<br />
<br />
<div align="center"><font color="grey"><i>Powered By <a href="http://0xproject.hellospace.net/#0xGuest">0xGuest</a></i></font></div>
</body>
</html>


