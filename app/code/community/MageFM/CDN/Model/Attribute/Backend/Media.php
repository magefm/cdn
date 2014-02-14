<?php

class MageFM_CDN_Model_Attribute_Backend_Media extends Mage_Catalog_Model_Product_Attribute_Backend_Media
{

    protected function _moveImageFromTmp($file)
    {
        $ioObject = new Varien_Io_File();

        if (strrpos($file, '.tmp') == strlen($file) - 4) {
            $file = substr($file, 0, strlen($file) - 4);
        }

        $dispretionPath = Varien_File_Uploader::getDispretionPath($file);
        $tmpFile = $this->_getConfig()->getBaseTmpMediaPathAddition() . $dispretionPath . '/' . $file;
        $file = $this->_getUniqueFileName($this->_getConfig()->getBaseMediaPathAddition() . $dispretionPath, $file);
        $newPath = $this->_getConfig()->getBaseMediaPathAddition() . $dispretionPath . '/' . $file;

        $storageHelper = Mage::helper('magefm_cdn/storage');
        if (!$storageHelper->renameFile($tmpFile, $newPath)) {
            throw new Exception('Error while renaming image.');
        }

        return $dispretionPath . '/' . $file;
    }

    protected function _getUniqueFileName($path, $file)
    {
        return MageFM_CDN_Model_Uploader::getNewFileName($path, $file);
    }

}
