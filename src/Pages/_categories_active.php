<?

use TMCms\Cache\Cacher;
use TMCms\DB\SQL;

defined('INC') or exit;

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) return;
$id = & $_GET['id'];

SQL::active($id, module_catalogue::$tables['categories']);

Cacher::getInstance()->getDefaultCacher()->delete(module_catalogue::$cache_key_categories);

back();