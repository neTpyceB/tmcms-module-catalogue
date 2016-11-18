<?
use TMCms\DB\SQL;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

SQL::orderCat($id, module_catalogue::$tables['specifications_params'], q_value('SELECT `spec_id` FROM `'. module_catalogue::$tables['specifications_params'] .'` WHERE `id` = "'. (int)$id .'"'), 'spec_id', $_GET['direct']);

back();