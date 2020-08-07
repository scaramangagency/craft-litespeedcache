<?php
/**
 * LiteSpeed Cache plugin for Craft CMS 3.x
 *
 * Clear the LiteSpeed cache on page save.
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2018 Scaramanga Agency
 */

namespace thoughtfulweb\litespeedcache\models;

use thoughtfulweb\litespeedcache\LitespeedCache;

use Craft;
use craft\base\Model;

/**
 * @author    Scaramanga Agency
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
    public $lsCacheLoc;
    public $lsPerUrl;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct(array $config = []) {
        $this->lsCacheLoc = null;
        $this->lsPerUrl   = false;

        parent::__construct($config);
    }
}
