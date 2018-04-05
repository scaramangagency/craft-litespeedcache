<?php
/**
 * LiteSpeed Cache plugin for Craft CMS 3.x
 *
 * Clear the LiteSpeed cache on page save.
 *
 * @link      https://thoughtfulweb.com
 * @copyright Copyright (c) 2018 Thoughtful Web
 */

namespace thoughtfulweb\litespeedcache\assetbundles\LitespeedCache;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Thoughtful Web
 * @package   LitespeedCache
 * @since     0.0.1
 */
class LitespeedCacheAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@thoughtfulweb/litespeedcache/assetbundles/litespeedcache/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/LitespeedCache.js',
        ];

        $this->css = [
            'css/LitespeedCache.css',
        ];

        parent::init();
    }
}
