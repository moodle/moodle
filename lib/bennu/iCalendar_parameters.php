<?php // $Id$

/**
 *  BENNU - PHP iCalendar library
 *  (c) 2005-2006 Ioannis Papaioannou (pj@moodle.org). All rights reserved.
 *
 *  Released under the LGPL.
 *
 *  See http://bennu.sourceforge.net/ for more information and downloads.
 *
 * @author Ioannis Papaioannou 
 * @version $Id$
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

class iCalendar_parameter {
    function multiple_values_allowed($parameter) {
        switch($parameter) {
            case 'DELEGATED-FROM':
            case 'DELEGATED-TO':
            case 'MEMBER':
                return true;
            default:
                return false;
        }
    }

    function default_value($parameter) {
        switch($parameter) {
            case 'CUTYPE':   return 'INDIVIDUAL';
            case 'FBTYPE':   return 'BUSY';
            case 'PARTSTAT': return 'NEEDS-ACTION';
            case 'RELATED':  return 'START';
            case 'RELTYPE':  return 'PARENT';
            case 'ROLE':     return 'REQ-PARTICIPANT';
            case 'RSVP':     return 'FALSE';
            default:         return NULL;
        }
    }

    function is_valid_value(&$parent_property, $parameter, $value) {
        switch($parameter) {
            // These must all be a URI
            case 'ALTREP':
            case 'DIR':
                return rfc2445_is_valid_value($value, RFC2445_TYPE_URI);
            break;

            // These must be CAL-ADDRESS, which is equivalent to URI
            case 'DELEGATED-FROM':
            case 'DELEGATED-TO':
            case 'MEMBER':
            case 'SENT-BY':
                return rfc2445_is_valid_value($value, RFC2445_TYPE_CAL_ADDRESS);
            break;

            // These are textual parameters, so the MUST NOT contain double quotes
            case 'CN':
                return (strpos($value, '"') === false);
            break;

            // These have enumerated legal values
            case 'CUTYPE':
                $value = strtoupper($value);
                return ($value == 'INDIVIDUAL' || $value == 'GROUP' || $value == 'RESOURCE' || $value == 'ROOM' || $value == 'UNKNOWN' || rfc2445_is_xname($value));
            break;

            case 'ENCODING':
                $value = strtoupper($value);
                return ($value == '8BIT' || $value == 'BASE64' || rfc2445_is_xname($value));
            break;

            case 'FBTYPE':
                $value = strtoupper($value);
                return ($value == 'FREE' || $value == 'BUSY' || $value == 'BUSY-UNAVAILABLE' || $value == 'BUSY-TENTATIVE' || rfc2445_is_xname($value));
            break;

            case 'FMTTYPE':
                $fmttypes = array(
                        'TEXT'        => array('PLAIN', 'RICHTEXT', 'ENRICHED', 'TAB-SEPARATED-VALUES', 'HTML', 'SGML',
                                               'VND.LATEX-Z', 'VND.FMI.FLEXSTOR'),
                        'MULTIPART'   => array('MIXED', 'ALTERNATIVE', 'DIGEST', 'PARALLEL', 'APPLEDOUBLE', 'HEADER-SET',
                                               'FORM-DATA', 'RELATED', 'REPORT', 'VOICE-MESSAGE', 'SIGNED', 'ENCRYPTED',
                                               'BYTERANGES'),
                        'MESSAGE'     => array('RFC822', 'PARTIAL', 'EXTERNAL-BODY', 'NEWS', 'HTTP'),
                        'APPLICATION' => array('OCTET-STREAM', 'POSTSCRIPT', 'ODA', 'ATOMICMAIL', 'ANDREW-INSET', 'SLATE',
                                               'WITA', 'DEC-DX', 'DCA-RFT', 'ACTIVEMESSAGE', 'RTF', 'APPLEFILE',
                                               'MAC-BINHEX40', 'NEWS-MESSAGE-ID', 'NEWS-TRANSMISSION', 'WORDPERFECT5.1',
                                               'PDF', 'ZIP', 'MACWRITEII', 'MSWORD', 'REMOTE-PRINTING', 'MATHEMATICA',
                                               'CYBERCASH', 'COMMONGROUND', 'IGES', 'RISCOS', 'ESHOP', 'X400-BP', 'SGML',
                                               'CALS-1840', 'PGP-ENCRYPTED', 'PGP-SIGNATURE', 'PGP-KEYS', 'VND.FRAMEMAKER',
                                               'VND.MIF', 'VND.MS-EXCEL', 'VND.MS-POWERPOINT', 'VND.MS-PROJECT',
                                               'VND.MS-WORKS', 'VND.MS-TNEF', 'VND.SVD', 'VND.MUSIC-NIFF', 'VND.MS-ARTGALRY',
                                               'VND.TRUEDOC', 'VND.KOAN', 'VND.STREET-STREAM', 'VND.FDF',
                                               'SET-PAYMENT-INITIATION', 'SET-PAYMENT', 'SET-REGISTRATION-INITIATION',
                                               'SET-REGISTRATION', 'VND.SEEMAIL', 'VND.BUSINESSOBJECTS',
                                               'VND.MERIDIAN-SLINGSHOT', 'VND.XARA', 'SGML-OPEN-CATALOG', 'VND.RAPID',
                                               'VND.ENLIVEN', 'VND.JAPANNET-REGISTRATION-WAKEUP', 
                                               'VND.JAPANNET-VERIFICATION-WAKEUP', 'VND.JAPANNET-PAYMENT-WAKEUP',
                                               'VND.JAPANNET-DIRECTORY-SERVICE', 'VND.INTERTRUST.DIGIBOX', 'VND.INTERTRUST.NNCP'),
                        'IMAGE'       => array('JPEG', 'GIF', 'IEF', 'G3FAX', 'TIFF', 'CGM', 'NAPLPS', 'VND.DWG', 'VND.SVF',
                                               'VND.DXF', 'PNG', 'VND.FPX', 'VND.NET-FPX'),
                        'AUDIO'       => array('BASIC', '32KADPCM', 'VND.QCELP'),
                        'VIDEO'       => array('MPEG', 'QUICKTIME', 'VND.VIVO', 'VND.MOTOROLA.VIDEO', 'VND.MOTOROLA.VIDEOP')
                );
                $value = strtoupper($value);
                if(rfc2445_is_xname($value)) {
                    return true;
                }
                @list($type, $subtype) = explode('/', $value);
                if(empty($type) || empty($subtype)) {
                    return false;
                }
                if(!isset($fmttypes[$type]) || !in_array($subtype, $fmttypes[$type])) {
                    return false;
                }
                return true;
            break;

            case 'LANGUAGE':
                $value = strtoupper($value);
                $parts = explode('-', $value);
                foreach($parts as $part) {
                    if(empty($part)) {
                        return false;
                    }
                    if(strspn($part, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') != strlen($part)) {
                        return false;
                    }
                }
                return true;
            break;

            case 'PARTSTAT':
                $value = strtoupper($value);
                switch($parent_property->parent_component) {
                    case 'VEVENT':
                        return ($value == 'NEEDS-ACTION' || $value == 'ACCEPTED' || $value == 'DECLINED' || $value == 'TENTATIVE'
                                || $value == 'DELEGATED' || rfc2445_is_xname($value));
                    break;
                    case 'VTODO':
                        return ($value == 'NEEDS-ACTION' || $value == 'ACCEPTED' || $value == 'DECLINED' || $value == 'TENTATIVE'
                                || $value == 'DELEGATED' || $value == 'COMPLETED' || $value == 'IN-PROCESS' || rfc2445_is_xname($value));
                    break;
                    case 'VJOURNAL':
                        return ($value == 'NEEDS-ACTION' || $value == 'ACCEPTED' || $value == 'DECLINED' || rfc2445_is_xname($value));
                    break;
                }
                return false;
            break;

            case 'RANGE':
                $value = strtoupper($value);
                return ($value == 'THISANDPRIOR' || $value == 'THISANDFUTURE');
            break;

            case 'RELATED':
                $value = strtoupper($value);
                return ($value == 'START' || $value == 'END');
            break;

            case 'RELTYPE':
                $value = strtoupper($value);
                return ($value == 'PARENT' || $value == 'CHILD' || $value == 'SIBLING' || rfc2445_is_xname($value));
            break;

            case 'ROLE':
                $value = strtoupper($value);
                return ($value == 'CHAIR' || $value == 'REQ-PARTICIPANT' || $value == 'OPT-PARTICIPANT' || $value == 'NON-PARTICIPANT' || rfc2445_is_xname($value));
            break;

            case 'RSVP':
                $value = strtoupper($value);
                return ($value == 'TRUE' || $value == 'FALSE');
            break;

            case 'TZID':
                if(empty($value)) {
                    return false;
                }
                return (strcspn($value, '";:,') == strlen($value));
            break;

            case 'VALUE':
                $value = strtoupper($value);
                return ($value == 'BINARY'    || $value == 'BOOLEAN'    || $value == 'CAL-ADDRESS' || $value == 'DATE'    ||
                        $value == 'DATE-TIME' || $value == 'DURATION'   || $value == 'FLOAT'       || $value == 'INTEGER' ||
                        $value == 'PERIOD'    || $value == 'RECUR'      || $value == 'TEXT'        || $value == 'TIME'    ||
                        $value == 'URI'       || $value == 'UTC-OFFSET' || rfc2445_is_xname($value));
            break;
        }
    }

    function do_value_formatting($parameter, $value) {
        switch($parameter) {
            // Parameters of type CAL-ADDRESS or URI MUST be double-quoted
            case 'ALTREP':
            case 'DIR':
            case 'DELEGATED-FROM':
            case 'DELEGATED-TO':
            case 'MEMBER':
            case 'SENT-BY':
                return '"'.$value.'"';
            break;

            // Textual parameter types must be double quoted if they contain COLON, SEMICOLON
            // or COMMA. Quoting always sounds easier and standards-conformant though.
            case 'CN':
                return '"'.$value.'"';
            break;

            // Parameters with enumerated legal values, just make them all caps
            case 'CUTYPE':
            case 'ENCODING':
            case 'FBTYPE':
            case 'FMTTYPE':
            case 'LANGUAGE':
            case 'PARTSTAT':
            case 'RANGE':
            case 'RELATED':
            case 'RELTYPE':
            case 'ROLE':
            case 'RSVP':
            case 'VALUE':
                return strtoupper($value);
            break;

            // Parameters we shouldn't be messing with
            case 'TZID':
                return $value;
            break;
        }
    }

    function undo_value_formatting($parameter, $value) {
    }

}

?>
