<?
use TMCms\DB\SQL;

defined('INC') or exit;

if (!isset($_GET['product_id']) || !ctype_digit((string)$_GET['product_id'])) return;
$product_id = & $_GET['product_id'];

$_POST = sql_prepare($_POST);

q('INSERT INTO `'. module_catalogue::$tables['images'] .'` (
	`product_id`, `image`, `order`
) VALUES (
	"'. (int)$product_id .'", "'. $_POST['image'] .'", "'.  SQL::getNextOrder(module_catalogue::$tables['images'], 'order', 'product_id', (int)$product_id) .'"
)');

go('?p='. P .'&do=images&product_id='. $product_id);