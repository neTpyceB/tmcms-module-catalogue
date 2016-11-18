<?
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\Columns;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsInputText;
use TMCms\HTML\Cms\Element\CmsSelect;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

echo Columns::getInstance()
		->add(q_value('SELECT `title` FROM `'. module_catalogue::$tables['specifications'] .'` WHERE `id` = "'. $id .'"'))
;

echo CmsForm::getInstance()
		->setAction('?p='. P .'&do=_specifications_params_add&id='. $id)
		->setSubmitButton(new CmsButton('Add'))
		->addField('Title', CmsInputText::getInstance('title')->multilng(1))
    ->addField('Type', CmsSelect::getInstance('type')->setOptions(module_catalogue::getSpecificationParamTypePairs()))

;