<?php

class MageFM_CDN_Model_Uploader extends Mage_Core_Model_File_Uploader
{

    public function save($destinationFolder, $newFileName = null)
    {
        if (!Mage::helper('magefm_cdn')->isEnabled()) {
            return parent::save($destinationFolder, $newFileName);
        }

        $this->_validateFile();
        $this->_result = false;

        $fileName = isset($newFileName) ? $newFileName : $this->_file['name'];
        $fileName = self::getCorrectFileName($fileName);

        if ($this->_enableFilesDispersion) {
            $fileName = $this->correctFileNameCase($fileName);
            $this->_dispretionPath = self::getDispretionPath($fileName);
        }

        if ($this->_allowRenameFiles) {
            $fileName = self::getNewFileName($destinationFolder . $this->_dispretionPath, $fileName);
        }

        $destinationFile = $destinationFolder . $this->_dispretionPath . '/' . $fileName;

        $this->_result = $this->_moveFile($this->_file['tmp_name'], $destinationFile);

        if ($this->_result && $this->_result['success']) {
            $this->_result = array(
                'tmp_name' => $this->_file['tmp_name'],
                'file' => $fileName,
                'url' => $this->_result['url'],
                'path' => $destinationFolder . $this->_dispretionPath,
            );

            $this->_uploadedFileName = $fileName;
            $this->_uploadedFileDir = $destinationFolder . $this->_dispretionPath;

            $this->_afterSave($this->_result);
        }

        return $this->_result;
    }

    public static function getNewFileName($destFile, $file = null)
    {
        if (!Mage::helper('magefm_cdn')->isEnabled()) {
            return parent::getNewFileName($destFile);
        }

        if (is_null($file)) {
            Mage::throwException('Can\'t convert filename.');
        }

        if (!Mage::helper('magefm_cdn/storage')->fileExists($destFile . '/' . $file)) {
            return $file;
        }

        $ext = null;
        $i = 0;

        if (preg_match('#(.*)\.([a-z0-9_-]*)$#', $file, $matches)) {
            $name = $matches[1];
            $ext = $matches[2];
        } else {
            $name = $file;
        }

        do {
            $i++;
            $fileName = "{$name}_{$i}" . (is_null($ext) ? '' : '.' . $ext);
        } while (Mage::helper('magefm_cdn/storage')->fileExists($destFile . '/' . $fileName));

        return $fileName;
    }

    protected function _moveFile($tmpPath, $destPath)
    {
        if (!Mage::helper('magefm_cdn')->isEnabled()) {
            return parent::_moveFile($tmpPath, $destPath);
        }

        return Mage::helper('magefm_cdn/storage')->saveFileFromPath($tmpPath, $destPath);
    }

}
