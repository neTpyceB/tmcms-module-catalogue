<?
defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

$data = q_assoc_row('SELECT `name`, `param_id` FROM '. module_catalogue::$tables['specifications_params_options'] .' WHERE `id` = "'. $id .'"');

q('UPDATE `'. module_catalogue::$tables['specifications_params_options'] .'` SET
	`name` = "'. Translations::update($_POST['name'], $data['name']) .'"
WHERE `id` = "'. $id .'"
');

go('?p='. P .'&do=specifications_params_options&id='. $data['param_id']);