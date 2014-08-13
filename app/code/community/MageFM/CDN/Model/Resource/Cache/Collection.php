<?php

class MageFM_CDN_Model_Resource_Cache_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('magefm_cdn/cache');
    }

}
