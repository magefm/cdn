<?php

class MageFM_CDN_Model_Resource_Cache extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('magefm_cdn/cache', 'id');
    }

}
