<?php

namespace MateuszKrasucki\MsisdnResolver;

openlog("MsisdnResolverLog", LOG_PID | LOG_PERROR, LOG_LOCAL0);
    
class Msisdn
{
    private $msisdnGiven = null;
    private $msisdn = null;
    private $nationalNumber = null;
    private $countryCode = null;
    private $countryId = null;
    private $mno = null;
    
    public function __construct($msisdnGiven = null)
    {
        if ($msisdnGiven != null) {
            $this->set($msisdnGiven);
        }
    }
    
    public function set($msisdnGiven)
    {
        $this->msisdnGiven = $msisdnGiven;
        if (!$this->processAndValidateMsisdnGiven()) {
            return false;
        }
        
        if (!$this->matchCountryCodeAndValidate()) {
            return false;
        }
        
        if (!$this->matchMnoAndValidate()) {
            return false;
        }
        
        return true;
    }
    
    public function getFullString()
    {
        return $this->getMnoString()
            . ", " . $this->getCountryCodeString()
            . ", " . $this->getNationalNumberString()
            . ", " . $this->getCountryIdString();
    }
    
    public function getMsisdnGiven()
    {
        return $this->msisdnGiven;
    }
    
    public function getMsisdn()
    {
        return $this->msisdn;
    }
    
    public function getMsisdnString()
    {
        return ($this->msisdn == null ? "Unknown" : $this->msisdn);
    }
    
    public function getNationalNumber()
    {
        return $this->nationalNumber;
    }
    
    public function getNationalNumberString()
    {
        return ($this->nationalNumber == null ? "Unknown" : $this->nationalNumber);
    }
    
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public function getCountryCodeString()
    {
        return ($this->countryId == null ? "Unknown" : $this->countryCode);
    }
    
    public function getCountryId()
    {
        return $this->countryId;
    }
    
    public function getCountryIdString()
    {
        return ($this->countryId == null ? "Unknown" : $this->countryId);
    }
    
    public function getMno()
    {
        return $this->mno;
    }
    
    public function getMnoString()
    {
        return ($this->mno == null ? "Unknown" : $this->mno);
    }
    
    private function processAndValidateMsisdnGiven()
    {
        if ($this->msisdnGiven != null) {
            $msisdnString = preg_replace('/\s+/', '', $this->msisdnGiven);
            $msisdnPattern = '/[1-9]\d{1,14}/';
            preg_match($msisdnPattern, $msisdnString, $matches);
            if ($matches) {
                $this->msisdn = $matches[0];
                return true;
            } else {
                syslog(LOG_WARNING, "Provided string " . $this->msisdnGiven
                                    . " is not valid.");
                return false;
            }
        } else {
            syslog(LOG_WARNING, "There is no MSISDN string set.");
            return false;
        }
    }
    
    private function matchCountryCodeAndValidate()
    {
        if ($this->msisdn != null) {
            $codesFilepath = __DIR__
                            . '/resources/'
                            . $this->msisdn[0] . '.json';

            if (file_exists($codesFilepath)) {
                $codes = json_decode(file_get_contents($codesFilepath), true)['codes'];
                foreach ($codes as $code) {
                    preg_match($code[0], $this->msisdn, $matches);
                    if ($matches) {
                        $this->countryCode = $code[1];
                        $this->countryId = $code[2];
                        $this->nationalNumber = substr($this->msisdn, strlen($this->countryCode));
                        return true;
                    }
                }
                syslog(LOG_WARNING, "MSISDN " . $this->msisdn
                                    . " can't be matched with any country pattern.");
                return false;
            } else {
                syslog(LOG_WARNING, "MSISDN " . $this->msisdn
                                . " can't be matched with any country pattern.");
                return false;
            }
        } else {
            syslog(LOG_WARNING, "There is no MSISDN set.");
            return false;
        }
    }
    
    private function matchMnoAndValidate()
    {
        if ($this->countryId != null) {
            $mnosFilepath = __DIR__ . '/resources/' . $this->countryId . '.json';

            if (file_exists($mnosFilepath)) {
                $prefixes = json_decode(file_get_contents($mnosFilepath), true)['prefixes'];
                foreach ($prefixes as $prefix) {
                    preg_match($prefix[0], $this->nationalNumber, $matches);
                    if ($matches) {
                        $this->mno = $prefix[1];
                        return true;
                    }
                }
                syslog(LOG_WARNING, "Match for MSISDN "
                                . $this->msisdn
                                . " not found in "
                                . $this->countryId
                                . " "
                                . $this->countryCode
                                . " mno data.");
                return false;
            } else {
                syslog(LOG_WARNING, "MNO data for "
                                . $this->countryId
                                . " "
                                . $this->countryCode
                                . " are not present.");
                return false;
            }
        } else {
            syslog(LOG_WARNING, "Country code is not determined.");
            return false;
        }
    }
}
