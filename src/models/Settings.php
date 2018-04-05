<?php
/**
 * LiteSpeed Cache plugin for Craft CMS 3.x
 *
 * Clear the LiteSpeed cache on page save.
 *
 * @link      https://thoughtfulweb.com
 * @copyright Copyright (c) 2018 Thoughtful Web
 */

namespace thoughtfulweb\litespeedcache\models;

use thoughtfulweb\litespeedcache\LitespeedCache;

use Craft;
use craft\base\Model;

/**
 * @author    Thoughtful Web
 * @package   LitespeedCache
 * @since     0.0.1
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================
    /**
     * @var string
     */
    public $lsCacheLoc = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['lsCacheLoc', 'string'],
            ['lsCacheLoc', 'default', 'value' => ''],
        ];
    }
}
