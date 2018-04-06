<?php
/**
 * LiteSpeed Cache plugin for Craft CMS 3.x
 *
 * Clear the LiteSpeed cache on page save.
 *
 * @link      https://thoughtfulweb.com
 * @copyright Copyright (c) 2018 Thoughtful Web
 */

namespace thoughtfulweb\litespeedcache\controllers;

use Craft;
use craft\web\Controller;

use thoughtfulweb\litespeedcache\LitespeedCache;
use thoughtfulweb\litespeedcache\services\ClearCache;
use thoughtfulweb\litespeedcache\models\Settings;


/**
 * @author    Thoughtful Web
 * @package   LitespeedCache
 * @since     0.0.1
 */
class LitespeedCacheController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = true;

    // Public Methods
    // =========================================================================


    /**
     * Constructor
     */
    public function __construct()
    {
      $this->module = Craft::$app;
    }

    public function actionForceClear()
    {
      $settings = LitespeedCache::$plugin->getSettings();

      LitespeedCache::$plugin->clearCache->destroyLiteSpeedCache($settings['lsCacheLoc']);
      Craft::$app->getSession()->setNotice(Craft::t('app', 'LiteSpeed cache cleared.'));
      return $this->redirectToPostedUrl();
    }
}