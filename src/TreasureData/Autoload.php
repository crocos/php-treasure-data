<?php
/**
 *  Autoload.php
 *
 *  The library entry point.
 *
 *  @package    TreasureData
 *  @author     Sotaro KARASAWA <sotarok@crocos.co.jp>
 *  @license    Apache License 2.0
 */

namespace TreasureData;

require_once __DIR__ . '/API.php';

$treasure_data_loader = new Autoload(__DIR__, __NAMESPACE__);
spl_autoload_register(array($treasure_data_loader, 'loadClass'));

class Autoload
{
    protected $path;
    protected $ns;
    protected $ns_sep = '\\';
    protected $suffix = '.php';

    public function __construct($path, $ns)
    {
        $this->path = $path;
        $this->ns = $ns;
    }

    public function loadClass($name)
    {
        if (strpos($name, $this->ns) !== 0) {
            return ;
        }

        $classname = $name;
        $filename = $this->path
            . DIRECTORY_SEPARATOR
            . str_replace(
                $this->ns_sep,
                DIRECTORY_SEPARATOR,
                substr($classname, strpos($classname, $this->ns_sep) + 1))
            . $this->suffix;

        return $this->loadFile($filename);
    }

    public function loadFile($filename)
    {
        if (is_readable($filename)) {
            return require_once $filename;
        }
        return false;
    }
}
