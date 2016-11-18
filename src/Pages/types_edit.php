<?

use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsInputText;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

echo CmsForm::getInstance()
		->addData('SELECT * FROM `'. module_catalogue::$tables['types'] .'` WHERE `id` = "'. $id .'"')
		->setAction('?p='. P .'&do=_types_edit&id='. $id)
		->setSubmitButton(new CmsButton('Update'))
		->addField('Title', CmsInputText::getInstance('title')->multilng(true))
;