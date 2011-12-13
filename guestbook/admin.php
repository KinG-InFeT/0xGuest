<?php
/*
 *
 * @project 0xGuest
 * @author KinG-InFeT
 * @licence GNU/GPL
 *
 * @file admin.php
 *
 * @link http://0xproject.netsons.org#0xGuest
 *
 */
ob_start();
session_start();
 
include("config.php");
include("lib/mysql.class.php");
include("lib/core.class.php");
include("lib/admin.class.php");
include("lib/login.class.php");

$template = new Core();
$admin    = new Admin();
$login    = new Login();

@$action = $_GET['action'];

$template->PrintHeader();

$login->form_login(@$_COOKIE['password']);

$template->PrintAdminMenu();
    
switch($action) {
	
	case 'del_sign':
		$admin->del_sign(@$_REQUEST['id']);
	break;
	
	case 'change_pass_admin':
		$admin->change_pass_admin();
	break;
	
	case 'themes':
		$admin->themes();
	break;

	case 'settings':
		$admin->settings();
	break;
	
	case 'updates':
		print $admin->updates(Core::VERSION);
	break;

	case 'logout':
		$login->logout(@$_COOKIE['password']);
	break;
	
	default:
		$admin->show_administration();
	break;
}

$template->PrintFooter();
?>
