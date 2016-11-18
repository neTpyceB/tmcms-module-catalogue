<?php

namespace TMCms\Modules\Catalogue;

use TMCms\Admin\Menu;
use TMCms\Admin\Messages;
use TMCms\Admin\Structure\Entity\Translation;
use TMCms\Admin\Users;
use TMCms\DB\SQL;
use TMCms\HTML\BreadCrumbs;
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\CmsTabs;
use TMCms\HTML\Cms\Column\ColumnActive;
use TMCms\HTML\Cms\Column\ColumnData;
use TMCms\HTML\Cms\Column\ColumnDelete;
use TMCms\HTML\Cms\Column\ColumnEdit;
use TMCms\HTML\Cms\Column\ColumnOrder;
use TMCms\HTML\Cms\Column\ColumnTree;
use TMCms\HTML\Cms\Columns;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsInputText;
use TMCms\HTML\Cms\Element\CmsMultipleSelect;
use TMCms\HTML\Cms\Element\CmsRow;
use TMCms\HTML\Cms\Element\CmsSelect;
use TMCms\HTML\Cms\Element\CmsTextarea;
use TMCms\HTML\Cms\Filter\Select;
use TMCms\HTML\Cms\Filter\Text;
use TMCms\HTML\Cms\FilterForm;
use TMCms\Log\App;
use TMCms\Modules\Catalogue\Entity\CatalogueAccompanyingProductEntity;
use TMCms\Modules\Catalogue\Entity\CatalogueAccompanyingProductEntityRepository;
use TMCms\Modules\Catalogue\Entity\CatalogueCategory;
use TMCms\Modules\Catalogue\Entity\CatalogueCategoryRepository;
use TMCms\Modules\Catalogue\Entity\CatalogueCategoryRelationEntityRepository;
use TMCms\Modules\Catalogue\Entity\CatalogueManufacturer;
use TMCms\Modules\Catalogue\Entity\CatalogueManufacturerRepository;
use TMCms\Modules\Catalogue\Entity\CatalogueProduct;
use TMCms\Modules\Catalogue\Entity\CatalogueProductColorRelationEntity;
use TMCms\Modules\Catalogue\Entity\CatalogueProductColorRelationEntityRepository;
use TMCms\Modules\Catalogue\Entity\CatalogueProductRepository;
use TMCms\Modules\Catalogue\Entity\CatalogueSimilarProductEntity;
use TMCms\Modules\Catalogue\Entity\CatalogueSimilarProductEntityRepository;
use TMCms\Modules\Catalogue\Entity\ReviewRelationEntity;
use TMCms\Modules\Catalogue\Entity\ReviewRelationEntityRepository;
use TMCms\Modules\Gallery\ModuleGallery;
use TMCms\Modules\Images\Entity\ImageEntityRepository;
use TMCms\Modules\ModuleManager;
use TMCms\Modules\Reviews\ModuleReviews;
use TMCms\Strings\Translations;
use TMCms\Strings\UID;

defined('INC') or exit;

$menu = Menu::getInstance();

$menu
	->addSubMenuItem('categories')
	->addSubMenuItem('manufacturers')
;

ModuleManager::requireModule('reviews');

