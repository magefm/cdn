<?php

Mage::getModel('magefm_cdn/autoloader')->registerAutoloader();

use Aws\S3\S3Client;
use Aws\S3\Exception\AccessDeniedException;
use Aws\S3\Exception\NoSuchKeyException;

class MageFM_CDN_Model_Storage_S3 implements MageFM_CDN_Model_Storage_StorageInterface
{

    public function saveContent($path, $content, $mimeType = null, $compress = false)
    {
        try {
            $s3 = $this->getClient();

            if ($compress == true) {
                $content = gzcompress($content, 9);
            }

            $result = $s3->putObject(array(
                'Bucket' => $this->getConfig('bucket'),
                'Key' => $path,
                'Body' => $content,
                'ACL' => 'public-read',
                'ContentType' => $mimeType,
                'ContentEncoding' => ($compress ? 'deflate' : null),
            ));

            return array(
                'success' => true,
                'url' => $result['ObjectURL']
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    public function fileExists($path)
    {
        try {
            $this->getClient()->headObject(array(
                'Bucket' => $this->getConfig('bucket'),
                'Key' => $path,
            ));

            return true;
        } catch (AccessDeniedException $e) {
            return false;
        } catch (NoSuchKeyException $e) {
            return false;
        }
    }

    public function renameFile($oldPath, $newPath)
    {
        try {
            $s3 = $this->getClient();

            $s3->copyObject(array(
                'Bucket' => $this->getConfig('bucket'),
                'Key' => $newPath,
                'CopySource' => urlencode($this->getConfig('bucket') . '/' . $oldPath),
                'ACL' => 'public-read',
            ));

            $s3->deleteObject(array(
                'Bucket' => $this->getConfig('bucket'),
                'Key' => $oldPath,
            ));

            return true;
        } catch (AccessDeniedException $e) {
            return false;
        }
    }

    public function delete($path)
    {
        try {
            $s3 = $this->getClient();

            $s3->deleteObject(array(
                'Bucket' => $this->getConfig('bucket'),
                'Key' => $path,
            ));

            return true;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function deleteFolder($path)
    {
        try {
            $s3 = $this->getClient();

            if (substr($path, 1, -1) !== '/') {
                $path .= "/";
            }

            $s3->deleteMatchingObjects($this->getConfig('bucket'), $path);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function getClient()
    {
        return S3Client::factory(array(
                    'key' => $this->getConfig('key'),
                    'secret' => $this->getConfig('secret'),
        ));
    }

    protected function getConfig($key)
    {
        return Mage::getStoreConfig('magefm_cdn/storage_s3/' . $key);
    }

}
