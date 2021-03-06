<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Options;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use StackoverflowApi\Options\ModuleOptions;
use Zend\Stdlib\AbstractOptions;

/**
 * Tests for \StackoverflowApi\Options\ModuleOptions
 * 
 * @covers \StackoverflowApi\Options\ModuleOptions
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Options
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