class CmsCatalogue {
	public function _default() {

		echo Columns::getInstance()
				->add('<a class="btn btn-success" href="?p=' . P . '&do=add">Add Product</a>', array('align' => 'right'))
		;

		$products = new CatalogueProductRepository();
		$products->addSimpleSelectFields(['id', 'title', 'recommend', 'active', 'price', 'amount', 'manufacturer_id', 'width', 'height', 'length']);

        // Check if set category id
        // Show only products with selected category
        if (isset($_GET['category_id'])) {
            $products_categories_collection = new CatalogueCategoryRelationEntityRepository();
            $products_categories_collection->setWhereCategoryId($_GET['category_id']);

            $products->mergeWithCollection($products_categories_collection, 'id', 'product_id');
        }

		$images = new ImageEntityRepository();
		$products->addSimpleSelectFieldsAsString('(SELECT COUNT(*) FROM `'. $images->getDbTableName() .'` WHERE `item_id` = `'. $products->getDbTableName() .'`.`id` AND `item_type` = "catalogueproduct") AS `images`');

//		$dates = new OfferDateEntityRepository();
//		$dates->addSelectFieldAsString('(SELECT COUNT(*) FROM `'. $dates->getDbTableName() .'` WHERE `offer_id` = `'. $offers->getDbTableName() .'`.`id`) AS `dates`');

//		$offers->mergeWithCollection($images, 'id', 'item_id', 'left');

		$categories = CatalogueCategoryRepository::getInstance()->getPairs('title');
		$manufacturers = CatalogueManufacturerRepository::getInstance()->getPairs('title');


		echo CmsTable::getInstance()
//			->setCallbackFunction(function($products){
//				foreach ($products as & $v) {
//					$v['size'] = $v['length'] . 'x' . $v['width'] . 'x' . $v['height'];
//				}
//				return $products;
//			})
			->addData($products)
			->addColumn(ColumnData::getInstance('title'))
//			->addColumn(ColumnData::getInstance('size'))
			->addColumn(ColumnData::getInstance('manufacturer_id')
				->nowrap(true)
				->title('Manufacturer')
				->width('1%')
				->href('?p='. P .'&manufacturer_id={%manufacturer_id%}')
				->setPairedDataOptionsForKeys($manufacturers)
			)
			->addColumn(ColumnData::getInstance('images')
				->enableNarrowWidth()
				->align('center')
				->setHref('?p='. P .'&do=images&id={%id%}')
			)
			->addColumn(ColumnData::getInstance('price')->nowrap(true))
			->addColumn(ColumnData::getInstance('amount')->enableNarrowWidth())
			->addColumn(ColumnActive::getInstance('recommend')->href('?p='. P .'&do=_recommend&id={%id%}')
				->ajax(true)
			)
			->addColumn(ColumnActive::getInstance('active')->ajax(true)->href('?p='. P .'&do=_active&id={%id%}'))
			->addColumn(ColumnEdit::getInstance())
			->addColumn(ColumnDelete::getInstance()->href('?p='. P .'&do=_delete&id={%id%}'))
			->attachFilterForm(
				FilterForm::getInstance()->setWidth('100%')
					//->addFilter('Category', Select::getInstance('category_id')->setOptions(array(-1 => 'All') + ModuleCatalogue::getCategoriesAsArray())->ignoreValue(-1)->html(false))
					->addFilter('Manufacturer', Select::getInstance('manufacturer_id')->setOptions(array(-1 => 'All') + ModuleCatalogue::getManufacturersPairs())->ignoreValue(-1)->html(false))
					->addFilter('ID', Text::getInstance('id'))
			)
		;
	}

