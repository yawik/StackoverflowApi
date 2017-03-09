<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApi\Client;

use Jobs\Entity\JobInterface;
use Zend\Http\Client as ZendHttpClient;
use Zend\Http\Request;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;

/**
 * Client for the stackoverflow api.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.1.0
 */
class Client extends ZendHttpClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * The job data transformer
     *
     * @var DataTransformer
     */
    private $transformer;

    /**
     * Creates an instance.
     *
     * @param string $authCode The stackoverflow api authentication code.
     */
    public function __construct($authCode)
    {
        $uri = 'https://talent.stackoverflow.com/api/jobs?code=' . (string) $authCode;

        parent::__construct($uri);
    }

    /**
     * Set the data transformer
     *
     * @param DataTransformer $transformer
     *
     * @return self
     */
    public function setTransformer(DataTransformer $transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Get the data transformer
     *
     * Create a new DataTransformer instance if none is set.
     *
     * @return DataTransformer
     */
    public function getTransformer()
    {
        if (!$this->transformer) {
            $this->setTransformer(new DataTransformer());
        }

        return $this->transformer;
    }

    /**
     * Send a job listing.
     *
     * @param JobInterface $job
     * @param array $data
     *
     * @return Response
     */
    public function sendJob($job, $data)
    {
        $this->reset();

        $xml = $this->getTransformer()->transform($job, $data);
        $this->getLogger()->debug($xml);
        $this->setRawBody($xml);
        $this->setMethod(Request::METHOD_POST);

        $response = $this->send();

        return $response;
    }

    /**
     * Delete a job listing.
     *
     * @param int $externalId
     *
     * @return Response
     */
    public function deleteJob($externalId)
    {
        $this->reset();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><job></job>');

        $xml->addChild('action', 'delete');
        $xml->addChild('jobid', $externalId);
        $xml->addChild('test', 'true');

        $xmlStr = $xml->asXML();

        $this->setRawBody($xmlStr);
        $this->setMethod(Request::METHOD_POST);

        $response = $this->send();

        return $response;
    }

    /**
     * Get the response object.
     *
     * @return Response
     */
    public function getResponse()
    {
        if (empty($this->response)) {
            $this->response = new Response();
        }
        return $this->response;
    }
}