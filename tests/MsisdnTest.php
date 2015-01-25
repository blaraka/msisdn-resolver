<?php

namespace MateuszKrasucki\MsisdnResolver;

use MateuszKrasucki\MsisdnResolver\Msisdn;

class MsisdnTest extends \PHPUnit_Framework_TestCase
{

    public function testWrongString()
    {
        $testStrings = ['numer', '+001230000'];
        foreach ($testStrings as $testString) {
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
    }
    
    public function testNonexistentCountryCode()
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
    
    
    public function testWithJSONExamples()
    {
        $examplesFilepath = __DIR__
                            . '/resources/test.json';
        $examples = json_decode(file_get_contents($examplesFilepath), true);
        
        foreach ($examples as $example) {
            $msisdn = new Msisdn($example[0]);

            $this->assertEquals($example[0], $msisdn->getMsisdnGiven());
            
            $this->assertEquals(($example[1] == 'Unknown' ? null : $example[1]), $msisdn->getMsisdn());
            $this->assertEquals(($example[2] == 'Unknown' ? null : $example[2]), $msisdn->getCountryCode());
            $this->assertEquals(($example[3] == 'Unknown' ? null : $example[3]), $msisdn->getCountryId());
            $this->assertEquals(($example[4] == 'Unknown' ? null : $example[4]), $msisdn->getNationalNumber());
            $this->assertEquals(($example[5] == 'Unknown' ? null : $example[5]), $msisdn->getMno());
        
            $this->assertEquals($example[1], $msisdn->getMsisdnString());
            $this->assertEquals($example[2], $msisdn->getCountryCodeString());
            $this->assertEquals($example[3], $msisdn->getCountryIdString());
            $this->assertEquals($example[4], $msisdn->getNationalNumberString());
            $this->assertEquals($example[5], $msisdn->getMnoString());
        
            $this->assertEquals(
                ($example[5] . ', ' . $example[2]
                . ', ' . $example[4] . ', ' . $example[3]),
                $msisdn->getFullString()
            );
        }
    }
}
