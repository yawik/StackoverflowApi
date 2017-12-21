<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Client;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use StackoverflowApi\Client\JobDescriptionFilter;
use Zend\Filter\StripTags;

/**
 * Tests for \StackoverflowApi\Client\JobDescriptionFilter
 * 
 * @covers \StackoverflowApi\Client\JobDescriptionFilter
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Client
 */
class JobDescriptionFilterTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var string|JobDescriptionFilter
     */
    private $target = JobDescriptionFilter::class;

    private $inheritance = [ StripTags::class ];

    public function testFilterHtml()
    {
        $html = $this->target->filter(__DIR__ . '/_job-description-filter-test.html');

        $this->assertNotContains('<script', $html);
        $this->assertNotContains('<style', $html);
        $this->assertNotContains('<div', $html);
        $this->assertNotContains('<a', $html);
        $this->assertNotContains('<i style=', $html);
    }
}
