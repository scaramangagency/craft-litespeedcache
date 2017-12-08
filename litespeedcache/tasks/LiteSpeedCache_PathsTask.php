<?php
namespace Craft;

// getTotalSteps nicked from supercool's cacheMonster plugin [https://github.com/supercool/Cache-Monster]

class LiteSpeedCache_PathsTask extends BaseTask
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
    return Craft::t('Retrieving cached elements');
  }

  /**
   * @inheritDoc ITask::getTotalSteps()
   *
   * @return int
   */
  public function getTotalSteps()
  {

    return 1;
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

    $paths = craft()->liteSpeedCache->getPaths($this->getSettings()->element);

    foreach ($paths as $path) {
      // If one of the records has been tagged as global, delete the lot
      if (strpos($path['cacheKey'], 'global%%') !== false) {
        $dir = '../.lscache';

        craft()->liteSpeedCache->destroyLiteSpeedCache($dir);
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
        $newPath = UrlHelper::getSiteUrl($newPath[0]);
      }

      LiteSpeedCachePlugin::log($newPath);

      if (!in_array($newPath, $result)) {
        $cleanPaths[] = $newPath;
        $urls[] = array($newPath, $path['locale']);
      }
    }



    $result = craft()->db->createCommand()->insertAll('lsclearance', array('path', 'locale'), $urls);

    foreach ($cleanPaths as $key=>$path)
    {
      $ch[$key] = curl_init();

      // Set query data here with the URL
      curl_setopt($ch[$key], CURLOPT_URL, $path);
      curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch[$key], CURLOPT_TIMEOUT, 3);

      curl_setopt($ch[$key], CURLOPT_CUSTOMREQUEST, "PURGE");
      // $remove = craft()->db->createCommand()->delete('lsclearance', 'path=:path', array(':path'=>$path));

      curl_multi_add_handle($mh, $ch[$key]);
    }

    do {
      curl_multi_exec($mh, $running);
      curl_multi_select($mh);
    } while ($running > 0);

    foreach(array_keys($ch) as $key){
      curl_multi_remove_handle($mh, $ch[$key]);
    }

    curl_multi_close($mh);


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
      'element'  => AttributeType::Mixed
    );
  }

  // Private Methods
  // =========================================================================

}