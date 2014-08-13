<?php

class MageFM_CDN_Helper_Image extends Mage_Catalog_Helper_Image
{

    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        if (!Mage::helper('magefm_cdn')->isEnabled()) {
            return parent::init($product, $attributeName, $imageFile);
        }

        $this->_reset();
        $this->_setModel(Mage::getModel('catalog/product_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setProduct($product);

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            $this->_getModel()->setBaseFile($this->getProduct()->getData($this->_getModel()->getDestinationSubdir()));
        }

        return $this;
    }

}
