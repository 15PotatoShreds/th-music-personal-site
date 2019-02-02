<?php
require 'vendor/autoload.php';
$db = (new MongoDB\Client("mongodb://localhost:27017"))->test;