<?php
class Controller
{
    protected $m_aErrors = array();

    function getContent(array $p_aUploadedFiles, $p_sDisplayType)
    {
        $sContent = '';

        $sValid = $this->validateFile($p_aUploadedFiles);

        if ($sValid === false) {
            // Nothing to do
            $sContent = '';//<p class="match">Files are identical</p>';
        } else if (is_string($sValid)) {
            $sContent = '<p class="warning">' . $sValid . '</p>';
        } else if ($sValid === true) {
            $oLeftXml = XmlSorter::forFile($p_aUploadedFiles['left']['tmp_name']);
            $sLeftXml = $oLeftXml->sortXml();
            if ($sLeftXml === false) {
                $this->addErrors($oLeftXml->getErrors(), $p_aUploadedFiles['left']['name']);
            }

            $oRightXml = XmlSorter::forFile($p_aUploadedFiles['right']['tmp_name']);
            $sRightXml = $oRightXml->sortXml();
            if ($sRightXml === false) {
                $this->addErrors($oRightXml->getErrors(), $p_aUploadedFiles['right']['name']);
            }

            if (empty($this->m_aErrors) === false) {
                $sContent = '<p class="warning">Invalid XML Content</p>'
                    . '<ul><li>'
                    . implode('</li><li>', $this->m_aErrors)
                    . '</li></ul>'
                ;
            } else {
                $sContent = $this->getDiff($sLeftXml, $sRightXml, $p_sDisplayType);
            }

        }

        return $sContent;
    }

    protected function addErrors(array $p_aErrors, $p_sFile)
    {
        foreach($p_aErrors as $t_uError){
            /** @var LibXMLError $t_uError */
            $this->m_aErrors[] = 'Error in file "' . $p_sFile . '":'
                . '<strong>' . $t_uError->message . '</strong>'
                . ' on line ' . $t_uError->line
                . ' , column ' . $t_uError->column
                . ' (code ' . $t_uError->code . ', level ' . $t_uError->level .')'
            ;
        }
    }

    /**
     * @param array $p_aUploadedFiles
     *
     * @return string
     */
    protected function validateFile(array $p_aUploadedFiles)
    {
        if (empty($p_aUploadedFiles)) {
            $sContent = false;
        } else if ($p_aUploadedFiles['left']['error'] === 4 OR $p_aUploadedFiles['right']['error'] === 4) {
            $sContent = 'Please select 2 XML files to compare';
        } else if (!is_uploaded_file($p_aUploadedFiles['left']['tmp_name'])
            OR !is_uploaded_file($p_aUploadedFiles['right']['tmp_name'])
        ) {
            $sContent = 'Uploaded files not legally uploaded';
        } else if ($_FILES['left']['error'] !== UPLOAD_ERR_OK OR $_FILES['right']['error'] !== UPLOAD_ERR_OK) {
            $aErrors = array(
                //  0 => 'There is no error, the file uploaded with success'
                  1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini'
                , 2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'
                , 3 => 'The uploaded file was only partially uploaded'
                // , 4 => 'No file was uploaded'
                , 6 => 'Missing a temporary folder'
            );
            $sContent = ''
                . 'There was an error uploading the files'
                . 'Left : ' . $aErrors[$_FILES['left']['error']]
                . 'Right: ' . $aErrors[$_FILES['right']['error']]
                . '';
        } else if ($p_aUploadedFiles['left']['size'] === 0 OR $p_aUploadedFiles['right']['size'] === 0) {
            $sContent = 'One (or both) of the uploaded files did not contain any content';
        } else {
            $sContent = true;
        }
        return $sContent;
    }

    /**
     * @param $sLeftXml
     * @param $sRightXml
     * @param $p_sDisplayType
     *
     * @return mixed|string
     * @throws InvalidARgumentException
     */
    protected function getDiff($sLeftXml, $sRightXml, $p_sDisplayType)
    {
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
        } elseif ($p_sDisplayType === 'inline') {
            $oRenderer = new Diff_Renderer_Html_Inline;
        } else {
            throw new InvalidARgumentException('Unknow display type "' . $p_sDisplayType . '"');
        }

        $sContent = $oDiff->render($oRenderer);
        if ($sContent === '') {
            $sContent = '<p class="match">Files are identical</p>';

        }

        return $sContent;
    }
}

#EOF