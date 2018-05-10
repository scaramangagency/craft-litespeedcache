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
use craft\db\Query;
use craft\elements\db\ElementQuery;

/**
 * @author    Thoughtful Web
 * @package   LitespeedCache
 * @since     1.1.0
 */
class getElements extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function returnTemplateCacheElements($ids) {
        $templateCacheElements = [];

        foreach ($ids as $row) {
            $getElement = (new Query())
                    ->select(['elementId'])
                    ->distinct(true)
                    ->from(['craft_templatecacheelements'])
                    ->where(['cacheId' => $row])
                    ->column();


            array_push($templateCacheElements, $getElement);
        }

        return $templateCacheElements;
    }
}


