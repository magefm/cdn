<?php

class MageFM_CDN_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function isEnabled()
    {
        return (bool) Mage::getStoreConfigFlag('magefm_cdn/general/enabled');
    }

}
