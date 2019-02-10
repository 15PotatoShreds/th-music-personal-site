<?php
require '../config.php';

$musicInfo = $DB->music->findOne(
    ['MusicID' => (int)$_POST['MusicID']]
);
$albumInfo = $DB->album->findOne(
    ['MusicList' => ['$in' => [$musicInfo['_id']]]],
);
$file = $_FILES['file'];

$filename = $musicInfo['DiscNo']
    .'-'.$musicInfo['TrackNo']
    .' '.$musicInfo['Title']
    .'.'.pathinfo($file['name'])['extension'];
$folder = $albumInfo['AlbumID'].' '.$albumInfo['AlbumName'];
$fileLocation = $ROOTFOLDER.'/'.$folder.'/'.$filename;

$dir = iconv("UTF-8", "GBK", $ROOTFOLDER.'/'.$folder);
if (!file_exists($dir)){
    mkdir ($dir,0777,true);
}

$DB->music->updateOne(
    ['_id' => $musicInfo['_id']],
    ['$set' => ['File' => $folder.'/'.$filename]]
);

move_uploaded_file($file['tmp_name'],$fileLocation);

print($fileLocation);