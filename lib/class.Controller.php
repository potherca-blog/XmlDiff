<?php

use Phalcon\Diff;
use Phalcon\Diff\Renderer\Html\BaseArray;
use Phalcon\Diff\Renderer\Html\Inline;
use Phalcon\Diff\Renderer\Html\SideBySide;
use Phalcon\Diff\Renderer\RenderInterface;

class Controller
{
    protected $m_aErrors = array();

    function getContent(array $p_aUploadedFiles, array $p_aOptions)
    {
        $sContent = '';

        $sValid = $this->validateFile($p_aUploadedFiles);

        if ($sValid === false) {
            // Nothing to do
            $sContent = '';//<p class="match">Files are identical</p>';
        } else if (is_string($sValid)) {
            $sContent = '<p class="error-warning">' . $sValid . '</p>';
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
                $sContent = '<p class="error-warning">Invalid XML Content</p>'
                    . '<ul><li>'
                    . implode('</li><li>', $this->m_aErrors)
                    . '</li></ul>'
                ;
            } else {
                $sDisplayType = $p_aOptions['DisplayMode'];
                $iContext = $p_aOptions['Context'];
                $bIgnoreCase = $p_aOptions['IgnoreCase'];

                $aOptions = array(
                    'context' => $iContext,
                    'ignoreCase' => $bIgnoreCase,
                    'ignoreNewLines' => true,
                    'ignoreWhitespace' => true,
                );

                $oDiff = $this->createDiff($sLeftXml, $sRightXml, $aOptions);
                $oHtmlRenderer = $this->createRenderer($sDisplayType);
                $oBaseArray = new BaseArray();

                $sContent = '';
                $sContent .= $this->getContentFromBaseArray($oDiff, $oBaseArray);
                $sContent .= $this->getContentFromRenderer($oDiff, $oHtmlRenderer);
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
     * @throws InvalidArgumentException
     */
    private function createDiff($p_sLeftXml, $p_sRightXml, $p_aOptions)
    {
        $aLeft = explode("\n", $p_sLeftXml);
        $aRight = explode("\n", $p_sRightXml);

        return new Diff($aLeft, $aRight, $p_aOptions);
    }

    private function createRenderer ($p_sDisplayType)
    {
        // Generate a side by side diff
        if ($p_sDisplayType === 'side-by-side') {
            $oRenderer = new SideBySide();
        } elseif ($p_sDisplayType === 'inline') {
            $oRenderer = new Inline();
        } else {
            throw new InvalidArgumentException('Unknow display type "' . $p_sDisplayType . '"');
        }

        return $oRenderer;
    }

    protected function getContentFromBaseArray(Diff $p_oDiff, BaseArray $p_oBaseArray)
    {
        $aChanges = $p_oDiff->render($p_oBaseArray);

        $aChangeCount = [
            'delete' => 0,
            'insert' => 0,
            'replace' => 0,
        ];

        foreach($aChanges[0] as $aChange){
            $sChangeType = $aChange['tag'];
            if ($sChangeType !== 'equal') {
                $aChangeCount[$sChangeType]++;
            }
        }

        $sTemplate = <<<HTML
            <div class="change-count callout text-center">
                <span class="label"><span class="secondary badge">%s</span> Total Differences</span>
                <span class="secondary label"><span class="success badge">%s</span> Additions</span>
                <span class="secondary label"><span class="warning badge">%s</span> Changes</span>
                <span class="secondary label"><span class="alert badge">%s</span> Deletes</span>
            </div>
HTML;

        $sContent = sprintf(
            $sTemplate,
            array_sum($aChangeCount),
            $aChangeCount['insert'],
            $aChangeCount['replace'],
            $aChangeCount['delete']
        );

        return $sContent;
    }

    protected function getContentFromRenderer(Diff $p_oDiff, RenderInterface $p_oRenderer)
    {
        $sContent = $p_oDiff->render($p_oRenderer);

        if ($sContent === '') {
            $sContent = '<p class="match">Files are identical</p>';

        }

        return $sContent;
    }
}

#EOF
