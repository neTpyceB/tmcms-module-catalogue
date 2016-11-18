<?php

namespace TMCms\Modules\Catalogue;

use TMCms\Admin\Users;
use TMCms\DB\TableTree;
use TMCms\Modules\Catalogue\Entity\CatalogueCategory;
use TMCms\Modules\Catalogue\Entity\CatalogueCategoryRelationEntityRepository;
use TMCms\Modules\Catalogue\Entity\CatalogueManufacturerRepository;
use TMCms\Modules\Catalogue\Entity\CatalogueProduct;
use TMCms\Modules\Catalogue\Entity\CatalogueProductRepository;
use TMCms\Modules\Wishlist\ModuleWishlist;
use TMCms\Routing\Structure;

defined('INC') or exit;

class ModuleCatalogue {
	public static $tables = array(
		'products' => 'm_catalogue',
		'categories' => 'm_catalogue_categories',
        'categories_relation' => 'm_catalogue_categories2products',
		'manufacturer' => 'm_catalogue_manufacturers',
	);

	public static $cache_key_categories = 'catalogue_tree_key';
	public static $cache_key_category_params_prefix = 'catalogue_Categories_params';// prefix . $id
	private static $measure_units = [
		'psc' => 'шт.',
		'm2' => 'м2',
		'running_м2' => 'пог. м2',
		'm3' => 'м3',
		'ton' => 'т.',
	];
	private static $color_pairs = [
		'gray' => 'Серый',
		'black' => 'Чёрный',
		'red' => 'Красный',
        'yellow' => 'Желтый',
        'blue' => 'Синий',
		'green' => 'Зелёный',
        'brown' => 'Коричневый'
	];
	private static $color_pairs_data = [
		[
			'key' => 'gray',
			'color' => '898989',
			'title' => 'Серый',
		],
		[
			'key' => 'black',
			'color' => '242424',
			'title' => 'Чёрный',
		],
        [
            'key' => 'red',
            'color' => '7c2425',
            'title' => 'Красный',
        ],
		[
			'key' => 'yellow',
			'color' => 'e9ca82',
			'title' => 'Желтый',
		],
        [
            'key' => 'blue',
            'color' => '1f3868',
            'title' => 'Синий',
        ],
        [
            'key' => 'green',
            'color' => '234d25',
            'title' => 'Зеленый',
        ],
		[
			'key' => 'brown',
			'color' => '2e210f',
			'title' => 'Коричневый',
		],
	];

	public static function getCategoriesTree() {
		return TableTree::getInstance(self::$tables['categories'])
			->setTitleColumn('title')
			->setOrderColumn('order')
			->getAsTree();
	}

	public static function getCategoriesTreeForSelects() {
		return TableTree::getInstance(self::$tables['categories'])
			->setTitleColumn('title')
			->setOrderColumn('order')
			->getAsArray4Options();
	}

	public static function getManufacturersPairs() {
		$transportation = new CatalogueManufacturerRepository();
		return $transportation->getPairs('title', 'id');
	}

	public static function getCategoriesAsArray() {
		return TableTree::getInstance(self::$tables['categories'])->setTitleColumn('title')->setOrderColumn('order')->getAsArray();
	}

	/**
	 * @param int $limit
	 * @return array
     */
	public static function getPopular($limit = 0)
	{
		$products = new CatalogueProductRepository();
		if ($limit) {
			$products->setLimit($limit);
		}
		$products->setWhereActive(true);
		$products->setOrderByRandom(true); // TODO order by popularity

		return $products->getAsArrayOfObjects();
	}

	public static function getRecommended($limit = 0)
	{
		$products = new CatalogueProductRepository();
		if ($limit) {
			$products->setLimit($limit);
		}

		$products->setWhereActive(true);
		$products->setWhereRecommend(true);

		$products->setOrderByRandom(true);

		return $products->getAsArrayOfObjects();
	}

	public static function getProducts($limit = 0, $order_by = '', $order_direction = 'ASC')
	{
		$products = new CatalogueProductRepository();
		if ($limit) {
			$products->setLimit($limit);
		}
		if ($order_by) {
			$products->setOrderByField($order_by);
		}
		if ($order_direction == 'DESC') {
			$products->setOrderDirectionDesc();
		}

		$products->setWhereActive(true);

		return $products->getAsArrayOfObjects();
	}

