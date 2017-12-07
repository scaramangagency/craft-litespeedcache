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
	        'lsPerUrl' => array(AttributeType::String, 'default' => 0)
	    );
	}

	public function getSettingsHtml()
	{
	   return craft()->templates->render('litespeedcache/settings', array(
	       'settings' => $this->getSettings()
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
			if ($this->getSettings()->lsPerUrl) {
				$element = $event->params['element'];
				$paths = craft()->liteSpeedCache->getPaths($element);

				$result = craft()->db->createCommand()
									->selectDistinct('path')
									->from('lsclearance')
									->queryColumn();

				$urls = [];

				foreach ((array) $paths as $path) {
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

					if (!in_array($newPath, $result)) {
						$urls[] = array($newPath, $path['locale']);
					}
				}

				$result = craft()->db->createCommand()->insertAll('lsclearance', array('path', 'locale'), $urls);
			}
		});

		/**
		 * Run the PURGE commands as a batched task
		 */
		craft()->on('elements.onSaveElement', function(Event $event)
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
