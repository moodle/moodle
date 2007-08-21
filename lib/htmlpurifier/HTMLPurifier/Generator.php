<?php

HTMLPurifier_ConfigSchema::define(
    'Output', 'CommentScriptContents', true, 'bool',
    'Determines whether or not HTML Purifier should attempt to fix up '.
    'the contents of script tags for legacy browsers with comments. This '.
    'directive was available since 2.0.0.'
);
HTMLPurifier_ConfigSchema::defineAlias('Core', 'CommentScriptContents', 'Output', 'CommentScriptContents');

// extension constraints could be factored into ConfigSchema
HTMLPurifier_ConfigSchema::define(
    'Output', 'TidyFormat', false, 'bool', <<<HTML
<p>
    Determines whether or not to run Tidy on the final output for pretty 
    formatting reasons, such as indentation and wrap.
</p>
<p>
    This can greatly improve readability for editors who are hand-editing
    the HTML, but is by no means necessary as HTML Purifier has already
    fixed all major errors the HTML may have had. Tidy is a non-default
    extension, and this directive will silently fail if Tidy is not
    available.
</p>
<p>
    If you are looking to make the overall look of your page's source
    better, I recommend running Tidy on the entire page rather than just
    user-content (after all, the indentation relative to the containing
    blocks will be incorrect).
</p>
<p>
    This directive was available since 1.1.1.
</p>
HTML
);
HTMLPurifier_ConfigSchema::defineAlias('Core', 'TidyFormat', 'Output', 'TidyFormat');

HTMLPurifier_ConfigSchema::define('Output', 'Newline', null, 'string/null', '
<p>
    Newline string to format final output with. If left null, HTML Purifier
    will auto-detect the default newline type of the system and use that;
    you can manually override it here. Remember, \r\n is Windows, \r
    is Mac, and \n is Unix. This directive was available since 2.0.1.
</p>
');

/**
 * Generates HTML from tokens.
 * @todo Refactor interface so that configuration/context is determined
 *     upon instantiation, no need for messy generateFromTokens() calls
 */
class HTMLPurifier_Generator
{
    
    /**
     * Bool cache of %HTML.XHTML
     * @private
     */
    var $_xhtml = true;
    
    /**
     * Bool cache of %Output.CommentScriptContents
     * @private
     */
    var $_scriptFix = false;
    
    /**
     * Cache of HTMLDefinition
     * @private
     */
    var $_def;
    
    /**
     * Generates HTML from an array of tokens.
     * @param $tokens Array of HTMLPurifier_Token
     * @param $config HTMLPurifier_Config object
     * @return Generated HTML
     */
    function generateFromTokens($tokens, $config, &$context) {
        $html = '';
        if (!$config) $config = HTMLPurifier_Config::createDefault();
        $this->_scriptFix   = $config->get('Output', 'CommentScriptContents');
        
        $this->_def = $config->getHTMLDefinition();
        $this->_xhtml = $this->_def->doctype->xml;
        
        if (!$tokens) return '';
        for ($i = 0, $size = count($tokens); $i < $size; $i++) {
            if ($this->_scriptFix && $tokens[$i]->name === 'script'
                && $i + 2 < $size && $tokens[$i+2]->type == 'end') {
                // script special case
                // the contents of the script block must be ONE token
                // for this to work
                $html .= $this->generateFromToken($tokens[$i++]);
                $html .= $this->generateScriptFromToken($tokens[$i++]);
                // We're not going to do this: it wouldn't be valid anyway
                //while ($tokens[$i]->name != 'script') {
                //    $html .= $this->generateScriptFromToken($tokens[$i++]);
                //}
            }
            $html .= $this->generateFromToken($tokens[$i]);
        }
        if ($config->get('Output', 'TidyFormat') && extension_loaded('tidy')) {
            
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
        // normalize newlines to system
        $nl = $config->get('Output', 'Newline');
        if ($nl === null) $nl = PHP_EOL;
        $html = str_replace("\n", $nl, $html);
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
            $attr = $this->generateAttributes($token->attr, $token->name);
            return '<' . $token->name . ($attr ? ' ' : '') . $attr . '>';
            
        } elseif ($token->type == 'end') {
            return '</' . $token->name . '>';
            
        } elseif ($token->type == 'empty') {
            $attr = $this->generateAttributes($token->attr, $token->name);
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
     * Special case processor for the contents of script tags
     * @warning This runs into problems if there's already a literal
     *          --> somewhere inside the script contents.
     */
    function generateScriptFromToken($token) {
        if ($token->type != 'text') return $this->generateFromToken($token);
        // return '<!--' . "\n" . trim($token->data) . "\n" . '// -->';
        // more advanced version:
        // thanks <http://lachy.id.au/log/2005/05/script-comments>
        $data = preg_replace('#//\s*$#', '', $token->data);
        return '<!--//--><![CDATA[//><!--' . "\n" . trim($data) . "\n" . '//--><!]]>';
    }
    
    /**
     * Generates attribute declarations from attribute array.
     * @param $assoc_array_of_attributes Attribute array
     * @return Generate HTML fragment for insertion.
     */
    function generateAttributes($assoc_array_of_attributes, $element) {
        $html = '';
        foreach ($assoc_array_of_attributes as $key => $value) {
            if (!$this->_xhtml) {
                // remove namespaced attributes
                if (strpos($key, ':') !== false) continue;
                if (!empty($this->_def->info[$element]->attr[$key]->minimized)) {
                    $html .= $key . ' ';
                    continue;
                }
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
        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
    }
    
}

