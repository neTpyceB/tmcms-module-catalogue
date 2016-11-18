<?
defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

if (q_check(module_catalogue::$tables['specifications_params'], '`spec_id` = '. $id .'')) error('There are params in this specifications.');

q('DELETE FROM `'. module_catalogue::$tables['specifications'] .'` WHERE `id` = '. $id .'');

back();