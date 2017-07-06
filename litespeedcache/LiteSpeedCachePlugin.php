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
		return '1.0.0';
	}

	public function getDeveloper()
	{
		return 'Thoughtful';
	}

	public function getDeveloperUrl()
	{
		return 'http://thoughtfulweb.com';
	}

	public function hasCpSection()
	{
	    return true;
	}

	public function init()
	{
		craft()->on('elements.onBeforeSaveElement', function(Event $event)
		{
			$element = $event->params['element'];
			$paths = craft()->liteSpeedCache->getPaths($element);

			$result = craft()->db->createCommand()
								->selectDistinct('path')
								->from('lsclearance')
								->queryColumn();

			$urls = [];

			foreach ((array) $paths as $path) {
				$newPath = preg_replace('/site:/', '', $path['path'], 1);

				if ($path['locale'] != 'en') {
					$newPath = $path['locale'] . '/' . $newPath;
				}

				$newPath = UrlHelper::getSiteUrl($newPath);

				if (!in_array($newPath, $result)) {
					$urls[] = array($newPath);
				}
			}
			$result = craft()->db->createCommand()->insertAll('lsclearance', array('path'), $urls);
		});

	}


	craft()->on('elements.onAfterSaveElement', function(Event $event)
	{
		craft()->liteSpeedCache->clearLitespeedQueue();
	}

}
