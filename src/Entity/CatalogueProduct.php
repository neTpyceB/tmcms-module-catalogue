<?php
namespace TMCms\Modules\Catalogue\Entity;

use TMCms\Orm\Entity;

/**
 * Class CatalogueProduct
 * @package TMCms\Modules\Catalogue
 *
 * @method bool getActive()
 * @method int getCategoryId()
 * @method int getCreatedTs()
 * @method string getMainImage()
 * @method float getPrice()
 * @method bool getRecommend()
 * @method string getTitle()
 * @method string getUid()
 * @method string getUnit()
 *
 * @method setCreatedTs(int $ts)
 */
class CatalogueProduct extends Entity {
	protected $db_table = 'm_catalogue';

	public function getColors()
	{
		return CatalogueProductColorRelationEntityRepository::findAllEntitiesByCriteria([
			'product_id' => $this->getId()
		]);
	}

	protected function beforeSave()
	{
		// If tags are not changed - do not make changes in DB
		if ($this->isFieldChangedForUpdate('category')) {

			$categories = new CatalogueCategoryRelationEntityRepository;
			$categories->setWhereProductId($this->getId());
			$categories->deleteObjectCollection();

			// Create new relations
			if (!empty($_POST['category'])) {
				$category_clone = new CatalogueCategoryRelationEntity();

				foreach ($_POST['category'] as $cat_id) {
					$category = clone $category_clone;
					$category->setProductId($this->getId());
					$category->setCategoryId($cat_id);
					$category->save();
				}
			}
		}

		return $this;
	}

	public function beforeCreate() {
		$this->setCreatedTs(NOW);

		return $this;
	}
}