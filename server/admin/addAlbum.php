<?php
require '../config.php';

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);
$result = $db->album->findOne([
    'AlbumID' => $data['albumID']
]);
if ($result) {
    echo("Already Exist");
} else {
    //Handle Producer
    $producer_ids = array();
    foreach ($data['producer'] as $producer) {
        $result = $db->producer->findOne(
            ['ProducerName' => $producer]
        );
        if ($result) {
            array_push($producer_ids, $result['_id']);
        } else {
            $result = $db->producer->insertOne(
                ['ProducerName' => $producer]
            );
            array_push($producer_ids, $result->getInsertedId());
        }
    }

    //Handle Music
    $music_ids = array();
    foreach ($data['musicList'] as $music) {
        $result = $db->music->insertOne([
            'MusicID' => $music['musicID'],
            'DiscNo' => $music['discNo'],
            'TrackNo' => $music['trackNo'],
            'Title' => $music['title']
        ]);
        array_push($music_ids, $result->getInsertedId());
    }

    //Handle Album
    $db->album->insertOne([
        'AlbumID' => $data['albumID'],
        'AlbumName' => $data['albumName'],
        'ProducerList' => $producer_ids,
        'MusicList' => $music_ids
    ]);

    echo("success");
}