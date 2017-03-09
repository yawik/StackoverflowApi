<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license    MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */

/** */
namespace StackoverflowApi\Client;

use Zend\Http\Response as ZfResponse;

/**
 * Stackoverflow response.
 *
 * Provide a method to extract the xml response body.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.1.0
 */
class Response extends ZfResponse
{

    /**
     * The xml tree.
     *
     * @var \SimpleXMLElement|false
     */
    protected $xml;

    /**
     * Generate a xml tree from the response body when possible.
     *
     * @return false|\SimpleXMLElement
     */
    public function getXml()
    {
        if (!$this->xml) {
            $body      = $this->getBody();
            $this->xml = 0 === strpos($body, '<?xml') ? new \SimpleXMLElement($body) : false;
        }

        return $this->xml;
    }
}