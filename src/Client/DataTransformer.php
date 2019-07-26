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

use Core\Entity\Collection\ArrayCollection;
use Jobs\Entity\CoordinatesInterface;
use Jobs\Entity\JobInterface;
use Jobs\Entity\Location;
use Jobs\View\Helper\ApplyUrl;
use Organizations\ImageFileCache\Manager as ImageManager;
use StackoverflowApi\Utils\XmlBuilder;
use Zend\View\Helper\ServerUrl;

/**
 * Transformer for converting a Yawik Job to Stackoverflow XML
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
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
     *
     *
     * @var JobDescriptionFilter
     */
    protected $descriptionFilter;

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
     * @return JobDescriptionFilter
     */
    public function getDescriptionFilter()
    {
        if (!$this->descriptionFilter) {
            $this->descriptionFilter = new JobDescriptionFilter();
        }
        
        return $this->descriptionFilter;
    }

    /**
     * @param JobDescriptionFilter $descriptionFilter
     *
     * @return self
     */
    public function setDescriptionFilter($descriptionFilter)
    {
        $this->descriptionFilter = $descriptionFilter;

        return $this;
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

        $jobSpec = [
            'action' => isset($options['action']) ? $options['action'] : 'post',
            //'test' => 'true',

        ];

        if (isset($options['externalId'])) {
            $jobSpec['jobid'] = $options['externalId'];
        }

        $jobSpec[':title'] = $job->getTitle();
        $jobSpec[':company'] = $job->getOrganization()->getOrganizationName()->getName();
        $jobSpec[':companyurl'] = $job->getOrganization()->getContact()->getWebsite() ?: '';

        if (($image = $job->getOrganization()->getImage()) && $serverUrl && $imageManager) {
            $imageUri = $imageManager->getUri($image);
            $jobSpec['logourl'] = $serverUrl($imageUri);
        }

        if ($companyDesc = $job->getOrganization()->getDescription()) {
            $jobSpec[':aboutcompany'] = $companyDesc;
        }

        $jobSpec['vendorid'] = $job->getId();

        if (!empty($options['applyUrl'])) {
            $jobSpec['howtoapply'] = $options['applyUrl'];
        } else if ($contactEmail = $job->getContactEmail()) {
            $jobSpec['howtoapply'] = $contactEmail;
        } else {
            $atsMode = $job->getAtsMode();
            if ($atsMode->isDisabled()) {
                $jobSpec['howtoapply'] = 'postalisch';
            } else if ($atsMode->isEmail()) {
                $jobSpec['howtoapply'] = $atsMode->getEmail();
            } else if ($atsMode->isUri()) {
                $jobSpec['howtoapply'] = $atsMode->getUri();
            } else if (is_callable($applyUrl) && $serverUrl) {
                $jobSpec['howtoapply'] = $serverUrl($applyUrl($job, ['linkOnly' => true, 'absolute' => true]));
            } else {
                $jobSpec['howtoapply'] =  'postalisch';
            }
        }



        /* Location override by additional data */
        if (isset($options['location'])) {
            $location = new Location($options['location']);
            $locations = new ArrayCollection([$location]);
        } else {
            $locations = $job->getLocations();
        }

        if ($locations->count()) {
            $loc = [];

            foreach ($locations as $location) {
                /* \Jobs\Entity\Location $location */
                $tmpLoc = [];
                $coords = $location->getCoordinates();
                $postalCode = $location->getPostalCode();
                $city = $location->getCity();
                $country = $location->getCountry();
                $region = $location->getRegion();

                $str = '';
                if ($postalCode) { $str .= $postalCode . ' '; }
                if ($city) { $str .= $city; }
                if ($region) { $str .= ', ' . $region; }
                if ($country) { $str .= ', ' . $country; }


                /*
                 * see https://talent.stackoverflow.com/de/api/doc
                 * Value of location tag should only be the city name.
                 * Additional address information must be in the "address" attribute
                 */
                $tmpLoc['_text'] = $city;
                if ($str != $city) { $tmpLoc['@address'] = $str; }
                if ($coords) {
                    $coords = $coords->getCoordinates();
                    $tmpLoc['@lon'] = str_replace(',', '.', (string) $coords[0]);
                    $tmpLoc['@lat'] = str_replace(',', '.', (string) $coords[1]);
                }

                $loc[] = $tmpLoc;
            }

            $jobSpec['locations']['location'] = $loc;
        }

        foreach (['topspot', 'featured', 'remote', 'relocation', 'visasponsorship', 'sysadmin'] as $boolOpt) {
            if (isset($options[$boolOpt])) {
                $jobSpec[$boolOpt] = $options[$boolOpt] ? 'true' : 'false';
            }
        }

        foreach (['length', 'coupon', 'pixel'] as $strOpt) {
            if (isset($options[$strOpt])) {
                $jobSpec[$strOpt] = $options[$strOpt];
            }
        }

        $link = $job->getLink();
        $jobSpec[':description'] = $link ? $this->getDescriptionFilter()->filter($link) : '<p></p>';

        if ($requirements = $job->getTemplateValues()->getRequirements()) {
            $jobSpec[':requirements'] = $requirements;
        }

        $tags = [];
        if (isset($options['keywords']) && is_array($options['keyword'])) {
            foreach ($options['keywords'] as $keyword) {
                $tags[] = ['tag' => $keyword];
            }
        } else {
            $tags[] = ['tag' => 'none'];
        }
        $jobSpec['tags'] = $tags;

        if (isset($options['advertisingregions']) && is_array($options['advertisingregions'])) {
            $regions = [];
            foreach ($options['advertisingregions'] as $adreg) {
                $regions[] = ['regioncode' => $adreg];
            }
            $jobSpec['advertisingregions'] = $regions;
        }

        if (isset($options['advertisingcountries']) && is_array($options['advertisingcountries'])) {
            $countries = [];
            foreach ($options['advertisingcountries'] as $adcountry) {
                $countries[] = ['regioncode' => $adcountry];
            }
            $jobSpec['advertisingcountrycodes'] = $countries;
        }


        return XmlBuilder::createXml(['job' => $jobSpec]);
    }
}
