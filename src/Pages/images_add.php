<?
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsHtml;
use TMCms\HTML\Cms\Element\CmsInputText;

defined('INC') or exit;

if (!isset($_GET['product_id']) || !ctype_digit((string)$_GET['product_id'])) return;
$product_id = & $_GET['product_id'];

echo CmsForm::getInstance()
		->setAction('?p='. P .'&do=_images_add&product_id='. $product_id)
		->setSubmitButton(new CmsButton('Add'))
		->addField('Product', CmsHTML::getInstance('product')->value(module_catalogue::getTitle($product_id)))
		->addField('Image', CmsInputText::getInstance('image')->setWidget(\TMCms\HTML\Cms\Widget\FileManager::getInstance()->path('/images/products/')))
;