	public function __products_form($data = NULL)
	{
		$form1 = CmsForm::getInstance()
				->addData($data)
				->addField('Uid', CmsInputText::getInstance('uid')
					->help('Auto-generated, required for URLs')
					->validateRequired()
				)
				->addField('Title', CmsInputText::getInstance('title')
					->validateRequired()
				)
                ->addField('Code', CmsInputText::getInstance('code'))
				->addField('Main image', CmsInputText::getInstance('main_image')
					->enableFilemanager(DIR_IMAGES_URL . 'products/')
				)
				->addField('Units', CmsSelect::getInstance('units')
					->setOptions(ModuleCatalogue::getUnitsPairs())
				)
				->addField('Amount', CmsInputText::getInstance('amount'))
				->addField('Price', CmsInputText::getInstance('price'))
				->addField('Price with discount', CmsInputText::getInstance('price_with_discount'))
				->addField('Discount percent', CmsInputText::getInstance('discount_percent'))
				->addField('Description', CmsTextarea::getInstance('description')->enableWysiwyg())
				->addField('Quick overview', CmsTextarea::getInstance('quick_overview'))
				->addField('Usefull', CmsTextarea::getInstance('usefull'))
				->outputTagForm(false)
		;

		$form_meta = CmsForm::getInstance()
				->addData($data)
				->addField('Meta Title', CmsInputText::getInstance('meta_title'))
				->addField('Meta keywords', CmsInputText::getInstance('meta_keywords'))
				->addField('Meta description', CmsInputText::getInstance('meta_description'))
				->addField('itemprop description', CmsInputText::getInstance('meta_long_description'))
				->outputTagForm(false)
		;

		$form2 = CmsForm::getInstance()
				->addData($data)
//				->addField('Width', CmsInputText::getInstance('width')
//					->help('cm')
//				)
//				->addField('Height', CmsInputText::getInstance('height')
//					->help('cm')
//				)
//				->addField('Length', CmsInputText::getInstance('length')
//					->help('cm')
//				)
				->addField('Weight', CmsInputText::getInstance('weight')
					->help('kg')
				)
                ->addField('On pallet', CmsInputText::getInstance('on_pallet')
                    ->help('quantity')
                )
                ->addField('Number per 1m2', CmsInputText::getInstance('1m_quantity')
                    ->help('quantity')
                )
				->outputTagForm(false)
		;

		$form3 = CmsForm::getInstance()
				->addData($data)
				->addField('Category', CmsMultipleSelect::getInstance('category')
					->setOptions(ModuleCatalogue::getCategoriesTreeForSelects())
				)
				->addField('Reviews', CmsMultipleSelect::getInstance('reviews')
					->setOptions(ModuleReviews::getPairs())
				)
				->addField('Manufacturer', CmsSelect::getInstance('manufacturer_id')
					->setOptions(ModuleCatalogue::getManufacturersPairs())
					->html(true)
				)
				->addField('Colors', CmsMultipleSelect::getInstance('colors')
					->setOptions(ModuleCatalogue::getColorPairs())
				)
				->addField('Similar products', CmsMultipleSelect::getInstance('similar_products')
					->setOptions(ModuleCatalogue::getProductPairs())
				)
				->addField('Accompanying products', CmsMultipleSelect::getInstance('accompanying_products')
					->setOptions(ModuleCatalogue::getProductPairs())
				)
				->outputTagForm(false)
		;

		$tabs = CmsTabs::getInstance()
				->addTab('Main', $form1)
				->addTab('Meta', $form_meta)
				->addTab('Params', $form2)
				->addTab('Relations', $form3)
		;

		return CmsForm::getInstance()
				->enableAjax()
				->addData($data)
				->setAction('?p=' . P . '&do=_add')
				->setSubmitButton(CmsButton::getInstance(__('Add')))
				->setCancelButton(CmsButton::getInstance(__('Cancel')))
				->addField('', CmsRow::getInstance('form')
					->value($tabs)
				)
		;
	}

	public function add()
	{
		echo BreadCrumbs::getInstance()
			->addCrumb(ucfirst(P))
			->addCrumb('Add Product')
		;

		echo self::__products_form();

		UID::text2uidJS(true, array('title' => 'uid'), 255, 1, 1);
	}

	public function edit()
	{
		$id = abs((int)$_GET['id']);
		if (!$id) return;

		$product = new CatalogueProduct($id);

		echo BreadCrumbs::getInstance()
			->addCrumb(ucfirst(P), '?p='. P)
			->addCrumb('Edit Product')
			->addCrumb($product->getTitle())
		;

		// Colors
		$similars = new CatalogueProductColorRelationEntityRepository();
		$similars->setWhereProductId($id);
		$product->setColors($similars->getPairs('color'));

		// Reviews
		$similars = new ReviewRelationEntityRepository();
		$similars->setWhereProductId($id);
		$product->setReviews($similars->getPairs('review_id'));

		// Similar products
		$similars = new CatalogueSimilarProductEntityRepository();
		$similars->setWhereProductId($id);
		$product->setSimilarProducts($similars->getPairs('similar_product_id'));

		// Accompanying products
		$accompanying = new CatalogueAccompanyingProductEntityRepository();
		$accompanying->setWhereProductId($id);
		$product->setAccompanyingProducts($accompanying->getPairs('accompanying_product_id'));


		$product->setCategory(CatalogueCategoryRelationEntityRepository::getInstance()
			->setWhereProductId($product->getId())
			->getPairs('category_id')
		);

		echo self::__products_form($product)
			->setAction('?p=' . P . '&do=_edit&id=' . $id)
			->setSubmitButton('Update');

		UID::text2uidJS(true, array('title' => 'uid'), 255, 0, 1);
	}

