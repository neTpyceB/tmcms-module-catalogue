<?
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\Column\ColumnData;
use TMCms\HTML\Cms\Column\ColumnDelete;
use TMCms\HTML\Cms\Column\ColumnEdit;
use TMCms\HTML\Cms\Columns;

defined('INC') or exit;

echo Columns::getInstance()
		->add('<a href="?p='. P .'&do=specifications_add">Add Specifications</a>', array('align' => 'right'))
;

echo CmsTable::getInstance()
		->addDataSql('
SELECT
	`s`.`id`,
	`s`.`title`,
	(SELECT COUNT(*) FROM `'. module_catalogue::$tables['specifications_params'] .'` AS `sp` WHERE `sp`.`spec_id` = `s`.`id`) AS `params`
FROM `'. module_catalogue::$tables['specifications'] .'` AS `s`
ORDER BY `s`.`title`
')
		->addColumn(ColumnEdit::getInstance('title')->width('99%')->href('?p='. P .'&do=specifications_edit&id={%id%}'))
		->addColumn(ColumnData::getInstance('params')->href('?p='. P .'&do=specifications_params&id={%id%}')->align('center'))
		->addColumn(ColumnDelete::getInstance()->href('?p='. P .'&do=_specifications_delete&id={%id%}'))
;