<?php
/*
 *
 * @project 0xGuest
 * @author KinG-InFeT
 * @licence GNU/GPL
 *
 * @file index.php
 *
 * @link http://0xproject.hellospace.net#0xGuest
 *
 */
ob_start();
session_start();

include("lib/core.class.php");

$template = new Core();

if (isset($_GET['page']) && is_numeric($_GET['page']) && ((int)$_GET['page']) > 0 ) 
	$page = (int) $_GET['page']; 
else
	$page = 1;

if(@$_GET['mode'] == 'inserit') {
	$template->inserit($_POST['author'], $_POST['comment'], $_POST['web_site'], $_POST['email'], $_POST['number']);
	exit;
}
	

$template->PrintHeader();
$template->PrintBody($page);
$template->PrintFooter();
?>
