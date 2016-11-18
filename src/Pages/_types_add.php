<?
use TMCms\DB\SQL;

defined('INC') or exit;

q('INSERT INTO `'. module_catalogue::$tables['types'] .'` (
	`title`, `order`
) VALUES (
	"'. Translations::save($_POST['title']) .'", "'. SQL::getNextOrder(module_catalogue::$tables['types']) .'"
)');

go('?p='. P .'&do=types');