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
		return '1.3.2';
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

	public function getDescription()
	{
	    return 'Clear the LS Cache on save in Craft CMS.';
	}

	public function getDocumentationUrl()
	{
	    return 'https://github.com/thoughtfulweb/craft-litespeedcache';
	}

	public function getReleaseFeedUrl()
	{
	    return 'https://raw.githubusercontent.com/thoughtfulweb/craft-litespeedcache/master/releases.json';
	}

	public function getSettingsHtml()
	{
	   return craft()->templates->render('litespeedcache/settings', array(
	       'settings' => $this->getSettings(),
	   ));
	}

	protected function defineSettings()
	{
	    return array(
	        'lsPerUrl' => array(AttributeType::String, 'default' => 0),
	        'lsCacheLoc' => array(AttributeType::String),
	        'elementIds' => AttributeType::Mixed
	    );
	}


	private $elementIds;

	public function init()
	{
		/**
		 * onBeforeSaveElement, grab the paths we need to clear from Craft and add them to the lsclearance table
		 */
		craft()->on('elements.onBeforeSaveElement', function(Event $event)
		{

			craft()->liteSpeedCache->getPaths($event->params['element']);

		});

		/**
		 * If we have Craft Commerce installed, purge the entire cache, as it doesn't run through the standard onBeforeSaveEntry
		 */
		craft()->on('commerce_products.onSaveProduct', function(Event $event)
		{
			craft()->liteSpeedCache->destroyLiteSpeedCache($this->getSettings()->lsCacheLoc);
		});

		craft()->on('entries.onSaveEntry', function(Event $event)
		{
			// If we are clearing per URL
			if (!$this->getSettings()->lsPerUrl) {

				craft()->liteSpeedCache->destroyLiteSpeedCache($this->getSettings()->lsCacheLoc);

			}
		});
	}


}
