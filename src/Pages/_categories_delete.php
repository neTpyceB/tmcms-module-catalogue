<?

use TMCms\Cache\Cacher;
use TMCms\DB\SQL;
use TMCms\DB\TableTree;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

$tree = TableTree::getInstance(module_catalogue::$tables['categories'])->getAsTree($id);
$ids = array();
foreach ($tree as &$v) $ids[] = $v['id'];
$ids[] = $id;

$ids = implode(',', $ids);

if (q_check(module_catalogue::$tables['products'], '`category_id` IN ('. $ids .')')) error('There are products in this category or subcategory.');

$translations = q_assoc_row('SELECT `title`, `description` FROM `'. module_catalogue::$tables['categories'] .'` WHERE `id` IN ('. $ids .')');

SQL::delete("cms_translations", $translations);

q('DELETE FROM `'. module_catalogue::$tables['categories'] .'` WHERE `id` IN ('. $ids .')');

Cacher::getInstance()->getDefaultCacher()->delete(module_catalogue::$cache_key_categories);

back();