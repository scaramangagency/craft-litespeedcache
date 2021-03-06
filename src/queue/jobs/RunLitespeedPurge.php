<?php
/**
 * LiteSpeed Cache plugin for Craft CMS 3.x
 *
 * Clear the LiteSpeed cache on page save.
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2018 Scaramanga Agency
 */

namespace thoughtfulweb\litespeedcache\queue\jobs;

use Craft;
use craft\db\Table;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\queue\BaseJob;
use craft\helpers\UrlHelper;

use thoughtfulweb\litespeedcache\LitespeedCache;
use thoughtfulweb\litespeedcache\services\ClearCache;

/**
 * RunLitespeedPurge job
 *
 * @author    Scaramanga Agency
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
                    ->from([Table::ELEMENTS])
                    ->where(['id' => $row])
                    ->all();

            foreach ($getElementType as $row) {
                if ($row['type'] == 'craft\elements\Entry') {
                    $getEntrySlug = Craft::$app->entries->getEntryById($row['id']);

                    if (!is_null($getEntrySlug)) {
                        if (!is_null($getEntrySlug->uri)) {
                            if (!in_array($getEntrySlug->url, $slugsToPurge)) {
                                array_push($slugsToPurge, $getEntrySlug->url);
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
