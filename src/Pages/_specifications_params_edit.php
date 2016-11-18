<?
defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

$data = q_assoc_row('SELECT `key`, `spec_id` FROM '. module_catalogue::$tables['specifications_params'] .' WHERE `id` = "'. $id .'"');

q('UPDATE `'. module_catalogue::$tables['specifications_params'] .'` SET
	`key` = "'. Translations::update($_POST['key'], $data['key']) .'",
	`type` = "'. sql_prepare($_POST['type']) .'"
WHERE `id` = "'. $id .'"
');

go('?p='. P .'&do=specifications_params&id='. $data['spec_id']);