<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace YawikStackoverflowApi\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Module options
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ModuleOptions extends AbstractOptions
{

    /**
     * The authorization code for the Stackoverflow api
     *
     * @var string
     */
    private $authorizationCode = '';

    /**
     * Set the authorization code.
     *
     * @param string $authorizationCode
     *
     * @return self
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = (string) $authorizationCode;

        return $this;
    }

    /**
     * Get the authorization code.
     *
     * @return string
     */
    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }
}