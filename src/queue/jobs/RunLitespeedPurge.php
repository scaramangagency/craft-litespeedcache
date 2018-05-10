<?php
/**
 * LiteSpeed Cache plugin for Craft CMS 3.x
 *
 * Clear the LiteSpeed cache on page save.
 *
 * @link      https://thoughtfulweb.com
 * @copyright Copyright (c) 2018 Thoughtful Web
 */

namespace thoughtfulweb\litespeedcache\queue\jobs;

use Craft;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\queue\BaseJob;
use craft\helpers\UrlHelper;

use thoughtfulweb\litespeedcache\LitespeedCache;
use thoughtfulweb\litespeedcache\services\ClearCache;

/**
 * RunLitespeedPurge job
 *
 * @author    Thoughtful Web
 * @since 1.1.0
 */
class RunLitespeedPurge extends BaseJob
{
    // Properties
    // =========================================================================

    /**
     * @var int|int[]|null The element ID(s) whose caches need to be cleared
     */
    public $elementId;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function execute($queue) {
        $slugsToPurge = [];

        $currentRow = 0;
        $totalRows = sizeof($this->elementId);

        foreach ($this->elementId as $row) {
            $this->setProgress($queue, $currentRow++ / $totalRows);

            $getElementType = (new Query())
                    ->select(['fieldLayoutId','type','id'])
                    ->distinct(true)
                    ->from(['craft_elements'])
                    ->where(['id' => $row])
                    ->all();

            foreach ($getElementType as $row) {
                if ($row['type'] == 'craft\elements\Entry') {
                    $getEntrySlug = Craft::$app->entries->getEntryById($row['id']);

                    if (sizeof($getEntrySlug)) {
                        if (!is_null($getEntrySlug->uri)) {
                            if (!in_array($getEntrySlug->url, $slugsToPurge)) {
                                array_push($slugsToPurge, UrlHelper::siteUrl($getEntrySlug->uri));
                            }
                        }
                    }
                }
            }
        }

        // Actually delete the caches now
        if (!empty($slugsToPurge)) {
            LitespeedCache::$plugin->clearCache->purgeLiteSpeedCache($slugsToPurge);
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string {
        return Craft::t('app', 'Purging Litespeed Cache');
    }
}