	public function _add()
	{
		$product = new CatalogueProduct;
		$product->loadDataFromArray($_POST);
		$product->save();

		// colors
		if (!isset($_POST['colors'])) {
			$_POST['colors'] = [];
		}
		$this->__update_colors($product->getId(), $_POST['colors']);

		// reviews
		if (!isset($_POST['reviews'])) {
			$_POST['reviews'] = [];
		}
		$this->__update_reviews($product->getId(), $_POST['reviews']);


		//similar products
		if (!isset($_POST['similar_products'])) {
			$_POST['similar_products'] = [];
		}
		$this->__update_similar_products($product->getId(), $_POST['similar_products']);

		//accompanying products
		if (!isset($_POST['accompanying_products'])) {
			$_POST['accompanying_products'] = [];
		}
		$this->__update_accompanying_products($product->getId(), $_POST['accompanying_products']);

		App::add('Product "' . $product->getTitle() . '" added');

		Messages::sendMessage('Product added');

		go('?p='. P .'&highlight='. $product->getId());
	}

	public function _edit()
	{
		$id = abs((int)$_GET['id']);
		if (!$id) return;

		$form = self::__products_form();
		if ($errors = $form->validateAndGetErrors($_POST)) {
			dump($errors);
		}

		$product = new CatalogueProduct($id);
		$product->loadDataFromArray($_POST);
		$product->save();

		// colors
		if (!isset($_POST['colors'])) {
			$_POST['colors'] = [];
		}
		$this->__update_colors($product->getId(), $_POST['colors']);


		// reviews
		if (!isset($_POST['reviews'])) {
			$_POST['reviews'] = [];
		}
		$this->__update_reviews($product->getId(), $_POST['reviews']);


		if (!isset($_POST['similar_products'])) {
			$_POST['similar_products'] = [];
		}
		$this->__update_similar_products($id, $_POST['similar_products']);

		if (!isset($_POST['accompanying_products'])) {
			$_POST['accompanying_products'] = [];
		}
		$this->__update_accompanying_products($id, $_POST['accompanying_products']);

		App::add('Product "' . $product->getTitle() . '" edited');

		Messages::sendMessage('Product updated');

		go('?p='. P .'&highlight='. $product->getId());
	}

	public function _active()
	{
		$id = abs((int)$_GET['id']);
		if (!$id) return;

		$product = new CatalogueProduct($id);
		$product->flipBoolValue('active');
		$product->save();

		App::add('Product "' . $product->getTitle() . '" ' . ($product->getActive() ? '' : 'de') . 'activated');

		Messages::sendGreenAlert('Product updated');

		if (IS_AJAX_REQUEST) {
			die('1');
		}
		back();
	}

	public function _recommend()
	{
		$id = abs((int)$_GET['id']);
		if (!$id) return;

		$product = new CatalogueProduct($id);
		$product->flipBoolValue('recommend');
		$product->save();

		App::add('Product "' . $product->getTitle() . '" updated');

		Messages::sendMessage('Product updated');

		if (IS_AJAX_REQUEST) {
			die('1');
		}
		back();
	}

