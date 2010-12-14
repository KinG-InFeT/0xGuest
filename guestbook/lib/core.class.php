<?php
/*
 *
 * @project 0xGuest
 * @author KinG-InFeT
 * @licence GNU/GPL
 *
 * @file core.class.php
 *
 * @link http://0xproject.hellospace.net#0xGuest
 *
 */

include ("config.php");
include ("lib/security.class.php");
		
if(!defined("__INSTALLED__"))
	die("Run <a href=\"install.php\">./install.php</a> for Installation 0xGuest!");

class Core extends Security {
	
	const VERSION = '2.0 - Beta';

	public function __construct () {
	
			include ("config.php");				
			include_once ("mysql.class.php");
			
			$this->sql = new MySQL ($db_host, $db_user, $db_pass, $db_name);
	}
	
	public function PrintHeader() {
	
	$this->config = mysql_fetch_array($this->sql->sendQuery("SELECT title, inserit_smile FROM `".__PREFIX__."config`"));
	
	$this->title = (preg_match("/admin/i",$_SERVER['PHP_SELF'])) ? "Administration - 0xGuest" : $this->config['title'];
	
	$this->check_active = ($this->config['inserit_smile'] == 1) ? "<font color=\"red\">[NO]</font>" : "<font color=\"green\">[YES]</font>";
	
	print "\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">"
		. "\n<html>"
		. "\n<head>"
		. "\n<title>".$this->title."</title>"
		. "\n<META name=\"Author\" content=\"KinG-InFeT\">"
		. "\n<META name=\"Generator\" content=\"VIM\">"
		. "\n<link rel = \"stylesheet\"  type = \"text/css\" href = \"style.css\">"
		. "\n<script   language=\"javascript\">"
		. "\nfunction focuson()  { "
		. "\ndocument.addcomment.number.focus()"
		. "\n}"
		. "\n function check()  {"
		. "\nif(document.addcomment.number.value==0)   {"
		. "\n	          alert(\"Security!\\n Enter the code Captcha.\");"
		. "\n	         document.addcomment.number.focus();"
		. "\n	     return false;"
		. "\n	}"
		. "\n}"
		. "\nfunction aggiungi(toggle)  {"
		. "\nvar body = document.getElementsByTagName('body')[0];"
		. "\n	if (toggle)  {"
		. "\n	if (document.getElementById('desc'))  {"
		. "\n		var desc = document.getElementById('desc');"
		. "\n		body.removeChild(desc);"
		. "\n		return;"
		. "\n	}"
		. "\n		var width = 500;"
		. "\n       var top = 400;"
		. "\n	var desc = document.createElement('div');"
		. "\n	desc.setAttribute('id', 'desc');"
		. "\n	desc.style.border = '1px dotted #444';"
		. "\n	desc.style.position = 'absolute';"
		. "\n	desc.style.width = width + 'px';"
		. "\n   desc.style.top = top + 'px';"
		. "\n	desc.style.marginLeft = (screen.width/2 - width/2) + 'px';"
		. "\n	desc.style.marginTop  = '-200px';"
		. "\n	desc.style.backgroundColor = '#000';"
		. "\n	desc.style.padding = '10px';"
		. "\n	desc.style.zIndex = '2';"
		. "\n	desc.style.textAlign = 'left';"
		. "\n	desc.style.opacity = 0.9;"
		. "\n		desc.innerHTML = "
		. "\n		'<a href=javascript:aggiungi(false) style=text-decoration: underline; text-align: right; font-size: 11px><p align = right>[X]</p></a><br />' +"
		. "\n		'<form name=\"aggiungi\" action=\"index.php?mode=inserit\" method=\"POST\" onSubmit=\"return check();\">' +"
		. "\n		'<b>Nick: </b><br/><input type=\"text\" name=\"author\" /><br />'+"
		. "\n		'<b>Web Site: </b><br/><input type=\"text\" name=\"web_site\" /><br />'+"
		. "\n		'<b>E-Mail: </b><br/><input type=\"text\" name=\"email\" /><br /><br />'+"
		. "\n		'Smile-> :), :D, ;), ^_^,  :( _<br />'+"
		. "\n		'Smile System Active? ".$this->check_active."<br />'+"
		. "\n		'<b>Comment: </b><br/><textarea name=\"comment\" cols=\"50\" rows=\"13\"></textarea><br /><br />'+"
		. "\n		'<img src=\"lib/captcha.php\"><br />'+"
		. "\n		'Enter Captcha Code (Case-Sensitive):<br />'+"
		. "\n		'<input name=\"number\" type=\"text\" id=\"number\"><br /><br />'+"
		. "\n		'<input type=\"submit\" value=\"Send\" />'+"
		. "\n		'</form>';"
		. "\n	body.appendChild(desc);"
		. "\n} else {"
		. "\n	var desc = document.getElementById('desc');"
		. "\n	body.removeChild(desc);"
		. "\n}"
		. "\n}"
		. "\n</script>"
		. "\n</head><body onLoad=\"return focuson();\" />"
		. "";
	}
	
