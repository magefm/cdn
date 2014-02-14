<?php

class MageFM_CDN_Helper_Storage extends Mage_Core_Helper_Abstract
{

    public function getStorageModel()
    {
        /**
         * @TODO check config and return the correct storage model
         */
        return Mage::getModel('magefm_cdn/storage_s3');
    }

    public function saveFileFromPath($oldName, $newName)
    {
        return $this->getStorageModel()->saveContent($newName, file_get_contents($oldName));
    }

    public function renameFile($oldPath, $newPath)
    {
        return $this->getStorageModel()->renameFile($oldPath, $newPath);
    }

    public function fileExists($path)
    {
        return $this->getStorageModel()->fileExists($path);
    }

}
