# CraftCMS LiteSpeed Cache

Destroy LiteSpeed cache on save, or force destroy.

**This will clear the cache based on URL's provided by Craft's native `deleteStaleTemplateCaches` method, as opposed to just a blanket destroy.**

It's labour intensive, and requires you don't use `globally` (as we need the path to issue a PURGE request), so the cache table can really grow in size.

I never added a `LiteSpeedCacheRecord.php`, because I didn't get this working properly before settling on the blanket destroy option, so you'll need to add a new table to your database

~~~~
  CREATE TABLE `craft_lsclearance` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `path` text,
      `cleared` int(11) DEFAULT '0',
      `dateCreated` datetime DEFAULT NULL,
      `dateUpdated` datetime DEFAULT NULL,
      `uid` char(36) DEFAULT '',
      PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=539 DEFAULT CHARSET=latin1;
~~~~

