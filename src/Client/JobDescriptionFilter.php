<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApi\Client;

use Zend\Filter\StripTags;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class JobDescriptionFilter extends StripTags
{
    public function __construct()
    {
        parent::__construct([
            'b', 'strong', 'p', 'ul', 'li', 'ol', 'table', 'tr', 'td', 'tbody',
            'thead', 'th', 'br', 'i', 'em'
        ]);
    }

    public function filter($value)
    {
        $oldErrorReporting = error_reporting(0);
        $dom = new \DOMDocument();
        $dom->loadHTMLFile($value);

        $body = $dom->getElementsByTagName('body')->item(0);

        foreach (['script', 'style'] as $name) {
            while ($elem = $body->getElementsByTagName($name)->item(0)) {
                $elem->parentNode->removeChild($elem);
            }
        }

        $html = $dom->saveHTML($body);
        error_reporting($oldErrorReporting);

        $html = parent::filter($html);

        return $html;
    }


}
