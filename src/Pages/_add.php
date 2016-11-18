<?
use TMCms\DB\SQL;
use TMCms\Strings\Converter;

defined('INC') or exit;

$_POST = sql_prepare($_POST);

if (q_check(module_catalogue::$tables['products'], '`uid` = "'. $_POST['uid'] .'"')) error('Product with this UID already exists.');

q('INSERT INTO `'. module_catalogue::$tables['products'] .'` (
	`title`, `subtitle`, `description`, `price`, `order`,
	`add_ts`, `category_id`, `uid`, `price_special`, `price_special_note`,
	`manufacturer_id`, `code`, `amount_in_stock`, `spec_id`
) VALUES (
	"'. Translations::save($_POST['title']) .'", "'. Translations::save($_POST['subtitle']) .'", "'. Translations::save($_POST['description']) .'", "'. Converter::number2price($_POST['price']) .'", "'. SQL::getNextOrder(module_catalogue::$tables['products'], 'order', 'category_id', (int)$_POST['category_id']) .'",
	"'. NOW .'", "'. (int)$_POST['category_id'] .'", "'. $_POST['uid'] .'", "'. Converter::number2price($_POST['price_special']) .'", "'. Translations::save($_POST['price_special_note']) .'",
	"'. (int)$_POST['manufacturer_id'] .'", "'. $_POST['code'] .'", "'. (int)$_POST['amount_in_stock'] .'", "'. (int)$_POST['spec_id'] .'"
)');

go('?p='. P);