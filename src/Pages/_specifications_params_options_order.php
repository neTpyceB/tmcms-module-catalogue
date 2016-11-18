<?
use TMCms\DB\SQL;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

SQL::orderCat($id, module_catalogue::$tables['specifications_params_options'], q_value('SELECT `param_id` FROM `'. module_catalogue::$tables['specifications_params_options'] .'` WHERE `id` = "'. (int)$id .'"'), 'param_id', $_GET['direct']);

back();