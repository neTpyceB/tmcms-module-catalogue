<?php

namespace TMCms\Modules\Catalogue\Entity;

use TMCms\Orm\EntityRepository;

class CatalogueSimilarProductEntityRepository extends EntityRepository {
    protected $table_structure = [
        'fields' => [
            'product_id' => [
                'type' => 'index',
            ],
            'similar_product_id' => [
                'type' => 'index',
            ],
        ],
    ];
}