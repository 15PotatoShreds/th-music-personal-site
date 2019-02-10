<?php
require '../config.php';

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);
$result = $DB->album->findOne([
    'AlbumID' => $data['albumID']
]);
if ($result) {
    echo("Already Exist");
} else {
    //Handle Music
    $music_ids = array();
    foreach ($data['musicList'] as $music) {
        $result = $DB->music->insertOne([
            'MusicID' => $music['musicID'],
            'DiscNo' => $music['discNo'],
            'TrackNo' => $music['trackNo'],
            'Title' => $music['title'],
            'File' => ''
        ]);
        array_push($music_ids, $result->getInsertedId());
    }

    //Handle Album
    $DB->album->insertOne([
        'AlbumID' => $data['albumID'],
        'AlbumName' => $data['albumName'],
        'Producer' => $data['producer'],
        'MusicList' => $music_ids
    ]);

    echo("success");
}