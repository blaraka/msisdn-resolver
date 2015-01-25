<?php
/*
 * MsisdnResolver package for PHP.
 * A simple PHP package for interpreting MSISDN numbers. 
 *
 * (c) Mateusz Krasucki 2015
 *
 * License in LICENSE file in root directory of repository.
 */

namespace MateuszKrasucki\MsisdnResolver;
    
class Msisdn
{
    private $msisdnGiven = null;
    private $msisdn = null;
    private $nationalNumber = null;
    private $countryCode = null;
    private $countryId = null;
    private $mno = null;
    
    /**
     * Constructor.
     *
     * @param string $msisdnGiven
     *
     * @return Msisdn class object
     */
    public function __construct($msisdnGiven = null)
    {
        //Opening log for warnings.
        openlog("MsisdnResolverLog", LOG_PID | LOG_PERROR, LOG_LOCAL0);
        if ($msisdnGiven != null) {
            $this->set($msisdnGiven);
        }
    }
    
    /**
     * Setting new $msisdnGiven. Identifcation procedure starter.
     *
     * @param string $msisdnGiven
     *
     * @return boolean true if every step of identifcation and validation was succesful
     */
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

    /**
     * @return string in format "MNO, country code, national number, country ISO-2 id
     */
    public function getFullString()
    {
        return $this->getMnoString()
            . ", " . $this->getCountryCodeString()
            . ", " . $this->getNationalNumberString()
            . ", " . $this->getCountryIdString();
    }

    /**
     * @return original provided msisdn string or null if not set
     */
    public function getMsisdnGiven()
    {
        return $this->msisdnGiven;
    }
    
    /**
     * @return processed msisdn string or null if not determined
     */
    public function getMsisdn()
    {
        return $this->msisdn;
    }

    /**
     * @return processed msisdn string or "Unknown" if not determined
     */
    public function getMsisdnString()
    {
        return ($this->msisdn == null ? "Unknown" : $this->msisdn);
    }

    /**
     * @return national number string or null if not determined
     */
    public function getNationalNumber()
    {
        return $this->nationalNumber;
    }

    /**
     * @return national number string or "Unknown" if not determined
     */
    public function getNationalNumberString()
    {
        return ($this->nationalNumber == null ? "Unknown" : $this->nationalNumber);
    }

    /**
     * @return country dialing code string or null if not determined
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return country dialing code string or "Unknown" if not determined
     */
    public function getCountryCodeString()
    {
        return ($this->countryId == null ? "Unknown" : $this->countryCode);
    }
    
    /**
     * @return ISO 3166-1 alpha-2 country symbol string or null if not detrmined
     */
    public function getCountryId()
    {
        return $this->countryId;
    }
    
    /**
     * @return ISO 3166-1 alpha-2 country symbol string or "Unknown" if not detrmined
     */
    public function getCountryIdString()
    {
        return ($this->countryId == null ? "Unknown" : $this->countryId);
    }
    
    /**
     * @return mobile network operator string or null if not detrmined
     */
    public function getMno()
    {
        return $this->mno;
    }

    /**
     * @return mobile network operator string or "Unknown" if not detrmined
     */
    public function getMnoString()
    {
        return ($this->mno == null ? "Unknown" : $this->mno);
    }
    
    private function processAndValidateMsisdnGiven()
    {
        if ($this->msisdnGiven != null) {
            $msisdnString = preg_replace('/\s+/', '', $this->msisdnGiven);
            $msisdnPattern = '/^[+]?[^0][1-9]\d{1,14}$/';
            //matching general msisdn pattern
            preg_match($msisdnPattern, $msisdnString, $matches);
            if ($matches) {
                //removing +
                $this->msisdn = preg_replace('/\+/', '', $matches[0]);
                return true;
            } else {
                syslog(
                    LOG_WARNING,
                    "Provided string " . $this->msisdnGiven
                    . " is not valid."
                );
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
            //checking if proper country codes patterns file exists
            if (file_exists($codesFilepath)) {
                //loading country codes patterns data for specific zone
                $codes = json_decode(file_get_contents($codesFilepath), true)['codes'];
                foreach ($codes as $code) {
                    //checking if current pattern matches
                    preg_match($code[0], $this->msisdn, $matches);
                    if ($matches) {
                        $this->countryCode = $code[1];
                        $this->countryId = $code[2];
                        $this->nationalNumber = substr($this->msisdn, strlen($this->countryCode));
                        return true;
                    }
                }
                syslog(
                    LOG_WARNING,
                    "MSISDN " . $this->msisdn
                    . " can't be matched with any country pattern."
                );
                return false;
            } else {
                syslog(
                    LOG_WARNING,
                    "MSISDN " . $this->msisdn
                    . " can't be matched with any country pattern."
                );
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
            //checking if mno patterns file for country exists
            if (file_exists($mnosFilepath)) {
                //loading mno patterns data for specific zone
                $prefixes = json_decode(file_get_contents($mnosFilepath), true)['prefixes'];
                foreach ($prefixes as $prefix) {
                    //checking if current pattern matches
                    preg_match($prefix[0], $this->nationalNumber, $matches);
                    if ($matches) {
                        $this->mno = $prefix[1];
                        return true;
                    }
                }
                syslog(
                    LOG_WARNING,
                    "Match for MSISDN " . $this->msisdn
                    . " not found in ". $this->countryId
                    . " " . $this->countryCode . " mno data."
                );
                return false;
            } else {
                syslog(
                    LOG_WARNING,
                    "MNO data for " . $this->countryId
                    . " " . $this->countryCode . " are not present."
                );
                return false;
            }
        } else {
            syslog(LOG_WARNING, "Country code is not determined.");
            return false;
        }
    }
}