	private function Pagination ($numHits, $limit, $page) {
	
		$numHits  = (int) $numHits; 
		$limit    = (int) $limit; 
		$page     = (int) $page; 
		$numPages = @ceil($numHits / $limit);
		
		if($page > $numPages && $numPages > 0)
			$page = $numPages;
			
		if($page < 1)
			$page = 1;
		
		$offset = ($page - 1) * $limit; 
		
		$ret = array(); 
		$ret['offset'] 		= $offset; 
		$ret['limit'] 		= $limit;
		$ret['numPages']	= $numPages; 
		$ret['page']		= $page; 
		
		return $ret; 
	}
	
	public function PrintBody($page) {
	
		$this->page = (int) $page;
		
		$this->config = mysql_fetch_array($this->sql->sendQuery("SELECT * FROM `".__PREFIX__."config`"));
	
		print "\n<h1 align=\"center\">".$this->config['title']."</h1><br /><h3>"
			. "\n<p align=\"center\"><a href=\"#aggiungi\" onClick = \"aggiungi (true);\" >[-INSERT A MESSAGE-]</a></p>";

		$this->result = $this->sql->sendQuery("SELECT * FROM `".__PREFIX__."signatures` ORDER by id DESC");
		
		$pager = $this->Pagination(mysql_num_rows($this->result), $this->config['limit'], $this->page);
		
		$this->offset   = $pager['offset'];
		$this->limit 	= $pager['limit'];
		$this->page 	= $pager['page'];
		
		$this->query  = $this->sql->sendQuery("SELECT * FROM `".__PREFIX__."signatures` ORDER by id DESC LIMIT ".$this->limit." OFFSET ".$this->offset.";");
		
		if(mysql_num_rows($this->result) < 1) {
		
			print "<h3 align=\"center\">No signature!</h3>";
			
		} else {
			
			while($this->sign = mysql_fetch_array($this->query)) {
				
				$this->commento = wordwrap($this->sign['commento'],100,"<br />",1);
				
				if($this->config['inserit_smile'] == 0)
					$this->commento = $this->Smile($this->commento);
				
				print "\n<table align=\"center\" style=\"width: 50%;\">"
			      	. "\n<tbody>"
					. "\n<tr><td><i>".$this->sign['nick']."</i> ~ <font size=\"2\">Written on ".$this->sign['data']."</font> ~ <a href=\"mailto:".$this->sign['email']."\"><img src=\"images/mail.png\" border=\"none\" /></a> ~ <a href=\"".$this->sign['web_site']."\" target=\"_blank\"><img src=\"images/web_site.png\" border=\"none\"/></a></td></tr>"
		      		. "\n<tr><td><div class=\"comment\">".$this->commento."</div></td></tr>"
		      		. "\n</tbody>"
					. "\n</table>"
					. "\n<hr width=\"50%\" />";
			}
			
			print "\n<p align=\"center\">";
			for ($i = 1; $i <= $pager['numPages']; $i++) {
				if ($i < $pager['numPages']) 
					print " <a href=\"index.php?page=".$i."\">[".$i."]</a> -";
				else
					print " <a href=\"index.php?page=".$i."\">[".$i."]</a>";
			}
			print "\n</p>";
		}
	}
	
	public function PrintAdminMenu() {
		print "<h2 align=\"center\">Administration - 0xGuest</h2>"
			. "\n<div class=\"admin_menu\">"
			. "\n<a href=\"admin.php\" target=\"_self\" ><span>[List of Signatures]</span></a> - "
			. "\n<a href=\"admin.php?action=settings\" target=\"_self\" ><span>[Settings]</span></a> - "
			. "\n<a href=\"admin.php?action=change_pass_admin\" target=\"_self\" ><span>[Change Admin Pass]</span></a> - "
			. "\n<a href=\"admin.php?action=themes\" target=\"_self\" > <span>[Theme]</span></a> - "
			. "\n<a href=\"admin.php?action=updates\" target=\"_self\" ><span>[Upgrade]</span></a> - "
			. "\n<a href=\"admin.php?action=logout&security=".$_SESSION['token']."\" target=\"_self\" ><span>[Logout]</span></a>"
			. "\n</div>"
			."";
	}
	
