<?
defined('INC') or exit;

if (!isset($_POST['title'], $_GET['id'])) return;
$id = &$_GET['id'];

q('UPDATE `'. module_catalogue::$tables['specifications'] .'` SET
	`title` = "'. $_POST['title'] .'"
WHERE `id` = "'. $id .'"
');

go('?p='. P .'&do=specifications');