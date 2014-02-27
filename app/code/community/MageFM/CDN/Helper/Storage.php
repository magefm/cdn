<?php

class MageFM_CDN_Helper_Storage extends Mage_Core_Helper_Abstract
{

    protected function getStorageModel()
    {
        $storage = Mage::getStoreConfig('magefm_cdn/general/storage');

        switch ($storage) {
            case 's3':
                return Mage::getModel('magefm_cdn/storage_' . $storage);
            default:
                Mage::throwException('MageFM CDN: Storage is not configured.');
        }
    }

    public function saveFileFromPath($oldName, $newName)
    {
        return $this->getStorageModel()->saveContent($newName, file_get_contents($oldName));
    }

    public function saveFileFromContent($path, $content, $mimeType = null)
    {
        return $this->getStorageModel()->saveContent($path, $content, $mimeType);
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
