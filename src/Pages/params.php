<?
use TMCms\Admin\Users;
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsInputText;

defined('INC') or exit;

if (!isset($_GET['product_id']) || !ctype_digit((string)$_GET['product_id'])) return;
$product_id = & $_GET['product_id'];

$data = q_assoc_row('SELECT `spec_id` FROM `'. module_catalogue::$tables['products'] .'` WHERE `id` = "'. $product_id .'"');

$params = q_pairs('
SELECT
	`p`.`id`,
	`d1`.`'. Users::getUserLng() .'` AS `key`
FROM `'. module_catalogue::$tables['specifications_params'] .'` AS `p`
JOIN `cms_translations` AS `d1` ON `d1`.`id` = `p`.`key`
WHERE `p`.`spec_id` = "'. $data['spec_id'] .'"
ORDER BY `p`.`order`
');

$form = CmsForm::getInstance()
		->addData(q_pairs('SELECT `param_id`, `value` FROM `'. module_catalogue::$tables['params'] .'` WHERE `product_id` = "'. $product_id .'"'))
		->setAction('?p='. P .'&do=_params&product_id='. $product_id)
		->setSubmitButton(new CmsButton('Update'))
;

foreach ($params as $k => $v) $form->addField($v, CmsInputText::getInstance($k)->multilng(true));

echo $form;