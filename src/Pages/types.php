<?
use TMCms\Admin\Users;
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\Column\ColumnActive;
use TMCms\HTML\Cms\Column\ColumnDelete;
use TMCms\HTML\Cms\Column\ColumnEdit;
use TMCms\HTML\Cms\Column\ColumnOrder;
use TMCms\HTML\Cms\Columns;

defined('INC') or exit;

echo Columns::getInstance()
		->add('<a href="?p='. P .'&do=types_add">Add Type</a>', array('align' => 'right'))
;

echo '<br>';

echo CmsTable::getInstance()
		->addDataSql('
SELECT
	`t`.`id`,
	`t`.`active`,
	`d1`.`'. Users::getUserLng() .'` AS `title`
FROM `'. module_catalogue::$tables['types'] .'` AS `t`
JOIN `cms_translations` AS `d1` ON `d1`.`id` = `t`.`title`
ORDER BY `t`.`order`
	')
		->addColumn(ColumnEdit::getInstance('title')->href('?p='. P .'&do=types_edit&id={%id%}'))
		->addColumn(ColumnOrder::getInstance('order')->href('?p='. P .'&do=_types_order&id={%id%}'))
		->addColumn(ColumnActive::getInstance('active')->href('?p='. P .'&do=_types_active&id={%id%}'))
		->addColumn(ColumnDelete::getInstance('delete')->href('?p='. P .'&do=_types_delete&id={%id%}'))
;