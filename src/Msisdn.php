<?php

    namespace MateuszKrasucki\Msisdn-resolver;
    
    class Msisdn 
    {
        private $msisdn_given = null;
        private $msisdn = null;
        private $national_number = null;
        private $country_code = null;
        private $country_id = null;
        private $mno = null;
        
        public function __construct($msisdn_given = null))
        {
        }
        
        public function set($msisdn_given = null)
        {
        }
        
        public function getFullString()
        {
            return $this->getMnoString() 
                    . ", " . $this->getCountryCodeString() 
                    . ", " . $this->getNationalNumberString() 
                    . ", " . $this->getCountryCodeString();
        }
        
        public function getMsisdnGiven()
        {
            return $this->msisdn_given;
        }
        
        public function getMsisdn()
        {
            return $this->mssisdn;
        }
        
        public function getMsisdnString()
        {
            return ($this->msisdn == null ? "Unknown" : $this->msisdn);
        }
        
        public function getNationalNumber()
        {
            return $this->national_number;
        }
        
        public function getNationalNumber()
        {
            return ($this->national_number == null ? "Unknown" : $this->national_number);
        }
        
        public function getCountryCode()
        {
            return $this->country_code;
        }

        public function getCountryCodeString()
        {
            return ($this->country_id == null ? "Unknown" : $this->country_code);
        }
        
        public function getCountryId()
        {
            return $this->country_id;
        }
        
        public function getCountryIdString()
        {
            return ($this->country_id == null ? "Unknown" : $this->country_id);
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
            if($this->msisdn_given != null)
            {
                $msisdn_string = preg_replace('/\s+/', '', $this->msisdn_given);
                $msisdn_pattern = '/[1-9]\d{1,14}/';
                preg_match($msisdn_pattern, $number_string, $matches);
                if($matches)	{
                    $this->msisdn = $matches[0];  
                    return true;
                }
                else  {
                    trigger_error("Provided string " . $this->msisdn_given . " is not valid.", E_USER_WARNING);
                    return false;
                }
            }
            else
            {
                trigger_error("There is no MSISDN string set.", E_USER_WARNING);
                return false;
            }
        }
        
        private function matchCountryCodeAndValidate()
        {
            if($this->msisdn != null)
            {
                $codes_filepath = __DIR__ 
                                    . '/resources/' 
                                    . $this->msisdn[0] . '.json';

                if(file_exists($codes_filepath))
                {
	                $codes = json_decode(file_get_contents($codes_filepath),true)['codes'];
	                foreach ($codes as $code)	
	                {
		                preg_match($code[0], $this->msisdn, $matches);
		                if($matches)	{
			                $this->country_code = $code[1];
			                $this->country_id = $code[2];
			                $this->national_number = substr($msisdn, strlen($countrycode));
			                return true;
		                }
		                else
		                {
		                    trigger_error("MSISDN " . $this->msisdn . " can't be matched with any country pattern.", E_USER_WARNING);
                            return false;
		                }
	                }
                }
                else	
                {
	                trigger_error("MSISDN " . $this->msisdn . " can't be matched with any country pattern.", E_USER_WARNING);
                    return false;
                }
            }
            else
            {
                trigger_error("There is no MSISDN set.", E_USER_WARNING);
                return false;
            }
        }
        
        private function matchMnoAndValidate()
        {
            if($this->country_id != null)
            {
                $mnos_filepath = __DIR__ . '/resources/' . $this->country_id . '.json';

                if(file_exists($mnos_filepath))	
                {
	                $prefixes = json_decode(file_get_contents($mnos_filepath),true)['prefixes'];
	                foreach ($prefixes as $prefix)	
	                {
		                preg_match($prefix[0], $national_number, $matches);
		                if($matches)	
		                {
			                $mno = $prefix[1];
			                return true;
		                }
		                else
		                {
		                    trigger_error("Match for MSISDN " 
		                                    . $this->msisdn
		                                    . " not found in "
		                                    . $this->country_id
		                                    . " "
		                                    . $this->country_code
		                                    . " prefixes data", E_USER_WARNING);
                            return false; 
		                }
	            }
	            else
	            {
		            trigger_error("Prefixed data for " 
		                            . $this->country_id
		                            . " "
		                            . $this->country_code
		                            . "are not present.", E_USER_WARNING);
                    return false; 
	            }
            }
            else	
            {
                trigger_error("Country code is not determined.", E_USER_WARNING);
                return false;          
        }
        
?>