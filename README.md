# CraftCMS LiteSpeed Cache
~~~~
**Doesnt work yet, PURGE command is not running correctly**
~~~~

Destroy LiteSpeed cache on save, or force destroy.

**This will clear the cache based on URL's provided by Craft's native `deleteStaleTemplateCaches` method, as opposed to just a blanket destroy.**

It can be pretty processor intensive, so use with caution.

## Usage

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

## Notes
I'd recommend setting `globally` to reduce the amount of cache records that you get, otherwise you'll end up with duplicate cache records if there are URL paramaters defined for a page.