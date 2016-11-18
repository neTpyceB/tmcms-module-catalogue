<?
defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

$translations = q_pairs('SELECT `name` FROM `'. module_catalogue::$tables['specifications_params_options'] .'` WHERE `id` = '. $id .'');

q('DELETE FROM `cms_translations` WHERE `id` = "'. implode(',', $translations) .'"');

q('DELETE FROM `'. module_catalogue::$tables['specifications_params_options'] .'` WHERE `id` = '. $id .'');

back();