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
use craft\db\Table;
use craft\db\Query;
use craft\base\Component;
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
                    ->from(Table::TEMPLATECACHEELEMENTS)
                    ->where(['cacheId' => $row])
                    ->column();


            array_push($templateCacheElements, $getElement);
        }

        return $templateCacheElements;
    }
}


