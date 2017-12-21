<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Utils;

use StackoverflowApi\Utils\XmlBuilder;

/**
 * Tests for \StackoverflowApi\Utils\XmlBuilder
 * 
 * @covers \StackoverflowApi\Utils\XmlBuilder
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Utils
 */
class XmlBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateDocument()
    {
        $specs = [
            'root' => [
                '@attr' => 'attr',
                'textNode' => 'content',
                ':cdataNode' => 'cdataContent',
                'single' => [
                    'singleNode' => [
                        'content',
                        '@test' => 'works',
                    ],
                    'onlyCdata' => [ ':ishere' ],
                ],
                'multi' => [
                    [
                        'multiNode' => 'this',
                    ],
                    [
                        'multiNode' => 'works',
                        '@attr' => 'almost',
                    ],
                    [
                        'different' => ':cdataText'
                    ],
                ],
            ],
        ];



        $expected = new \DOMDocument('1.0', 'utf-8');
        $root = $expected->createElement('root');
        $root->setAttribute('attr', 'attr');
        $root->appendChild($expected->createElement('textNode', 'content'));

        $cdataNode = $expected->createElement('cdataNode');
        $cdataNode->appendChild($expected->createCDATASection('cdataContent'));
        $root->appendChild($cdataNode);

        $single = $expected->createElement('single');
        $singleNode = $expected->createElement('singleNode', 'content');
        $singleNode->setAttribute('test', 'works');
        $single->appendChild($singleNode);
        $onlyCdata = $expected->createElement('onlyCdata');
        $onlyCdata->appendChild($expected->createCDATASection('ishere'));
        $single->appendChild($onlyCdata);
        $root->appendChild($single);

        $multi1 = $expected->createElement('multi');
        $multi1Child = $expected->createElement('multiNode', 'this');
        $multi1->appendChild($multi1Child);
        $root->appendChild($multi1);

        $multi2 = $expected->createElement('multi');
        $multi2->setAttribute('attr', 'almost');
        $multi2Child = $expected->createElement('multiNode', 'works');
        $multi2->appendChild($multi2Child);
        $root->appendChild($multi2);

        $multi3 = $expected->createElement('multi');
        $multi3Child = $expected->createElement('different');
        $multi3ChildCdata = $expected->createCDATASection('cdataText');
        $multi3Child->appendChild($multi3ChildCdata);
        $multi3->appendChild($multi3Child);
        $root->appendChild($multi3);

        $expected->appendChild($root);

        $expected = $expected->saveXML();

        $this->assertEquals($expected, XmlBuilder::createXml($specs));

    }
}
