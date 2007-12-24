<?php

require_once 'HTMLPurifier/Token.php';
require_once 'HTMLPurifier/Encoder.php';
require_once 'HTMLPurifier/EntityParser.php';

// implementations
require_once 'HTMLPurifier/Lexer/DirectLex.php';
if (version_compare(PHP_VERSION, "5", ">=")) {
    // You can remove the if statement if you are running PHP 5 only.
    // We ought to get the strict version to follow those rules.
    require_once 'HTMLPurifier/Lexer/DOMLex.php';
}

HTMLPurifier_ConfigSchema::define(
    'Core', 'ConvertDocumentToFragment', true, 'bool', '
This parameter determines whether or not the filter should convert
input that is a full document with html and body tags to a fragment
of just the contents of a body tag. This parameter is simply something
HTML Purifier can do during an edge-case: for most inputs, this
processing is not necessary.
');
HTMLPurifier_ConfigSchema::defineAlias('Core', 'AcceptFullDocuments', 'Core', 'ConvertDocumentToFragment');

HTMLPurifier_ConfigSchema::define(
    'Core', 'LexerImpl', null, 'mixed/null', '
<p>
  This parameter determines what lexer implementation can be used. The
  valid values are:
</p>
<dl>
  <dt><em>null</em></dt>
  <dd>
    Recommended, the lexer implementation will be auto-detected based on
    your PHP-version and configuration.
  </dd>
  <dt><em>string</em> lexer identifier</dt>
  <dd>
    This is a slim way of manually overridding the implementation.
    Currently recognized values are: DOMLex (the default PHP5 implementation)
    and DirectLex (the default PHP4 implementation). Only use this if
    you know what you are doing: usually, the auto-detection will
    manage things for cases you aren\'t even aware of.
  </dd>
  <dt><em>object</em> lexer instance</dt>
  <dd>
    Super-advanced: you can specify your own, custom, implementation that
    implements the interface defined by <code>HTMLPurifier_Lexer</code>.
    I may remove this option simply because I don\'t expect anyone
    to use it.
  </dd>
</dl>
<p>
  This directive has been available since 2.0.0.
</p>
'
);

HTMLPurifier_ConfigSchema::define(
    'Core', 'MaintainLineNumbers', null, 'bool/null', '
<p>
  If true, HTML Purifier will add line number information to all tokens.
  This is useful when error reporting is turned on, but can result in
  significant performance degradation and should not be used when
  unnecessary. This directive must be used with the DirectLex lexer,
  as the DOMLex lexer does not (yet) support this functionality. 
  If the value is null, an appropriate value will be selected based
  on other configuration. This directive has been available since 2.0.0.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'Core', 'AggressivelyFixLt', false, 'bool', '
This directive enables aggressive pre-filter fixes HTML Purifier can
perform in order to ensure that open angled-brackets do not get killed
during parsing stage. Enabling this will result in two preg_replace_callback
calls and one preg_replace call for every bit of HTML passed through here.
It is not necessary and will have no effect for PHP 4.
This directive has been available since 2.1.0.
');

/**
 * Forgivingly lexes HTML (SGML-style) markup into tokens.
 * 
 * A lexer parses a string of SGML-style markup and converts them into
 * corresponding tokens.  It doesn't check for well-formedness, although its
 * internal mechanism may make this automatic (such as the case of
 * HTMLPurifier_Lexer_DOMLex).  There are several implementations to choose
 * from.
 * 
 * A lexer is HTML-oriented: it might work with XML, but it's not
 * recommended, as we adhere to a subset of the specification for optimization
 * reasons.
 * 
 * This class should not be directly instantiated, but you may use create() to
 * retrieve a default copy of the lexer.  Being a supertype, this class
 * does not actually define any implementation, but offers commonly used
 * convenience functions for subclasses.
 * 
 * @note The unit tests will instantiate this class for testing purposes, as
 *       many of the utility functions require a class to be instantiated.
 *       Be careful when porting this class to PHP 5.
 * 
 * @par
 * 
 * @note
 * We use tokens rather than create a DOM representation because DOM would:
 * 
 * @par
 *  -# Require more processing power to create,
 *  -# Require recursion to iterate,
 *  -# Must be compatible with PHP 5's DOM (otherwise duplication),
 *  -# Has the entire document structure (html and body not needed), and
 *  -# Has unknown readability improvement.
 * 
 * @par
 * What the last item means is that the functions for manipulating tokens are
 * already fairly compact, and when well-commented, more abstraction may not
 * be needed.
 * 
 * @see HTMLPurifier_Token
 */
class HTMLPurifier_Lexer
{
    
    // -- STATIC ----------------------------------------------------------
    
    /**
     * Retrieves or sets the default Lexer as a Prototype Factory.
     * 
     * Depending on what PHP version you are running, the abstract base
     * Lexer class will determine which concrete Lexer is best for you:
     * HTMLPurifier_Lexer_DirectLex for PHP 4, and HTMLPurifier_Lexer_DOMLex
     * for PHP 5 and beyond.  This general rule has a few exceptions to it
     * involving special features that only DirectLex implements.
     * 
     * @static
     * 
     * @note The behavior of this class has changed, rather than accepting
     *       a prototype object, it now accepts a configuration object.
     *       To specify your own prototype, set %Core.LexerImpl to it.
     *       This change in behavior de-singletonizes the lexer object.
     * 
     * @note In PHP4, it is possible to call this factory method from 
     *       subclasses, such usage is not recommended and not
     *       forwards-compatible.
     * 
     * @param $prototype Optional prototype lexer or configuration object
     * @return Concrete lexer.
     */
    function create($config) {
        
        if (!is_a($config, 'HTMLPurifier_Config')) {
            $lexer = $config;
            trigger_error("Passing a prototype to 
              HTMLPurifier_Lexer::create() is deprecated, please instead
              use %Core.LexerImpl", E_USER_WARNING);
        } else {
            $lexer = $config->get('Core', 'LexerImpl');
        }
        
        if (is_object($lexer)) {
            return $lexer;
        }
        
        if (is_null($lexer)) { do {
            // auto-detection algorithm
            
            // once PHP DOM implements native line numbers, or we
            // hack out something using XSLT, remove this stipulation
            $line_numbers = $config->get('Core', 'MaintainLineNumbers');
            if (
                $line_numbers === true ||
                ($line_numbers === null && $config->get('Core', 'CollectErrors'))
            ) {
                $lexer = 'DirectLex';
                break;
            }
            
            if (version_compare(PHP_VERSION, "5", ">=") && // check for PHP5
                class_exists('DOMDocument')) { // check for DOM support
                $lexer = 'DOMLex';
            } else {
                $lexer = 'DirectLex';
            }
            
        } while(0); } // do..while so we can break
        
        // instantiate recognized string names
        switch ($lexer) {
            case 'DOMLex':
                return new HTMLPurifier_Lexer_DOMLex();
            case 'DirectLex':
                return new HTMLPurifier_Lexer_DirectLex();
            case 'PH5P':
                // experimental Lexer that must be manually included
                return new HTMLPurifier_Lexer_PH5P();
            default:
                trigger_error("Cannot instantiate unrecognized Lexer type " . htmlspecialchars($lexer), E_USER_ERROR);
        }
        
    }
    
    // -- CONVENIENCE MEMBERS ---------------------------------------------
    
    function HTMLPurifier_Lexer() {
        $this->_entity_parser = new HTMLPurifier_EntityParser();
    }
    
    /**
     * Most common entity to raw value conversion table for special entities.
     * @protected
     */
    var $_special_entity2str =
            array(
                    '&quot;' => '"',
                    '&amp;'  => '&',
                    '&lt;'   => '<',
                    '&gt;'   => '>',
                    '&#39;'  => "'",
                    '&#039;' => "'",
                    '&#x27;' => "'"
            );
    
    /**
     * Parses special entities into the proper characters.
     * 
     * This string will translate escaped versions of the special characters
     * into the correct ones.
     * 
     * @warning
     * You should be able to treat the output of this function as
     * completely parsed, but that's only because all other entities should
     * have been handled previously in substituteNonSpecialEntities()
     * 
     * @param $string String character data to be parsed.
     * @returns Parsed character data.
     */
    function parseData($string) {
        
        // following functions require at least one character
        if ($string === '') return '';
        
        // subtracts amps that cannot possibly be escaped
        $num_amp = substr_count($string, '&') - substr_count($string, '& ') -
            ($string[strlen($string)-1] === '&' ? 1 : 0);
        
        if (!$num_amp) return $string; // abort if no entities
        $num_esc_amp = substr_count($string, '&amp;');
        $string = strtr($string, $this->_special_entity2str);
        
        // code duplication for sake of optimization, see above
        $num_amp_2 = substr_count($string, '&') - substr_count($string, '& ') -
            ($string[strlen($string)-1] === '&' ? 1 : 0);
        
        if ($num_amp_2 <= $num_esc_amp) return $string;
        
        // hmm... now we have some uncommon entities. Use the callback.
        $string = $this->_entity_parser->substituteSpecialEntities($string);
        return $string;
    }
    
    /**
     * Lexes an HTML string into tokens.
     * 
     * @param $string String HTML.
     * @return HTMLPurifier_Token array representation of HTML.
     */
    function tokenizeHTML($string, $config, &$context) {
        trigger_error('Call to abstract class', E_USER_ERROR);
    }
    
    /**
     * Translates CDATA sections into regular sections (through escaping).
     * 
     * @static
     * @protected
     * @param $string HTML string to process.
     * @returns HTML with CDATA sections escaped.
     */
    function escapeCDATA($string) {
        return preg_replace_callback(
            '/<!\[CDATA\[(.+?)\]\]>/s',
            array('HTMLPurifier_Lexer', 'CDATACallback'),
            $string
        );
    }
    
    /**
     * Special CDATA case that is especiall convoluted for <script>
     */
    function escapeCommentedCDATA($string) {
        return preg_replace_callback(
            '#<!--//--><!\[CDATA\[//><!--(.+?)//--><!\]\]>#s',
            array('HTMLPurifier_Lexer', 'CDATACallback'),
            $string
        );
    }
    
    /**
     * Callback function for escapeCDATA() that does the work.
     * 
     * @static
     * @warning Though this is public in order to let the callback happen,
     *          calling it directly is not recommended.
     * @params $matches PCRE matches array, with index 0 the entire match
     *                  and 1 the inside of the CDATA section.
     * @returns Escaped internals of the CDATA section.
     */
    function CDATACallback($matches) {
        // not exactly sure why the character set is needed, but whatever
        return htmlspecialchars($matches[1], ENT_COMPAT, 'UTF-8');
    }
    
    /**
     * Takes a piece of HTML and normalizes it by converting entities, fixing
     * encoding, extracting bits, and other good stuff.
     */
    function normalize($html, $config, &$context) {
        
        // extract body from document if applicable
        if ($config->get('Core', 'ConvertDocumentToFragment')) {
            $html = $this->extractBody($html);
        }
        
        // normalize newlines to \n
        $html = str_replace("\r\n", "\n", $html);
        $html = str_replace("\r", "\n", $html);
        
        if ($config->get('HTML', 'Trusted')) {
            // escape convoluted CDATA
            $html = $this->escapeCommentedCDATA($html);
        }
        
        // escape CDATA
        $html = $this->escapeCDATA($html);
        
        // expand entities that aren't the big five
        $html = $this->_entity_parser->substituteNonSpecialEntities($html);
        
        // clean into wellformed UTF-8 string for an SGML context: this has
        // to be done after entity expansion because the entities sometimes
        // represent non-SGML characters (horror, horror!)
        $html = HTMLPurifier_Encoder::cleanUTF8($html);
        
        return $html;
    }
    
    /**
     * Takes a string of HTML (fragment or document) and returns the content
     */
    function extractBody($html) {
        $matches = array();
        $result = preg_match('!<body[^>]*>(.+?)</body>!is', $html, $matches);
        if ($result) {
            return $matches[1];
        } else {
            return $html;
        }
    }
    
}

