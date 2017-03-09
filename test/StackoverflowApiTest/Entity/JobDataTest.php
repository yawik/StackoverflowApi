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

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityTrait;
use Core\Entity\IdentifiableEntityTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use StackoverflowApi\Client\Response;
use StackoverflowApi\Entity\ApiResponse;
use StackoverflowApi\Entity\JobData;
use StackoverflowApi\Entity\JobDataInterface;

/**
 * Tests for \StackoverflowApi\Entity\JobData
 * 
 * @covers \StackoverflowApi\Entity\JobData
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Entity
 */
class JobDataTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var string|JobData
     */
    private $target = JobData::class;

    private $inheritance = [ JobDataInterface::class ];

    private $traits = [ EntityTrait::class, IdentifiableEntityTrait::class ];

    private $properties = [
        [ 'jobId', [ 'value' => 'testJobId', 'default' => null ] ],
        [ 'isOnline', [ 'value' => 'test', 'expect' => true, 'default' => false, 'getter_method' => '*' ]],
        [ 'isOnline', [ 'value' => 12, 'expect' => true, 'getter_method' => '*' ]],
        [ 'isOnline', [ 'value' => '', 'expect' => false, 'getter_method' => '*' ]],
        [ 'isOnline', [ 'value' => null, 'expect' => false, 'getter_method' => '*' ]],
        [ 'isOnline', [ 'value' => true, 'getter_method' => '*' ]],
        [ 'isOnline', [ 'value' => false, 'getter_method' => '*' ]],
        [ 'externalId', 'external-id' ],
        [ 'externalUrl', 'external-url' ],


    ];

    public function testGetResponsesReturnsArrayCollection()
    {
        $responses = $this->target->getResponses();

        $this->assertInstanceOf(ArrayCollection::class, $responses);
    }

    public function testAddResponse()
    {
        $response = new ApiResponse(new Response());
        $this->assertSame($this->target, $this->target->addResponse($response), 'fluent interface broken');
        $this->assertEquals(1, $this->target->getResponses()->count());
        $this->assertSame($response, $this->target->getResponses()->first());
    }

    public function testGetLastResponse()
    {
        $this->assertFalse($this->target->getLastResponse());

        $response1 = new ApiResponse(new Response());
        $response2 = new ApiResponse(new Response());

        $this->target->addResponse($response1);
        $this->target->addResponse($response2);

        $this->assertSame($response2, $this->target->getLastResponse());
    }
}