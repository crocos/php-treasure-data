<?php
/**
 *
 */

require_once __DIR__ . '/example.inc.php';

try {
    $td = new \TreasureData\API('test_db', null, $api_key);
    $res = $td->job->result(2207);

    echo $res, PHP_EOL;
} catch (\TreasureData\Exception $e) {
    echo $e->getMessage(), PHP_EOL;
}
