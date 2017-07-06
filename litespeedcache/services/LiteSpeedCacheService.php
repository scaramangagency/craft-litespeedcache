<?php
namespace Craft;

class LiteSpeedCacheService extends BaseApplicationComponent
{

	/*
		getPaths() and _getQuery() are mostly inherited from supercool's cacheMonster plugin
		https://github.com/supercool/Cache-Monster
	*/
	private $_elementIds;
	private $_elementType;
	private $_batch;
	private $_batchRows;
	private $_noMoreRows;
	private $_cacheIdsToBeDeleted;
	private $_totalCriteriaRowsToBeDeleted;

	public function getPaths($element)
	{
		$elementId = $element->id;

		// What type of element(s) are we dealing with?
		$this->_elementType = craft()->elements->getElementTypeById($elementId);
		if (!$this->_elementType)
		{
			return 0;
		}

		if (is_array($elementId))
		{
			$this->_elementIds = $elementId;
		}
		else
		{
			$this->_elementIds = array($elementId);
		}

		// Figure out how many rows we're dealing with
		$totalRows = $this->_getQuery()->count('id');
		$this->_batch = 0;
		$this->_noMoreRows = false;
		$this->_cacheIdsToBeDeleted = array();
		$this->_totalCriteriaRowsToBeDeleted = 0;

		// Loop each of the relavent rows in the `templatecachecriteria` table
		for ($i=0; $i < $totalRows; $i++) {

			// Do we need to grab a fresh batch?
			if (empty($this->_batchRows))
			{
				if (!$this->_noMoreRows)
				{
					$this->_batch++;
					$this->_batchRows = $this->_getQuery()
						->order('id')
						->offset(100*($this->_batch-1) - $this->_totalCriteriaRowsToBeDeleted)
						->limit(100)
						->queryAll();
					// Still no more rows?
					if (!$this->_batchRows)
					{
						$this->_noMoreRows = true;
					}
				}
				if ($this->_noMoreRows)
				{
					return;
				}
			}

			$row = array_shift($this->_batchRows);
			// Have we already deleted this cache?
			if (in_array($row['cacheId'], $this->_cacheIdsToBeDeleted))
			{
				$this->_totalCriteriaRowsToBeDeleted++;
			}
			else
			{
				// Create an ElementCriteriaModel that resembles the one that led to this query
				$params = JsonHelper::decode($row['criteria']);
				$criteria = craft()->elements->getCriteria($row['type'], $params);
				// Chance overcorrecting a little for the sake of templates with pending elements,
				// whose caches should be recreated (see http://craftcms.stackexchange.com/a/2611/9)
				$criteria->status = null;
				// See if any of the updated elements would get fetched by this query
				if (array_intersect($criteria->ids(), $this->_elementIds))
				{
					// Delete this cache
					$this->_cacheIdsToBeDeleted[] = $row['cacheId'];
					$this->_totalCriteriaRowsToBeDeleted++;
				}
			}

		}

		// Get the cacheIds that are directly applicable to this element
		$query = craft()->db->createCommand()
			->selectDistinct('cacheId')
			->from('templatecacheelements')
			->where('elementId = :elementId', array(':elementId' => $elementId))
			->queryColumn();

		$this->_cacheIdsToBeDeleted = array_merge($this->_cacheIdsToBeDeleted, $query);

		if ($this->_cacheIdsToBeDeleted)
		{

			// Get the paths that those caches related to
			$query = craft()->db->createCommand()
				->selectDistinct('path, locale')
				->from('templatecaches')
				->where(array('in', 'id', $this->_cacheIdsToBeDeleted));

			$paths = $query->queryAll();

			// Return an array of them
			if ($paths) {

				if (!is_array($paths)) {
					$paths = array($paths);
				}

				return $paths;
			} else {
				return false;
			}

		} else {
			return false;
		}
	}

	private function _getQuery()
	{
		$query = craft()->db->createCommand()
			->from('templatecachecriteria');
		if (is_array($this->_elementType))
		{
			$query->where(array('in', 'type', $this->_elementType));
		}
		else
		{
			$query->where('type = :type', array(':type' => $this->_elementType));
		}
		return $query;
	}

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

	public function clearLitespeedQueue()
	{

	  $results = craft()->db->createCommand()
	            ->selectDistinct('path, id')
	            ->from('lsclearance')
	            ->queryAll();

	  foreach ($results as $result) {
	    $fp = fsockopen('127.0.0.1', '80', $errno, $errstr, 2);
	    if (!$fp) {
	        echo "$errstr ($errno)\n";
	    } else {
	      $out = "PURGE " . $result['path'] . " HTTP/1.0\r\n"
	           . "Host: https://www.host.com\r\n"
	           . "Connection: Close\r\n\r\n";
	      fwrite($fp, $out);
	      while (!feof($fp)) {
	          echo fgets($fp, 128);
	      }
	      fclose($fp);

	      $remove = craft()->db->createCommand()->delete('lsclearance', 'id=:id', array(':id'=>$result['id']));
	    }
	  }
	}

}
