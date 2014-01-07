<?php

class MageFM_CDN_Model_Observer
{

    public function catalogProductSaveAfter(Varien_Event_Observer $observer)
    {
        try {
            $product = $observer->getEvent()->getProduct();

            foreach ($product->getStoreIds() as $storeId) {
                Mage::app()->setCurrentStore($storeId);
                $this->generateProductImages($product);
                $this->generate($product, 'small_image', 135);
                $this->generate($product, 'image', 265);
                $this->generate($product, 'thumbnail', 56);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        Mage::app()->setCurrentStore(0);
    }

    protected function generate(Mage_Catalog_Model_Product $product, $type, $width = null, $height = null)
    {
        $model = Mage::getModel('catalog/product_image');
        $model->setDestinationSubdir($type);
        $model->setWidth($width);
        $model->setHeight($height);
        $model->setBaseFile($product->getData($type));
        $model->resize();
        $model->saveFile();
    }

    protected function generateProductImages($product)
    {
        foreach ($product->getMediaGalleryImages() as $image) {
            $model = Mage::getModel('catalog/product_image');
            $model->setDestinationSubdir('image');
            $model->setBaseFile($image->getFile());
            $model->saveFile();
        }
    }

}
