<?php
namespace Craft;

// getTotalSteps nicked from supercool's cacheMonster plugin [https://github.com/supercool/Cache-Monster]

class LiteSpeedCache_BuildTask extends BaseTask
{

    // Properties
    // =========================================================================

    /**
     * @var
     */
    private $_paths;


    // Public Methods
    // =========================================================================


    /**
     * @inheritDoc ITask::getDescription()
     *
     * @return string
     */
    public function getDescription()
    {
        return Craft::t('Building a URL list...');
    }

    /**
     * @inheritDoc ITask::getTotalSteps()
     *
     * @return int
     */
    public function getTotalSteps()
    {
        // Get the actual paths out of the settings
        $paths = $this->getSettings()->paths;

        // Make our internal paths array
        $this->_paths = array();

        // Split the $paths array into chunks of 20 - each step
        // will be a batch of 20 requests
        $this->_paths = array_chunk($paths, 20);

        // Count our final chunked array
        return count($this->_paths);
    }

    /**
     * @inheritDoc ITask::runStep()
     *
     * @param int $step
     *
     * @return bool
     */
    public function runStep($step)
    {

        // Loop the paths in this step
        $result = craft()->db->createCommand()
                  ->selectDistinct('path')
                  ->from('lsclearance')
                  ->queryColumn();

        $urls = [];
        $cleanPaths = [];

        foreach ($this->_paths[$step] as $path) {
            // If one of the records has been tagged as global, delete the lot
            if (strpos($path['cacheKey'], 'global%%') !== false) {

                craft()->liteSpeedCache->destroyLiteSpeedCache($this->getSettings()->lsCacheLoc);

                return true;
            }

            if (is_null($path['locale'])) {
                $path['locale'] = craft()->i18n->getPrimarySiteLocale();
            }

            // Otherwise get the URL from the cacheKey
            // (which needs the key to be craft.request.path)
            $newPath = explode('%%', $path['cacheKey']);

            // Add the locale to the URL if we need to
            if ($path['locale'] != craft()->i18n->getPrimarySiteLocale()) {
                $newPath = UrlHelper::getSiteUrl($newPath[0], null, null, $path['locale']);
            } else {
                $newPath = UrlHelper::getSiteUrl($newPath[0], null, null, $path['locale']);
            }



            if (!in_array($newPath, $result)) {
                $cleanPaths[] = $newPath;
                $urls[] = array($newPath, $path['locale']);
            }

            LiteSpeedCachePlugin::log('Queue URL: ' . $newPath);
        }

        $result = craft()->db->createCommand()->insertAll('lsclearance', array('path', 'locale'), $urls);

        $cleanPaths = array_unique($cleanPaths);

        craft()->liteSpeedCache->makeTask('LiteSpeedCache_Purge', $cleanPaths);

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
    * @inheritDoc BaseSavableComponentType::defineSettings()
    *
    * @return array
    */
    protected function defineSettings()
    {
        return array(
            'paths'  => AttributeType::Mixed
        );
    }

}