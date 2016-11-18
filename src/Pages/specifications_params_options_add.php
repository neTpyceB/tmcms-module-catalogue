<?
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\Columns;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsInputText;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

echo Columns::getInstance()
		->add(Translations::get(q_value('SELECT `key` FROM `'. module_catalogue::$tables['specifications_params'] .'` WHERE `id` = "'. $id .'"'), LNG))
;

echo CmsForm::getInstance()
		->setAction('?p='. P .'&do=_specifications_params_options_add&id='. $id)
		->setSubmitButton(new CmsButton('Add'))
		->addField('Title', CmsInputText::getInstance('name')->multilng(1))

;