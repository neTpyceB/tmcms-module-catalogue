<?
defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

module_catalogue::completelyDeleteSticker($id);

back();