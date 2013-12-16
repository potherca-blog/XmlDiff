<?php
class XmlDiff
{
    public function compare($p_sOne, $p_sTwo)
    {
        $sOne   = file_get_contents($p_sOne);
        $sTwo   = file_get_contents($p_sTwo);

        $aOne = $this->xmlStringToSortedArray($sOne);
        $aTwo = $this->xmlStringToSortedArray($sTwo);
        
        $aInOneButNotInTwo = $this->arrayDiffKeysRecursive($aOne, $aTwo);
        $aInTwoButNotInOne = $this->arrayDiffKeysRecursive($aTwo, $aOne);
    
        return array(
              'In ' . $p_sOne . ' but not in ' . $p_sTwo => $this->diffArrayToXml($aInOneButNotInTwo)
            , 'In ' . $p_sTwo . ' but not in ' . $p_sOne => $this->diffArrayToXml($aInTwoButNotInOne)
        );
        
    }
    
    function diffArrayToXml($p_aDiff){
        if(empty($p_aDiff)){
            $sDiff = '';
        } else {
            $sDiff = $this->arrayToXml($p_aDiff);
        }
        
        return $sDiff;
    }        
    
    /**
     * @author  Gajus Kuizinas <g.kuizinas@anuary.com>
     * @version 1.0.0 (2013 03 19)
     * @url     https://github.com/gajus/flow/blob/master/flow.inc.php
     */
    protected function arrayDiffKeysRecursive(array $aSubjectOne, array $aSubjectTwo) 
    {
        $aDiff = array_diff_key($aSubjectOne, $aSubjectTwo);
        $aIntersect = array_intersect_key($aSubjectOne, $aSubjectTwo);

        foreach ($aIntersect as $t_sKey => $t_mValue) {
            if (is_array($aSubjectOne[$t_sKey]) && is_array($aSubjectTwo[$t_sKey])) {
                $mDifference = $this->arrayDiffKeysRecursive($aSubjectOne[$t_sKey], $aSubjectTwo[$t_sKey]);

                if ($mDifference) {
                    $aDiff[$t_sKey] = $mDifference;
                }
            }
        }

        return $aDiff;
    }
    
    protected function arrayToXml(array $p_aSubject) {
        $p_oXml = new SimpleXMLElement('<?xml version="1.0"?><root></root>');
        
        $oXml = $this->arrayToXmlRecursive($p_aSubject, $p_oXml);
        
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
                $oSubNode = $p_oXml->addChild($t_sKey);
                $this->arrayToXmlRecursive($t_mValue, $oSubNode);
            }
            else {
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
        $this->arraySortByKeysRecursive($aXml);
        return $aXml;
    }
}   
 
    $oXmlDiff = new XmlDiff();
    
    $aDiffOne = $oXmlDiff->compare('1.xml', '2.xml');    
    $aDiffTwo = $oXmlDiff->compare('2.xml', '3.xml');    

    echo '<pre>'
        . 'Diff between 1 and 2: '
        . htmlentities(var_export($aDiffOne, true)) . PHP_EOL
        . 'Diff between 2 and 3: '
        . htmlentities(var_export($aDiffTwo, true))
    ;

#EOF
