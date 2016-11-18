<?php

namespace TMCms\Modules\Catalogue\Entity;

use TMCms\Orm\EntityRepository;

class CatalogueProductColorRelationEntityRepository extends EntityRepository {
    protected $db_table = 'm_catalogue_product_colors';
    protected $table_structure = [
        'fields' => [
            'product_id' => [
                'type' => 'index',
            ],
            'color' => [
                'type' => 'varchar',
            ],
        ],
    ];
}