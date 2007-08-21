<?php

require_once 'HTMLPurifier/AttrDef.php';
require_once 'HTMLPurifier/IDAccumulator.php';

HTMLPurifier_ConfigSchema::define(
    'Attr', 'EnableID', false, 'bool',
    'Allows the ID attribute in HTML.  This is disabled by default '.
    'due to the fact that without proper configuration user input can '.
    'easily break the validation of a webpage by specifying an ID that is '.
    'already on the surrounding HTML.  If you don\'t mind throwing caution to '.
    'the wind, enable this directive, but I strongly recommend you also '.
    'consider blacklisting IDs you use (%Attr.IDBlacklist) or prefixing all '.
    'user supplied IDs (%Attr.IDPrefix).  This directive has been available '.
    'since 1.2.0, and when set to true reverts to the behavior of pre-1.2.0 '.
    'versions.'
);
HTMLPurifier_ConfigSchema::defineAlias(
    'HTML', 'EnableAttrID', 'Attr', 'EnableID'
);

HTMLPurifier_ConfigSchema::define(
    'Attr', 'IDPrefix', '', 'string',
    'String to prefix to IDs.  If you have no idea what IDs your pages '.
    'may use, you may opt to simply add a prefix to all user-submitted ID '.
    'attributes so that they are still usable, but will not conflict with '.
    'core page IDs. Example: setting the directive to \'user_\' will result in '.
    'a user submitted \'foo\' to become \'user_foo\'  Be sure to set '.
    '%HTML.EnableAttrID to true before using '.
    'this.  This directive was available since 1.2.0.'
);

HTMLPurifier_ConfigSchema::define(
    'Attr', 'IDPrefixLocal', '', 'string',
    'Temporary prefix for IDs used in conjunction with %Attr.IDPrefix.  If '.
    'you need to allow multiple sets of '.
    'user content on web page, you may need to have a seperate prefix that '.
    'changes with each iteration.  This way, seperately submitted user content '.
    'displayed on the same page doesn\'t clobber each other. Ideal values '.
    'are unique identifiers for the content it represents (i.e. the id of '.
    'the row in the database). Be sure to add a seperator (like an underscore) '.
    'at the end.  Warning: this directive will not work unless %Attr.IDPrefix '.
    'is set to a non-empty value! This directive was available since 1.2.0.'
);

HTMLPurifier_ConfigSchema::define(
    'Attr', 'IDBlacklistRegexp', null, 'string/null',
    'PCRE regular expression to be matched against all IDs. If the expression '.
    'is matches, the ID is rejected. Use this with care: may cause '.
    'significant degradation. ID matching is done after all other '.
    'validation. This directive was available since 1.6.0.'
);

/**
 * Validates the HTML attribute ID.
 * @warning Even though this is the id processor, it
 *          will ignore the directive Attr:IDBlacklist, since it will only
 *          go according to the ID accumulator. Since the accumulator is
 *          automatically generated, it will have already absorbed the
 *          blacklist. If you're hacking around, make sure you use load()!
 */

class HTMLPurifier_AttrDef_HTML_ID extends HTMLPurifier_AttrDef
{
    
    // ref functionality disabled, since we also have to verify
    // whether or not the ID it refers to exists
    
    function validate($id, $config, &$context) {
        
        if (!$config->get('Attr', 'EnableID')) return false;
        
        $id = trim($id); // trim it first
        
        if ($id === '') return false;
        
        $prefix = $config->get('Attr', 'IDPrefix');
        if ($prefix !== '') {
            $prefix .= $config->get('Attr', 'IDPrefixLocal');
            // prevent re-appending the prefix
            if (strpos($id, $prefix) !== 0) $id = $prefix . $id;
        } elseif ($config->get('Attr', 'IDPrefixLocal') !== '') {
            trigger_error('%Attr.IDPrefixLocal cannot be used unless '.
                '%Attr.IDPrefix is set', E_USER_WARNING);
        }
        
        //if (!$this->ref) {
            $id_accumulator =& $context->get('IDAccumulator');
            if (isset($id_accumulator->ids[$id])) return false;
        //}
        
        // we purposely avoid using regex, hopefully this is faster
        
        if (ctype_alpha($id)) {
            $result = true;
        } else {
            if (!ctype_alpha(@$id[0])) return false;
            $trim = trim( // primitive style of regexps, I suppose
                $id,
                'A..Za..z0..9:-._'
              );
            $result = ($trim === '');
        }
        
        $regexp = $config->get('Attr', 'IDBlacklistRegexp');
        if ($regexp && preg_match($regexp, $id)) {
            return false;
        }
        
        if (/*!$this->ref && */$result) $id_accumulator->add($id);
        
        // if no change was made to the ID, return the result
        // else, return the new id if stripping whitespace made it
        //     valid, or return false.
        return $result ? $id : false;
        
    }
    
}

