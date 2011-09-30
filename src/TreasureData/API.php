<?php
/**
 *  API.php
 *
 *  The library entry point.
 *
 *  @package    TreasureData
 *  @author     Sotaro KARASAWA <sotarok@crocos.co.jp>
 *  @license    Apache License 2.0
 */

namespace TreasureData;

if (!extension_loaded('curl')) {
    throw new \Exception('TreasureData requires curl extension.');
}

use TreasureData\Exception;
use TreasureData\API\Base;


/**
 *  TreasureData\API
 *
 *  @package    TreasureData
 *  @author     Sotaro KARASAWA <sotarok@crocos.co.jp>
 *  @license    Apache License 2.0
 */
class API
{
    const CONFFILE = '~/.td/td.conf';
    const ENDPOINT   = 'http://api.treasure-data.com';
    const APIVERSION = 'v3';

    protected $db_name = null;
    protected static $api_key = null;

    protected static $instances = array();

    public static $is_debug = false;

    public function __construct($db_name, $api_key = null, $conf_file = null)
    {

        $this->db_name = $db_name;

        if ($api_key != null) {
            self::$api_key = $api_key;
        }
        else {
            if (null === $conf_file) {
                $conf_file = str_replace('~/', getenv('HOME') . '/', self::CONFFILE);
            }
            if (!(file_exists($conf_file) && is_readable($conf_file))) {
                throw new Exception("Config file not found or not readable: $conf_file");
            }

            $conf_lines = file($conf_file);
            $api_key = null;
            foreach ($conf_lines as $line) {
                $line_array =  explode('=', $line);
                if (count($line_array) != 2) {
                    continue;
                }
                list($key, $value) = $line_array;
                if (trim($key) == 'apikey') {
                    $api_key = trim($value);
                }
            }
            if (null === $api_key) {
                throw new Exception("There is no apikey in config file.");
            }

            self::$api_key = $api_key;
        }
    }

    public static function setDebug($is_debug = false)
    {
        static::$is_debug = (bool)$is_debug;
    }

    public static function getAPIKey()
    {
        return self::$api_key;
    }

    public function __get($name)
    {
        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        }

        if (!Base::hasAPI($name)) {
            throw new Exception("Specified non exist API: $name");
        }

        static::$instances[$name] = Base::getAPI($name, $this->db_name);
        return static::$instances[$name];
    }

    public static function log($msg, $file = 'php://stderr')
    {
        if (static::$is_debug) {
            $fp = fopen($file, 'a+');
            fprintf($fp, '[DEBUG] '. str_replace('%', '%%', $msg) . PHP_EOL);
            fclose($fp);
        }
    }
}
