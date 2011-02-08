<?php
/*
 *
 * @project 0xGuest
 * @author KinG-InFeT
 * @licence GNU/GPL
 *
 * @file admin.class.php
 *
 * @link http://0xproject.hellospace.net#0xGuest
 *
 */
 
class Admin extends Security  {

	public function __construct () {
	
			include ("config.php");
			include_once ("mysql.class.php");
			
			$this->sql = new MySQL ($db_host, $db_user, $db_pass, $db_name);
	}
	
	public function show_administration() {
		
		print "<h3 align=\"center\">List of Pastes</h3>\n";	
		
		print "\n<table align=\"center\" cellpadding=\"10\" cellspacing=\"1\">"
			. "\n<tbody>"
			. "\n	<tr align=\"center\">"
			. "\n	  <td style=\"width: 5%;\"><b>ID</b></td>"
			. "\n	  <td style=\"width: 30%;\"><b>Author</b></td>"
			. "\n	  <td><b>Signature</b></td>"
			. "\n	  <td><b>IP</b></td>"
			. "\n	  <td><b>Date</b></td>"
			. "\n	  <td><b>[Manage]</b></td>"
			. "\n	</tr>";
		
		$this->signs = $this->sql->sendQuery("SELECT * FROM ".__PREFIX__."signatures ORDER by id DESC");
		
		while($this->sign = mysql_fetch_array($this->signs)) {
		
			$this->commento = $this->sign['commento'];
			$this->strl = strlen($this->sign['commento']); // lunghezza della stringa
			$this->max_char = 120;
			
			//thanks html.it
			if ($this->strl >= $this->max_char) { // se più lunga di tot caratteri
				$this->commento = substr($this->commento,0,$this->max_char); // li taglia
				$strsp = strrpos($this->commento," "); //poi trova l'ultimo spazio
				$this->commento  = substr($this->commento,0,$strsp); //taglia sino lì...
				$this->commento .= "..."; //ed aggiunge i puntini
			}else{
				$this->commento = $this->sign['commento'];
			}

			print "\n\t<tr>"
				. "\n	  <td style=\"width: 5%;\">".$this->sign['id']."</td>"
				. "\n	  <td style=\"width: 30%;\">".$this->sign['nick']."</td>"
				. "\n	  <td style=\"width: 30%;\">".$this->commento."</td>"
				. "\n	  <td>".$this->sign['ip']."</td>"
				. "\n	  <td>".$this->sign['data']."</td>"
				. "\n	  <td><a href=\"admin.php?action=del_sign&id=".$this->sign['id']."&security=".$_SESSION['token']."\">[X]</a></td>"
				. "\n	</tr>";
		}
		print " </tbody>\n"
			. "</table>\n"
			. "</div>\n";
	}
	
	public function del_sign($id) {
	
		$this->id = intval($id);
		
		$this->my_is_numeric($this->id);
		
		if(empty($this->id)) {
			die("Hacking Attemp!");
		}else{
			$this->security_token($_GET['security'], $_SESSION['token']);
			
			$this->sql->sendQuery("DELETE FROM ".__PREFIX__."signatures WHERE id = '".$this->id."'");

			die(header('Location: admin.php'));
		}
	}
	
