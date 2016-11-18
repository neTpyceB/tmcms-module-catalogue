<?
use TMCms\Strings\Converter;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

$_POST = sql_prepare($_POST);

if (q_check(module_catalogue::$tables['products'], '`uid` = "'. $_POST['uid'] .'" AND `id` != "'. $id .'"')) error('Product with this UID already exists.');

//$translations = q_assoc_row('SELECT `title`, `subtitle`, `description`, `price_special_note` FROM `'. module_catalogue::$tables['products'] .'` WHERE `id` = "'. $id .'"');

q('UPDATE `'. module_catalogue::$tables['products'] .'` SET
	`title` = "'. $_POST['title'] .'",
	`add_ts` = "'. strtotime($_POST['add_ts']) .'",
	`show_till_ts` = "'. strtotime($_POST['show_till_ts']) .'",
	`description` = "'. $_POST['description'] .'",
	`price` = "'. Converter::number2price($_POST['price']) .'",
	`category_id` = "'. (int)$_POST['category_id'] .'",
	`uid` = "'. $_POST['uid'] .'"
 WHERE `id` = "'. $id .'"');

go('?p='. P);