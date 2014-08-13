<?php

class MageFM_CDN_Model_Image extends Mage_Catalog_Model_Product_Image
{

    protected $imageUrl;

    /**
     * @TODO generate placeholder if file does not exists
     * @TODO support watermark
     */
    public function setBaseFile($file)
    {
        if (!Mage::helper('magefm_cdn')->isEnabled()) {
            return parent::setBaseFile($file);
        }

        $this->_isBaseFilePlaceholder = false;

        // build new filename (most important params)
        $path = array(
            Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrlAddition(),
            'cache',
            Mage::app()->getStore()->getId(),
            $path[] = $this->getDestinationSubdir()
        );

        if ((!empty($this->_width)) || (!empty($this->_height))) {
            $path[] = "{$this->_width}x{$this->_height}";
        }

        // add misk params as a hash
        $miscParams = array(
            ($this->_keepAspectRatio ? '' : 'non') . 'proportional',
            ($this->_keepFrame ? '' : 'no') . 'frame',
            ($this->_keepTransparency ? '' : 'no') . 'transparency',
            ($this->_constrainOnly ? 'do' : 'not') . 'constrainonly',
            $this->_rgbToString($this->_backgroundColor),
            'angle' . $this->_angle,
            'quality' . $this->_quality
        );

        // if has watermark add watermark params to hash
        if ($this->getWatermarkFile()) {
            $miscParams[] = $this->getWatermarkFile();
            $miscParams[] = $this->getWatermarkImageOpacity();
            $miscParams[] = $this->getWatermarkPosition();
            $miscParams[] = $this->getWatermarkWidth();
            $miscParams[] = $this->getWatermarkHeigth();
        }

        $path[] = md5(implode('_', $miscParams));

        // append prepared filename
        $this->_newFile = implode('/', $path) . $file;
        $this->imageUrl = Mage::getSingleton('catalog/product_media_config')->getMediaUrl(substr($file, 0, 1) == '/' ? substr($file, 1) : $file);

        return $this;
    }

    public function saveFile()
    {
        if (!Mage::helper('magefm_cdn')->isEnabled()) {
            return parent::saveFile();
        }

        $this->getImageProcessor()->save($this->_baseFile);
        Mage::helper('magefm_cdn/storage')->saveFileFromPath($this->_baseFile, $this->_newFile);
        unlink($this->_baseFile);
        return $this;
    }

    public function clearCache()
    {
        if (!Mage::helper('magefm_cdn')->isEnabled()) {
            return parent::clearCache();
        }

        Mage::throwException('You cannot clear this cache because new images will not be automatically generated.');
    }

    public function isCached()
    {
        if (!Mage::helper('magefm_cdn')->isEnabled()) {
            return parent::isCached();
        }

        $entity = Mage::getModel('magefm_cdn/cache')->loadByStoreAndPath(Mage::app()->getStore()->getId(), $this->_newFile);

        if ($entity->getId()) {
            return true;
        }

        $result = Mage::helper('magefm_cdn/storage')->fileExists($this->_newFile);

        if ($result) {
            $entity->setStoreId(Mage::app()->getStore()->getId());
            $entity->setPath($this->_newFile);
            $entity->save();
        } else {
            Mage::log($this->_newFile, null, 'magefm_cdn_iscached.log', true);
        }

        return $result;
    }

    public function getImageProcessor()
    {
        if (!Mage::helper('magefm_cdn')->isEnabled()) {
            return parent::getImageProcessor();
        }

        $originalUrl = $this->imageUrl;
        $tmpExt = explode('.', $originalUrl);
        $ext = array_pop($tmpExt);
        $tmpFile = tempnam(sys_get_temp_dir(), 'magefm_cdn_');
        $newFile = "{$tmpFile}.{$ext}";
        unlink($tmpFile);
        file_put_contents($newFile, file_get_contents($originalUrl));
        $this->_baseFile = $newFile;

        return parent::getImageProcessor();
    }

}
