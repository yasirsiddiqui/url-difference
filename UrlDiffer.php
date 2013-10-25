<?php

 /**
 * PHP Calculate difference between two URLs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */   

    const SCHEME_DELIM  =  "://";
    const UNIX_SLASH = '/';
    const PATH_DELIM = '?';
    const PARAM_DELIM = '&';
    const NAME_VAL_DELIM = '=';
    const LINE_BREAK = '<br>';

class UrlDiffer {
    
    private $url1;
    private $url2;
    private $differences;
    private $differ_hostname;
    private $hostname1;
    private $hostname2;
    
    
    /**
         * Function Constructor
         *
         * @param $url1 // First url to be compared
         *
         * @param $url2 // Second Url to be compared
    */
    
    public function __construct($url1,$url2,$differ_hostname = false) {
        
        // Assign values to private members
        $this->differences = array();
        $this->differ_hostname = $differ_hostname;
        $this->url1 = $url1;
        $this->url2 = $url2;
    }
    
    /**
         * Function getDifference
         *
         * Calculate difference between two urls passed in constructor
         *
    */
    
    public function getDifference() {
        
         $this->do_Difference();
    }
    
    /**
         * Function do_Difference
         *
         * Calculate difference between two urls passed in constructor
         *
    */
    private function do_Difference() {
        
        // Check if host name difference is also required?
        if($this->differ_hostname) {
            // Split out host names
            $this->hostname1 = $this->getHostName($this->url1);
            $this->hostname2 = $this->getHostName($this->url2);
            // Get difference between host names
            $this->diffHostNames($this->hostname1,$this->hostname2);
        }
        // Parse url params
        $url1params = $this->parseUrlParams($this->url1);
        $url2params = $this->parseUrlParams($this->url2);
        // Calculate difference in Params
        $this->calcParamsDiff($url1params,$url2params);
        // Format difference to be shown as user freindly
        $this->formatAllDifferences($this->differences);
    }
    
     /**
         * Function formatAllDifferences
         *
         * Prints difference values
         *
         * @param $diff // An array containg differences values
         *
    */
    private function formatAllDifferences($diff) {
 
        foreach($diff as $value) {
            echo $value;
        }
    }
    
     
     /**
         * Function calcParamsDiff
         *
         * Calculates difference in url parameters
         *
         * @param $url1params First url // First url parameters
         *
         * @param $url2params Second url // Second url parameters 
         *
    */
    private function calcParamsDiff($url1params,$url2params) {
        
        foreach($url1params as $paramname=>$paramvalue) {
            if(key_exists($paramname,$url2params)){
                if($paramvalue != $url2params[$paramname]) {
                   $this->formatParamsDifference($paramname,$paramvalue,$url2params[$paramname]);
                }
            }
            else {
                $this->formatParamsDifference($paramname,$paramvalue);
            }
        }
        
        foreach($url2params as $paramname=>$paramvalue) {
            if(!key_exists($paramname,$url1params)){
                $this->formatParamsDifference($paramname,"",$paramvalue);
            }
        }
    }
    
    /**
         * Function parseUrlParams
         *
         * Gets parameters from a give url
         *
         * @param $url Url // Url for parsing perameters
         *
    */
    private function parseUrlParams($url) {
        
        $params = array();
        if(strpos($url,PATH_DELIM)===false){
            return $params;
        }
        else {
            
            $param_pos = strpos($url,PATH_DELIM)+1;
            $pieces = explode(PARAM_DELIM,substr($url,$param_pos));
            foreach($pieces as $token) {
                if(empty($token))
                    continue;
                else if(strpos($token,NAME_VAL_DELIM)===false){
                    $params[$token] = "";
                }
                else{
                    $values = explode(NAME_VAL_DELIM,$token);
                    $params[$values[0]] = $values[1];
                } 
            }
        }
        return $params;
    }
    
    /**
         * Function formatParamsDifference
         *
         * Formats differences
         *
         * @param $key // Array key or the url parameter
         *
         * @param $keyvalueurl1 // Value of the key in first url
         *
         * @param $keyvalueurl2 // Value of the key in second url
         *
    */
    private function formatParamsDifference($key,$keyvalueurl1 = "",$keyvalueurl2 = "") {
        
       // Format difference string 
       $difference = "Key: ".$key.LINE_BREAK;
       if($keyvalueurl1)
        $difference.= "In First URL Key '".$key."' = ".$keyvalueurl1.LINE_BREAK;
       if($keyvalueurl2) 
        $difference.= "In Second URL Key '".$key."' = ".$keyvalueurl2.LINE_BREAK;
        // Push difference value to array
         $this->differences[] = $difference;
    }
    
    /**
         * Function formatHostNameDifference
         *
         * Formats differences
         *
         * @param $hostname1 // Hostname of first url
         *
         * @param $hostname2 // Hostname of second url
         *
    */
    private function formatHostNameDifference($hostname1,$hostname2) {
        
        // Format difference string
        $difference = "HostName:".LINE_BREAK."First URL: ".$hostname1.LINE_BREAK."Second URL: ".$hostname2.LINE_BREAK;
        // Push difference value to array
        $this->differences[] = $difference;
    }
    
    /**
         * Function diffHostNames
         *
         * Calcualtes if host names rpovided are different
         *
         * @param $hostname1 // Hostname of first url
         *
         * @param $hostname2 // Hostname of second url
         *
    */
    private function diffHostNames($hostname1,$hostname2) {
        
        if($hostname1 != $hostname2) {
            $this->formatHostNameDifference($hostname1,$hostname2);
        }
    }
    
    /**
         * Function getHostName
         *
         * Extracts hostname from give url
         *
         * @param $url // Url
         *
         *
    */
    private function getHostName($url) {
        
        
        if(strpos($url,SCHEME_DELIM)!==false) {
            $index = strpos($url,SCHEME_DELIM);
            $hostbegin = $index + strlen(SCHEME_DELIM);
        }
        else {
            $hostbegin = 0;
        }
                
        if(strpos(substr($url,$hostbegin,strlen($url)),UNIX_SLASH)!==false) {
            
            $hostend = strpos($url,UNIX_SLASH,$hostbegin); 
        }
        else {
            
            if(strpos(substr($url,$hostbegin,strlen($url)),PATH_DELIM)!==false) {
                $hostend = strpos($url,PATH_DELIM,$hostbegin); 
            }
            else {
                $hostend = strlen(substr($url,$hostbegin))+1;    
            }
            
        }
        
        return substr($url,$hostbegin,$hostend-$hostbegin);
    } 
}
?>