<?


use TMCms\Cache\Cacher;
use TMCms\DB\TableTree;

defined('INC') or exit;

if (!isset($_POST['pid'], $_POST['title'], $_GET['id'])
	|| !ctype_digit((string)$_GET['id'])
	|| !ctype_digit((string)$_POST['pid'])
) return;
$id = &$_GET['id'];
$pid = &$_POST['pid'];

if (q_check(module_catalogue::$tables['categories'], '`uid` = "'. $_POST['uid'] .'" AND `id` != "'. $id .'"')) error('Category with this UID already exists.');

if ($pid && !q_check(module_catalogue::$tables['categories'], '`pid` = 0 AND `id` != "'. $id .'"')) error('It is not possible to move last root category');

$tree = TableTree::getInstance(module_catalogue::$tables['categories'])->getAsTree($id);

foreach ($tree as &$v) {
	if ($id == $pid) error('It is not possible to move parent category in itself or in it\'s subcategory.');
}
unset($v);

$data = q_assoc_row('SELECT `title` FROM `'. module_catalogue::$tables['categories'] .'` WHERE `id` = "'. $id .'"');

q('UPDATE `'. module_catalogue::$tables['categories'] .'` SET
	`pid` = "'. $pid .'",
	`specification_id` = "'. (int)$_POST['specification_id'] .'",
	`title` = "'. Translations::update($_POST['title'], $data['title']) .'",
	`uid` = "'. sql_prepare($_POST['uid']) .'",
	`icon_img` = "'. sql_prepare($_POST['icon_img']) .'"
WHERE `id` = "'. $id .'"
');

Cacher::getInstance()->getDefaultCacher()->delete(module_catalogue::$cache_key_categories);


go('?p='. P .'&do=categories');