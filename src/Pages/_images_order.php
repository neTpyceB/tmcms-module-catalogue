<?
use TMCms\DB\SQL;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

SQL::orderCat($id, module_catalogue::$tables['images'], q_value('SELECT `product_id` FROM `'. module_catalogue::$tables['images'] .'` WHERE `id` = "'. (int)$id .'"'), 'product_id', $_GET['direct']);

back();