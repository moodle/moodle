<?php

require_once 'HTMLPurifier/Generator.php';
require_once 'HTMLPurifier/Token.php';
require_once 'HTMLPurifier/Encoder.php';

// OUT OF DATE, NEEDS UPDATING!

class HTMLPurifier_Printer
{
    
    /**
     * Instance of HTMLPurifier_Generator for HTML generation convenience funcs
     */
    var $generator;
    
    /**
     * Instance of HTMLPurifier_Config, for easy access
     */
    var $config;
    
    /**
     * Initialize $generator.
     */
    function HTMLPurifier_Printer() {
        $this->generator = new HTMLPurifier_Generator();
    }
    
    /**
     * Give generator necessary configuration if possible
     */
    function prepareGenerator($config) {
        // hack for smoketests/configForm.php
        if (empty($config->conf['HTML'])) return;
        $context = new HTMLPurifier_Context();
        $this->generator->generateFromTokens(array(), $config, $context);
    }
    
    /**
     * Main function that renders object or aspect of that object
     * @note Parameters vary depending on printer
     */
    // function render() {}
    
    /**
     * Returns a start tag
     * @param $tag Tag name
     * @param $attr Attribute array
     */
    function start($tag, $attr = array()) {
        return $this->generator->generateFromToken(
                    new HTMLPurifier_Token_Start($tag, $attr ? $attr : array())
               );
    }
    
    /**
     * Returns an end teg
     * @param $tag Tag name
     */
    function end($tag) {
        return $this->generator->generateFromToken(
                    new HTMLPurifier_Token_End($tag)
               );
    }
    
    /**
     * Prints a complete element with content inside
     * @param $tag Tag name
     * @param $contents Element contents
     * @param $attr Tag attributes
     * @param $escape Bool whether or not to escape contents
     */
    function element($tag, $contents, $attr = array(), $escape = true) {
        return $this->start($tag, $attr) .
               ($escape ? $this->escape($contents) : $contents) .
               $this->end($tag);
    }
    
    function elementEmpty($tag, $attr = array()) {
        return $this->generator->generateFromToken(
            new HTMLPurifier_Token_Empty($tag, $attr)
        );
    }
    
    function text($text) {
        return $this->generator->generateFromToken(
            new HTMLPurifier_Token_Text($text)
        );
    }
    
    /**
     * Prints a simple key/value row in a table.
     * @param $name Key
     * @param $value Value
     */
    function row($name, $value) {
        if (is_bool($value)) $value = $value ? 'On' : 'Off';
        return
            $this->start('tr') . "\n" .
                $this->element('th', $name) . "\n" .
                $this->element('td', $value) . "\n" .
            $this->end('tr')
        ;
    }
    
    /**
     * Escapes a string for HTML output.
     * @param $string String to escape
     */
    function escape($string) {
        $string = HTMLPurifier_Encoder::cleanUTF8($string);
        $string = htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        return $string;
    }
    
    /**
     * Takes a list of strings and turns them into a single list
     * @param $array List of strings
     * @param $polite Bool whether or not to add an end before the last
     */
    function listify($array, $polite = false) {
        if (empty($array)) return 'None';
        $ret = '';
        $i = count($array);
        foreach ($array as $value) {
            $i--;
            $ret .= $value;
            if ($i > 0 && !($polite && $i == 1)) $ret .= ', ';
            if ($polite && $i == 1) $ret .= 'and ';
        }
        return $ret;
    }
    
    /**
     * Retrieves the class of an object without prefixes, as well as metadata
     * @param $obj Object to determine class of
     * @param $prefix Further prefix to remove
     */
    function getClass($obj, $sec_prefix = '') {
        static $five = null;
        if ($five === null) $five = version_compare(PHP_VERSION, '5', '>=');
        $prefix = 'HTMLPurifier_' . $sec_prefix;
        if (!$five) $prefix = strtolower($prefix);
        $class = str_replace($prefix, '', get_class($obj));
        $lclass = strtolower($class);
        $class .= '(';
        switch ($lclass) {
            case 'enum':
                $values = array();
                foreach ($obj->valid_values as $value => $bool) {
                    $values[] = $value;
                }
                $class .= implode(', ', $values);
                break;
            case 'composite':
                $values = array();
                foreach ($obj->defs as $def) {
                    $values[] = $this->getClass($def, $sec_prefix);
                }
                $class .= implode(', ', $values);
                break;
            case 'multiple':
                $class .= $this->getClass($obj->single, $sec_prefix) . ', ';
                $class .= $obj->max;
                break;
        }
        $class .= ')';
        return $class;
    }
    
}