	public function settings() {
	
		print "<h2 align=\"center\">Settings</h2><br />\n";

		if(!empty($_POST['title']) && is_numeric($_POST['inserit_smile']) && is_numeric($_POST['limit'])) {
			
			$this->security_token($_POST['security'], $_SESSION['token']);
			
			$this->title         = $this->VarProtect( $_POST['title']  );
			$this->inserit_smile = (int) $_POST['inserit_smile'];
			$this->limit         = (int) $_POST['limit'];
						 			
			$this->sql->sendQuery("UPDATE `".__PREFIX__."config` SET 
									`title` = '".$this->title."',
									`inserit_smile` = ".$this->inserit_smile.",
									`limit` = ".$this->limit."");
			
			print "<script>alert(\"Upgrade Settings.\"); window.location=\"admin.php\";</script>";
		
		}else{
			$this->config = mysql_fetch_array($this->sql->sendQuery("SELECT * FROM ".__PREFIX__."config"));
			
			print "\n<br /><br />"
				. "\n<form method=\"POST\" action=\"admin.php?action=settings\" />"
				. "\n<table align=\"center\" style=\"text-align: center;\" border=\"0\" cellpadding=\"2\" width=\"50%\" cellspacing=\"2\">"
				. "\n<tbody>"
				. "\n<tr>"
				. "\n	<td>Title of 0xGuest:</td>"
				. "\n	<td><input type=\"text\" name=\"title\" value=\"".$this->config['title']."\" /></td>"
				. "\n</tr>"
				. "\n<tr>"
				. "\n	<td>Do you want to allow the inclusion in the signatures of smile?:</td>"
				. "\n<td>"
				. "\n<select name=\"inserit_smile\">"
				. "\n<option value=\"0\">No</option>"
				. "\n<option value=\"1\">Yes</option>"
				. "\n</select>"
				. "\n</td>"
				. "\n</tr>"
				. "\n<tr>"
				. "\n	<td>How many signatures per page?:</td>"
				. "\n<td>"
				. "\n<input type=\"text\" name=\"limit\" value=\"".$this->config['limit']."\" />"
				. "\n</td>"
				. "\n</tr>"
				. "\n</tbody>"
				. "\n</table>"
				. "\n<br /><p align=\"center\"><input type=\"submit\" value=\"Send\" /></p>"
				. "\n<input type=\"hidden\" name=\"security\" value=\"".$_SESSION['token']."\" />"
				. "\n</form>"
				."";
		}	
	}
		
	public function updates($version) {

		print "<h2 align=\"center\">Upgrade System</h2><br />\n";	
		
		$update = NULL;
		
		if ($fsock = @fsockopen('www.0xproject.hellospace.net', 80, $errno, $errstr, 10)) {
			@fputs($fsock, "GET /versions/0xGuest.txt HTTP/1.1\r\n");
			@fputs($fsock, "HOST: www.0xproject.hellospace.net\r\n");
			@fputs($fsock, "Connection: close\r\n\r\n");
	
			$get_info = FALSE;
			
			while (!@feof($fsock)) {
				if ($get_info)
					$update .= @fread($fsock, 1024);
				else
					if (@fgets($fsock, 1024) == "\r\n")
						$get_info = TRUE;
			}
			
			@fclose($fsock);
			
			$update = htmlspecialchars($update);
			
			$update1  = str_replace(".", "", $update);
			$version1 = str_replace(".", "", $version);
	
			if ($version1 <= $update1)
				$version_info = "<p style=\"color:green\">There are no updates for your system.</p><br />";
			else
				$version_info = "\n<p style=\"color:red\">Updates are available for the system.<br />\nUpgrade to the latest version: ". $update."\n"
							  . "<br /><br />Link Download: <a href=\"http://0xproject.hellospace.net/#0xGuest\">Download Recent Version</a><br />\n";
		}else{
			if ($errstr)
				$version_info = '<p style="color:red">' . sprintf("Unable to open connection to 0xProject Server, reported error is:<br />%s", $errstr) . '</p>';
			else
				$version_info = '<p>Unable to use socket functions.</p>';
		}
		
		return ("<br /><br /><big><big>".$version_info."</big></big>");
	}
	
	public function themes() {
		
		print "<h2 align=\"center\">Theme Management</h2><br />\n";	
		
		if (!empty($_POST['send']) && ($_POST['send'] == 1) && !empty($_POST['theme_file'])) {

			$this->security_token($_POST['security'], $_SESSION['token']);

			$scrivi_file = fopen("style.css","w");
			fwrite($scrivi_file,$_POST['theme_file']) or die("Error writing file style.css");
			fclose($scrivi_file);
				
			print "<script>alert(\"Theme Changed!\"); window.location.href = 'admin.php?action=themes';</script>";

		}else{

			$leggi_file  = fopen("style.css","r");
			$dim_file    = filesize("style.css");
			$this->theme = fread($leggi_file,$dim_file);
			fclose($leggi_file);

			print "\n<form method=\"POST\" action=\"admin.php?action=themes\" />"
				. "\n<p align=\"center\">Edit Themes File:<br />"
				. "\n<textarea name=\"theme_file\" rows=\"25\" cols=\"160\">".htmlspecialchars($this->theme)."</textarea><br />"
				. "\n<input type=\"hidden\" name=\"security\" value=\"".$_SESSION['token']."\" />"
				. "\n<input type=\"hidden\" name=\"send\" value=\"1\" />"
				. "\n<input type=\"submit\" value=\"Edit Theme\" /></p>"
				. "\n</form>"
				. "";
		}
	}
	
	public function change_pass_admin() {
		
		print "<h2 align=\"center\">Change Admin Password</h2><br />\n";
					
		if(!empty($_POST['new_pass'])) {
			$this->security_token($_POST['security'], $_SESSION['token']);
				
			$this->sql->sendQuery("UPDATE ".__PREFIX__."users SET password = '".md5($_POST['new_pass'])."' WHERE id = 1");
			print "<script>alert('Password Changed'); location.href = 'admin.php?action=change_pass_admin';</script>";
		}else{
			print "\n<form method = \"POST\" action=\"admin.php?action=change_pass_admin\" />"
				. "\n<p>New Password: <input type=\"password\" name=\"new_pass\" /><br />"
				. "\n<input type=\"hidden\" name=\"security\" value=\"".$_SESSION['token']."\" />"
				. "\n<input type=\"submit\" value=\"Edit Password\" />"
				. "\n</form></p>"
				. "";	
		}
	}				
}	
?>		


