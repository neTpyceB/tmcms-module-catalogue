<?
use TMCms\Admin\Users;
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\Column\ColumnActive;
use TMCms\HTML\Cms\Column\ColumnData;
use TMCms\HTML\Cms\Column\ColumnDelete;
use TMCms\HTML\Cms\Column\ColumnEdit;
use TMCms\HTML\Cms\Column\ColumnOrder;
use TMCms\HTML\Cms\Filter\Select;
use TMCms\HTML\Cms\Filter\Text;
use TMCms\HTML\Cms\FilterForm;

defined('INC') or exit;

//echo Columns::getInstance()
//		->add('<a href="?p='. P .'&do=add">Add Product</a>', array('align' => 'right'))
//;

echo CmsTable::getInstance()
		->addDataSql('
SELECT
	`p`.`id`,
	`p`.`category_id`,
	`p`.`active`,
	`p`.`special`,
	`p`.`price`,
	`p`.`title`,
	`d2`.`'. Users::getUserLng() .'` AS `category`,
	(SELECT COUNT(*) FROM `'. module_catalogue::$tables['images'] .'` WHERE `product_id` = `p`.`id`) AS `images`
FROM `'. module_catalogue::$tables['products'] .'` AS `p`
LEFT JOIN `'. module_catalogue::$tables['categories'] .'` AS `c` ON `p`.`category_id` = `c`.`id`
LEFT JOIN `cms_translations` AS `d2` ON `d2`.`id` = `c`.`title`
ORDER BY `p`.`order`
		')
		->addColumn(ColumnEdit::getInstance('title')->width('99%')->href('?p='. P .'&do=edit&id={%id%}'))
		->addColumn(ColumnData::getInstance('category')->nowrap(true)->width('1%')->href('?p='. P .'&category_id={%category_id%}'))
//		->addColumn(ColumnData::getInstance('images')->nowrap(true)->align('center')->href('?p='. P .'&do=images&product_id={%id%}'))
//		->addColumn(ColumnData::getInstance('specifications')->value('Set...')->align('center')->href('?p='. P .'&do=params&product_id={%id%}'))
		->addColumn(ColumnData::getInstance('price')->nowrap(true)->title('Price')->help('In LVL'))
//		->addColumn(ColumnActive::getInstance('special')->href('?p='. P .'&do=_special&id={%id%}'))
		->addColumn(ColumnActive::getInstance('active')->href('?p='. P .'&do=_active&id={%id%}'))
//		->addColumn(ColumnOrder::getInstance()->href('?p='. P .'&do=_order&id={%id%}'))
		->addColumn(ColumnDelete::getInstance()->href('?p='. P .'&do=_delete&id={%id%}'))
		->attachFilterForm(
			FilterForm::getInstance()->setWidth('100%')
				->addFilter('Category', Select::getInstance('category_id')->setOptions(array(-1 => 'All') + module_catalogue::getCategoriesAsArray())->ignoreValue(-1)->html(false))
				->addFilter('ID', Text::getInstance('id')->setColumn('p.id'))
		)
;