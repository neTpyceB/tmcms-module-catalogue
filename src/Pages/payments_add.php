<?

use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsInputText;

defined('INC') or exit;

echo CmsForm::getInstance()
		->setAction('?p='. P .'&do=_payments_add')
		->setSubmitButton(new CmsButton('Add'))
		->addField('Title', CmsInputText::getInstance('title')->multilng(true))
;