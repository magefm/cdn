<?php

class MageFM_CDN_Model_Design_Package extends Mage_Core_Model_Design_Package
{

    public function getMergedJsUrl($files)
    {
        $targetFilename = 'js/' . md5(implode(',', $files)) . '.js';

        if ($this->_mergeFiles($files, $targetFilename, false, null, 'js', 'text/javascript')) {
            return Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure()) . $targetFilename;
        }

        return '';
    }

    public function getMergedCssUrl($files)
    {
        // secure or unsecure
        $isSecure = Mage::app()->getRequest()->isSecure();
        $mergerDir = $isSecure ? 'css_secure' : 'css';

        // base hostname & port
        $baseMediaUrl = Mage::getBaseUrl('media', $isSecure);
        $hostname = parse_url($baseMediaUrl, PHP_URL_HOST);
        $port = parse_url($baseMediaUrl, PHP_URL_PORT);

        if (false === $port) {
            $port = $isSecure ? 443 : 80;
        }

        // merge into target file
        $targetFilename = $mergerDir . '/' . md5(implode(',', $files) . "|{$hostname}|{$port}") . '.css';
        $mergeFilesResult = $this->_mergeFiles($files, $targetFilename, false, array($this, 'beforeMergeCss'), 'css', 'text/css');

        if ($mergeFilesResult) {
            return $baseMediaUrl . $targetFilename;
        }

        return '';
    }

    public function cleanMergedJsCss()
    {
        Mage::app()->getCache()->clean('matchingTag', array('magefm_cdn'));
        Mage::helper('magefm_cdn/storage')->deleteFolder('css');
        Mage::helper('magefm_cdn/storage')->deleteFolder('css_secure');
        Mage::helper('magefm_cdn/storage')->deleteFolder('js');
    }

    protected function _mergeFiles(array $srcFiles, $targetFile = false, $mustMerge = false, $beforeMergeCallback = null, $extensionsFilter = array(), $mimeType = null)
    {
        return Mage::helper('core')->mergeFiles($srcFiles, $targetFile, $mustMerge, $beforeMergeCallback, $extensionsFilter, $mimeType);
    }

}
