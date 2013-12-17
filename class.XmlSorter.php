<?php

/**
 * Controller for the web frontend.
 * 
 * 
 */
class XmlSorter
{
    static public function forFile($p_sXmlPath)
    {
        if (is_file($p_sXmlPath) === false) {
            throw new InvalidArgumentException('Giveb path "' . $p_sXmlPath . '"is not a file');
        } else {
            $sXml = file_get_contents($p_sXmlPath);
            $oSorter = new self($sXml);
            return $oSorter->sortXml($sXml);
        }
    }
    
    public function __contruct($p_sXml)
    {
        $this->m_sOriginalXml = $p_sXml;
    }
    
    public function formatXml($p_sXml){
        $oDocument = new DOMDocument('1.0');
        $oDocument->preserveWhiteSpace = false;
        $oDocument->formatOutput = true;
        $oDocument->loadXML($p_sXml);
        return $oDocument->saveXML();
    }
    
    public function sortXml($p_sXml){
        $aSortedXml = $this->xmlStringToSortedArray($p_sXml);
        $sXml = $this->arrayToXml($aSortedXml);
        return $this->formatXml($sXml);
    }
    
    
    protected function arrayToXml(array $p_aSubject) {
        $p_oXml = new SimpleXMLElement('<?xml version="1.0"?><root></root>');        
        $oXml = $this->arrayToXmlRecursive($p_aSubject, $p_oXml);
        //@TODO: Get rid of ROOT node
        return $oXml->asXml();
    }

    /**
     * @url http://pastebin.com/pYuXQWee
     * @see http://stackoverflow.com/a/5965940/153049
     */
    protected function arrayToXmlRecursive($p_aSubject, $p_oXml) {
        foreach($p_aSubject as $t_sKey => $t_mValue) {
            //$t_sKey = (is_numeric($t_sKey) ? 'item':'') . $t_sKey; // Uncomment if you need to fix numeric keys in the array
            if(is_array($t_mValue)) {
                if($t_sKey === '@attributes'){
                    foreach($t_mValue as $t_sAttributeName => $t_sAttributeValue) {
                        $p_oXml->addAttribute($t_sAttributeName, $t_sAttributeValue);
                    }
                } else {
                    $oSubNode = $p_oXml->addChild($t_sKey);
                    $this->arrayToXmlRecursive($t_mValue, $oSubNode);
                }
            } else {
                $p_oXml->addChild($t_sKey, $t_mValue);
            }
        }
        
        return $p_oXml;
    }

    protected function xmlToArray($p_sXml){
        $xml = simplexml_load_string($p_sXml);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        
        return $array;
    }
    
    protected function arraySortByKeysRecursive($p_aSubject){
        ksort($p_aSubject);
        foreach($p_aSubject as $t_sKey => $t_mValue){
            if(is_array($t_mValue)){
                $p_aSubject[$t_sKey] = $this->arraySortByKeysRecursive($t_mValue);
            }
        }
        
        return $p_aSubject;
    }
    
    protected function xmlStringToSortedArray($p_sXmlString){
        $aXml = $this->xmlToArray($p_sXmlString);
        $aXml = $this->arraySortByKeysRecursive($aXml);
        return $aXml;
    }
}   

#EOF
