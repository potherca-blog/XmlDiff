<?php

require 'class.DiffXml.php';

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
