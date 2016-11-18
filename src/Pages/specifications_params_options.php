<?
use TMCms\Admin\Users;
use TMCms\HTML\BreadCrumbs;
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\Column\ColumnDelete;
use TMCms\HTML\Cms\Column\ColumnEdit;
use TMCms\HTML\Cms\Column\ColumnOrder;
use TMCms\HTML\Cms\Columns;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

$param = q_assoc_row('SELECT `id`, `key`, `spec_id` FROM `'. module_catalogue::$tables['specifications_params'] .'` WHERE `id` = "'. $id .'"');
$specification = q_assoc_row('SELECT `id`, `title` FROM `'. module_catalogue::$tables['specifications'] .'` WHERE `id` = "'. $param['spec_id'] .'"');

echo Columns::getInstance()
		->add(
            BreadCrumbs::getInstance()
                ->addCrumb('<a href="?p='. P .'&do=specifications&id='. $specification['id'] .'">'. $specification['title'] .'</a>')
                ->addCrumb('<a href="?p='. P .'&do=specifications_params&id='. $param['spec_id'] .'">'. Translations::get($param['key'], LNG) .'</a>')
        )
		->add('<a href="?p='. P .'&do=specifications_params_options_add&id='. $id .'">Add Option</a>', array('align' => 'right'))
;

echo CmsTable::getInstance()
		->addDataSql('
SELECT
	`p`.`id`,
	`d1`.`'. Users::getUserLng() .'` AS `name`
FROM `'. module_catalogue::$tables['specifications_params_options'] .'` AS `p`
JOIN `cms_translations` AS `d1` ON `d1`.`id` = `p`.`name`
WHERE `p`.`param_id` = "'. $id .'"
ORDER BY `p`.`order`
')
		->addColumn(ColumnEdit::getInstance('name')->width('99%')->href('?p='. P .'&do=specifications_params_options_edit&id={%id%}'))
		->addColumn(ColumnOrder::getInstance('order')->href('?p='. P .'&do=_specifications_params_options_order&spec_id='. $id .'&id={%id%}'))
		->addColumn(ColumnDelete::getInstance()->href('?p='. P .'&do=_specifications_params_options_delete&id={%id%}'))
;