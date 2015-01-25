<?php

use MateuszKrasucki\MsisdnResolver\Msisdn;

class MsisdnTest extends PHPUnit_Framework_TestCase
{
    public function testWrongString()
    {
        $testString = 'numer';
        $msisdn = new Msisdn($testString);
        $this->assertEquals($testString, $msisdn->getMsisdnGiven());
        $this->assertEquals(null, $msisdn->getMsisdn());
    }
}