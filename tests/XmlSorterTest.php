<?php

namespace Test\Cases
{
    use XmlSorter;

    /**
     * Test class for XmlSorter
     */
    class XmlSorterTest extends \PHPUnit_Framework_TestCase
    {
        protected $m_sXml = '<Root>
                <Container>
                    <item number="1" />
                    <item number="2">Content</item>
                    <item number="3"></item>
                </Container>
            </Root>';

        /** @var  XmlSorter */
        private $xmlSorter;

        /**
         * @return XmlSorter
         */
        public function getXmlSorter()
        {
            return $this->xmlSorter;
        }

        /**
         * @param \XmlSorter $newXmlSorter
         */
        public function setXmlSorter(XmlSorter $newXmlSorter)
        {
            $this->xmlSorter = $newXmlSorter;
        }

        public function setUp()
        {
            $this->setXmlSorter(new XmlSorter());
        }

        /**
         * @ test
         */
        public function formatXml_ReturnedXmlContainsSameAmountOfItemsAsGivenXml_WhenGivenXml()
        {
            $oXmlSorter = $this->getXmlSorter();
            $sFormattedXml = $oXmlSorter->formatXml($this->m_sXml);
            $this->assertSelectCount('item', 3, $sFormattedXml);
        }

        /**
         * @ test
         */
        public function sortXml_ReturnedXmlContainsSameAmountOfItemsAsGivenXml_WhenGivenXml()
        {
            $oXmlSorter = $this->getXmlSorter();
            $sFormattedXml = $oXmlSorter->sortXml($this->m_sXml);
            var_export($sFormattedXml);
            $this->assertSelectCount('item', 3, $sFormattedXml);
        }

        /**
         * @ test
         */
        public function xmlStringToSortedArray_ReturnedArrayContainsSameAmountOfItemsAsGivenXml_WhenGivenXml()
        {
            $oXmlSorter = $this->getXmlSorter();
            $aSortedXml = $oXmlSorter->sortXml($this->m_sXml);
            var_dump($aSortedXml);

        }

        /**
         * @ test
         */
        public function xmlStringToArray_ReturnedArrayContainsSameAmountOfItemsAsGivenXml_WhenGivenXml()
        {
            $oXmlSorter = $this->getXmlSorter();
            $aSortedXml = $oXmlSorter->xmlStringToArray($this->m_sXml);
            var_export($aSortedXml);

        }

        /**
         * @test
         */
        public function sortDomNodes_ReturnedDomNodeAttributesAreSorted_WhenGivenDomNodeWithUnsortedAttributes()
        {
            /** @var \DOMElement $oDomNode */
            $oDocument = new \DOMDocument();
            $oDocument->loadXML('<Root foo="" bar="" baz="" />');
            $oXmlSorter = $this->getXmlSorter();
            $oDomNode = $oDocument->getElementsByTagName('Root')->item(0);
            $oXmlSorter->sortDomNode($oDomNode);

            $oAttributes = $oDomNode->attributes;
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
        public function sortDomNodes_ReturnedDomNodesAreSorted_WhenGivenDomNodeWithUnsortedNodes()
        {
            /** @var \DOMElement $oDomNode */
            $oDocument = new \DOMDocument();
            $oDocument->loadXML('<Root><Foo/><Bar/><Baz/></Root>');

            $oXmlSorter = $this->getXmlSorter();
            $oDomNode = $oDocument->getElementsByTagName('Root')->item(0);
            $oXmlSorter->sortDomNode($oDomNode);

            $oChildNodes = $oDomNode->childNodes;
            $aActual = array(
                  $oChildNodes->item(0)->nodeName
                , $oChildNodes->item(1)->nodeName
                , $oChildNodes->item(2)->nodeName
            );

            $aExpected = array('Bar','Baz','Foo');

            $this->assertSame($aExpected, $aActual);
        }
    }
}

#EOFindex