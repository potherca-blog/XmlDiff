<?php
ob_start();

$sRootPath = __DIR__ . '/..';

require $sRootPath . '/vendor/autoload.php';

$sDisplayMode = 'side-by-side';
$iContext = 3;

if (isset($_POST['display-mode']) && empty($_POST['display-mode']) === false) {
    $sDisplayMode = $_POST['display-mode'];
}

if (isset($_POST['context'])) {
    $iContext = (int) $_POST['context'];
}


try {
    $oController = new Controller();
    $sContent = $oController->getContent($_FILES, ['DisplayMode' => $sDisplayMode, 'Context' => $iContext]);

    $oTemplate = Template::fromFile('template.index.html');

    $sContent = str_replace('<div id="content"></div>', $sContent, $oTemplate->toString());
} catch(Exception $eAny){
    var_dump($eAny);
    $sContent = '<p class="warning">' . $eAny->getMessage() . '</p>';
}
$sBufferedContents = ob_get_contents();

echo $sContent;

#EOF