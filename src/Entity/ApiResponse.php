<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApi\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\EntityTrait;
use StackoverflowApi\Client\Response;

/**
 * Stackoverflow API Response entity.
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.1.0
 */
class ApiResponse implements ApiResponseInterface
{
    use EntityTrait;

    /**
     * The stringified response.
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $response;

    /**
     * The response object.
     *
     * @var Response
     */
    protected $responseObject;


    /**
     * @param Response $response
     */
    public function __construct(Response $response) {
        $this->response = $response->toString();
        $this->responseObject = $response;
    }

    /**
     * Proxies to Response object.
     *
     * @param string $method
     * @param array $args
     *
     * @return self|Response
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        $response = $this->getResponse();
        $callback = [$response, $method];

        if (is_callable($callback)) {
            $returned = call_user_func_array($callback, $args);

            return $returned === $response ? $this : $returned;
        }

        throw new \BadMethodCallException('Unknown method "' . $method . '"');
    }

    /**
     * Get the response object.
     *
     * @return Response
     */
    public function getResponse()
    {
        if (!$this->responseObject) {
            $this->responseObject = Response::fromString($this->response);
        }

        return $this->responseObject;
    }
}
