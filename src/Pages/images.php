<?
use TMCms\HTML\Cms\CmsGallery;
use TMCms\HTML\Cms\Columns;

defined('INC') or exit;

if (!isset($_GET['product_id']) || !ctype_digit((string)$_GET['product_id'])) return;
$product_id = & $_GET['product_id'];

echo Columns::getInstance()
		->add(module_catalogue::getTitle($product_id))
		->add('<a href="?p='. P .'&do=images_add&product_id='. $product_id .'">Add Image</a>', array('align' => 'right'))
;

echo '<br>';

$images = q_assoc('SELECT `id`, `image`,  `active` FROM `'. module_catalogue::$tables['images'] .'` WHERE `product_id` = "'. $product_id .'" ORDER BY `order`');

echo CmsGallery::getInstance($images)
		->linkMove('?p='. P .'&do=_images_order')
		->linkDelete('?p='. P .'&do=_images_delete')
		->linkActive('?p='. P .'&do=_images_active')
;