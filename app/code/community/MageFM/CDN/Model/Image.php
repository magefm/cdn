<?php

class MageFM_CDN_Model_Image extends Mage_Catalog_Model_Product_Image
{

    /**
     * @TODO generate placeholder if file does not exists
     * @TODO support watermark
     */
    public function setBaseFile($file)
    {
        if (!Mage::getStoreConfigFlag('magefm_cdn/general/enabled')) {
            return parent::setBaseFile($file);
        }

        $this->_isBaseFilePlaceholder = false;

        $originalUrl = Mage::getSingleton('catalog/product_media_config')->getMediaUrl() . (substr($file, 0, 1) == '/' ? substr($file, 1) : $file);
        $newFile = tempnam(sys_get_temp_dir(), 'magefm_cdn_');
        file_put_contents($newFile, file_get_contents($originalUrl));
        $this->_baseFile = $newFile;

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

        $path[] = md5(implode('_', $miscParams));

        // append prepared filename
        $this->_newFile = implode('/', $path) . $file;

        return $this;
    }

    public function saveFile()
    {
        die(__METHOD__);
        parent::saveFile();
    }

}
