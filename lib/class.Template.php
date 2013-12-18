<?php

class Template extends DOMDocument
{
    /**
     * @var DomXPath
     */
    protected $m_oFinder;

    /**
     * @return DomXPath
     */
    public function getFinder()
    {
        if(!isset($this->m_oFinder))
        {
            $this->m_oFinder = new DomXPath($this);
        }#if

        return $this->m_oFinder;
    }

    /**
     * @return DOMElement
     */
    public function getHead()
    {
        return $this->getElementsByTagName('head')->item(0);
    }

    /**
     * @return DOMElement
     */
    public function getBody()
    {
        return $this->getElementsByTagName('body')->item(0);
    }

    /**
     * @param string $sFile
     *
     * @return self
     */
    static public function fromFile($p_sFile)
    {
        $sFile = __DIR__ . '/' . $p_sFile;
        if(is_file($sFile) === false){
            throw new InvalidArgumentException('Given template file "' . $sFile . '" does not exist');
        } else {
            /** @var Template $oTemplate */
            $oTemplate = new static();
            $oTemplate->formatOutput = true;
            $oTemplate->loadHTMLFile($sFile);
            return $oTemplate;
        }
    }

    /**
     * @param $p_sClassName
     *
     * @return DOMElement|null
     */
    public function getFirstElementWithClassName($p_sClassName)
    {
        $oDOMNodeList = $this->getElementsByClassName($p_sClassName);
        return self::getFirstElementFromList($oDOMNodeList);
    }

    /**
     * @param $p_sTagName
     *
     * @return DOMElement|null
     */
    public function getFirstElementByTagName($p_sTagName)
    {
        $oDOMNodeList = $this->getElementsByTagName($p_sTagName);
        return self::getFirstElementFromList($oDOMNodeList);
    }

    protected static function getFirstElementFromList(DOMNodeList $p_oDOMNodeList)
    {
        $oNode = null;

        if ($p_oDOMNodeList->length > 0) {
            $oNode = $p_oDOMNodeList->item(0);
        }

        return $oNode;
    }

    /**
     * @param $p_sClassName
     *
     * @return DOMNodeList
     */
    public function getElementsByClassName($p_sClassName)
    {
        //@FIXME: The XPath is not stringent enough. If you look for class 'foo' then class "bar foobar" will also be returned
        return $this->getFinder()->query("//*[contains(@class, '$p_sClassName')]");
    }

    /**
     * @param $p_sPath
     *
     * @return DOMNodeList
     */
    public function getElementsByPath($p_sPath)
    {
        return $this->getFinder()->query($p_sPath);
    }

    /**
     * @param DOMElement $p_oDomNode
     * @return DOMNodeList
     */
    public function removeChildrenFromNode(DOMElement $p_oDomNode)
    {
        //@TODO: Add removed children to a DOMNodeList and return that.
        if ($p_oDomNode->hasChildNodes()) {
            $oChildNodes = $p_oDomNode->childNodes;

            while ($oChildNodes->length > 0) {
                $p_oDomNode->removeChild($oChildNodes->item(0));
            }#while
        }#if
    }

    /**
     * @param string $p_sTagName
     * @param string $p_sTextValue
     * @param array  $p_aAttributes
     *
     * @return DOMElement
     */
    public function createElementWithAttributes($p_sTagName, $p_sTextValue = null, array $p_aAttributes)
    {
        $oDomElement = $this->createElement($p_sTagName, $p_sTextValue);

        foreach($p_aAttributes as $p_sTagName => $attributeValue){
            $oDomElement->setAttribute($p_sTagName, $attributeValue);
        }#foreach

        return $oDomElement;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->saveHTML();
    }
}

#EOF
