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
        $this->assertEquals(null, $msisdn->getNationalNumber());
        $this->assertEquals(null, $msisdn->getCountryCode());
        $this->assertEquals(null, $msisdn->getCountryId());
        $this->assertEquals(null, $msisdn->getMno());
        
        $this->assertEquals('Unknown', $msisdn->getMsisdnString());
        $this->assertEquals('Unknown', $msisdn->getNationalNumberString());
        $this->assertEquals('Unknown', $msisdn->getCountryCodeString());
        $this->assertEquals('Unknown', $msisdn->getCountryIdString());
        $this->assertEquals('Unknown', $msisdn->getMnoString());
        
        $this->assertEquals('Unknown, Unknown, Unknown, Unknown', $msisdn->getFullString());
        
        $testString = '+001230000';
        $msisdn = new Msisdn($testString);
        $this->assertEquals($testString, $msisdn->getMsisdnGiven());
        $this->assertEquals(null, $msisdn->getMsisdn());
        $this->assertEquals(null, $msisdn->getNationalNumber());
        $this->assertEquals(null, $msisdn->getCountryCode());
        $this->assertEquals(null, $msisdn->getCountryId());
        $this->assertEquals(null, $msisdn->getMno());
        
        $this->assertEquals('Unknown', $msisdn->getMsisdnString());
        $this->assertEquals('Unknown', $msisdn->getNationalNumberString());
        $this->assertEquals('Unknown', $msisdn->getCountryCodeString());
        $this->assertEquals('Unknown', $msisdn->getCountryIdString());
        $this->assertEquals('Unknown', $msisdn->getMnoString());
        
        $this->assertEquals('Unknown, Unknown, Unknown, Unknown', $msisdn->getFullString());
    }
    
    public function testNonexistentCode()
    {
        $testString = '+425230000';
        $msisdn = new Msisdn($testString);
        $this->assertEquals($testString, $msisdn->getMsisdnGiven());
        $this->assertEquals('425230000', $msisdn->getMsisdn());
        $this->assertEquals(null, $msisdn->getNationalNumber());
        $this->assertEquals(null, $msisdn->getCountryCode());
        $this->assertEquals(null, $msisdn->getCountryId());
        $this->assertEquals(null, $msisdn->getMno());
        
        $this->assertEquals('425230000', $msisdn->getMsisdnString());
        $this->assertEquals('Unknown', $msisdn->getNationalNumberString());
        $this->assertEquals('Unknown', $msisdn->getCountryCodeString());
        $this->assertEquals('Unknown', $msisdn->getCountryIdString());
        $this->assertEquals('Unknown', $msisdn->getMnoString());
        
        $this->assertEquals('Unknown, Unknown, Unknown, Unknown', $msisdn->getFullString());
    }
}