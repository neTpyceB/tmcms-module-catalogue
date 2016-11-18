<?php
namespace TMCms\Modules\Catalogue\Entity;

use TMCms\Orm\Entity;

/**
 * Class CatalogueCategory
 * @package TMCms\Modules\Catalogue
 *
 * @method int getPid()
 * @method string getTitle()
 */
class CatalogueCategory extends Entity {
	protected $db_table = 'm_catalogue_categories';

	public function deleteObject() {
		// Delete all products in Category
		$products_collection = new CatalogueProductRepository();
		$products_collection->setWhereCategoryId($this->getId());
		$products_collection->deleteObjectCollection();

		// Delete all relations with products
		$categories_collection = new CatalogueCategoryRelationEntityRepository();
		$categories_collection->setWhereCategoryId($this->getId());
		$categories_collection->deleteObjectCollection();

		// Delete all subcategories
		$products_collection = new CatalogueCategoryRepository();
		$products_collection->setWherePid($this->getId());
		$products_collection->deleteObjectCollection();

		parent::deleteObject();
	}
}