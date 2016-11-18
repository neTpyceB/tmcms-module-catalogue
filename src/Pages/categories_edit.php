<?
use TMCms\Admin\Users;
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsInputText;
use TMCms\HTML\Cms\Element\CmsSelect;
use TMCms\Strings\UID;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

echo CmsForm::getInstance()
		->addData('SELECT * FROM `'. module_catalogue::$tables['categories'] .'` WHERE `id` = "'. $id .'"')
		->setAction('?p='. P .'&do=_categories_edit&id='. $id)
		->setSubmitButton(new CmsButton('Update'))
		->addField('Parent category', CmsSelect::getInstance('pid')->setOptions(array(0 => '---') + module_catalogue::getCategoriesAsArray())->html(0))
        ->addField('Specification', CmsSelect::getInstance('specification_id')->setOptions(module_catalogue::getSpecificationPairs())->html(0))
		->addField('Title', CmsInputText::getInstance('title')->multilng(true))
        ->addField('Icon image', CmsInputText::getInstance('icon_img')->setWidget(\TMCms\HTML\Cms\Widget\FileManager::getInstance()->path(DIR_PUBLIC_URL . 'images/catalogue_icons/')))
		->addField('UID', CmsInputText::getInstance('uid'))
//		->addField('Description', CmsTextarea::getInstance('description')->multilng(true)->setWidget(new Wysiwyg))
;

UID::text2uidJS(true, array('title_'. Users::getUserLng() .'_' => 'uid'), 255, 1, 1);