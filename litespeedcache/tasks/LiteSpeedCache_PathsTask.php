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
    return Craft::t('Retrieving cached elements...');
  }

  /**
   * @inheritDoc ITask::getTotalSteps()
   *
   * @return int
   */
  public function getTotalSteps()
  {
    // Get the actual paths out of the settings
    $paths = $this->getSettings()->element;

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

    foreach ($this->_paths[$step] as $path) {

        $ourPaths = craft()->liteSpeedCache->getPaths($path);

        craft()->liteSpeedCache->makeTask('LiteSpeedCache_Build', $ourPaths);

    }

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