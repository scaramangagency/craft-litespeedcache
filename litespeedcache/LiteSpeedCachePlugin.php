<?php
namespace Craft;

class LiteSpeedCachePlugin extends BasePlugin
{

	public function getName()
	{
		return Craft::t('Litespeed Cache');
	}

	public function getVersion()
	{
		return '1.2.0';
	}

	public function getDeveloper()
	{
		return 'Thoughtful';
	}

	public function getDeveloperUrl()
	{
		return 'https://www.thoughtfulweb.com';
	}

	public function hasCpSection()
	{
	    return true;
	}

	protected function defineSettings()
	{
	    return array(
	        'lsPerUrl' => array(AttributeType::String, 'default' => 0),
	        'elementIds' => AttributeType::Mixed
	    );
	}

	private $elementIds;

	public function getSettingsHtml()
	{
	   return craft()->templates->render('litespeedcache/settings', array(
	       'settings' => $this->getSettings(),
	   ));
	}

	public function init()
	{
		/**
		 * onBeforeSaveElement, grab the paths we need to clear from Craft and add them to the lsclearance table
		 */
		craft()->on('elements.onBeforeSaveElement', function(Event $event)
		{
			// If we are clearing per URL
			$this->elementIds[] = $event->params['element'];
		});

		/**
		 * Run the PURGE commands as a batched task
		 */
			craft()->on('commerce_products.onSaveProduct', function(Event $event)
			{
				// If we are clearing per URL
				if ($this->getSettings()->lsPerUrl) {

					$paths = craft()->liteSpeedCache->buildPaths('LiteSpeedCache_Paths', $this->elementIds);
					// craft()->liteSpeedCache->makeTask('LiteSpeedCache_Build', $paths);

					// $paths = craft()->db->createCommand()
					//           ->selectDistinct('path, id, locale')
					//           ->from('lsclearance')
					//           ->queryAll();

					// $cleanPaths = [];

					// foreach ($paths as $path)
					// {
					// 	$cleanPaths[] = $path['path'];
					// }

					// craft()->liteSpeedCache->makeTask('LiteSpeedCache_Purge', $cleanPaths);

				} else {
					$dir = '../.lscache';

					craft()->liteSpeedCache->destroyLiteSpeedCache($dir);
				}
			});

		craft()->on('entries.onSaveEntry', function(Event $event)
		{
			// If we are clearing per URL
			if ($this->getSettings()->lsPerUrl) {

				$paths = craft()->db->createCommand()
				          ->selectDistinct('path, id, locale')
				          ->from('lsclearance')
				          ->queryAll();

				$cleanPaths = [];

				foreach ($paths as $path)
				{
					$cleanPaths[] = $path['path'];
				}

				craft()->liteSpeedCache->makeTask('LiteSpeedCache_Purge', $cleanPaths);

			} else {
				$dir = '../.lscache';

				craft()->liteSpeedCache->destroyLiteSpeedCache($dir);
			}
		});
	}


}
