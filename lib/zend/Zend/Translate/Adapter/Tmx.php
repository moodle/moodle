<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Locale */
require_once 'Zend/Locale.php';

/** Zend_Translate_Adapter */
require_once 'Zend/Translate/Adapter.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Translate_Adapter_Tmx extends Zend_Translate_Adapter {
    // Internal variables
    private $_file    = false;
    private $_tu      = null;
    private $_tuv     = null;
    private $_seg     = null;
    private $_content = null;
    private $_data    = array();

    /**
     * Generates the tmx adapter
     * This adapter reads with php's xml_parser
     *
     * @param  string              $data     Translation data
     * @param  string|Zend_Locale  $locale   OPTIONAL Locale/Language to set, identical with locale identifier,
     *                                       see Zend_Locale for more information
     * @param  array               $options  OPTIONAL Options to set
     */
    public function __construct($data, $locale = null, array $options = array())
    {
        parent::__construct($data, $locale, $options);
    }


    /**
     * Load translation data (TMX file reader)
     *
     * @param  string  $filename  TMX file to add, full path must be given for access
     * @param  string  $locale    Locale has no effect for TMX because TMX defines all languages within
     *                            the source file
     * @param  array   $option    OPTIONAL Options to use
     * @throws Zend_Translation_Exception
     * @return array
     */
    protected function _loadTranslationData($filename, $locale, array $options = array())
    {
        $this->_data = array();
        if (!is_readable($filename)) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception('Translation file \'' . $filename . '\' is not readable.');
        }

        $encoding = $this->_findEncoding($filename);
        $this->_file = xml_parser_create($encoding);
        xml_set_object($this->_file, $this);
        xml_parser_set_option($this->_file, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler($this->_file, "_startElement", "_endElement");
        xml_set_character_data_handler($this->_file, "_contentElement");

        if (!xml_parse($this->_file, file_get_contents($filename))) {
            $ex = sprintf('XML error: %s at line %d',
                          xml_error_string(xml_get_error_code($this->_file)),
                          xml_get_current_line_number($this->_file));
            xml_parser_free($this->_file);
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception($ex);
        }

        return $this->_data;
    }

    private function _startElement($file, $name, $attrib)
    {
        if ($this->_seg !== null) {
            $this->_content .= "<".$name;
            foreach($attrib as $key => $value) {
                $this->_content .= " $key=\"$value\"";
            }
            $this->_content .= ">";
        } else {
            switch(strtolower($name)) {
                case 'tu':
                    if (isset($attrib['tuid']) === true) {
                        $this->_tu = $attrib['tuid'];
                    }
                    break;
                case 'tuv':
                    if (isset($attrib['xml:lang']) === true) {
                        $this->_tuv = $attrib['xml:lang'];
                        if (isset($this->_data[$this->_tuv]) === false) {
                            $this->_data[$this->_tuv] = array();
                        }
                    }
                    break;
                case 'seg':
                    $this->_seg     = true;
                    $this->_content = null;
                    break;
                default:
                    break;
            }
        }
    }

    private function _endElement($file, $name)
    {
        if (($this->_seg !== null) and ($name !== 'seg')) {
            $this->_content .= "</".$name.">";
        } else {
            switch (strtolower($name)) {
                case 'tu':
                    $this->_tu = null;
                    break;
                case 'tuv':
                    $this->_tuv = null;
                    break;
                case 'seg':
                    $this->_seg = null;
                    if (!empty($this->_content) or (!isset($this->_data[$this->_tuv][$this->_tu]))) {
                        $this->_data[$this->_tuv][$this->_tu] = $this->_content;
                    }
                    break;
                default:
                    break;
            }
        }
    }

    private function _contentElement($file, $data)
    {
        if (($this->_seg !== null) and ($this->_tu !== null) and ($this->_tuv !== null)) {
            $this->_content .= $data;
        }
    }

    private function _findEncoding($filename)
    {
        $file = file_get_contents($filename, null, null, 0, 100);
        if (strpos($file, "encoding") !== false) {
            $encoding = substr($file, strpos($file, "encoding") + 9);
            $encoding = substr($encoding, 1, strpos($encoding, $encoding[0], 1) - 1);
            return $encoding;
        }
        return 'UTF-8';
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return "Tmx";
    }
}
