<?php

namespace TMCms\Modules\Catalogue\Entity;

use TMCms\Orm\EntityRepository;

/**
 * CatalogueCategoryRelationEntityRepository
 * @package TMCms\Modules\Catalogue\Entity
 */
class CatalogueCategoryRelationEntityRepository extends EntityRepository
{
    protected $db_table = 'm_catalogue_categories2products';
    protected $table_structure = [
        'fields' => [
            'category_id' => [
                'type' => 'index',
            ],
            'product_id' => [
                'type' => 'index',
            ],
        ],
    ];
}