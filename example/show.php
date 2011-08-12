<?php
/**
 *
 */

require_once __DIR__ . '/example.inc.php';

try {
    $td = new \TreasureData\API('test_db', null, $api_key);
    $res = $td->job->show(2207);

    var_dump($res);
} catch (\TreasureData\Exception $e) {
    echo $e->getMessage(), PHP_EOL;
}
