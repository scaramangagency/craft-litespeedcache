<?php
namespace Craft;

class LiteSpeedCacheController extends BaseController
{

	public function actionClearLiteSpeedCache()
  {
		$dir = '../.lscache';

		craft()->liteSpeedCache->destroyLiteSpeedCache($dir);
		craft()->userSession->setNotice(Craft::t('LiteSpeed cache cleared.'));
		return $this->redirectToPostedUrl();
	}

}
