<?php

class MageFM_CDN_Helper_Image extends Mage_Catalog_Helper_Image
{

    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        if (!Mage::getStoreConfigFlag('magefm_cdn/general/enabled')) {
            return parent::init($product, $attributeName, $imageFile);
        }

        $this->_reset();
        $this->_setModel(Mage::getModel('catalog/product_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setProduct($product);
        $this->baseFile = $this->getProduct()->getData($this->_getModel()->getDestinationSubdir());

        return $this;
    }

    public function __toString()
    {
        if (!Mage::getStoreConfigFlag('magefm_cdn/general/enabled')) {
            return parent::__toString();
        }

        $model = $this->_getModel();

        $url = array(
            Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrl(),
            'cache',
            Mage::app()->getStore()->getId(),
            $model->getDestinationSubdir(),
        );
        
        $width = $model->getWidth();
        $height = $model->getHeight();

        if ((!empty($width)) || (!empty($height))) {
            $url[] = "{$width}x{$height}";
        }

        /** @TODO use $miscParams from model */
        $miscParams = array(
            'proportional',
            'frame',
            'transparency',
            'notconstrainonly',
            'ffffff',
            'angle' . $model->getAngle(),
            'quality' . $model->getQuality()
        );

        $url[] = md5(implode('_', $miscParams));
        $url[] = substr($this->baseFile, 1);

        return implode('/', $url);
    }

}
