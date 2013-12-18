<?php
ob_start();

$sRootPath = __DIR__;

require $sRootPath . '/lib/class.XmlSorter.php';
require $sRootPath . '/lib/class.Controller.php';
require $sRootPath . '/lib/class.Template.php';
require $sRootPath . '/vendor/phpspec/php-diff/lib/Diff.php';
require $sRootPath . '/vendor/phpspec/php-diff/lib/Diff/Renderer/Html/SideBySide.php';
require $sRootPath . '/vendor/phpspec/php-diff/lib/Diff/Renderer/Html/Inline.php';

$sDisplayMode = isset($_POST['display-mode']) && empty($_POST['display-mode']) === false
    ? $_POST['display-mode']
    :'side-by-side'
;


try {
    $oController = new Controller();
    $sContent = $oController->getContent($_FILES, $sDisplayMode);

    $oTemplate = Template::fromFile('template.index.html');

    $sContent = str_replace('<div id="content"></div>', $sContent, $oTemplate->toString());
} catch(Exception $eAny){
    var_dump($eAny);
    $sContent = '<p class="warning">' . $eAny->getMessage() . '</p>';
}
$sBufferedContents = ob_get_contents();

echo $sContent;

#EOF