<?php

class MageFM_CDN_Model_Cache extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('magefm_cdn/cache');
        $this->setCreatedAt(now());
    }

    public function loadByStoreAndPath($storeId, $path)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('store_id', $storeId);
        $collection->addFieldToFilter('path', $path);
        $collection->setPageSize(1);

        return $collection->getFirstItem();
    }

}
