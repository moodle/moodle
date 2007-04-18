<?php

require_once 'HTMLPurifier/Lexer.php';

HTMLPurifier_ConfigSchema::define(
    'Core', 'CleanUTF8DuringGeneration', false, 'bool',
    'When true, HTMLPurifier_Generator will also check all strings it '.
    'escapes for UTF-8 well-formedness as a defense in depth measure. '.
    'This could cause a considerable performance impact, and is not '.
    'strictly necessary due to the fact that the Lexers should have '.
    'ensured that all the UTF-8 strings were well-formed.  Note that '.
    'the configuration value is only read at the beginning of '.
    'generateFromTokens.'
);

HTMLPurifier_ConfigSchema::define(
    'Core', 'XHTML', true, 'bool',
    'Determines whether or not output is XHTML or not.  When disabled, HTML '.
    'Purifier goes into HTML 4.01 removes XHTML-specific markup constructs, '.
    'such as boolean attribute expansion and trailing slashes in empty tags. '.
    'This directive was available since 1.1.'
);

// extension constraints could be factored into ConfigSchema
HTMLPurifier_ConfigSchema::define(
    'Core', 'TidyFormat', false, 'bool',
    '<p>Determines whether or not to run Tidy on the final output for pretty '.
    'formatting reasons, such as indentation and wrap.</p><p>This can greatly '.
    'improve readability for editors who are hand-editing the HTML, but is '.
    'by no means necessary as HTML Purifier has already fixed all major '.
    'errors the HTML may have had. Tidy is a non-default extension, and this directive '.
    'will silently fail if Tidy is not available.</p><p>If you are looking to make '.
    'the overall look of your page\'s source better, I recommend running Tidy '.
    'on the entire page rather than just user-content (after all, the '.
    'indentation relative to the containing blocks will be incorrect).</p><p>This '.
    'directive was available since 1.1.1.</p>'
);

/**
 * Generates HTML from tokens.
 */
class HTMLPurifier_Generator
{
    
    /**
     * Bool cache of %Core.CleanUTF8DuringGeneration
     * @private
     */
    var $_clean_utf8 = false;
    
    /**
     * Bool cache of %Core.XHTML
     * @private
     */
    var $_xhtml = true;
    
    /**
     * Generates HTML from an array of tokens.
     * @param $tokens Array of HTMLPurifier_Token
     * @param $config HTMLPurifier_Config object
     * @return Generated HTML
     */
    function generateFromTokens($tokens, $config, &$context) {
        $html = '';
        if (!$config) $config = HTMLPurifier_Config::createDefault();
        $this->_clean_utf8 = $config->get('Core', 'CleanUTF8DuringGeneration');
        $this->_xhtml = $config->get('Core', 'XHTML');
        if (!$tokens) return '';
        foreach ($tokens as $token) {
            $html .= $this->generateFromToken($token);
        }
        if ($config->get('Core', 'TidyFormat') && extension_loaded('tidy')) {
            
            $tidy_options = array(
               'indent'=> true,
               'output-xhtml' => $this->_xhtml,
               'show-body-only' => true,
               'indent-spaces' => 2,
               'wrap' => 68,
            );
            if (version_compare(PHP_VERSION, '5', '<')) {
                tidy_set_encoding('utf8');
                foreach ($tidy_options as $key => $value) {
                    tidy_setopt($key, $value);
                }
                tidy_parse_string($html);
                tidy_clean_repair();
                $html = tidy_get_output();
            } else {
                $tidy = new Tidy;
                $tidy->parseString($html, $tidy_options, 'utf8');
                $tidy->cleanRepair();
                $html = (string) $tidy;
            }
        }
        return $html;
    }
    
    /**
     * Generates HTML from a single token.
     * @param $token HTMLPurifier_Token object.
     * @return Generated HTML
     */
    function generateFromToken($token) {
        if (!isset($token->type)) return '';
        if ($token->type == 'start') {
            $attr = $this->generateAttributes($token->attr);
            return '<' . $token->name . ($attr ? ' ' : '') . $attr . '>';
            
        } elseif ($token->type == 'end') {
            return '</' . $token->name . '>';
            
        } elseif ($token->type == 'empty') {
            $attr = $this->generateAttributes($token->attr);
             return '<' . $token->name . ($attr ? ' ' : '') . $attr .
                ( $this->_xhtml ? ' /': '' )
                . '>';
            
        } elseif ($token->type == 'text') {
            return $this->escape($token->data);
            
        } else {
            return '';
            
        }
    }
    
    /**
     * Generates attribute declarations from attribute array.
     * @param $assoc_array_of_attributes Attribute array
     * @return Generate HTML fragment for insertion.
     */
    function generateAttributes($assoc_array_of_attributes) {
        $html = '';
        foreach ($assoc_array_of_attributes as $key => $value) {
            if (!$this->_xhtml) {
                // remove namespaced attributes
                if (strpos($key, ':') !== false) continue;
                // also needed: check for attribute minimization
            }
            $html .= $key.'="'.$this->escape($value).'" ';
        }
        return rtrim($html);
    }
    
    /**
     * Escapes raw text data.
     * @param $string String data to escape for HTML.
     * @return String escaped data.
     */
    function escape($string) {
        if ($this->_clean_utf8) $string = HTMLPurifier_Lexer::cleanUTF8($string);
        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
    }
    
}

?>