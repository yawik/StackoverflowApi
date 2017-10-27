<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApi\Service;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Jobs\Entity\JobInterface;
use StackoverflowApi\Client\Client;
use StackoverflowApi\Entity\ApiResponse;
use StackoverflowApi\Entity\JobData;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;

/**
 * Manager for job listings on stackoverflow.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.1.0
 */
class JobsManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Stackoverflow client.
     *
     * @var \StackoverflowApi\Client\Client
     */
    private $client;

    /**
     * Creates a job manager instance.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send a job to stackoverflow.
     *
     * Determines automatically, wether the job needs to be posted or updated.
     *
     * @param JobInterface $job
     * @param array        $data
     *
     * @return bool
     */
    public function send(JobInterface $job, $data)
    {

        /* @var \StackoverflowApi\Entity\JobData $jobData */

        $log = $this->getLogger();
        $log->info('Send Job: ' . $job->getId());
        $status = true;

        $jobData = $job->hasAttachedEntity('stackoverflow') ? $job->getAttachedEntity('stackoverflow') : $job->createAttachedEntity(JobData::class, ['jobId' => $job->getId()], 'stackoverflow');

        if ($jobData->isOnline()) {
            $data['action'] = 'put';
            $data['externalId'] = $jobData->getExternalId();
            $log->debug('--> Job is already online: External id ' . $jobData->getExternalId() . ': update');
        } else {
            $data['action'] = 'post';
            $log->debug('--> Job is not online: insert');
        }

        $log->debug('--> data:' , $data);
        $response = $this->client->sendJob($job, $data);

        $apiResponse = new ApiResponse($response);

        $result   = $response->getXml();
        if ($result) {
            //$result = $result->response;
            if ('success' == $result->result) {
                $jobData->setExternalId($result->jobid)
                        ->setExternalUrl($result->joburl)
                        ->setIsOnline(true);
                $log->info('==> Successfully send ' . $job->getId(), ['id' => $result->jobid, 'url' => $result->joburl]);

            } else {
                $status = false;
                $log->err('==> Sending job ' . $job->getId() . ' failed.');
                $errors = (array) $result->errors->error;
                $log->debug($response->getStatusCode() . ': ' . $response->getReasonPhrase(), ['errors' => $errors, 'body' => $response->getBody()]);
            }
        } else {
            $status = false;
            $log->err('==> Unexpected error: ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase());
            $log->debug($response->getBody());
        }

        /* temporarely disabled due to encondig issues. */
        /*$jobData->addResponse($apiResponse);*/

        return $status;
    }

    /**
     * Delete a job from stackoverflow
     *
     * @param JobInterface $job
     *
     * @return bool
     */
    public function remove(JobInterface $job)
    {
        /* @var \StackoverflowApi\Entity\JobData $jobData */
        $jobData = $job->getAttachedEntity('stackoverflow');

        if ($jobData->isOnline()) {
            $response = $this->client->deleteJob($jobData->getExternalId());

            $apiResponse = new ApiResponse($response);

            $jobData->addResponse($apiResponse);

            $result = $response->getXml();
            if ($result && 'success' == $result->result) {
                $jobData->setIsOnline(false);
                return true;
            }
        }

        return false;
    }
}