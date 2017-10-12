<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApi\Utils;

/**
 * Util to create XML documents/strings from an array specification.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class XmlBuilder
{
    /**
     * static class
     */
    private function __construct() {}

    /**
     * Creates a DOMDocument
     *
     * @param array $specs
     *
     * @return \DOMDocument
     */
    public static function createDocument(array $specs)
    {
        $xml = new \DOMDocument('1.0', 'utf-8');

        self::build($xml, $specs);

        return $xml;
    }

    /**
     * Creates a XML string
     *
     * @param array $specs
     *
     * @return string
     */
    public static function createXml(array $specs)
    {
        $doc = self::createDocument($specs);
        return $doc->saveXML();
    }

    /**
     * Builds the DOMDocument.
     *
     * Format of specs:
     *
     *
     * [
     *  'node' => 'textNode',       #create the node 'node' with textNode 'textNode' (=> <node>textNode</node>)
     *  ':node' => 'cdataNode',     #create the node 'node' with CDATA (=> <node><![CDATA[cdataNode]]></node>)
     *  '@attr' => 'value',         # adds an attribute to the node (=> <node attr="value">)
     *  'node' => [                 # Add multiple nodes when provide enumerated array
     *      [ 'subNode' => [
     *          [ '@attr' => 'value',
     *            'text',           # Creates a textNode and append it to current node
     *          ],
     *          [ '@attr' => 'value',
     *            ':cdata'          # Create a cdata node and append it to current node
     *          ]
     *      ],
     *      [
     *         '@attr' => popel',
     *          'subSubNode' => [
     *          ]
     *      ],
     *  'single' => [
     *      'maybe some text',
     *      'child' => 'text',
     *      'some more text',
     *   ],
     *  ]
     *
     * @param \DOMDocument|\DOMElement $node
     * @param array $specs
     */
    private static function build($node, array $specs)
    {
        $doc = $node->ownerDocument ?: $node;

        foreach ($specs as $name => $spec) {
            if (0 === strpos($name, '@')) {
                $node->setAttribute(substr($name, 1), $spec);
                continue;
            }

            if (is_string($spec) && 0 !== strpos($name, '_')) {
                if (is_numeric($name)) {
                    if (0 === strpos($spec, ':')) {
                        $name = '_cdata';
                        $spec = substr($spec, 1);
                    } else {
                        $name = '_text';
                    }
                } else if (0 === strpos($name, ':')) {
                    $spec = ['_cdata' => $spec];
                    $name = substr($name, 1);
                } else if (0 === strpos($spec, ':')) {
                    $spec = ['_cdata' => substr($spec, 1)];
                } else {
                    $spec = ['_text' => $spec];
                }
            }

            if (is_array($spec)) {
                if (isset($spec[ 0 ]) && !is_string($spec[ 0 ])) {
                    foreach ($spec as $s) {
                        self::build($node, [$name => $s]);
                    }
                    continue;
                }

                $child = $doc->createElement($name);
                $node->appendChild($child);
                self::build($child, $spec);
                continue;
            }

            $content = '_text' == $name ? $doc->createTextNode($spec) : $doc->createCdataSection($spec);
            $node->appendChild($content);
        }
    }
}
