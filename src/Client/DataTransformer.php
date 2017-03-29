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

use Jobs\Entity\CoordinatesInterface;
use Jobs\Entity\JobInterface;
use Jobs\View\Helper\ApplyUrl;
use Organizations\ImageFileCache\Manager as ImageManager;
use Zend\View\Helper\ServerUrl;

/**
 * Transformer for converting a Yawik Job to Stackoverflow XML
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @codeCoverageIgnore
 * @todo write test
 * @since 0.1.0
 */
class DataTransformer 
{

    /**
     * Url helper for the apply link.
     *
     * @var ApplyUrl
     */
    protected $applyUrlHelper;

    /**
     * Server url view Helper
     *
     * @var ServerUrl
     */
    protected $serverUrlHelper;

    /**
     * Organization Image File Cache Manager
     *
     * @var ImageManager
     */
    protected $organizationImageManager;

    /**
     * @param ApplyUrl $applyUrlHelper
     *
     * @return self
     */
    public function setApplyUrlHelper($applyUrlHelper)
    {
        $this->applyUrlHelper = $applyUrlHelper;

        return $this;
    }

    /**
     * @return ApplyUrl
     */
    public function getApplyUrlHelper()
    {
        return $this->applyUrlHelper;
    }

    /**
     * @param \Organizations\ImageFileCache\Manager $organizationImageManager
     *
     * @return self
     */
    public function setOrganizationImageManager($organizationImageManager)
    {
        $this->organizationImageManager = $organizationImageManager;

        return $this;
    }

    /**
     * @return \Organizations\ImageFileCache\Manager
     */
    public function getOrganizationImageManager()
    {
        return $this->organizationImageManager;
    }

    /**
     * @param \Zend\View\Helper\ServerUrl $serverUrlHelper
     *
     * @return self
     */
    public function setServerUrlHelper($serverUrlHelper)
    {
        $this->serverUrlHelper = $serverUrlHelper;

        return $this;
    }

    /**
     * @return \Zend\View\Helper\ServerUrl
     */
    public function getServerUrlHelper()
    {
        return $this->serverUrlHelper;
    }


    /**
     * Transform a job to stackoverflow XML
     *
     * @param JobInterface $job
     * @param array        $options
     *
     * @return string
     */
    public function transform(JobInterface $job, $options = [])
    {
        $applyUrl = $this->getApplyUrlHelper();
        $serverUrl = $this->getServerUrlHelper();
        $imageManager = $this->getOrganizationImageManager();

        /* @var \Jobs\Entity\Job $job */
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><job></job>');

        /*
         * FOR TESTING
         */
        $xml->addChild('test', 'true');


        $xml->addChild('action', isset($options['action']) ? $options['action'] : 'post');

        if (isset($options['externalId'])) {
            $xml->addChild('jobid', $options['externalId']);
        }

        $xml->addChild('title', $job->getTitle());
        $xml->addChild('company', $job->getOrganization()->getOrganizationName()->getName());
        $xml->addChild('companyurl', $job->getOrganization()->getContact()->getWebsite() ?: 'http://cross-solution.de');
        if (($image = $job->getOrganization()->getImage()) && $serverUrl && $imageManager) {
            $imageUri = $imageManager->getUri($image);
            $xml->addChild('logourl', $serverUrl($imageUri));
        }

        if ($companyDesc = $job->getOrganization()->getDescription()) {
            $xml->addChild('aboutcompany', $companyDesc);
        }

        $xml->addChild('vendorid', $job->getId());

        $atsMode = $job->getAtsMode();
        if ($atsMode->isDisabled()) {
            $xml->addChild('howtoapply', 'postalisch');
        } else if ($atsMode->isEmail()) {
            $xml->addChild('howtoapply', $atsMode->getEmail());
        } else if ($atsMode->isUri()) {
            $xml->addChild('howtoapply', $atsMode->getUri());
        } else if (is_callable($applyUrl) && $serverUrl) {
            $xml->addChild('howtoapply', $serverUrl($applyUrl($job, ['linkOnly' => true, 'absolute' => true])));
        } else {
            $xml->addChild('howtoapply', 'postalisch');
        }



        $locations = $job->getLocations();
        if ($locations->count()) {
            $loc = $xml->addChild('locations');

            foreach ($locations as $location) {
                $l = $loc->addChild('location', $location->getCity());
                $coords = $location->getCoordinates();
                if (CoordinatesInterface::TYPE_POINT == $coords->getType()) {
                    $c = $coords->getCoordinates();
                    $l->addAttribute('lon', $c[0]);
                    $l->addAttribute('lat', $c[1]);
                }
            }
        }

        foreach (['topspot', 'featured', 'remote', 'relocation', 'visasponsorship', 'sysadmin'] as $boolOpt) {
            if (isset($options[$boolOpt])) {
                $xml->addChild($boolOpt, $options[$boolOpt] ? 'true' : 'false');
            }
        }

        foreach (['length', 'coupon', 'pixel'] as $strOpt) {
            if (isset($options[$strOpt])) {
                $xml->addChild($strOpt, $options[$strOpt]);
            }
        }

        $xml->addChild('description', html_entity_decode($job->getTemplateValues()->getDescription())  ?: '<p></p>');

        if ($requirements = $job->getTemplateValues()->getRequirements()) {
            $xml->addChild('requirements', $requirements);
        }

        $tags = $xml->addChild('tags');
        if (isset($options['keywords']) && is_array($options['keyword'])) {
            foreach ($options['keywords'] as $keyword) {
                $tags->addChild('tag', $keyword);
            }
        } else {
            $tags->addChild('tag', 'none');
        }

        if (isset($options['advertisingregions']) && is_array($options['advertisingregions'])) {
            $regions = $xml->addChild('advertisingregioncodes');
            foreach ($options['advertisingregions'] as $adreg) {
                $regions->addChild('regioncode', $adreg);
            }
        }

        if (isset($options['advertisingcountries']) && is_array($options['advertisingcountries'])) {
            $countries = $xml->addChild('advertisingcountrycodes');
            foreach ($options['advertisingcountries'] as $adcountry) {
                $countries->addChild('regioncode', $adcountry);
            }
        }


        return $xml->asXml();
    }
}