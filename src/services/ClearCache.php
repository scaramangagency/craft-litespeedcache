<?php
/**
 * LiteSpeed Cache plugin for Craft CMS 3.x
 *
 * Clear the LiteSpeed cache on page save.
 *
 * @link      https://thoughtfulweb.com
 * @copyright Copyright (c) 2018 Thoughtful Web
 */

namespace thoughtfulweb\litespeedcache\services;

use thoughtfulweb\litespeedcache\LitespeedCache;

use Craft;
use craft\base\Component;

/**
 * @author    Thoughtful Web
 * @package   LitespeedCache
 * @since     0.0.1
 */
class ClearCache extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function destroyLiteSpeedCache($dir, $odir = NULL)
    {
        if ($odir == NULL) {
            $odir = $dir;
        }
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        LitespeedCache::$plugin->clearcache->destroyLiteSpeedCache($dir."/".$object,$odir);
                    else
                        unlink($dir."/".$object);
                    }
                }
            if ($odir != $dir) {
                rmdir($dir);
            }
        }

        return true;
    }
}
