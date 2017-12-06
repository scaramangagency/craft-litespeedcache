<?php

namespace Craft;

class LiteSpeedCacheVariable
{
  public function checkTemplateCache($slug = null)
  {
    return craft()->liteSpeedCache->checkTemplateCache($slug);
  }
}