	public function _delete()
	{
		$id = abs((int)$_GET['id']);
		if (!$id) return;

		$product = new CatalogueProduct($id);
		$product->deleteObject();

		$product_collection = new CatalogueCategoryRelationEntityRepository();
		$product_collection->setWhereProductId($id);
		$product_collection->deleteObjectCollection();

		//Similar rows
		$similar = new CatalogueSimilarProductEntityRepository();
		$similar->setWhereProductId($id);
		$similar->deleteObjectCollection();

		//Accompanying rows
		$accompanying = new CatalogueAccompanyingProductEntityRepository();
		$accompanying->setWhereProductId($id);
		$accompanying->deleteObjectCollection();

		App::add('Product "' . $product->getTitle() . '" deleted');

		Messages::sendMessage('Product deleted');

		back();
	}


	/** CATEGORIES */
	public function categories() {
		echo Columns::getInstance()
			->add('<a class="btn btn-success" href="?p=' . P . '&do=categories_add">Add Category</a>', array('align' => 'right'))
		;

		echo '<br>';

		$data = '
SELECT
	`c`.`id`,
	`c`.`pid`,
	`c`.`title`,
	`c`.`active`,
	(SELECT COUNT(*) FROM `'. ModuleCatalogue::$tables['categories_relation'] .'` AS `p` WHERE `p`.`category_id` = `c`.`id`) AS `products`
FROM `'. ModuleCatalogue::$tables['categories'] .'` AS `c`
ORDER BY `c`.`order`
		';

		echo CmsTable::getInstance()
			->addData($data)
			->addColumn(ColumnTree::getInstance('id')
				->href('?p='. P .'&do=categories_edit&id={%id%}')
				->ajax(true)
				->saveInnerState(true)
				->title('Title')
				->setShowKey('title')
				->width('99%'))
			->addColumn(ColumnData::getInstance('products')->href('?p='. P .'&category_id={%id%}')->width('1%')->align('center')->nowrap(1))
			->addColumn(ColumnOrder::getInstance('order')->href('?p='. P .'&do=_categories_order&id={%id%}'))
			->addColumn(ColumnActive::getInstance('active')->href('?p='. P .'&do=_categories_active&id={%id%}')->ajax(true))
			->addColumn(ColumnDelete::getInstance('delete')->href('?p='. P .'&do=_categories_delete&id={%id%}'))
		;
	}

	private static function __categories_add_edit_form() {
		return CmsForm::getInstance()
			->setAction('?p='. P .'&do=_categories_add')
			->setSubmitButton(new CmsButton('Add Category'))
			->addField('Parent category', CmsSelect::getInstance('pid')->setOptions(array(0 => '---') + ModuleCatalogue::getCategoriesAsArray())->html(0)->setSelected(isset($_GET['add_sub_id']) ? $_GET['add_sub_id'] : 0))
				->addField('Title', CmsInputText::getInstance('title'))
				->addField('Description', CmsTextarea::getInstance('description')->enableWysiwyg())
				->addField('Meta Tag Title', CmsInputText::getInstance('meta_title'))
				->addField('Meta Tag Description', CmsInputText::getInstance('meta_description'))
			->addField('Meta Tag Keywords', CmsInputText::getInstance('meta_keywords'))
			->addField('UID', CmsInputText::getInstance('uid'))
		;
	}

	public function categories_add() {
		echo self::__categories_add_edit_form();

		UID::text2uidJS(true, array('title' => 'uid'), 255, 1, 1);
	}

	public function _categories_add() {
		$category = new CatalogueCategory();
		$category->loadDataFromArray($_POST);
		$category->save();

		App::add('Category'. $category->getTitle() .' created');
		Messages::sendMessage('Category created');
		go('?p='. P .'&do=categories&highlight='. $category->getId());
	}

	public function categories_edit() {
		$category = new CatalogueCategory($_GET['id']);
		echo self::__categories_add_edit_form()
			->addData($category)
			->setAction('?p='. P .'&do=_categories_edit&id='. $category->getId())
			->setSubmitButton(new CmsButton('Update Category'))
		;

		UID::text2uidJS(true, array('title' => 'uid'), 255, 1, 1);
	}

