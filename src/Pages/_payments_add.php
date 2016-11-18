<?
use TMCms\DB\SQL;

defined('INC') or exit;

q('INSERT INTO `'. module_catalogue::$tables['payments'] .'` (
	`title`, `order`
) VALUES (
	"'. Translations::save($_POST['title']) .'", "'. SQL::getNextOrder(module_catalogue::$tables['payments']) .'"
)');

go('?p='. P .'&do=payments');