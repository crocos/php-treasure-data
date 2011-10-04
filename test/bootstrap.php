<?php
/**
 *
 */

define('PHPTD_SRC_DIR' , dirname(__FILE__) . '/../src');
define('PHPTD_TEST_DIR', dirname(__FILE__));
define('PHPTD_TMP_DIR' , dirname(__FILE__) . '/../tmp');

PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(dirname(__FILE__));

require_once PHPTD_SRC_DIR . '/TreasureData/Autoload.php';