	public function _categories_edit() {
		$category = new CatalogueCategory($_GET['id']);
		$category->loadDataFromArray($_POST);
		$category->save();

		App::add('Category'. $category->getTitle() .' updated');
		Messages::sendMessage('Category updated');
		go('?p='. P .'&do=categories&highlight='. $category->getId());
	}

	public static function _categories_active() {
		$category = new CatalogueCategory($_GET['id']);
		$category
			->flipBoolValue('active')
			->save()
		;

		App::add('Catalogue Category '. $category->getTitle() . ' changed active status');

		Messages::sendGreenAlert('Catalogue Category '. $category->getTitle() . ' changed active status');

		die('1');
	}

	public static function _categories_order() {
		$category = new CatalogueCategory($_GET['id']);

		SQL::orderCat($_GET['id'], $category->getDbTableName(), $category->getPid(), 'pid', $_GET['direct']);

		App::add('Catalogue Category order changed');

		Messages::sendMessage('Catalogue Category order changed');

		back();
	}

	public static function _categories_delete() {

        $query = q('DELETE FROM '.ModuleCatalogue::$tables['categories'].' WHERE id = '.$_GET['id'].'');
        $query->fetchAll();

        $query = q('DELETE FROM '.ModuleCatalogue::$tables['categories_relation'].' WHERE category_id = '.$_GET['id'].'');
        $query->fetchAll();

		App::add('Catalogue Category deleted');

		Messages::sendMessage('Catalogue Category deleted');

		back();
	}


	/** MANUFACTURERS */
	public function manufacturers() {
		echo Columns::getInstance()
			->add('<a class="btn btn-success" href="?p=' . P . '&do=manufacturers_add">Add Manufacturer</a>', array('align' => 'right'))
		;
		echo '<br>';
		$data = 'SELECT * FROM `'. ModuleCatalogue::$tables['manufacturer'] .'`';
		echo CmsTable::getInstance()
			->addData($data)
			->addColumn(ColumnData::getInstance('id')
				->href('?p='. P .'&do=manufacturers_edit&id={%id%}')
				->width('1%'))
			->addColumn(ColumnData::getInstance('title')
				->href('?p='. P .'&do=manufacturers_edit&id={%id%}')
				->title('title')
				->width('50%'))
			->addColumn(ColumnData::getInstance('address')
				->width('50%'))
			->addColumn(ColumnEdit::getInstance('edit')
				->href('?p=' . P . '&do=manufacturers_edit&id={%id%}')
				->width('1%')
				->value('Edit')
			)
			->addColumn(ColumnDelete::getInstance('delete')->href('?p='. P .'&do=_manufacturers_delete&id={%id%}'))
		;
	}

	private static function __manufacturers_add_edit_form() {
		return CmsForm::getInstance()
			->setAction('?p='. P .'&do=_manufacturers_add')
			->setSubmitButton(new CmsButton('Add Manufacturer'))
			->addField('Title', CmsInputText::getInstance('title'))
			->addField('Address', CmsInputText::getInstance('address'))
			->addField('Latitude', CmsInputText::getInstance('latitude'))
			->addField('Longitude', CmsInputText::getInstance('longitude'))
		;
	}

	public function manufacturers_add() {
		echo self::__manufacturers_add_edit_form();
	}

	public function _manufacturers_add() {
		$manufacturer = new CatalogueManufacturer();
		$manufacturer->loadDataFromArray($_POST);
		$manufacturer->save();

		App::add('Manufacturer'. $manufacturer->getTitle() .' created');
		Messages::sendMessage('Manufacturer created');
		go('?p='. P .'&do=manufacturers&highlight='. $manufacturer->getId());
	}

	public function manufacturers_edit() {
		$manufacturer = new CatalogueManufacturer($_GET['id']);
		echo self::__manufacturers_add_edit_form()
			->addData($manufacturer)
			->setAction('?p='. P .'&do=_manufacturers_edit&id='. $manufacturer->getId())
			->setSubmitButton(new CmsButton('Update Manufacturer'))
		;
	}

