<?php
namespace Craft;

class LiteSpeedCachePlugin extends BasePlugin
{

	// Properties
	// =========================================================================

	/**
	 * @var
	 */
	private $_settings;

	// Public Methods
	// =========================================================================

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
			$dir = '../.lscache';
			craft()->liteSpeedCache->destroyLiteSpeedCache($dir);
		});

	}

}
