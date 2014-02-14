<?php

include '/home/www/workspace/magento/lib/aws.phar';

use Aws\S3\S3Client;
use Aws\S3\Exception\AccessDeniedException;

class MageFM_CDN_Model_Storage_S3 implements MageFM_CDN_Model_Storage_StorageInterface
{

    public function saveContent($path, $content)
    {
        try {
            $s3 = $this->getClient();

            $result = $s3->putObject(array(
                'Bucket' => $this->getConfig('bucket'),
                'Key' => $path,
                'Body' => $content,
                'ACL' => 'public-read',
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

    protected function getClient()
    {
        return S3Client::factory(array(
                'key' => $this->getConfig('key'),
                'secret' => $this->getConfig('secret'),
        ));
    }

    protected function getConfig($key)
    {
        return Mage::getStoreConfig('magefm/cdn/storage/s3/' . $key);
    }

}
