<?php
require 'vendor/autoload.php';
$DB = (new MongoDB\Client("mongodb://localhost:27017"))->test;
$ROOTFOLDER = "D:/test";