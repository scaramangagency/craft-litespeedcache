<?php
namespace Craft;

class LiteSpeedCacheController extends BaseController
{

  /**
   * Delete the entire LS Cache when triggered manually from the CP
   *
   * @return redirect to form defined URL
   */
	public function actionClearLiteSpeedCache()
	{
		$dir = '../.lscache';

		craft()->liteSpeedCache->destroyLiteSpeedCache($dir);
		craft()->userSession->setNotice(Craft::t('LiteSpeed cache cleared.'));
		return $this->redirectToPostedUrl();
	}

}