	public function _manufacturers_edit() {
		$manufacturer = new CatalogueManufacturer($_GET['id']);
		$manufacturer->loadDataFromArray($_POST);
		$manufacturer->save();

		App::add('Manufacturer'. $manufacturer->getTitle() .' updated');
		Messages::sendMessage('Manufacturer updated');

		go('?p='. P .'&do=manufacturers&highlight='. $manufacturer->getId());
	}

	public static function _manufacturers_active() {
		$manufacturer = new CatalogueManufacturer($_GET['id']);
		$manufacturer
			->flipBoolValue('active')
			->save()
		;

		App::add('Manufacturer '. $manufacturer->getTitle() . ' changed active status');

		Messages::sendMessage('Manufacturer '. $manufacturer->getTitle() . ' changed active status');

		die('1');
	}

	public static function _manufacturers_delete() {
		$manufacturer = new CatalogueManufacturer($_GET['id']);
		$manufacturer->deleteObject();

		App::add('Manufacturer '. $manufacturer->getTitle() . 'deleted');

		Messages::sendMessage('Manufacturer '. $manufacturer->getTitle() . 'deleted');

		back();
	}

	private function __update_colors($id, $colors)
	{
		// Remove old
		$exist_reviews = new CatalogueProductColorRelationEntityRepository();
		$exist_reviews->setWhereProductId($id);
		$exist_reviews->deleteObjectCollection();

		// Insert new
		$review_to_clone = new CatalogueProductColorRelationEntity();
		foreach ($colors as $color_key) {
			$similar = clone $review_to_clone;
			$similar->loadDataFromArray([
				'product_id' => $id,
				'color' => $color_key,
			]);
			$similar->save();
		}
	}

	private function __update_reviews($id, $reviews)
	{
		// Remove old
		$exist_reviews = new ReviewRelationEntityRepository();
		$exist_reviews->setWhereProductId($id);
		$exist_reviews->deleteObjectCollection();

		// Insert new
		$review_to_clone = new ReviewRelationEntity();
		foreach ($reviews as $similar_id) {
			$similar = clone $review_to_clone;
			$similar->loadDataFromArray([
				'product_id' => $id,
				'review_id' => $similar_id,
			]);
			$similar->save();
		}
	}

	private function __update_similar_products($id, $similar_products)
	{
		// Remove old
		$similars = new CatalogueSimilarProductEntityRepository();
		$similars->setWhereProductId($id);
		$similars->deleteObjectCollection();

		// Insert new
		$similar_to_clone = new CatalogueSimilarProductEntity();
		foreach ($similar_products as $similar_id) {
			$similar = clone $similar_to_clone;
			$similar->loadDataFromArray([
				'product_id' => $id,
				'similar_product_id' => $similar_id,
			]);
			$similar->save();
		}
	}

	private function __update_accompanying_products($id, $accompanying_products)
	{
		// Remove old
		$accompanying = new CatalogueAccompanyingProductEntityRepository();
		$accompanying->setWhereProductId($id);
		$accompanying->deleteObjectCollection();

		// Insert new
		$accompanying_to_clone = new CatalogueAccompanyingProductEntity();
		foreach ($accompanying_products as $accompanying_id) {
			$accompanying = clone $accompanying_to_clone;
			$accompanying->loadDataFromArray([
				'product_id' => $id,
				'accompanying_product_id' => $accompanying_id,
			]);
			$accompanying->save();
		}
	}


	/** IMAGES */
	public function images() {
		$id = abs((int)$_GET['id']);
		if (!$id) return;

		$product = new CatalogueProduct($id);

		echo ModuleGallery::getViewForCmsModules($product);
	}

	public function _images_delete() {
		$id = abs((int)$_GET['id']);
		if (!$id) return;

		ModuleGallery::deleteImageForCmsModules($id);

		back();
	}

	public function _images_move() {
		$id = abs((int)$_GET['id']);
		if (!$id) return;

		ModuleGallery::orderImageForCmsModules($id, $_GET['direct']);

		back();
	}
}
