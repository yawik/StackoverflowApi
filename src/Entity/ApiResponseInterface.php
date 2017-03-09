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

use Core\Entity\EntityInterface;
use StackoverflowApi\Client\Response;

/**
 * Interface for the ApiResponse entity.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.1.0
 */
interface ApiResponseInterface extends EntityInterface
{

    /**
     * @param Response $response
     */
    public function __construct(Response $response);

    /**
     * Get the response object.
     *
     * @return Response
     */
    public function getResponse();

}