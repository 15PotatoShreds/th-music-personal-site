<?php
require '../config.php';

$result = [];
$cursor = $DB->album->find(
    [],
    ['projection' => ['_id' => 0]]
);
foreach ($cursor as $document) {
    $producers = [];
    foreach ($document["Producer"] as $producer) {
        array_push($producers, $producer);
    }
    $musicList = [];
    $musics = $DB->music->find(
        ['_id' => ['$in' => $document['MusicList']]],
        ['projection' => ['_id' => 0]]
    );
    foreach ($musics as $music) {
        array_push($musicList, array(
            "MusicID" => $music["MusicID"],
            "DiscNo" => $music["DiscNo"],
            "TrackNo" => $music["TrackNo"],
            "Title" => $music["Title"],
            "File" => $music["File"]
        ));
    }
    array_push($result, array(
        "AlbumID" => $document["AlbumID"],
        "AlbumName" => $document["AlbumName"],
        "Producer" => $producers,
        "MusicList" => $musicList
    ));
}
echo json_encode($result);
