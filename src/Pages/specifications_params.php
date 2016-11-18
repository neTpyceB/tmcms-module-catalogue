<?
use TMCms\Admin\Users;
use TMCms\HTML\BreadCrumbs;
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\Column\ColumnActive;
use TMCms\HTML\Cms\Column\ColumnData;
use TMCms\HTML\Cms\Column\ColumnDelete;
use TMCms\HTML\Cms\Column\ColumnEdit;
use TMCms\HTML\Cms\Column\ColumnOrder;
use TMCms\HTML\Cms\Columns;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

$specification = q_assoc_row('SELECT `id`, `title` FROM `'. module_catalogue::$tables['specifications'] .'` WHERE `id` = "'. $id .'"');

echo Columns::getInstance()
		->add(
        BreadCrumbs::getInstance()
            ->addCrumb('<a href="?p='. P .'&do=specifications&id='. $specification['id'] .'">'. $specification['title'] .'</a>')
    )
		->add('<a href="?p='. P .'&do=specifications_params_add&id='. $id .'">Add Parameter</a>', array('align' => 'right'))
;

echo CmsTable::getInstance()
		->addDataSql('
SELECT
	`p`.`id`,
	`p`.`type`,
	`p`.`primary`,
	`d1`.`'. Users::getUserLng() .'` AS `key`,
	(SELECT COUNT(*) FROM `'. module_catalogue::$tables['specifications_params_options'] .'` AS `o` WHERE `o`.`param_id` = `p`.`id`) AS `options`
FROM `'. module_catalogue::$tables['specifications_params'] .'` AS `p`
LEFT JOIN `cms_translations` AS `d1` ON `d1`.`id` = `p`.`key`
WHERE `p`.`spec_id` = "'. $id .'"
ORDER BY `p`.`order`')
        ->setCallbackFunction('_specifications_params_callback')
		->addColumn(ColumnEdit::getInstance('key')->width('99%')->href('?p='. P .'&do=specifications_params_edit&id={%id%}'))
		->addColumn(ColumnData::getInstance('type'))
		->addColumn(ColumnData::getInstance('options')->href('?p='. P .'&do=specifications_params_options&id={%id%}')->align('center'))
		->addColumn(ColumnActive::getInstance('primary')->href('?p='. P .'&do=_specifications_params_primary&spec_id='. $id .'&id={%id%}'))
		->addColumn(ColumnOrder::getInstance('order')->href('?p='. P .'&do=_specifications_params_order&spec_id='. $id .'&id={%id%}'))
		->addColumn(ColumnDelete::getInstance()->href('?p='. P .'&do=_specifications_params_delete&id={%id%}'))
;

function _specifications_params_callback($data) {
    foreach ($data as & $v) {
        if ($v['type'] != 'SELECT') {
            $v['options'] = '';
        }
    }

    return $data;
}