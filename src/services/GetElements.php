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
use craft\db\Table;
use craft\db\Query;
use craft\base\Component;
use craft\elements\db\ElementQuery;

/**
 * @author    Scaramanga Agency
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


