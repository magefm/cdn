<?php

interface MageFM_CDN_Model_Storage_StorageInterface
{

    public function fileExists($path);

    public function renameFile($oldPath, $newPath);

    public function saveContent($path, $content, $mimeType = null, $compress = false);
}
