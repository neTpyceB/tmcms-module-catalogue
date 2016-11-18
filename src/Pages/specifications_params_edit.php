<?
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsInputText;
use TMCms\HTML\Cms\Element\CmsSelect;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

echo CmsForm::getInstance()
		->addData('SELECT * FROM `'. module_catalogue::$tables['specifications_params'] .'` WHERE `id` = "'. $id .'"')
		->setAction('?p='. P .'&do=_specifications_params_edit&id='. $id)
		->setSubmitButton(new CmsButton('Update'))
		->addField('Title', CmsInputText::getInstance('key')->multilng(1))
		->addField('Type', CmsSelect::getInstance('type')->setOptions(module_catalogue::getSpecificationParamTypePairs()))
;