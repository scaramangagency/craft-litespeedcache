# CraftCMS LSCache Purge for Craft CMS 3.x

PURGE the LiteSpeed Cache on saving entries.

## Installation

To install the plugin, search for **LiteSpeed Cache** on the Plugin store, or install manually with the following instructions:

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to require the plugin:

        composer require thoughtfulweb/lite-speed-cache

3. In the Control Panel, go to *Settings → Plugins* and click the “Install” button for **LiteSpeed Cache**.

## Use

Choose whether or not to clear caches by URL, and set the directory where your LSCache folder is located in the plugin settings. If you do not select the per-URL option, the entire LSCache folder will be destroyed on every page save.

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Notes

Due to CloudFlare being a reverse proxy, ou cannot use CloudFlare and still use per-URL purging. Either do not route through CloudFlare, or just enable the global purge.

This plugin will not be triggered at present from CraftCommerce, as the hook for page saving is different than the standard AFTER_SAVE_ELEMENT, and the class docs aren't available for Commerce 2 yet.