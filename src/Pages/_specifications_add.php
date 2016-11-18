<?
defined('INC') or exit;

$_POST = sql_prepare($_POST);

q('INSERT INTO `'. module_catalogue::$tables['specifications'] .'` (
	`title`
) VALUES (
	"'. $_POST['title'] .'"
)');

go('?p='. P .'&do=specifications');