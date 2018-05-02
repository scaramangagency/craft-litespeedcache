<?php
/**
 * LiteSpeed Cache plugin for Craft CMS 3.x
 *
 * Clear the LiteSpeed cache on page save.
 *
 * @link      https://thoughtfulweb.com
 * @copyright Copyright (c) 2018 Thoughtful Web
 */

namespace thoughtfulweb\litespeedcache;


use Craft;
use craft\base\Element;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\services\Elements;
use craft\web\Controller;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\events\DeleteTemplateCachesEvent;
use craft\services\TemplateCaches;

use thoughtfulweb\litespeedcache\models\Settings;
use thoughtfulweb\litespeedcache\services\ClearCache;
use thoughtfulweb\litespeedcache\services\GetElements;
use thoughtfulweb\litespeedcache\queue\jobs\RunLitespeedPurge;

use yii\base\Event;

/**
 * Class LitespeedCache
 *
 * @author    Thoughtful Web
 * @package   LitespeedCache
 * @since     0.0.1
 *
 * @property  LitespeedCacheServiceService $litespeedCacheService
 */
class LitespeedCache extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var LitespeedCache
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $settings = LitespeedCache::$plugin->getSettings();

        $this->setComponents([
            'clearCache' => ClearCache::class,
            'getElements' => GetElements::class
        ]);

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['lite-speed-cache/litespeed-cache/force-clear'] = 'lite-speed-cache/litespeed-cache/forceClear';
            }
        );


        if ($settings['lsPerUrl']) {
            Event::on(
                TemplateCaches::class,
                TemplateCaches::EVENT_BEFORE_DELETE_CACHES,
                function (DeleteTemplateCachesEvent $event) {

                    $templateCacheElements = LitespeedCache::$plugin->getElements->returnTemplateCacheElements($event->cacheIds);

                    if (!empty($templateCacheElements)) {
                        Craft::$app->getQueue()->push(new RunLitespeedPurge([
                            'elementId' => $templateCacheElements,
                        ]));
                    }
                }
            );
        } else {
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_SAVE_ELEMENT,
                function (ElementEvent $event) {
                    $settings = LitespeedCache::$plugin->getSettings();
                    LitespeedCache::$plugin->clearCache->destroyLiteSpeedCache($settings['lsCacheLoc']);
                }
            );
        }


        Craft::info(
            Craft::t(
                'lite-speed-cache',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'lite-speed-cache/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
