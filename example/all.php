<?php
/**
 *
 */

require_once __DIR__ . '/example.inc.php';

try {
    $td = new \TreasureData\API('test_db', null, $api_key);

    $job_id = $td->job->issue(
        "SELECT count(*) FROM test WHERE v['action'] = 'login'"
    );
    while (1) {
        $res = $td->job->show($job_id);
        if ($res->status == 'success') {
            break;
        }
        sleep(1);
    }
    $res = $td->job->result($job_id);

    echo $res, PHP_EOL;
} catch (\TreasureData\Exception $e) {
    echo $e->getMessage(), PHP_EOL;
}
