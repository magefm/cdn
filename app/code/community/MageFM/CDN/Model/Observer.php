<?php

class MageFM_CDN_Model_Observer
{

    public function catalogProductSaveAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfigFlag('magefm_cdn/general/enabled')) {
            return;
        }

        try {
            $product = $observer->getEvent()->getProduct();

            foreach (array('image', 'small_image', 'thumbnail') as $imageType) {
                $sizes = Mage::getStoreConfig('magefm_cdn/resize/' . $imageType);

                if (empty($sizes)) {
                    continue;
                }

                $sizes = explode('|', $sizes);

                foreach ($sizes as $size) {
                    $this->generateCache($product, $imageType, $size);
                }
            }
        } catch (Exception $e) {
            var_dump($e->getMessage(), $e->getTrace());
        }

        die(__METHOD__);
    }

    protected function generateCache(Mage_Catalog_Model_Product $product, $type, $rawSize)
    {
        list($width, $height) = explode('x', $rawSize, 2);

        $width = (empty($width) ? null : $width);
        $height = (empty($height) ? null : $height);

        foreach ($product->getMediaGalleryImages() as $image) {
            $model = Mage::getModel('catalog/product_image');
            $model->setDestinationSubdir($type);
            $model->setWidth($width);
            $model->setHeight($height);
            $model->setBaseFile($image['file']);
            $model->resize();
            $model->saveFile();
        }
    }

    public function adminhtmlCatalogProductGalleryUploadPreDispatch(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfigFlag('magefm_cdn/general/enabled')) {
            return;
        }

        // call modified version of Mage_Adminhtml_Catalog_Product_GalleryController::uploadAction
        $controller = $observer->getControllerAction();
        $this->adminhtmlCatalogProductGalleryUploadPreDispatchOriginalAction($controller);

        // and the request ends here
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
    }

    /**
     * This is a copy of the Mage_Adminhtml_Catalog_Product_GalleryController::uploadAction
     * method. Because Magento used the new keyword to create the uploader model, it's
     * impossible to overwrite in a normal way.
     */
    protected function adminhtmlCatalogProductGalleryUploadPreDispatchOriginalAction(Mage_Adminhtml_Catalog_Product_GalleryController $controller)
    {
        $session = Mage::getSingleton('adminhtml/session');

        try {
            $uploader = Mage::getModel('magefm_cdn/uploader', 'image');
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->addValidateCallback('catalog_product_image', Mage::helper('catalog/image'), 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save(
                Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPathAddition()
            );

            Mage::dispatchEvent('catalog_product_gallery_upload_image_after', array(
                'result' => $result,
                'action' => $controller
            ));

            $result['file'] = $result['file'] . '.tmp';
            $result['cookie'] = array(
                'name' => session_name(),
                'value' => $session->getSessionId(),
                'lifetime' => $session->getCookieLifetime(),
                'path' => $session->getCookiePath(),
                'domain' => $session->getCookieDomain()
            );
        } catch (Exception $e) {
            $result = array(
                'error' => get_class($e) . $e->getMessage(),
                'errorcode' => $e->getCode()
            );
        }

        Mage::log(print_r($result, true), null, 'uploader.log', true);

        $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

}
