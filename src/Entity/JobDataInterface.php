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
use Core\Entity\IdentifiableEntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * JobData Interface
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.1.0
 */
interface JobDataInterface extends EntityInterface, IdentifiableEntityInterface
{

    /**
     * Set the id of the associated job.
     *
     * @param string $id
     *
     * @return self
     */
    public function setJobId($id);

    /**
     * Get the id of the asociated job.
     *
     * @return string
     */
    public function getJobId();

    /**
     * Set the external id.
     *
     * @param int $id
     *
     * @return self
     */
    public function setExternalId($id);

    /**
     * Get the external id.
     *
     * @return int
     */
    public function getExternalId();

    /**
     * Set the external url.
     *
     * @param string $externalUrl
     *
     * @return self
     */
    public function setExternalUrl($externalUrl);

    /**
     * Get the external url
     *
     * @return string
     */
    public function getExternalUrl();

    /**
     * Set wether the associated job is successfully posted on stackoverflow.
     *
     * @param bool $flag
     *
     * @return self
     */
    public function setIsOnline($flag);

    /**
     * Is the associated job posted on stackoverflow?
     *
     * @return bool
     */
    public function isOnline();

    /**
     * Add an api response to the stack.
     *
     * @param ApiResponseInterface $response
     *
     * @return self
     */
    public function addResponse(ApiResponseInterface $response);

    /**
     * Get the response collection
     *
     * @return Collection
     */
    public function getResponses();

    /**
     * Get the last response from the stack.
     *
     * @return ApiResponseInterface
     */
    public function getLastResponse();
    
}