<?php
/**
 *
 */

namespace TreasureData\API;

use TreasureData\Exception;
use TreasureData\API\Base;

class Job extends Base
{
    const PATH = 'job';

    public function issue($query)
    {
        $path = sprintf('issue/hive/%s', $this->db_name);

        $result = $this->request($path, array('query' => $query), true);
        $json = json_decode($result);
        if (!$json->job_id) {
            throw new Exception('Failed to job issued.');
        }

        return $json->job_id;
    }

    public function show($id)
    {
        $path = sprintf('show/%s', $id);

        $result = $this->request($path);
        return json_decode($result);
    }

    public function result($id, $format = 'tsv')
    {
        $path = sprintf('result/%s', $id);

        $result = $this->request($path, array('format' => $format));
        return $result;
    }
}
