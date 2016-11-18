<?
use TMCms\DB\SQL;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

q('INSERT INTO `'. module_catalogue::$tables['specifications_params'] .'` (
	`key`, `order`, `spec_id`, `type`
) VALUES (
	"'. Translations::save($_POST['title']) .'", "'. SQL::getNextOrder(module_catalogue::$tables['specifications_params'], 'order', 'spec_id', $id) .'", "'. $id .'", "'. sql_prepare($_POST['type']) .'"
)');

go('?p='. P .'&do=specifications_params&id='. $id);