<?php

namespace Craft;

class LiteSpeedCacheRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'lsclearance';
    }

    protected function defineAttributes()
    {
        return array(
            'path' => array(AttributeType::String, 'required' => true),
            'cleared' => array(AttributeType::String, 'required' => true)
        );
    }
}