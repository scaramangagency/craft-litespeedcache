<?php
namespace Craft;

class LiteSpeedCacheService extends BaseApplicationComponent
{

	private $_elementIds;
	private $_elementType;
	private $_batch;
	private $_batchRows;
	private $_noMoreRows;
	private $_cacheIdsToBeDeleted;
	private $_totalCriteriaRowsToBeDeleted;

	/**
	 * Gets the caches that are about to be deleted by the DeleteStaleTemplateCachesTask
	 * and returns the paths for them.
	 *
	 * I basically nicked the logic from supercools cacheMonster plugin [https://github.com/supercool/Cache-Monster]
	 * Who basically nicked the logic from the DeleteStaleTemplateCachesTask
	 * to work out which caches we are dealing with.
	 *
	 * @method getPaths
	 * @param  int           $element the element we want to purge
	 * @return array                  an array of paths to purge
	 */
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
			$paths = craft()->db->createCommand()
				->selectDistinct('cacheKey, path, locale')
				->from('templatecaches')
				->where(array('in', 'id', $this->_cacheIdsToBeDeleted))
				->queryAll();

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

	/**
	 * Returns a DbCommand object for selecting criteria that could be dropped by this task.
	 *
	 * @return DbCommand
	 */
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

	/**
	 * Recursively delete the entire .lscache directory at the root level
	 */
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

	/**
	 * Loop through cache records we need to clear, and fire a PURGE request for each one
	 */
	public function clearLitespeedQueue()
	{

	  	$results = craft()->db->createCommand()
					->selectDistinct('path, id')
					->from('lsclearance')
					->queryAll();

	  	foreach ($results as $result) {

	  		$ch = curl_init();

	  		// Set query data here with the URL
	  		curl_setopt($ch, CURLOPT_URL, $result['path']);
	  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  		curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	  		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PURGE");
	  		curl_exec($ch);
	  		curl_close($ch);

		  	$remove = craft()->db->createCommand()->delete('lsclearance', 'id=:id', array(':id'=>$result['id']));
		}
	}

}
