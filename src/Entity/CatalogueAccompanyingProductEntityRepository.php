<?php

namespace TMCms\Modules\Catalogue\Entity;

use TMCms\Orm\EntityRepository;

class CatalogueAccompanyingProductEntityRepository extends EntityRepository {
    protected $table_structure = [
        'fields' => [
            'product_id' => [
                'type' => 'index',
            ],
            'accompanying_product_id' => [
                'type' => 'index',
            ],
        ],
    ];
}