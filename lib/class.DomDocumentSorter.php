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
        $oDocument = clone $p_oDocument;

        $oSorter = new self();
        $oSorter->sortDomDocument($oDocument);

        return $oDocument;
    }

    /**
     * @param \DOMDocument $p_oDomDocument
     */
    public function sortDomDocument(DOMDocument $p_oDomDocument)
    {
        foreach ($p_oDomDocument->childNodes as $t_oDomElement) {
            $this->sortDomElement($t_oDomElement, $p_oDomDocument);
        }
    }

    /**
     * Replace childNodes by sorted childNodes
     *
     * @param DOMElement $p_oDomElement
     * @param DOMDocument $p_oDocument
     */
    protected function sortDomElement(DOMElement $p_oDomElement)
    {
        $this->sortAttributes($p_oDomElement);

        if ($p_oDomElement->hasChildNodes()) {
            $oChildNodes = $p_oDomElement->childNodes;
            $aChildren = array();
            foreach ($oChildNodes as $t_sIndex => $t_oDomElement) {
                if ($t_oDomElement instanceof DOMText) {
                    // Remove empty whitespace
                    $sText = trim($t_oDomElement->textContent);
                    if(empty($sText)){
                        $p_oDomElement->removeChild($t_oDomElement);
                    }
                } else if($t_oDomElement instanceof DOMElement) {
                    $aChildren[] = clone $t_oDomElement;
                    $p_oDomElement->removeChild($t_oDomElement);
                } else {
                    var_dump(get_class($t_oDomElement));
                }
                unset($t_oDomElement);
            }

            // @FIXME: Things seem to go wrong round about here.
            // Either the sorting is wrong or text-nodes muddle things up or the
            // original nodes do not get properly removed and messes up things
            // when replacement get appended.
            $bSorted = usort($aChildren, function (DOMElement $p_oLeft, DOMElement $p_oRight) {
                return strcasecmp($p_oRight->tagName, $p_oLeft->tagName);
            });

            /*
             * We can just add the first child right away
             * Every child after that we compare to that node
             *
             * If the child's tag name is alphabetically lower we move down
             * the chain (if there are any next sibling) until we find a sibling
             * who's name is lower than the current one
             *
             * The same logic applies up the chang for a higher sorting name
             */

            foreach ($aChildren as $t_oDomElement) {
                $this->sortDomElement($t_oDomElement);
                $p_oDomElement->appendChild(clone $t_oDomElement);
            }

        }
    }

    /**
     * replace attributes with sorted attributes
     * @param DOMElement $p_oDomNode
     */
    protected function sortAttributes(DOMElement $p_oDomNode)
    {
        $oDOMNamedNodeMap = $p_oDomNode->attributes;
        if( $oDOMNamedNodeMap instanceof DOMNamedNodeMap){
            /* Remove all attributes and place them back in order */
            $aAttributes = array();
            foreach ($oDOMNamedNodeMap as $t_sNodeName => $t_oAttribute) {
                /** @var DOMAttr $t_oAttribute */
                $aAttributes[$t_oAttribute->name] = $t_oAttribute->value;
                $p_oDomNode->removeAttribute($t_sNodeName);
            }

            ksort($aAttributes);
            foreach ($aAttributes as $t_sAttributeName => $t_sNodeValue) {
                $p_oDomNode->setAttribute($t_sAttributeName, $t_sNodeValue);
            }
        }
    }
}