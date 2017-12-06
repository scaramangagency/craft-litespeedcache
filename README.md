# CraftCMS LSCache Purge

Destroy LSCache on save, or force destroy.

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

For any parameters that are **truly** global parameters, like navigation, prefix the key with `global%%`. This will trigger a global purge instead of purging per URL, as every page will need to be refreshed for navigation changes to take effect.
~~~~
{% cache globally using key 'global%%navigation' %}
~~~~

On your _default_ document, you must add a header to tell LSCache to **not** cache the entry if it cannot find a cache record in craft_templatecaches table. If there is no cache record associated with the entry, pressing save will not fire a purge request to the URL.
~~~~
{% if not craft.liteSpeedCache.checkTemplateCache(craft.request.path)|length %}
    {% header "X-LiteSpeed-Cache-Control: no-cache" %}
{% endif %}
~~~~

## Notes

I'd recommend setting `globally` to reduce the amount of cache records that you get, otherwise you'll end up with hunderds of cache records if there are URL paramaters defined for a page.

This plugin does run the LSCache clearances as a batched task, so hopefully it shouldn't be too intensive on the server. If you're noticing massive latency issues, it might be worth knocking it back to global purging.