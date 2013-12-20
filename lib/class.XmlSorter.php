<?php

class XmlSorter
{
    protected $m_aXmlErrors = array();
    protected $m_sOriginalXml;

    /**
     * @return mixed
     */
    public function getXml()
    {
        return $this->m_sOriginalXml;
    }

    /**
     * @param mixed $p_sXml
     */
    public function setXml($p_sXml)
    {
        $this->m_sOriginalXml = (string) $p_sXml;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->m_aXmlErrors;
    }

    static public function forFile($p_sXmlPath)
    {
        if (is_file($p_sXmlPath) === false) {
            throw new InvalidArgumentException('Given path "' . $p_sXmlPath . '"is not a file');
        } else {
            $sXml = file_get_contents($p_sXmlPath);
            $oSorter = new self($sXml);
            $oSorter->setXml($sXml);

            return $oSorter;
        }
    }

    public function __construct($p_sXml='')
    {
        $this->m_sOriginalXml = $p_sXml;
    }

    public function sortXml($p_sXml='')
    {
        if (empty($p_sXml)) {
            $sXml = $this->getXml();
        } else {
            $sXml = $p_sXml;
        }

        $oDocument = $this->xmlStringToDomDocument($sXml);

        return DomDocumentSorter::sort($oDocument)->saveXML();
    }

    /**
     * @param $sXml
     *
     * @return DOMDocument
     */
    protected function xmlStringToDomDocument($sXml)
    {
        $oDocument = new DOMDocument('1.0');
        $oDocument->preserveWhiteSpace = false;
        $oDocument->formatOutput = true;

        $uOriginalErrorHandler = libxml_use_internal_errors(true);
        $bResult = $oDocument->loadXML($sXml);
        $this->m_aXmlErrors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($uOriginalErrorHandler);

        return $oDocument;
    }
}

#EOF
