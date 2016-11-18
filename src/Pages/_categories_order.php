<?
use TMCms\Cache\Cacher;
use TMCms\DB\SQL;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

$parent = module_catalogue::getCategoryParent($id);
$pid = $parent ? $parent['id'] : 0;

SQL::orderCat($id, module_catalogue::$tables['categories'], $pid, 'pid', $_GET['direct']);

Cacher::getInstance()->getDefaultCacher()->delete(module_catalogue::$cache_key_categories);

back();