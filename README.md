MageFM CDN
==========

This is a Magento CDN module, built to use when you need scale multiple Magento instances horizontally, avoinding the need of tools like s3fs, rsync, etc to keep the `media` folder in-sync on every instance.

Features
--------

- Store product images on CDN;
- Store merged CSS and Javascript files on CDN;
- Upload product images directly to CDN;

Supported storages
------------------

- Amazon Simple Storage Service

Installation
------------

1. Copy all files to the Magento directory;
2. Clean your cache;
3. Configure your Amazon S3 credentials at System > Configuration > MageFM > CDN;
4. Configure your Base Media URL at System > Configuration > Generatel > Web;
