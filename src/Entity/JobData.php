<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016- 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApi\Entity;

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityTrait;
use Core\Entity\IdentifiableEntityTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Stackoverflow specific data container to be attached to a job entity.
 *
 * @ODM\Document(collection="stackoverflowapi.jobdata", repositoryClass="\Core\Repository\DefaultRepository")
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.1.0
 */
class JobData implements JobDataInterface
{
    use EntityTrait, IdentifiableEntityTrait;

    /**
     * The id of the associated job
     *
     * @ODM\Field(type="string")
     * @var string
     */
    private $jobId;

    /**
     * The external id (Stackoverflow API)
     *
     * @ODM\Field(type="int")
     * @var int
     */
    private $externalId;

    /**
     * The external url.
     *
     * @ODM\Field(type="string")
     * @var string
     */
    private $externalUrl;

    /**
     * Flag wether the associated job is online.
     *
     * @ODM\Field(type="boolean")
     * @var bool
     */
    private $isOnline = false;

    /**
     * Api response stack.
     *
     * @ODM\EmbedMany(targetDocument="StackoverflowApi\Entity\ApiResponse")
     * @var Collection
     */
    private $responses;

    public function setJobId($id)
    {
        $this->jobId = (string) $id;

        return $this;
    }

    public function getJobId()
    {
        return $this->jobId;
    }

    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getExternalId()
    {
        return $this->externalId;
    }

    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $externalUrl;

        return $this;
    }

    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    public function setIsOnline($flag)
    {
        $this->isOnline = (bool) $flag;

        return $this;
    }

    public function isOnline()
    {
        return $this->isOnline;
    }

    public function addResponse(ApiResponseInterface $response)
    {
        $this->getResponses()->add($response);

        return $this;
    }

    public function getResponses()
    {
        if (!$this->responses) {
            $this->responses = new ArrayCollection();
        }

        return $this->responses;
    }

    public function getLastResponse()
    {
        return $this->getResponses()->last();
    }
}