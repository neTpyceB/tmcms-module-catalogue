<?

use TMCms\Cache\Cacher;
use TMCms\DB\SQL;

defined('INC') or exit;

if (q_check(module_catalogue::$tables['categories'], '`uid` = "'. $_POST['uid'] .'" AND `pid` = "'. (int)$_POST['pid'] .'"')) error('Category with this UID already exists.');

$specification_id = isset($_POST['specification_id']) && $_POST['specification_id'] ? (int)$_POST['specification_id'] : 0;
if (!$specification_id) {
    $specification_id = q('INSERT INTO `'. module_catalogue::$tables['specifications'] .'` (
            `title`
        ) VALUES (
            "'. sql_prepare($_POST['specification_title']) .'"
        )', false, true);
}


// Create new specification?
if ($_POST['specs']['update']) {

    // Params for specification
    $inserted_ids = [];
    foreach ($_POST['specs']['update'] as $id => $spec) {
        $empty = true;
        foreach ($spec as $key => $value) {
            if ($key == 'type') {
                $param_type = $key;
                unset($spec[$key]);
            } else {
                if ($value) {
                    $empty = false;
                }
            }
            if ($empty || isset($inserted_ids[$id])) continue; // Prevent empty or dupes

            q('INSERT INTO `' . module_catalogue::$tables['specifications_params'] . '` (
                `key`, `order`, `spec_id`, `type`
            ) VALUES (
                "' . Translations::save($spec) . '",
                "' . SQL::getNextOrder(module_catalogue::$tables['specifications_params'], 'order', 'spec_id', $specification_id) . '",
                "' . $specification_id . '",
                "' . sql_prepare($param_type) . '"
            )');

            $inserted_ids[$id] = true;
        }
    }
}

q('INSERT INTO `'. module_catalogue::$tables['categories'] .'` (
	`title`, `order`,
	`pid`, `uid`, `icon_img`,
	`specification_id`
) VALUES (
	"'. Translations::save($_POST['title']) .'", "'. SQL::getNextOrder(module_catalogue::$tables['categories'], 'order', 'pid', (int)$_POST['pid']) .'",
	"'. (int)$_POST['pid'] .'", "'. sql_prepare($_POST['uid']) .'", "'. sql_prepare($_POST['icon_img']) .'",
	"'. $specification_id .'"
)');

Cacher::getInstance()->getDefaultCacher()->delete(module_catalogue::$cache_key_categories);

go('?p='. P .'&do=categories');