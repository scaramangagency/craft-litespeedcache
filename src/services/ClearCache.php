<?php
/**
 * LiteSpeed Cache plugin for Craft CMS 3.x
 *
 * Clear the LiteSpeed cache on page save.
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2018 Scaramanga Agency
 */

namespace thoughtfulweb\litespeedcache\services;

use thoughtfulweb\litespeedcache\LitespeedCache;

use Craft;
use craft\base\Component;

/**
 * @author    Scaramanga Agency
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
    public function destroyLiteSpeedCache($dir, $odir = NULL) {
        if ($odir == NULL) {
            $odir = $dir;
        }
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        LitespeedCache::$plugin->clearCache->destroyLiteSpeedCache($dir."/".$object,$odir);
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

    public function purgeLiteSpeedCache($slugs) {
        $mh = curl_multi_init();
        $ch = array();

        foreach ($slugs as $key => $path) {
            $ch[$key] = curl_init();
            // Set query data here with the URL
            curl_setopt($ch[$key], CURLOPT_URL, $path);
            curl_setopt($ch[$key], CURLOPT_VERBOSE, true);
            curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch[$key], CURLOPT_TIMEOUT, 3);
            curl_setopt($ch[$key], CURLOPT_CUSTOMREQUEST, "PURGE");
            LitespeedCache::log('Queue URL: '.$path);
            curl_multi_add_handle($mh, $ch[$key]);
        }

        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        foreach ($ch as $key => $result) {
            $resp = curl_getinfo($result, CURLINFO_HTTP_CODE);
            $url = curl_getinfo($result, CURLINFO_EFFECTIVE_URL);
            LitespeedCache::log('Attempted to purge URL '.$url.' and got a status code of '. $resp);
            curl_multi_remove_handle($mh, $ch[$key]);
        }

        curl_multi_close($mh);

        return true;
    }
}
