<?php

/*!
 * @mainpage
 * 
 * HTML Purifier is an HTML filter that will take an arbitrary snippet of
 * HTML and rigorously test, validate and filter it into a version that
 * is safe for output onto webpages. It achieves this by:
 * 
 *  -# Lexing (parsing into tokens) the document,
 *  -# Executing various strategies on the tokens:
 *      -# Removing all elements not in the whitelist,
 *      -# Making the tokens well-formed,
 *      -# Fixing the nesting of the nodes, and
 *      -# Validating attributes of the nodes; and
 *  -# Generating HTML from the purified tokens.
 * 
 * However, most users will only need to interface with the HTMLPurifier
 * class, so this massive amount of infrastructure is usually concealed.
 * If you plan on working with the internals, be sure to include
 * HTMLPurifier_ConfigSchema and HTMLPurifier_Config.
 */

/*
    HTML Purifier 1.6.1 - Standards Compliant HTML Filtering
    Copyright (C) 2006 Edward Z. Yang

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

// almost every class has an undocumented dependency to these, so make sure
// they get included
require_once 'HTMLPurifier/ConfigSchema.php';
require_once 'HTMLPurifier/Config.php';
require_once 'HTMLPurifier/Context.php';

require_once 'HTMLPurifier/Lexer.php';
require_once 'HTMLPurifier/Generator.php';
require_once 'HTMLPurifier/Strategy/Core.php';
require_once 'HTMLPurifier/Encoder.php';

/**
 * Main library execution class.
 * 
 * Facade that performs calls to the HTMLPurifier_Lexer,
 * HTMLPurifier_Strategy and HTMLPurifier_Generator subsystems in order to
 * purify HTML.
 * 
 * @todo We need an easier way to inject strategies, it'll probably end
 *       up getting done through config though.
 */
class HTMLPurifier
{
    
    var $version = '1.6.1';
    
    var $config;
    var $filters;
    
    var $lexer, $strategy, $generator;
    
    /**
     * Final HTMLPurifier_Context of last run purification. Might be an array.
     * @public
     */
    var $context;
    
    /**
     * Initializes the purifier.
     * @param $config Optional HTMLPurifier_Config object for all instances of
     *                the purifier, if omitted, a default configuration is
     *                supplied (which can be overridden on a per-use basis).
     *                The parameter can also be any type that
     *                HTMLPurifier_Config::create() supports.
     */
    function HTMLPurifier($config = null) {
        
        $this->config = HTMLPurifier_Config::create($config);
        
        $this->lexer        = HTMLPurifier_Lexer::create();
        $this->strategy     = new HTMLPurifier_Strategy_Core();
        $this->generator    = new HTMLPurifier_Generator();
        
    }
    
    /**
     * Adds a filter to process the output. First come first serve
     * @param $filter HTMLPurifier_Filter object
     */
    function addFilter($filter) {
        $this->filters[] = $filter;
    }
    
    /**
     * Filters an HTML snippet/document to be XSS-free and standards-compliant.
     * 
     * @param $html String of HTML to purify
     * @param $config HTMLPurifier_Config object for this operation, if omitted,
     *                defaults to the config object specified during this
     *                object's construction. The parameter can also be any type
     *                that HTMLPurifier_Config::create() supports.
     * @return Purified HTML
     */
    function purify($html, $config = null) {
        
        $config = $config ? HTMLPurifier_Config::create($config) : $this->config;
        
        $context = new HTMLPurifier_Context();
        $html = HTMLPurifier_Encoder::convertToUTF8($html, $config, $context);
        
        for ($i = 0, $size = count($this->filters); $i < $size; $i++) {
            $html = $this->filters[$i]->preFilter($html, $config, $context);
        }
        
        // purified HTML
        $html = 
            $this->generator->generateFromTokens(
                // list of tokens
                $this->strategy->execute(
                    // list of un-purified tokens
                    $this->lexer->tokenizeHTML(
                        // un-purified HTML
                        $html, $config, $context
                    ),
                    $config, $context
                ),
                $config, $context
            );
        
        for ($i = $size - 1; $i >= 0; $i--) {
            $html = $this->filters[$i]->postFilter($html, $config, $context);
        }
        
        $html = HTMLPurifier_Encoder::convertFromUTF8($html, $config, $context);
        $this->context =& $context;
        return $html;
    }
    
    /**
     * Filters an array of HTML snippets
     * @param $config Optional HTMLPurifier_Config object for this operation.
     *                See HTMLPurifier::purify() for more details.
     * @return Array of purified HTML
     */
    function purifyArray($array_of_html, $config = null) {
        $context_array = array();
        foreach ($array_of_html as $key => $html) {
            $array_of_html[$key] = $this->purify($html, $config);
            $context_array[$key] = $this->context;
        }
        $this->context = $context_array;
        return $array_of_html;
    }
    
    
}

?>