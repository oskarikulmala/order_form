<?php
/**
 * @file
 * Contains \Drupal\order_form\Helper\FivaldiXMLConstructor.
 */

namespace Drupal\order_form\Helper;

use XMLWriter;

/**
 * Contribute XML constructor.
 */
class FivaldiXMLConstructor extends XMLWriter
{

    /**
     * Constructor.
     * @param string $prm_rootElementName A root element's name of a current xml document
     * @param string $prm_xsltFilePath Path of a XSLT file.
     * @access public
     * @param null
     */
    public function __construct($filename, $prm_rootElementName, $prm_xsltFilePath=''){
        $this->openURI($filename);
        $this->startDocument('1.0', 'ISO-8859-15');
        $this->setIndent(4);
        $this->startElement($prm_rootElementName);
        $this->writeAttribute ("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $this->writeAttribute ("xsi:noNamespaceSchemaLocation", "https://asp.fivaldi.net/fvjul/FivaldiXML.xsd");
      }

    /**
     * Set a simple element with a text to a current xml document.
     * @access public
     * @param string $prm_elementName An element's name
     * @param string $prm_ElementText An element's text
     * @return null
     */
    public function setElement($prm_elementName, $prm_ElementText){
        $this->startElement($prm_elementName);
        $this->text($prm_ElementText);
        $this->endElement();
    }

    /**
     * Construct elements and texts from an array (or use setElement
     * function if not an array).
     * The array should contain an attribute's name in index part
     * and a attribute's text in value part.
     * @access public
     * @param array $prm_array Contains attributes and texts
     * @return null
     */
    public function fromArray($prm_array){
      if(is_array($prm_array)){
        foreach ($prm_array as $index => $element){
          if(is_array($element)){
            $this->startElement($index);
            $this->fromArray($element);
            $this->endElement();
          }
          else
            $this->setElement($index, $element);
        }
      }
    }
    /**
     * Construct elements and texts from an array
     *  - but does not close the array.
     * The array should contain an attribute's name in index part
     * and a attribute's text in value part.
     * @access public
     * @param array $prm_array Contains attributes and texts
     * @return null
     */
    public function startArray($prm_array){
      if(is_array($prm_array)){
        foreach ($prm_array as $index => $element){
          if(is_array($element)){
            $this->startElement($index);
            $this->fromArray($element);
            //$this->endElement();
          }
          else
            $this->setElement($index, $element);
        }
      }
    }

    /**
     * Return the content of a current xml document.
     * @access public
     * @param null
     * @return string Xml document
     */
    public function getDocument(){
        $this->endElement();
        $this->endDocument();
        return $this->outputMemory();
    }

    /**
     * Output the content of a current xml document.
     * @access public
     * @param null
     */
    public function output(){
        header('Content-type: text/xml');
        echo $this->getDocument();
    }


}
