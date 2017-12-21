<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license    MIT
 * @copyright  2016 -2017 Cross Solution <http://cross-solution.de>
 */

/** */
namespace StackoverflowApi\Listener;

use Jobs\Entity\StatusInterface;
use Jobs\Listener\Events\JobEvent;
use Jobs\Listener\Response\JobResponse;
use StackoverflowApi\Service\JobsManager;

/**
 * Jobs events listener to post, update or remove job listing to / from the stackoverflow api.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.1.0
 */
class JobsListener
{
    /**
     * The manager.
     *
     * @var \StackoverflowApi\Service\JobsManager
     */
    private $manager;

    /**
     * Create an instance.
     *
     * @param JobsManager $manager
     */
    public function __construct(JobsManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Callback for "JobAccepted" event.
     *
     * @param JobEvent $event
     *
     * @return JobResponse
     */
    public function onJobAccepted(JobEvent $event)
    {
        if (!$event->hasPortal('stackoverflow')) {
            return $this->createResponse('Portal "stackoverflow" not activated for this job.');
        }

        $job     = $event->getJobEntity();
        $options = $event->getParam('extraData');
        $options = isset($options[ 'channels' ][ 'stackoverflow' ]) ? $options[ 'channels' ][ 'stackoverflow' ] : [];

        return $this->createResponse($this->manager->send($job, $options));
    }

    /**
     * Callback for "StatusChanged" event.
     *
     * @param JobEvent $event
     *
     * @return JobResponse
     */
    public function onJobStatusChanged(JobEvent $event)
    {
        $job = $event->getJobEntity();

        if (!$job->hasAttachedEntity('stackoverflow') || StatusInterface::INACTIVE != $job->getStatus()->getName()) {
            return $this->createResponse('Job (' . $job->getId() . ') was not exported or is not inactive');
        }

        return $this->createResponse($this->manager->remove($job));
    }

    /**
     * Create a JobResponse.
     *
     * @param string|bool $success
     * @param string      $message
     *
     * @return JobResponse
     */
    private function createResponse($success, $message = '')
    {
        if (is_string($success)) {
            return new JobResponse('stackoverflow', JobResponse::RESPONSE_DENIED, $success);
        }

        if ($success) {
            return new JobResponse('stackoverflow', JobResponse::RESPONSE_OK, $message);
        }

        return new JobResponse('stackoverflow', JobResponse::RESPONSE_FAIL, $message);
    }
}
