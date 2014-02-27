<?php

class MageFM_CDN_Helper_Core extends Mage_Core_Helper_Data
{

    public function mergeFiles(array $srcFiles, $targetFile = false, $mustMerge = false, $beforeMergeCallback = null, $extensionsFilter = array(), $mimeType = null)
    {
        $targetPath = '/' . $targetFile;

        try {
            /**
             * @TODO cache the S3 response here
             */
            $storage = Mage::helper('magefm_cdn/storage');

            if ($storage->fileExists($targetPath)) {
                return true;
            }

            // filter by extensions
            if ($extensionsFilter) {
                if (!is_array($extensionsFilter)) {
                    $extensionsFilter = array($extensionsFilter);
                }

                if (!empty($srcFiles)) {
                    foreach ($srcFiles as $key => $file) {
                        $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (!in_array($fileExt, $extensionsFilter)) {
                            unset($srcFiles[$key]);
                        }
                    }
                }
            }

            if (empty($srcFiles)) {
                // no translation intentionally
                throw new Exception('No files to compile.');
            }

            $data = '';
            foreach ($srcFiles as $file) {
                if (!file_exists($file)) {
                    continue;
                }

                $contents = file_get_contents($file) . "\n";

                if ($beforeMergeCallback && is_callable($beforeMergeCallback)) {
                    $contents = call_user_func($beforeMergeCallback, $file, $contents);
                }

                $data .= $contents;
            }

            if (!$data) {
                // no translation intentionally
                throw new Exception(sprintf("No content found in files:\n%s", implode("\n", $srcFiles)));
            }

            $storage->saveFileFromContent($targetPath, $data, $mimeType);
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
        }

        return false;
    }

}