	public static function getOneProductMainView(CatalogueProduct $product)
	{
		$star_uid = mt_rand(0, 100);



		$icon = '';
		$icon_text = '';
		if ((NOW - $product->getCreatedTs()) < (14 * 86400)) { // 2 weeks
			$icon = 'ribbon-new';
			$icon_text = 'New';
		} elseif ($product->getPriceWithDiscount() > 0) {
			$icon = 'ribbon-sale';
            $icon_text = '-'.sprintf('%0.2f', ($product->getPrice()-$product->getPriceWithDiscount()) / ($product->getPrice() / 100)).'%';
		}

		$category = self::getProductFirstCategory($product);

		if (!$category) {
			return;
		}

		$link = $category->getUid() . '/' . $product->getUid() .'.html';

		ob_start(); ?>

		<div class="product" data-product-id="<?= $product->getId() ?>">
			<div class="entry-media">
				<img data-src="<?= $product->getMainImage() ?>&resizefit=450x600" alt="<?= $product->getTitle() ?>" class="lazyOwl thumb">
				<div class="hover">
					<a href="<?= $link ?>" class="entry-url"></a>
					<ul class="icons unstyled">
						<?php if ($icon): ?>
							<li>
								<div class="information-ribbon simple <?= $icon ?>"><?= $icon_text ?></div>
							</li>
						<?php endif  ?>
						<li>
							<a href="<?= $product->getMainImage() ?>&resizefit=450x600" class="information-ribbon look-ribbon" data-toggle="lightbox"></a>
						</li>
						<li>
							<a href="#" class="information-ribbon shop-ribbon add-to-cart"></a>
						</li>
					</ul>
					<div class="rate-bar">
						<input type="range" value="3.5" step="0.5" id="backing<?= $product->getId() . $star_uid?>">
						<div class="rateit" data-rateit-backingfld="#backing<?= $product->getId() . $star_uid ?>" data-rateit-starwidth="12" data-rateit-starheight="12" data-rateit-resetable="false"  data-rateit-ispreset="true" data-rateit-min="0" data-rateit-max="5"></div>
					</div>
				</div>
			</div>
			<div class="entry-main">
				<h5 class="entry-title">
					<a href="<?= $link ?>"><?= $product->getTitle() ?></a>
				</h5>
				<div class="entry-price">

                    <?php if ($product->getPriceWithDiscount() && $product->getPriceWithDiscount() > 0) : ?>
                        <s class="entry-discount">&#8381;&nbsp;<?= $product->getPrice() ?> (<?= ModuleCatalogue::getUnitNameByKey($product->getUnit()) ?>)</s>
                        <strong class="accent-color price">&#8381;&nbsp;<?= $product->getPriceWithDiscount(); ?></strong>
                    <?php else: ?>
                        <strong class="accent-color price">&#8381;&nbsp;<?= $product->getPrice() ?></strong>
                    <?php endif; ?>

				</div>
                <div class="entry-links clearfix">
                    <?php if(USER_LOGGED_ID): ?>

                        <?php $wish_data = ModuleWishlist::getWish($product, USER_LOGGED_ID); ?>
                        <span>
                            <a href="#" data-product="product-<?= $product->getId(); ?>" class="pull-center m-r add_wishlist <?php if($wish_data): ?>hide<?php endif; ?>">+ Add to Wishlist</a>
                            <a href="#" data-product="product-<?= $product->getId(); ?>" class="pull-center m-r delete_wishlist <?php if(!$wish_data): ?>hide<?php endif; ?>">+ Remove from Wishlist</a>
                        </span>

                    <?php endif; ?>
                    <?php /*
                                <a href="#" class="pull-right">+ Add to Compare</a>
                                */
                    ?>
                </div>
			</div>
		</div>
		<?php return ob_get_clean();
	}

	public static function getUnitsPairs()
	{
		return self::$measure_units;
	}

	public static function getUnitNameByKey($key)
	{
		return isset(self::$measure_units[$key]) ? self::$measure_units[$key] : $key;
	}

	public static function getProductPairs()
	{
		$products = new CatalogueProductRepository();
		$products->addSimpleSelectFields(['id', 'title']);

		$products->setWhereActive(true);

		return $products->getPairs('title');
	}

	public static function getColorPairs()
	{
		return self::$color_pairs;
	}

	public static function getColorPairData()
	{
		return self::$color_pairs_data;
	}

	private static function getProductFirstCategory(CatalogueProduct $product)
	{
		$category = new CatalogueCategoryRelationEntityRepository();
		$category->setWhereProductId($product->getId());
		$category->setLimit(1);

		$cat_id = $category->getFirstObjectFromCollection();
		if (!$cat_id) {
			return NULL;
		}

		$category = new CatalogueCategory($cat_id->getCategoryId());

		return $category;
	}
}