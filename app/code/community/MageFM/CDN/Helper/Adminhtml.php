<?php

class MageFM_CDN_Helper_Adminhtml extends Mage_Core_Helper_Abstract
{

    /**
     * This methods overwrites Mage_Adminhtml_Model_System_Config_Backend_File::_beforeSave
     * in a bad way, just because the "$uploader = new class".
     * 
     * @param Mage_Adminhtml_Model_System_Config_Backend_File $model
     * @param type $uploadDir
     * @param type $allowedExtensions
     * @return Mage_Adminhtml_Model_System_Config_Backend_File
     */
    public function systemConfigBackendFileBeforeSave(Mage_Adminhtml_Model_System_Config_Backend_File $model, $uploadDir, $allowedExtensions)
    {
        $value = $model->getValue();

        if ($_FILES['groups']['tmp_name'][$model->getGroupId()]['fields'][$model->getField()]['value']) {

            try {
                $file = array();
                $tmpName = $_FILES['groups']['tmp_name'];
                $file['tmp_name'] = $tmpName[$model->getGroupId()]['fields'][$model->getField()]['value'];
                $name = $_FILES['groups']['name'];
                $file['name'] = $name[$model->getGroupId()]['fields'][$model->getField()]['value'];
                $uploader = new MageFM_CDN_Model_Uploader($file);
                $uploader->setAllowedExtensions($allowedExtensions);
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $model, 'validateMaxSize');
                $result = $uploader->save($uploadDir);
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $model;
            }

            $filename = $result['file'];
            if ($filename) {
                $filename = $model->prependScopeInfo($filename);
                $model->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                $model->setValue('');
            } else {
                $model->unsValue();
            }
        }

        return $model;
    }

}
