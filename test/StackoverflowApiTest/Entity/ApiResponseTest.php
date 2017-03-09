<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Entity;

use Core\Entity\EntityTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use StackoverflowApi\Client\Response;
use StackoverflowApi\Entity\ApiResponse;
use StackoverflowApi\Entity\ApiResponseInterface;

/**
 * Tests for \StackoverflowApi\Entity\ApiResponse
 * 
 * @covers \StackoverflowApi\Entity\ApiResponse
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Entity
 */
class ApiResponseTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait;

    /**
     *
     *
     * @var array | ApiResponse | \ReflectionClass
     */
    private $target = [
        ApiResponse::class,
        'getTargetConstructorArgs',
        '@testInheritance' => ['as_reflection' => true],
        '@testUsesTraits'  => ['as_reflection' => true],
        '@testConstruction' => false,
        '@testGetResponse' => false,
    ];

    private $inheritance = [ ApiResponseInterface::class ];

    private $traits = [ EntityTrait::class ];

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $responseMock;

    private function getTargetConstructorArgs()
    {
        $response = $this->getMockBuilder(Response::class)->getMock();
        $this->responseMock = $response;

        return [$response];
    }

    public function testConstruction()
    {
        $response = new Response();
        $responseStr = $response->toString();

        $target = new ApiResponse($response);

        $this->assertAttributeSame($response, 'responseObject', $target);
        $this->assertAttributeSame($responseStr, 'response', $target);
    }

    public function testCallProxiesToResponseObject()
    {
        $this->responseMock->expects($this->once())->method('getBody')->willReturn('body');

        $this->assertEquals('body', $this->target->getBody());
    }

    public function testCallReturnsSelfWhenProxiedMethodReturnsResponse()
    {
        $this->responseMock->method('getBody')->will($this->returnSelf());

        $this->assertSame($this->target, $this->target->getBody());
    }

    public function testCallUnknownMethodThrowsException()
    {
        $this->expectException('\BadMethodCallException');

        $this->target->callUnknownMethod();
    }

    public function testGetResponse()
    {

        $response = new Response();
        $responseStr = $response->toString();

        $target = new ApiResponse($response);
        $reflection = new \ReflectionClass(ApiResponse::class);
        $objProp = $reflection->getProperty('responseObject');
        $objProp->setAccessible(true);
        $objProp->setValue($target, null);
        $responseStr = new Response();
        $responseStr = $responseStr->toString();

        $response = $target->getResponse();

        $this->assertEquals($responseStr, $response->toString());
        $this->assertNotSame($this->responseMock, $response);
        $this->assertSame($response, $target->getResponse());
    }
}