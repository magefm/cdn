<?php

class MageFM_CDN_Model_Source_Storage
{

    public function toOptionArray()
    {
        $options = array();

        foreach ($this->toArray() as $value => $label) {
            $options[] = array('value' => $value, 'label' => $label);
        }

        return $options;
    }

    public function toArray()
    {
        return array(
            's3' => Mage::helper('magefm_cdn')->__('Amazon Simple Storage Service'),
        );
    }

}
