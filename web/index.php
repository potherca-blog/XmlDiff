<?php
ob_start();

$sRootPath = __DIR__ . '/..';

require $sRootPath . '/vendor/autoload.php';

$aOptions = [
    'Context' => 3,
    'DisplayMode' => 'inline',
    'IgnoreCase' => true,
];

if (isset($_POST['context']) && is_numeric($_POST['context'])) {
    $aOptions['Context'] = (int) $_POST['context'];
}

if (isset($_POST['side-by-side'])) {
    $aOptions['DisplayMode'] = 'side-by-side';
}

if (isset($_POST['case-sensitive'])) {
    $aOptions['IgnoreCase'] = false;
}

try {
    $oController = new Controller();
    $sContent = $oController->getContent($_FILES, $aOptions);

    $oTemplate = Template::fromFile('template.index.html');

    $oInputList = $oTemplate->getElementsByTagName('input');

    $aTargetNodes = [
        'case-sensitive' => 'IgnoreCase',
        'context' => 'Context',
        'side-by-side' => 'DisplayMode',
    ];

    foreach($oInputList as $t_oDomElement) {
        $sNodeName = $t_oDomElement->getAttribute('name');
        if (array_key_exists($sNodeName, $aTargetNodes)) {
            $sNodeType = $t_oDomElement->getAttribute('type');

            if ($sNodeType === 'checkbox' && isset($_POST[$sNodeName])) {
                $t_oDomElement->setAttribute('checked', true);
            } else {
                $sKey = $aTargetNodes[$sNodeName];
                $t_oDomElement->setAttribute('value', $aOptions[$sKey]);
            }
        }
    }

    $sContent = str_replace('<div id="content"></div>', $sContent, $oTemplate->toString());

} catch(Exception $eAny){
    $sContent = '<p class="warning">' . $eAny->getMessage() . '</p>';
}
$sBufferedContents = ob_get_contents();

echo $sContent;

#EOF