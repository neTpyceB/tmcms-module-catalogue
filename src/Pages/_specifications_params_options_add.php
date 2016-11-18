<?
use TMCms\DB\SQL;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

q('INSERT INTO `'. module_catalogue::$tables['specifications_params_options'] .'` (
	`name`, `order`, `param_id`
) VALUES (
	"'. Translations::save($_POST['name']) .'", "'. SQL::getNextOrder(module_catalogue::$tables['specifications_params_options'], 'order', 'param_id', $id) .'", "'. $id .'"
)');

go('?p='. P .'&do=specifications_params_options&id='. $id);