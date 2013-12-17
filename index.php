<!DOCTYPE html>
<?php
    if (isset($_POST['display-mode'])) {
        $sContent = getContent($_FILES, $_POST['display-mode']);
    } else {
        $sContent = getContent($_FILES);
    }
?>
<html>
<head>
    <title>Compare two XML files</title>
	<link rel="stylesheet" href="styles.css" />
</head>
<body>
    <!-- -->
    <form action="" method="post" enctype="multipart/form-data">
        <p>XML Files to compare:</p>
        <p>
            <label>
                Old: 
                <input name="left" type="file" />
            </label>
            <label>
                New:
                <input name="right" type="file" />
            </label>
        </p>
        <p>
            Display Diff
            <label>
                <input type="radio" name="display-mode" value="side-by-side" /> Side-by-side
            </label>
            <label>
                <input type="radio" name="display-mode" value="inline" /> Inline
            </label>
        </p>
        <p>
            <input type="submit" value="Send files" />
        </p>
    </form>
    <!-- -->
    <?= $sContent?>
</body>
</html>
<?php

function getContent(array $p_aUploadedFiles, $p_sDisplayType = 'side-by-side'){
    $sContent = '';
    
    if (empty($p_aUploadedFiles)) {
        // Nothing to do
    } else {
        if ($p_aUploadedFiles['left']['error'] === 4 OR $p_aUploadedFiles['right']['error'] === 4) {
            $sContent = '<p class="warning">Please select 2 XML files to compare</p>';
        } else {
            if (!is_uploaded_file($p_aUploadedFiles['left']['tmp_name']) OR ! is_uploaded_file($p_aUploadedFiles['right']['tmp_name'])) {
                $sContent = '<p class="warning">Uploaded files not legally uploaded</p>';
            } else {
                if ($_FILES['left']['error'] !== UPLOAD_ERR_OK OR $_FILES['right']['error'] !== UPLOAD_ERR_OK) { 
                    $aErrors = array( 
                            //  0 => 'There is no error, the file uploaded with success' 
                              1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini' 
                            , 2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form' 
                            , 3 => 'The uploaded file was only partially uploaded'
                            // , 4 => 'No file was uploaded'
                            , 6 => 'Missing a temporary folder'
                    );
                    $sContent = '<p class="warning">'
                        . 'There was an error uploading the files'
                        . 'Left : ' . $aErrors[$_FILES['left']['error']]
                        . 'Right: ' . $aErrors[$_FILES['right']['error']]
                        . '</p>'
                    ;
                } else {
		            require __DIR__ . '/class.XmlSorter.php';
		            require __DIR__ . '/vendor/phpspec/php-diff/lib/Diff.php';
		            require __DIR__ . '/vendor/phpspec/php-diff/lib/Diff/Renderer/Html/SideBySide.php';
		            require __DIR__ . '/vendor/phpspec/php-diff/lib/Diff/Renderer/Html/Inline.php';

                    $sLeftXml = XmlSorter::forFile($p_aUploadedFiles['left']['tmp_name']);
                    $sRightXml = XmlSorter::forFile($p_aUploadedFiles['right']['tmp_name']);

            		$aLeft = explode("\n", $sLeftXml);
            		$aRight = explode("\n", $sRightXml);

            		$aOptions = array(
		                //'context' => 999,
		                'ignoreNewLines' => true,
		                'ignoreWhitespace' => true,
		                'ignoreCase' => false
                    );

		            // Initialize the diff class
		            $oDiff = new Diff($aLeft, $aRight, $aOptions);

		            // Generate a side by side diff
                    if ($p_sDisplayType === 'side-by-side') {
    		            $oRenderer = new Diff_Renderer_Html_SideBySide;
                    } elseif($p_sDisplayType === 'inline') {
                        $oRenderer = new Diff_Renderer_Html_Inline;
                    } else {
                        throw new InvalidARgumentException('Unknow display type "' . $p_sDisplayType . '"');
                    }

		            $sContent = $oDiff->render($oRenderer);

                }
            }
        }
    }
    
    return $sContent;
}

#EOF
