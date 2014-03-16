<?php

class MageFM_CDN_Model_System_Config_Backend_Email_Logo extends Mage_Adminhtml_Model_System_Config_Backend_Email_Logo
{

    protected function _getUploadDir()
    {
        return $this->_appendScopeInfo(self::UPLOAD_DIR);
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
