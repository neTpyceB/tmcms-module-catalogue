<?php
namespace TMCms\Modules\Catalogue\Entity;

use TMCms\Orm\EntityRepository;

/**
 * Class CatalogueProductRepository
 * @package TMCms\Modules\Catalogue
 *
 * @method setWhereActive(bool $flag)
 * @method setWhereRecommend(bool $flag)
 * @method setWhereCategoryId(int $id)
 */
class CatalogueProductRepository extends EntityRepository {
	protected $db_table = 'm_catalogue';

}