<?
use TMCms\DB\SQL;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

SQL::orderCat($id, module_catalogue::$tables['products'], q_value('SELECT `category_id` FROM `'. module_catalogue::$tables['products'] .'` WHERE `id` = "'. $id .'"'), 'category_id', $_GET['direct']);

back();