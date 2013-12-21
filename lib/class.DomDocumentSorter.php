<?php

class DomDocumentSorter
{
    /**
     * @param DOMDocument $p_oDocument
     *
     * @return DOMDocument
     */
    static public function sort(DOMDocument $p_oDocument)
    {
        $oSorter = new self();
        return $oSorter->sortDomDocument(clone $p_oDocument);
    }

    /**
     * @param \DOMDocument $p_oSubjectDocument
     *
     * @return \DOMDocument
     */
    public function sortDomDocument(DOMDocument $p_oSubjectDocument)
    {
        $oTargetDocument = new DOMDocument('1.0');
        $oTargetDocument->preserveWhiteSpace = false;
        $oTargetDocument->formatOutput = true;

        $oSubjectElement = $p_oSubjectDocument->documentElement;

        $oTargetElement = $this->copyElement($oSubjectElement, $oTargetDocument);
        $this->copyChildrenSorted($oSubjectElement, $oTargetElement);

        $oTargetDocument->appendChild($oTargetElement);

        // @FIXME: For some reason the $oTargetDocument output is not properly formatted.
        //          The cause for this behaviour needs to be found so the hack below can be removed
        $oPrettyDocument = new DOMDocument('1.0');
        $oPrettyDocument->preserveWhiteSpace = false;
        $oPrettyDocument->formatOutput = true;
        $oPrettyDocument->loadXML($oTargetDocument->saveXML());

        return $oPrettyDocument;
    }

    /**
     * Replace childNodes by sorted childNodes
     *
     * @param DOMElement $p_oSubjectElement
     * @param DOMElement $p_oTargetElement
     *
     * @return \DOMElement
     */
    protected function copyChildrenSorted(DOMElement $p_oSubjectElement, DOMElement $p_oTargetElement)
    {
        if ($p_oSubjectElement->hasChildNodes()) {
            $aChildren = array();
            foreach ($p_oSubjectElement->childNodes as $t_sIndex => $t_aElements) {
                $oElementCopy = $this->copyElement($t_aElements, $p_oTargetElement->ownerDocument);
                if ($t_aElements instanceof DOMText) {
                    $sText = trim($t_aElements->textContent);
                    if(! empty($sText)){
                        $p_oTargetElement->appendChild($oElementCopy);
                    } else {
                        // Ignore empty whitespace
                    }
                } else if($t_aElements instanceof DOMElement) {
                    $aChildren[] = array('copy' => $oElementCopy, 'original' => $t_aElements);
                } else {
                    // @CHECKME: What do we do with comments and others? Just clone everything over?
                    $p_oTargetElement->appendChild($oElementCopy);
                }
                unset($t_aElements);
            }

            $bSorted = usort($aChildren, function (array $p_aLeft, array $p_aRight) {
                    if ($p_aLeft['original'] instanceof DOMElement && $p_aRight['original'] instanceof DOMElement) {
                        return strcasecmp($p_aLeft['original']->tagName, $p_aRight['original']->tagName);
                    } else {
                        return 0;
                    }
            });

            array_reverse($aChildren);
            foreach ($aChildren as $t_aElements) {
                $p_oTargetElement->appendChild($t_aElements['copy']);
                $this->copyChildrenSorted($t_aElements['original'], $t_aElements['copy']);
            }
        }
    }

    /**
     * @param DOMElement $p_oDomNode
     * @param DOMElement $p_oSubjectElement
     */
    protected function copyAttributesSorted(DOMElement $p_oDomNode,DOMElement $p_oSubjectElement)
    {
        $oDOMNamedNodeMap = $p_oDomNode->attributes;
        if( $oDOMNamedNodeMap instanceof DOMNamedNodeMap){
            /* Get all attributes and place them back in order */
            $aAttributes = array();
            foreach ($oDOMNamedNodeMap as $t_sNodeName => $t_oAttribute) {
                /** @var DOMAttr $t_oAttribute */
                $aAttributes[$t_oAttribute->name] = $t_oAttribute->value;
            }

            ksort($aAttributes);
            foreach ($aAttributes as $t_sAttributeName => $t_sNodeValue) {
                $p_oSubjectElement->setAttribute($t_sAttributeName, $t_sNodeValue);
            }
        }
    }

    /**
     * @param DOMNode $p_oSubjectElement
     * @param DOMDocument $p_oDocument
     *
     * @throws Exception
     *
     * @return DOMElement
     */
    protected function copyElement(DOMNode $p_oSubjectElement, DOMDocument $p_oDocument)
    {
        if ($p_oSubjectElement instanceof DOMElement) {
            $oTargetElement = $p_oDocument->createElement(
                $p_oSubjectElement->nodeName, null // Text value is copied over by cloning TextNodes
            );
            $this->copyAttributesSorted($p_oSubjectElement, $oTargetElement);
        } else if ($p_oSubjectElement instanceof DOMText) {
            $oTargetElement = $p_oDocument->createTextNode(trim($p_oSubjectElement->textContent));
        } else {
            throw new Exception(
                  'Nodes of type "' . get_class($p_oSubjectElement) .'" are not yet supported. '
                . 'Please contact the creator of the software and demand support for '
                  . get_class($p_oSubjectElement)
            );
        }

        return $oTargetElement;
    }
}