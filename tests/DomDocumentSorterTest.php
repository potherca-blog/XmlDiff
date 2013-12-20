<?php

namespace Test\Cases
{

    /**
     * Test class for Sorter
     */
    class DomDocumentSorterTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         */
        public function sort_ReturnedDomDocumentContainsSameAmountOfChildNodesAsGivenDomDocument_WhenGivenDomDocument()
        {
            $oDocument = new \DOMDocument();
            $oDocument->loadXML('
                <Root>
                    <item number="1" />
                    <item number="2">Content</item>
                    <item number="3"></item>
                </Root>'
            );

            $oSortedDocument = \DomDocumentSorter::sort($oDocument);
            $this->assertSelectCount('item', 3, $oSortedDocument);
        }

        /**
         * @test
         */
        public function sort_ReturnedDomNodeAttributeValuesAreUnaltered_WhenGivenDomNodeWithUnsortedAttributes()
        {
            /** @var \DOMElement $oDomNode */
            $oDocument = new \DOMDocument();
            $oDocument->loadXML('<Root foo="three" bar="one" baz="" />');

            $oSortedDocument = \DomDocumentSorter::sort($oDocument);

            $oAttributes = $oSortedDocument->firstChild->attributes;
            $aActual = array(
                  $oAttributes->item(0)->nodeValue
                , $oAttributes->item(1)->nodeValue
                , $oAttributes->item(2)->nodeValue
            );

            $aExpected = array('one', '', 'three');

            $this->assertSame($aExpected, $aActual);
        }

        /**
         * @test
         */
        public function sort_ReturnedDomNodeAttributesAreSorted_WhenGivenDomNodeWithUnsortedAttributes()
        {
            /** @var \DOMElement $oDomNode */
            $oDocument = new \DOMDocument();
            $oDocument->loadXML('<Root foo="" bar="" baz="" />');

            $oSortedDocument = \DomDocumentSorter::sort($oDocument);

            $oAttributes = $oSortedDocument->firstChild->attributes;
            $aActual = array(
                  $oAttributes->item(0)->nodeName
                , $oAttributes->item(1)->nodeName
                , $oAttributes->item(2)->nodeName
            );

            $aExpected = array('bar','baz','foo');

            $this->assertSame($aExpected, $aActual);
        }

        /**
         * @test
         */
        public function sort_ReturnedDomNodesAreSorted_WhenGivenDomNodeWithUnsortedNodes()
        {
            /** @var \DOMElement $oDomNode */
            $oDocument = new \DOMDocument();
            $oDocument->loadXML('
                <Root>
                    <Foo/>
                    <Bar/>
                    <Baz/>
                </Root>
            ');

            $oSortedDocument = \DomDocumentSorter::sort($oDocument);

            $oChildNodes = $oSortedDocument->firstChild->childNodes;
            $aActual = array();
            foreach ($oChildNodes as $t_oChild) {
                if ($t_oChild instanceof \DOMElement) {
                    $aActual[] = $t_oChild->tagName;
                }
            }

            $aExpected = array('Bar','Baz','Foo');

            $this->assertSame($aExpected, $aActual);
        }

        /**
         * @test
         */
        public function sort_ReturnedSubNodesAreSorted_WhenGivenDomNodeWithUnsortedNodes()
        {
            /** @var \DOMElement $oDomNode */
            $oDocument = new \DOMDocument();
            $oDocument->loadXML('
                <Root>
                    <Test>
                        <Baz/>
                        <Foo/>
                        <Bar/>
                    </Test>
                </Root>
            ');

            $oSortedDocument = \DomDocumentSorter::sort($oDocument);
            $oChildNodes = $oSortedDocument->getElementsByTagName('Test')->item(0)->childNodes;
            $aActual = array();
            foreach ($oChildNodes as $t_oChild) {
                if ($t_oChild instanceof \DOMElement) {
                    $aActual[] = $t_oChild->tagName;
                }
            }

            $aExpected = array('Bar','Baz','Foo');

            $this->assertSame($aExpected, $aActual);
        }

        /**
         * @test
         */
        public function sort_ReturnedDomDocumentIsSorted_WhenGivenUnsortedDoDocument()
        {
            /** @var \DOMElement $oDomNode */
            $oDocument = new \DOMDocument();

            $oDocument->loadXML('
            <Root>
                <Foo>
                    <D foo="four" bar="">
                        <Boz foo="eight" bar="seven">
                            Some more content
                        </Boz>
                        <Baz foo="" bar="Five">
                            Some more content
                        </Baz>
                    </D>
                    <C>
                        Some content
                    </C>
                </Foo>
                <Bar>
                    <B foz="" foo="two" />
                    <A bar="one"></A>
                </Bar>
            </Root>
            ');
            $oSortedDocument = \DomDocumentSorter::sort($oDocument);

            $sExpected = '<?xml version="1.0"?>
            <Root>
                <Bar>
                    <A bar="one"/>
                    <B foo="two" foz=""/>
                </Bar>
                <Foo>
                    <C>
                        Some content
                    </C>
                    <D bar="" foo="four">
                        <Baz bar="Five" foo="">
                            Some more content
                        </Baz>
                        <Boz bar="seven" foo="eight">
                            Some more content
                        </Boz>
                    </D>
                </Foo>
            </Root>
            ';

            $sActual = $oSortedDocument->saveXML();

            $this->assertSame($sExpected, $sActual);
        }
    }
}

#EOFindex