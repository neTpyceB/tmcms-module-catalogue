<?
defined('INC') or exit;

if (!isset($_GET['product_id']) || !ctype_digit((string)$_GET['product_id'])) return;
$product_id = & $_GET['product_id'];

$_POST = sql_prepare($_POST);

foreach (q_pairs('SELECT `value` FROM `'. module_catalogue::$tables['params'] .'` WHERE `product_id` = "'. $product_id .'"') as $v)	Translations::delete($v);

q('DELETE FROM `'. module_catalogue::$tables['params'] .'` WHERE `product_id` = "'. $product_id .'"');

foreach ($_POST as $k => $v) q('INSERT INTO `'. module_catalogue::$tables['params'] .'` (`product_id`, `param_id`, `value`) VALUES ("'. $product_id .'", "'. $k .'", "'. Translations::save($v) .'")');
go('?p='. P);