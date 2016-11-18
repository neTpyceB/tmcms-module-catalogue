<?
use TMCms\Admin\Users;
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\Column\ColumnActive;
use TMCms\HTML\Cms\Column\ColumnData;
use TMCms\HTML\Cms\Column\ColumnDelete;
use TMCms\HTML\Cms\Column\ColumnImg;
use TMCms\HTML\Cms\Column\ColumnOrder;
use TMCms\HTML\Cms\Column\ColumnTree;
use TMCms\HTML\Cms\Columns;

defined('INC') or exit;

echo Columns::getInstance()
		->add('<a href="?p='. P .'&do=categories_add">Add Category</a>', array('align' => 'right'))
;

echo '<br>';

echo CmsTable::getInstance()
		->addDataSql('
SELECT
	`c`.`id`,
	`c`.`pid`,
	`c`.`active`,
	`c`.`icon_img`,
	`d1`.`'. Users::getUserLng() .'` AS `title`,
	(SELECT COUNT(*) FROM `'. module_catalogue::$tables['products'] .'` WHERE `category_id` = `c`.`id`) AS `products`
FROM `'. module_catalogue::$tables['categories'] .'` AS `c`
JOIN `cms_translations` AS `d1` ON `d1`.`id` = `c`.`title`
ORDER BY `c`.`order`
	')
    ->addColumn(ColumnImg::getInstance('icon_img')->width(30)->title('Icon'))
		->addColumn(ColumnTree::getInstance('id')
				->href('?p='. P .'&do=categories_edit&id={%id%}')
				->ajax(true)
				->saveInnerState(true)
				->title('Title')
				->setShowKey('title')
				->width('99%'))
		->setCallbackFunction('_default_callback')
		->addColumn(ColumnData::getInstance('copy_branch')->href('?p='. P .'&do=copy_branch&from_id={%id%}')->width('1%')->align('center')->nowrap(1)->value('Copy to...'))
		->addColumn(ColumnData::getInstance('add_sub_id')->href('?p='. P .'&do=categories_add&add_sub_id={%id%}')->width('1%')->align('center')->nowrap(1)->title('Add sub cat')->value('Add +'))
		->addColumn(ColumnData::getInstance('products')->href('?p='. P .'&category_id={%id%}')->width('1%')->align('center')->nowrap(1)->title('Stickers'))
		->addColumn(ColumnOrder::getInstance('order')->href('?p='. P .'&do=_categories_order&id={%id%}'))
		->addColumn(ColumnActive::getInstance('active')->href('?p='. P .'&do=_categories_active&id={%id%}'))
		->addColumn(ColumnDelete::getInstance('delete')->href('?p='. P .'&do=_categories_delete&id={%id%}'))
;

function _default_callback($data) {
	foreach ($data as & $v) if (!$v['title']) $v['title'] = '---';
	return $data;
}