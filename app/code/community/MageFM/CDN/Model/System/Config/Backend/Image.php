<?php

class MageFM_CDN_Model_System_Config_Backend_Image extends Mage_Adminhtml_Model_System_Config_Backend_Image
{

    protected function _getUploadDir()
    {
        $fieldConfig = $this->getFieldConfig();
        /* @var $fieldConfig Varien_Simplexml_Element */

        if (empty($fieldConfig->upload_dir)) {
            Mage::throwException(Mage::helper('catalog')->__('The base directory to upload file is not specified.'));
        }

        $uploadDir = (string) $fieldConfig->upload_dir;

        $el = $fieldConfig->descend('upload_dir');

        /**
         * Add scope info
         */
        if (!empty($el['scope_info'])) {
            $uploadDir = $this->_appendScopeInfo($uploadDir);
        }

        return $uploadDir;
    }

    protected function _beforeSave()
    {
        return Mage::helper('magefm_cdn/adminhtml')->systemConfigBackendFileBeforeSave($this, $this->_getUploadDir(), $this->_getAllowedExtensions());
    }

    public function prependScopeInfo($filename)
    {
        if ($this->_addWhetherScopeInfo()) {
            $filename = $this->_prependScopeInfo($filename);
        }

        return $filename;
    }

}
