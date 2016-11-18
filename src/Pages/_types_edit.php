<?
defined('INC') or exit;

if (!isset($_GET['id'])	|| !ctype_digit((string)$_GET['id'])) return;
$id = &$_GET['id'];

$data = q_assoc_row('SELECT `title` FROM `'. module_catalogue::$tables['types'] .'` WHERE `id` = "'. $id .'"');

q('UPDATE `'. module_catalogue::$tables['types'] .'` SET
	`title` = "'. Translations::update($_POST['title'], $data['title']) .'"
WHERE `id` = "'. $id .'"
');

go('?p='. P .'&do=types');