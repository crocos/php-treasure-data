<?php
/**
 *
 */

namespace TreasureData\API;

use TreasureData\API;
use TreasureData\Exception;

/**
 *  TreasureData\API\Base
 *
 *  @package    TreasureData
 *  @author     Sotaro KARASAWA <sotarok@crocos.co.jp>
 *  @license    Apache License 2.0
 */
abstract class Base
{
    protected $db_name = null;

    public static $api_list = array(
        'job',
    );

    /**
     *  __construct
     *
     *  @param      string  $db_name
     */
    public function __construct($db_name)
    {
        $this->db_name = $db_name;
    }

    public function getDbName()
    {
        return $this->db_name;
    }

    protected function request($path, $params = array(), $is_post = false)
    {
        $url = API::ENDPOINT
            . '/' . API::APIVERSION
            . '/' . static::PATH
            . '/' . ltrim($path, '/');
        if ($is_post) {
            $url .= '?' . http_build_query($params);
        }

        API::log("API request to $url");

        $ch = curl_init($url);
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("AUTHORIZATION: TD1 " . API::getAPIKey()));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $ret = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(sprintf('API request failed on curl error: \'%s\'', curl_error($ch)));
        }

        curl_close($ch);

        if ($ret === null) {
            throw new Exception(sprintf('API result is null: \'%s\'', $url));
        }
        return $ret;
    }

    public static function hasAPI($name)
    {
        if (in_array($name, static::$api_list)) {
            return true;
        }
        return false;
    }

    public static function getAPI($name, $db_name)
    {
        $class =  __NAMESPACE__ . '\\' . ucfirst($name);
        return new $class($db_name);
    }
}
