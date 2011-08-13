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

spl_autoload_register(__NAMESPACE__ . '\\API::loadClass');
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

    public function setDebug($is_debug = false)
    {
        static::$is_debug = (bool)$is_debug;
    }

    public function getAPIKey()
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

    public static function loadClass($name)
    {
        if (!preg_match('/^TreasureData\\\\(.+)/', $name, $matches)) {
            return ;
        }

        $classname = $matches[1];
        $filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';
        if (is_readable($filename)) {
            require_once $filename;
        }
    }

    public static function log($msg, $file = 'php://stderr')
    {
        if (static::$is_debug) {
            $fp = fopen($file, 'a+');
            fprintf($fp, '[DEBUG] '. $msg . PHP_EOL, FILE_APPEND);
            fclose($fp);
        }
    }
}
