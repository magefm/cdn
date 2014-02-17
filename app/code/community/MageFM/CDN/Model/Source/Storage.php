<?php

class MageFM_CDN_Model_Source_Storage
{

    public function toOptionArray()
    {
        return array(
            array('value' => 's3', 'label' => Mage::helper('magefm_cdn')->__('Amazon Simple Storage Service')),
        );
    }

    public function toArray()
    {
        return array(
            's3' => Mage::helper('magefm_cdn')->__('Amazon Simple Storage Service'),
        );
    }

}
