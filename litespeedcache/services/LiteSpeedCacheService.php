<?php
namespace Craft;

class LiteSpeedCacheService extends BaseApplicationComponent
{
	public function destroyLiteSpeedCache($dir, $odir = NULL)
	{
	  if ($odir == NULL) {
	   	$odir = $dir;
	  }
	  if (is_dir($dir)) {
	    $objects = scandir($dir);
	    foreach ($objects as $object) {
	      if ($object != "." && $object != "..") {
	        if (is_dir($dir."/".$object))
	          craft()->liteSpeedCache->destroyLiteSpeedCache($dir."/".$object,$odir);
	        else
	          unlink($dir."/".$object);
	      }
	    }
	    if ($odir != $dir) {
	      rmdir($dir);
	    }
	  }

	  return true;
	}
}
