<?php
/**
 *
 */


namespace TreasureData;

use TreasureData\API;

class APITest extends \PHPUnit_Framework_TestCase
{
    public function testVersion()
    {
        $this->assertEquals(API::VERSION, '0.2.0-dev');
    }
}
