<?php

$this->startSetup();

$this->run("
CREATE TABLE `{$this->getTable('magefm_cdn/cache')}` (
    id integer UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id smallint UNSIGNED NOT NULL,
    path text NOT NULL,
    created_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB ;
");

$this->endSetup();