	public function PrintFooter() {
		
		$this->footer_link = (preg_match("/(admin|view)/i",$_SERVER['PHP_SELF'])) ? "\n<p align=\"center\"><a href=\"index.php\" >[-Home Page-]</a></p>" : "\n<p align=\"center\">\n<a href=\"admin.php\" >[-Administration Panel-]</a></p>";
		
		$this->signs = mysql_num_rows($this->sql->sendQuery("SELECT * FROM `".__PREFIX__."signatures`"));
		
		print "\n<br /><br />"
			. "\n<div class=\"footer\">"
			. "\n<p style=\"float: left;\"><i>Powered By <a href=\"http://0xproject.hellospace.net/#0xGuest\">0xGuest</a> v".Core::VERSION."</i></p>\n"
			. "\n<p style=\"float: right;\">Signatures in Database: ".$this->signs."</p>\n"
			. $this->footer_link
			. "\n</div>"
			. "\n</body>"
			. "\n</html>";
	}
	
	private function check_validate_url($url) {
	
		$this->url = trim($url);
		
		//|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i
		if(preg_match('/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}'.'((:[0-9]{1,5})?\/.*)?$/i' ,$this->url))
			return TRUE;
		else
			return FALSE;
	}
	
	private function check_validate_email($email) {
	
		$this->email = trim($email);
		
		$this->email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
		
		return filter_var($this->email, FILTER_VALIDATE_EMAIL);
		
		//if(preg_match("/^([ a-zA-Z0-9 ])+([ a-zA-Z0-9 \ ._-])*@([ a-zA-Z0-9_-]) + ( [a-zA-Z0-9 \ ._-]+)+$/", $this->email))
		//	return TRUE;
		//else
		//	return FALSE;
	
	}
	
	private function Smile ($text) {
	
		//$this->text = str_replace("\n", "<br />", $text);
		$this->text = $text;
		
		$this->text = str_replace(":)", "<img alt=\":)\" src=\"images/smile/01.jpg\">", $this->text);
		$this->text = str_replace(":D", "<img alt=\":D\" src=\"images/smile/02.jpg\">", $this->text);
		$this->text = str_replace(";)", "<img alt=\";)\" src=\"images/smile/03.jpg\" >", $this->text);
		$this->text = str_replace("^_^", "<img alt=\"^_^\" src=\"images/smile/04.gif\">", $this->text);
		$this->text = str_replace(":(", "<img alt=\":(\" src=\"images/smile/06.gif\">", $this->text);
	
		return $this->text;
	}
	
	private function check_include_smile($text) {
	
		$this->text = trim($text);
		
		if(preg_match("/(:\)|;\)|:\(|\^_\^|:\(|:D)/i", $this->text))
			return TRUE;
		else
			return FALSE;
	
	}
	
	public function inserit($author, $comment, $web_site, $email, $captcha) {
		
		if($captcha != $_SESSION['captcha'])
			die("<script>alert(\"Error! Captcha is NOT correct!\"); window.location=\"index.php\";</script>");
		
		if(empty($author))
			die("<script>alert(\"Error! Author NOT include!\"); window.location=\"index.php\";</script>");
		
		if(strlen($author) > 30)
			die("<script>alert(\"Name too big! Maximum 30 characters.\"); window.location=\"index.php\";</script>");
		
		if(empty($comment))
			die("<script>alert(\"Error! Comment NOT include!\"); window.location=\"index.php\";</script>");
		
		if(strlen($comment) > 400)
			die("<script>alert(\"Comment too big! Maximum 400 characters.\"); window.location=\"index.php\";</script>");
		
		if($this->check_include_smile($comment))
			die("<script>alert(\"The system for the smile is not active!\"); window.location=\"index.php\";</script>");
		
		if($email != NULL) {
			if($this->check_validate_email($email) == FALSE)
				die("<script>alert(\"Error! Email NOT Valid! Exemple: email@provider.it\"); window.location=\"index.php\";</script>");
		}
		
		if($web_site != NULL) {
			if($this->check_validate_url($web_site) == FALSE)
				die("<script>alert(\"Error! Url web site NOT Valid! Exemple: http://www.miosito.com/\"); window.location=\"index.php\";</script>");			
		}else{
			$web_site = "http://www.0xproject.hellospace.net/";    //default
		}
		
		//security parser
		$this->author   = $this->VarProtect( $author   );
		$this->comment  = $this->VarProtect( $comment  );
		$this->email    = $this->VarProtect( $email    );
		$this->web_site = $this->VarProtect( $web_site );
		
		//info
		$this->date   = @date("d/m/y");
		$this->ip     = $_SERVER['REMOTE_ADDR'];
		
		$this->sql->sendQuery("INSERT INTO `".__PREFIX__."signatures` (`nick`, `commento`, `data`, `ip`, `web_site`, `email`
								) VALUES (
							  '".$this->author."', '".$this->comment."', '".$this->date."', '".$this->ip."', '".$this->web_site."', '".$this->email."');");
		
		header('Location: index.php');
		
		exit;

		
	}
}
?>
