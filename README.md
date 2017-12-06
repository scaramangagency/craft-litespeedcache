# CraftCMS LiteSpeed Cache

Destroy LiteSpeed cache on save, or force destroy.

## Installation

1. Move the litespeedcache folder into your craft/plugins directory
2. Install the plugin in the Control Panel
3. Turn on *Clear caches per URL?* if you need it on the settings page.

## Markup

Cache records need to have the path set as the key.
~~~~
{% cache globally using key craft.request.path %}
{% cache globally using key craft.request.path ~ '/p' ~ craft.request.getPageNum %}
~~~~

If you need to add extra data to the key (e.g if you're using a different `limit` parameter for different browser sizes) then you must seperate this information from the path using `%%`
~~~~
{% set cacheKeyType = craft.request.isMobileBrowser() ? 'mobile' : 'desktop' %}
{% cache globally using key craft.request.path ~ '/p' ~ craft.request.getPageNum ~ '%%' ~ cacheKeyType until cacheUntil %}
~~~~

For any parameters that are **truly** global parameters, like navigation, prefix the key with `global%%`. This will trigger a blanket destroy instead of per-URL, as every page will need to be refreshed for navigation changes to take effect.
~~~~
{% cache globally using key 'global%%navigation' %}
~~~~

## Notes

I'd recommend setting `globally` to reduce the amount of cache records that you get, otherwise you'll end up with hunderds of cache records if there are URL paramaters defined for a page. This plugin can also be pretty processor intensive, so test if you experience huge slowdowns whilst using *Clear caches per URL*

## Support

If you're having issues with the *Clear caches per URL*, switch it off and default back to the blanket deletion.
