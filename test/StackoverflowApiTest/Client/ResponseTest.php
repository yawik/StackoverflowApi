<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Client;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use StackoverflowApi\Client\Response;
use Zend\Http\Response as ZfResponse;

/**
 * Tests for \StackoverflowApi\Client\Response
 * 
 * @covers \StackoverflowApi\Client\Response
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Client
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var Response
     */
    private $target = Response::class;

    private $inheritance = [ ZfResponse::class ];

    public function testGetXmlReturnsFalse()
    {
        $this->assertFalse($this->target->getXml());
    }

    public function testGetXmlCreatesSimpleXmlElement()
    {
        $this->target->setContent('<?xml version="1.0" encoding="utf-8"?><test><data>test-data</data></test>');
        $xml = $this->target->getXml();

        $this->assertInstanceOf(\SimpleXMLElement::class, $xml);
        $this->assertEquals('test-data', $xml->data);

        $xml2 = $this->target->getXml();

        $this->assertSame($xml2, $this->target->getXml(), 'Cache generated xml does not work');

    }
}