<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * About validateUrlSyntax():
 * This function will verify if a http URL is formatted properly, returning
 * either with true or false.
 *
 * I used rfc #2396 URI: Generic Syntax as my guide when creating the
 * regular expression. For all the details see the comments below.
 *
 * Usage:
 *     validateUrlSyntax( url_to_check[, options])
 *
 *     url_to_check - string - The url to check
 *
 *     options - string - A optional string of options to set which parts of
 *          the url are required, optional, or not allowed. Each option
 *          must be followed by a "+" for required, "?" for optional, or
 *          "-" for not allowed.
 *
 *          s - Scheme. Allows "+?-", defaults to "s?"
 *              H - http:// Allows "+?-", defaults to "H?"
 *              S - https:// (SSL). Allows "+?-", defaults to "S?"
 *              E - mailto: (email). Allows "+?-", defaults to "E-"
 *              F - ftp:// Allows "+?-", defaults to "F-"
 *                  Dependant on scheme being enabled
 *          u - User section. Allows "+?-", defaults to "u?"
 *              P - Password in user section. Allows "+?-", defaults to "P?"
 *                  Dependant on user section being enabled
 *          a - Address (ip or domain). Allows "+?-", defaults to "a+"
 *              I - Ip address. Allows "+?-", defaults to "I?"
 *                  If I+, then domains are disabled
 *                  If I-, then domains are required
 *                  Dependant on address being enabled
 *          p - Port number. Allows "+?-", defaults to "p?"
 *          f - File path. Allows "+?-", defaults to "f?"
 *          q - Query section. Allows "+?-", defaults to "q?"
 *          r - Fragment (anchor). Allows "+?-", defaults to "r?"
 *
 *  Paste the funtion code, or include_once() this template at the top of the page
 *  you wish to use this function.
 *
 *
 * Examples:
 * <code>
 *  validateUrlSyntax('http://george@www.cnn.com/#top')
 *
 *  validateUrlSyntax('https://games.yahoo.com:8080/board/chess.htm?move=true')
 *
 *  validateUrlSyntax('http://www.hotmail.com/', 's+u-I-p-q-r-')
 *
 *  validateUrlSyntax('/directory/file.php#top', 's-u-a-p-f+')
 *
 *
 *  if (validateUrlSyntax('http://www.canowhoopass.com/', 'u-'))
 *  {
 *      echo 'URL SYNTAX IS VERIFIED';
 *  } else {
 *      echo 'URL SYNTAX IS ILLEGAL';
 *  }
 * </code>
 *
 * Last Edited:
 *  December 15th 2004
 *
 *
 * Changelog:
 *  December 15th 2004
 *    -Added new TLD's - .jobs, .mobi, .post and .travel. They are official, but not yet active.
 *
 *  August 31th 2004
 *    -Fixed bug allowing empty username even when it was required
 *    -Changed and added a few options to add extra schemes
 *    -Added mailto: ftp:// and http:// options
 *    -https option was 'l' now it is 'S' (capital)
 *    -Added password option. Now passwords can be disabled while usernames are ok (for email)
 *    -IP Address option was 'i' now it is 'I' (capital)
 *    -Options are now case sensitive
 *    -Added validateEmailSyntax() and validateFtpSyntax() functions below<br>
 *
 *  August 27th, 2004
 *    -IP group range is more specific. Used to allow 0-299. Now it is 0-255
 *    -Port range more specific. Used to allow 0-69999. Now it is 0-65535<br>
 *    -Fixed bug disallowing 'i-' option.<br>
 *    -Changed license to GPL
 *
 *  July 8th, 2004
 *    -Fixed bug disallowing 'l-' option. Thanks Dr. Cheap
 *
 *  June 15, 2004
 *    -Added options parameter to make it easier for people to plug the function in
 *     without needed to rework the code.
 *    -Split the example application away from the function
 *
 *  June 1, 2004
 *    -Complete rewrite
 *    -Now more modular
 *      -Easier to disable sections
 *      -Easier to port to other languages
 *      -Easier to port to verify email addresses
 *    -Uses only simple regular expressions so it is more portable
 *    -Follows RFC closer for domain names. Some "play" domains may break
 *    -Renamed from 'verifyUrl()' to 'validateUrlSyntax()'
 *    -Removed extra code which added 'http://' and trailing '/' if it was missing
 *      -That code was better suited for a massaging function, not verifying
 *    -Bug fixes:
 *      -Now splits up and forces '/path?query#fragment' order
 *      -No longer requires a path when using a query or fragment
 *
 *  August 29, 2003
 *    -Allowed port numbers above 9999. Now allows up to 69999
 *
 *  Sometime, 2002
 *    -Added new top level domains
 *      -aero, coop, museum, name, info, biz, pro
 *
 *  October 5, 2000
 *    -First Version
 *
 *
 * Intentional Limitations:
 *  -Does not verify url actually exists. Only validates the syntax
 *  -Strictly follows the RFC standards. Some urls exist in the wild which will
 *   not validate. Including ones with square brackets in the query section '[]'
 *
 *
 * Known Problems:
 *  -None at this time
 *
 *
 * Author(s):
 *  Rod Apeldoorn - rod(at)canowhoopass(dot)com
 *
 *
 * Homepage:
 *  http://www.canowhoopass.com/
 *
 *
 * Thanks!:
 *  -WEAV -Several members of Weav helped to test - http://weav.bc.ca/
 *  -There were also a number of emails from other developers expressing
 *   thanks and suggestions. It is nice to be appreciated. Thanks!
 *
 * Alternate Commercial Licenses:
 * For information in regards to alternate licensing, contact me.
 *
 * @package moodlecore
 * @copyright Copyright 2004, Rod Apeldoorn {@link http://www.canowhoopass.com/}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *  BEGINNING OF validateUrlSyntax() function
 */
function validateUrlSyntax( $urladdr, $options="" ){

    // Force Options parameter to be lower case
    // DISABLED PERMAMENTLY - OK to remove from code
    //    $options = strtolower($options);

    // Check Options Parameter
    if (!preg_match( '/^([sHSEFuPaIpfqr][+?-])*$/', $options ))
    {
        trigger_error("Options attribute malformed", E_USER_ERROR);
    }

    // Set Options Array, set defaults if options are not specified
    // Scheme
    if (strpos( $options, 's') === false) $aOptions['s'] = '?';
    else $aOptions['s'] = substr( $options, strpos( $options, 's') + 1, 1);
    // http://
    if (strpos( $options, 'H') === false) $aOptions['H'] = '?';
    else $aOptions['H'] = substr( $options, strpos( $options, 'H') + 1, 1);
    // https:// (SSL)
    if (strpos( $options, 'S') === false) $aOptions['S'] = '?';
    else $aOptions['S'] = substr( $options, strpos( $options, 'S') + 1, 1);
    // mailto: (email)
    if (strpos( $options, 'E') === false) $aOptions['E'] = '-';
    else $aOptions['E'] = substr( $options, strpos( $options, 'E') + 1, 1);
    // ftp://
    if (strpos( $options, 'F') === false) $aOptions['F'] = '-';
    else $aOptions['F'] = substr( $options, strpos( $options, 'F') + 1, 1);
    // User section
    if (strpos( $options, 'u') === false) $aOptions['u'] = '?';
    else $aOptions['u'] = substr( $options, strpos( $options, 'u') + 1, 1);
    // Password in user section
    if (strpos( $options, 'P') === false) $aOptions['P'] = '?';
    else $aOptions['P'] = substr( $options, strpos( $options, 'P') + 1, 1);
    // Address Section
    if (strpos( $options, 'a') === false) $aOptions['a'] = '+';
    else $aOptions['a'] = substr( $options, strpos( $options, 'a') + 1, 1);
    // IP Address in address section
    if (strpos( $options, 'I') === false) $aOptions['I'] = '?';
    else $aOptions['I'] = substr( $options, strpos( $options, 'I') + 1, 1);
    // Port number
    if (strpos( $options, 'p') === false) $aOptions['p'] = '?';
    else $aOptions['p'] = substr( $options, strpos( $options, 'p') + 1, 1);
    // File Path
    if (strpos( $options, 'f') === false) $aOptions['f'] = '?';
    else $aOptions['f'] = substr( $options, strpos( $options, 'f') + 1, 1);
    // Query Section
    if (strpos( $options, 'q') === false) $aOptions['q'] = '?';
    else $aOptions['q'] = substr( $options, strpos( $options, 'q') + 1, 1);
    // Fragment (Anchor)
    if (strpos( $options, 'r') === false) $aOptions['r'] = '?';
    else $aOptions['r'] = substr( $options, strpos( $options, 'r') + 1, 1);


    // Loop through options array, to search for and replace "-" to "{0}" and "+" to ""
    foreach($aOptions as $key => $value)
    {
        if ($value == '-')
        {
            $aOptions[$key] = '{0}';
        }
        if ($value == '+')
        {
            $aOptions[$key] = '';
        }
    }

    // DEBUGGING - Unescape following line to display to screen current option values
    // echo '<pre>'; print_r($aOptions); echo '</pre>';


    // Preset Allowed Characters
    $alphanum    = '[a-zA-Z0-9]';  // Alpha Numeric
    $unreserved  = '[a-zA-Z0-9_.!~*' . '\'' . '()-]';
    $escaped     = '(%[0-9a-fA-F]{2})'; // Escape sequence - In Hex - %6d would be a 'm'
    $reserved    = '[;/?:@&=+$,]'; // Special characters in the URI

    // Beginning Regular Expression
                       // Scheme - Allows for 'http://', 'https://', 'mailto:', or 'ftp://'
    $scheme            = '(';
    if     ($aOptions['H'] === '') { $scheme .= 'http://'; }
    elseif ($aOptions['S'] === '') { $scheme .= 'https://'; }
    elseif ($aOptions['E'] === '') { $scheme .= 'mailto:'; }
    elseif ($aOptions['F'] === '') { $scheme .= 'ftp://'; }
    else
    {
        if ($aOptions['H'] === '?') { $scheme .= '|(http://)'; }
        if ($aOptions['S'] === '?') { $scheme .= '|(https://)'; }
        if ($aOptions['E'] === '?') { $scheme .= '|(mailto:)'; }
        if ($aOptions['F'] === '?') { $scheme .= '|(ftp://)'; }
        $scheme = str_replace('(|', '(', $scheme); // fix first pipe
    }
    $scheme            .= ')' . $aOptions['s'];
    // End setting scheme

                       // User Info - Allows for 'username@' or 'username:password@'. Note: contrary to rfc, I removed ':' from username section, allowing it only in password.
                       //   /---------------- Username -----------------------\  /-------------------------------- Password ------------------------------\
    $userinfo          = '((' . $unreserved . '|' . $escaped . '|[;&=+$,]' . ')+(:(' . $unreserved . '|' . $escaped . '|[;:&=+$,]' . ')+)' . $aOptions['P'] . '@)' . $aOptions['u'];

                       // IP ADDRESS - Allows 0.0.0.0 to 255.255.255.255
    $ipaddress         = '((((2(([0-4][0-9])|(5[0-5])))|([01]?[0-9]?[0-9]))\.){3}((2(([0-4][0-9])|(5[0-5])))|([01]?[0-9]?[0-9])))';

                       // Tertiary Domain(s) - Optional - Multi - Although some sites may use other characters, the RFC says tertiary domains have the same naming restrictions as second level domains
    $domain_tertiary   = '(' . $alphanum . '(([a-zA-Z0-9-]{0,62})' . $alphanum . ')?\.)*';

/* MDL-9295 - take out domain_secondary here and below, so that URLs like http://localhost/ and lan addresses like http://host/ are accepted.
                       // Second Level Domain - Required - First and last characters must be Alpha-numeric. Hyphens are allowed inside.
    $domain_secondary  = '(' . $alphanum . '(([a-zA-Z0-9-]{0,62})' . $alphanum . ')?\.)';
*/

// we want more relaxed URLs in Moodle: MDL-11462
                       // Top Level Domain - First character must be Alpha. Last character must be AlphaNumeric. Hyphens are allowed inside.
    $domain_toplevel   = '([a-zA-Z](([a-zA-Z0-9-]*)[a-zA-Z0-9])?)';
/*                       // Top Level Domain - Required - Domain List Current As Of December 2004. Use above escaped line to be forgiving of possible future TLD's
    $domain_toplevel   = '(aero|biz|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|post|pro|travel|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|az|ax|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)';
*/

                       // Address can be IP address or Domain
    if ($aOptions['I'] === '{0}') {       // IP Address Not Allowed
        $address       = '(' . $domain_tertiary . /* MDL-9295 $domain_secondary . */ $domain_toplevel . ')';
    } elseif ($aOptions['I'] === '') {  // IP Address Required
        $address       = '(' . $ipaddress . ')';
    } else {                            // IP Address Optional
        $address       = '((' . $ipaddress . ')|(' . $domain_tertiary . /* MDL-9295 $domain_secondary . */ $domain_toplevel . '))';
    }
    $address = $address . $aOptions['a'];

                       // Port Number - :80 or :8080 or :65534 Allows range of :0 to :65535
                       //    (0-59999)         |(60000-64999)   |(65000-65499)    |(65500-65529)  |(65530-65535)
    $port_number       = '(:(([0-5]?[0-9]{1,4})|(6[0-4][0-9]{3})|(65[0-4][0-9]{2})|(655[0-2][0-9])|(6553[0-5])))' . $aOptions['p'];

                       // Path - Can be as simple as '/' or have multiple folders and filenames
    $path              = '(/((;)?(' . $unreserved . '|' . $escaped . '|' . '[:@&=+$,]' . ')+(/)?)*)' . $aOptions['f'];

                       // Query Section - Accepts ?var1=value1&var2=value2 or ?2393,1221 and much more
    $querystring       = '(\?(' . $reserved . '|' . $unreserved . '|' . $escaped . ')*)' . $aOptions['q'];

                       // Fragment Section - Accepts anchors such as #top
    $fragment          = '(\#(' . $reserved . '|' . $unreserved . '|' . $escaped . ')*)' . $aOptions['r'];


    // Building Regular Expression
    $regexp = '#^' . $scheme . $userinfo . $address . $port_number . $path . $querystring . $fragment . '$#i';

    // DEBUGGING - Uncomment Line Below To Display The Regular Expression Built
    // echo '<pre>' . htmlentities(wordwrap($regexp,70,"\n",1)) . '</pre>';

    // Running the regular expression
    if (preg_match( $regexp, $urladdr ))
    {
        return true; // The domain passed
    }
    else
    {
        return false; // The domain didn't pass the expression
    }

} // END Function validateUrlSyntax()



/**
 * About ValidateEmailSyntax():
 * This function uses the ValidateUrlSyntax() function to easily check the
 * syntax of an email address. It accepts the same options as ValidateURLSyntax
 * but defaults them for email addresses.
 *
 *
 * Usage:
 * <code>
 *  validateEmailSyntax( url_to_check[, options])
 * </code>
 *  url_to_check - string - The url to check
 *
 *  options - string - A optional string of options to set which parts of
 *          the url are required, optional, or not allowed. Each option
 *          must be followed by a "+" for required, "?" for optional, or
 *          "-" for not allowed. See ValidateUrlSyntax() docs for option list.
 *
 *  The default options are changed to:
 *      s-H-S-E+F-u+P-a+I-p-f-q-r-
 *
 *  This only allows an address of "name@domain".
 *
 * Examples:
 * <code>
 *  validateEmailSyntax('george@fakemail.com')
 *  validateEmailSyntax('mailto:george@fakemail.com', 's+')
 *  validateEmailSyntax('george@fakemail.com?subject=Hi%20George', 'q?')
 *  validateEmailSyntax('george@212.198.33.12', 'I?')
 * </code>
 *
 *
 * Author(s):
 *  Rod Apeldoorn - rod(at)canowhoopass(dot)com
 *
 *
 * Homepage:
 *  http://www.canowhoopass.com/
 *
 *
 * License:
 *  Copyright 2004 - Rod Apeldoorn
 *
 *  Released under same license as validateUrlSyntax(). For details, contact me.
 */

function validateEmailSyntax( $emailaddr, $options="" ){

    // Check Options Parameter
    if (!preg_match( '/^([sHSEFuPaIpfqr][+?-])*$/', $options ))
    {
        trigger_error("Options attribute malformed", E_USER_ERROR);
    }

    // Set Options Array, set defaults if options are not specified
    // Scheme
    if (strpos( $options, 's') === false) $aOptions['s'] = '-';
    else $aOptions['s'] = substr( $options, strpos( $options, 's') + 1, 1);
    // http://
    if (strpos( $options, 'H') === false) $aOptions['H'] = '-';
    else $aOptions['H'] = substr( $options, strpos( $options, 'H') + 1, 1);
    // https:// (SSL)
    if (strpos( $options, 'S') === false) $aOptions['S'] = '-';
    else $aOptions['S'] = substr( $options, strpos( $options, 'S') + 1, 1);
    // mailto: (email)
    if (strpos( $options, 'E') === false) $aOptions['E'] = '?';
    else $aOptions['E'] = substr( $options, strpos( $options, 'E') + 1, 1);
    // ftp://
    if (strpos( $options, 'F') === false) $aOptions['F'] = '-';
    else $aOptions['F'] = substr( $options, strpos( $options, 'F') + 1, 1);
    // User section
    if (strpos( $options, 'u') === false) $aOptions['u'] = '+';
    else $aOptions['u'] = substr( $options, strpos( $options, 'u') + 1, 1);
    // Password in user section
    if (strpos( $options, 'P') === false) $aOptions['P'] = '-';
    else $aOptions['P'] = substr( $options, strpos( $options, 'P') + 1, 1);
    // Address Section
    if (strpos( $options, 'a') === false) $aOptions['a'] = '+';
    else $aOptions['a'] = substr( $options, strpos( $options, 'a') + 1, 1);
    // IP Address in address section
    if (strpos( $options, 'I') === false) $aOptions['I'] = '-';
    else $aOptions['I'] = substr( $options, strpos( $options, 'I') + 1, 1);
    // Port number
    if (strpos( $options, 'p') === false) $aOptions['p'] = '-';
    else $aOptions['p'] = substr( $options, strpos( $options, 'p') + 1, 1);
    // File Path
    if (strpos( $options, 'f') === false) $aOptions['f'] = '-';
    else $aOptions['f'] = substr( $options, strpos( $options, 'f') + 1, 1);
    // Query Section
    if (strpos( $options, 'q') === false) $aOptions['q'] = '-';
    else $aOptions['q'] = substr( $options, strpos( $options, 'q') + 1, 1);
    // Fragment (Anchor)
    if (strpos( $options, 'r') === false) $aOptions['r'] = '-';
    else $aOptions['r'] = substr( $options, strpos( $options, 'r') + 1, 1);

    // Generate options
    $newoptions = '';
    foreach($aOptions as $key => $value)
    {
        $newoptions .= $key . $value;
    }

    // DEBUGGING - Uncomment line below to display generated options
    // echo '<pre>' . $newoptions . '</pre>';

    // Send to validateUrlSyntax() and return result
    return validateUrlSyntax( $emailaddr, $newoptions);

} // END Function validateEmailSyntax()



/**
 * About ValidateFtpSyntax():
 * This function uses the ValidateUrlSyntax() function to easily check the
 * syntax of an FTP address. It accepts the same options as ValidateURLSyntax
 * but defaults them for FTP addresses.
 *
 *
 * Usage:
 * <code>
 *  validateFtpSyntax( url_to_check[, options])
 * </code>
 *  url_to_check - string - The url to check
 *
 *  options - string - A optional string of options to set which parts of
 *          the url are required, optional, or not allowed. Each option
 *          must be followed by a "+" for required, "?" for optional, or
 *          "-" for not allowed. See ValidateUrlSyntax() docs for option list.
 *
 *  The default options are changed to:
 *      s?H-S-E-F+u?P?a+I?p?f?q-r-
 *
 * Examples:
 * <code>
 *  validateFtpSyntax('ftp://netscape.com')
 *  validateFtpSyntax('moz:iesucks@netscape.com')
 *  validateFtpSyntax('ftp://netscape.com:2121/browsers/ns7/', 'u-')
 * </code>
 *
 * Author(s):
 *  Rod Apeldoorn - rod(at)canowhoopass(dot)com
 *
 *
 * Homepage:
 *  http://www.canowhoopass.com/
 *
 *
 * License:
 *  Copyright 2004 - Rod Apeldoorn
 *
 *  Released under same license as validateUrlSyntax(). For details, contact me.
 */

function validateFtpSyntax( $ftpaddr, $options="" ){

    // Check Options Parameter
    if (!preg_match( '/^([sHSEFuPaIpfqr][+?-])*$/', $options ))
    {
        trigger_error("Options attribute malformed", E_USER_ERROR);
    }

    // Set Options Array, set defaults if options are not specified
    // Scheme
    if (strpos( $options, 's') === false) $aOptions['s'] = '?';
    else $aOptions['s'] = substr( $options, strpos( $options, 's') + 1, 1);
    // http://
    if (strpos( $options, 'H') === false) $aOptions['H'] = '-';
    else $aOptions['H'] = substr( $options, strpos( $options, 'H') + 1, 1);
    // https:// (SSL)
    if (strpos( $options, 'S') === false) $aOptions['S'] = '-';
    else $aOptions['S'] = substr( $options, strpos( $options, 'S') + 1, 1);
    // mailto: (email)
    if (strpos( $options, 'E') === false) $aOptions['E'] = '-';
    else $aOptions['E'] = substr( $options, strpos( $options, 'E') + 1, 1);
    // ftp://
    if (strpos( $options, 'F') === false) $aOptions['F'] = '+';
    else $aOptions['F'] = substr( $options, strpos( $options, 'F') + 1, 1);
    // User section
    if (strpos( $options, 'u') === false) $aOptions['u'] = '?';
    else $aOptions['u'] = substr( $options, strpos( $options, 'u') + 1, 1);
    // Password in user section
    if (strpos( $options, 'P') === false) $aOptions['P'] = '?';
    else $aOptions['P'] = substr( $options, strpos( $options, 'P') + 1, 1);
    // Address Section
    if (strpos( $options, 'a') === false) $aOptions['a'] = '+';
    else $aOptions['a'] = substr( $options, strpos( $options, 'a') + 1, 1);
    // IP Address in address section
    if (strpos( $options, 'I') === false) $aOptions['I'] = '?';
    else $aOptions['I'] = substr( $options, strpos( $options, 'I') + 1, 1);
    // Port number
    if (strpos( $options, 'p') === false) $aOptions['p'] = '?';
    else $aOptions['p'] = substr( $options, strpos( $options, 'p') + 1, 1);
    // File Path
    if (strpos( $options, 'f') === false) $aOptions['f'] = '?';
    else $aOptions['f'] = substr( $options, strpos( $options, 'f') + 1, 1);
    // Query Section
    if (strpos( $options, 'q') === false) $aOptions['q'] = '-';
    else $aOptions['q'] = substr( $options, strpos( $options, 'q') + 1, 1);
    // Fragment (Anchor)
    if (strpos( $options, 'r') === false) $aOptions['r'] = '-';
    else $aOptions['r'] = substr( $options, strpos( $options, 'r') + 1, 1);

    // Generate options
    $newoptions = '';
    foreach($aOptions as $key => $value)
    {
        $newoptions .= $key . $value;
    }

    // DEBUGGING - Uncomment line below to display generated options
    // echo '<pre>' . $newoptions . '</pre>';

    // Send to validateUrlSyntax() and return result
    return validateUrlSyntax( $ftpaddr, $newoptions);

} // END Function validateFtpSyntax()
