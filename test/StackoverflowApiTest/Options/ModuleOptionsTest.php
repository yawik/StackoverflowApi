<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Options;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use StackoverflowApi\Options\ModuleOptions;
use Zend\Stdlib\AbstractOptions;

/**
 * Tests for \YawikStackoverflowApi\Options\ModuleOptions
 * 
 * @covers \Ystow\Options\ModuleOptions
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = ModuleOptions::class;

    private $inheritance = [ AbstractOptions::class ];

    private $properties = [
        [ 'authorizationCode', ['value' => 'testAuthCode', 'default' => '']]
    ];
}