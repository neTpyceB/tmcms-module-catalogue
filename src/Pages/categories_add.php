<?

use TMCms\Admin\Users;
use TMCms\HTML\Cms\CmsFieldset;
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\Column\ColumnInput;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsInputText;
use TMCms\HTML\Cms\Element\CmsRow;
use TMCms\HTML\Cms\Element\CmsSelect;
use TMCms\HTML\Cms\Filter\Html;
use TMCms\Strings\UID;

defined('INC') or exit;

$form_main = CmsForm::getInstance()
        ->outputTagForm(false)
		->addField('Parent category', CmsSelect::getInstance('pid')->setOptions(array(0 => '---') + module_catalogue::getCategoriesAsArray())->html(0)->setSelected(isset($_GET['add_sub_id']) ? $_GET['add_sub_id'] : 0))
		->addField('Existing specification', CmsSelect::getInstance('specification_id')->setOptions([0 => '--- new ---'] + module_catalogue::getSpecificationPairs())->html(0))
		->addField('Title', CmsInputText::getInstance('title')->multilng(true))
		->addField('Icon image', CmsInputText::getInstance('icon_img')->setWidget(\TMCms\HTML\Cms\Widget\FileManager::getInstance()->path(DIR_PUBLIC_URL . 'images/catalogue_icons/')))
		->addField('UID', CmsInputText::getInstance('uid'))
//		->addField('Description', CmsTextarea::getInstance('description')->multilng(true)->setWidget(new Wysiwyg))
;

$params_fields = [
    ['field' => CmsInputText::getInstance('specification_title'), 'name' => 'Title']
];

$lngs = \TMCms\Routing\Languages::getPairs();

$data = [];
for ($i = 1; $i < 40 ; $i++) {
    $data[] = [
        'id' => $i
    ];
//    $params_fields[] = ['field' => CmsInputText::getInstance('param_'. $i .'_title')->multilng(true), 'name' => 'Param '. $i .' title'];
//    $params_fields[] = ['field' => CmsSelect::getInstance('param_'. $i .'_type')->setOptions(module_catalogue::getSpecificationParamTypePairs()), 'name' => 'Param '. $i .' type'];
}

$form_specification = CmsForm::getInstance()
//    ->addFieldBlock('New specification group', $params_fields)
    ->addField('New spec. title', CmsInputText::getInstance('specification_title'))
;

$table = CmsTable::getInstance('specs')
    ->addDataArray($data)
    ->addColumn(\TMCms\HTML\Cms\Column\ColumnAutoNumber::getInstance('number')->width('1%'))
    ->addColumn(ColumnInput::getInstance('type')->setTypeSelect()->setOptions(module_catalogue::getSpecificationParamTypePairs()))
;

foreach ($lngs as $k => $v) {
    $table->addColumn(ColumnInput::getInstance($k)->setTypeText());
}


$form = CmsForm::getInstance()
    ->setAction('?p='. P .'&do=_categories_add')
    ->setSubmitButton(new CmsButton('Add'))
    ->addField('', CmsRow::getInstance('')->value($form_main))
    ->addField('', CmsRow::getInstance('')->value('<br><br><br><br>Create NEW Specifications'))
    ->addField('', CmsRow::getInstance('')->value($form_specification))
    ->addField('', CmsRow::getInstance('')->value($table))
;

echo $form;

UID::text2uidJS(true, array('title_'. Users::getUserLng() .'_' => 'uid'), 255, 1, 1);