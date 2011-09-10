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

require_once __DIR__ . '/API.php';
spl_autoload_register(__NAMESPACE__ . '\\API::loadClass');
