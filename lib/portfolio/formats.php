<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    core
 * @subpackage portfolio
 * @author     Penny Leach <penny@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This file contains all the class definitions of the export formats.
 * They are implemented in php classes rather than just a simpler hash
 * Because it provides an easy way to do subtyping using php inheritance.
 */

defined('MOODLE_INTERNAL') || die();

/**
 * base class to inherit from
 * do not use this anywhere in supported_formats
 */
abstract class portfolio_format {

    /**
     * array of mimetypes this format supports
     */
    public static function mimetypes() {
        throw new coding_exception('mimetypes() method needs to be overridden in each subclass of portfolio_format');
    }

    /**
     * for multipart formats, eg html with attachments,
     * we need to have a directory to place associated files in
     * inside the zip file. this is the name of that directory
     */
    public static function get_file_directory() {
        throw new coding_exception('get_file_directory() method needs to be overridden in each subclass of portfolio_format');
    }

    /**
     * given a file, return a snippet of markup in whatever format
     * to link to that file.
     * usually involves the path given by {@link get_file_directory}
     * this is not supported in subclasses of portfolio_format_file
     * since they're all just single files.
     *
     * @param stored_file $file
     * @param array       $options array of options to pass. can contain:
     *              attributes => hash of existing html attributes (eg title, height, width, etc)
     *                    and whatever the sub class adds into this list
     *
     * @return string some html or xml or whatever
     */
    public static function file_output($file, $options=null) {
        throw new coding_exception('file_output() method needs to be overridden in each subclass of portfolio_format');
    }

    public static function make_tag($file, $path, $attributes) {
        $srcattr = 'href';
        $tag     = 'a';
        $content = $file->get_filename();
        if (in_array($file->get_mimetype(), portfolio_format_image::mimetypes())) {
            $srcattr = 'src';
            $tag     = 'img';
            $content = '';
        }

        $attributes[$srcattr] = $path; // this will override anything we might have been passed (which is good)
        $dom = new DomDocument();
        $elem = null;
        if ($content) {
            $elem = $dom->createElement($tag, $content);
        } else {
            $elem = $dom->createElement($tag);
        }

        foreach ($attributes as $key => $value) {
            $elem->setAttribute($key, $value);
        }
        $dom->appendChild($elem);
        return $dom->saveXML($elem);
    }

    /**
     * whether this format conflicts with the given format
     * this is used for the case where an export location
     * "generally" supports something like FORMAT_PLAINHTML
     * but then in a specific export case, must add attachments
     * which means that FORMAT_RICHHTML is supported in that case
     * which implies removing support for FORMAT_PLAINHTML.
     * Note that conflicts don't have to be bi-directional
     * (eg FORMAT_PLAINHTML conflicts with FORMAT_RICHHTML
     * but not the other way around) and things within the class hierarchy
     * are resolved automatically anyway.
     *
     * This is really just between subclasses of format_rich
     * and subclasses of format_file.
     *
     * @param string $format one of the FORMAT_XX constants
     *
     * @return boolean
     */
    public static function conflicts($format) {
        return false;
    }
}

/**
* the most basic type - pretty much everything is a subtype
*/
class portfolio_format_file extends portfolio_format {

    public static function mimetypes() {
        return array();
    }

    public static function get_file_directory() {
        return false;
    }

    public static function file_output($file, $options=null) {
        throw new portfolio_exception('fileoutputnotsupported', 'portfolio');
    }
}

/**
* image format, subtype of file.
*/
class portfolio_format_image extends portfolio_format_file {
    /**
     * return all mimetypes that use image.gif (eg all images)
     */
    public static function mimetypes() {
        return mimeinfo_from_icon('type', 'image', true);
    }

    public static function conflicts($format) {
        return ($format == PORTFOLIO_FORMAT_RICHHTML
            || $format == PORTFOLIO_FORMAT_PLAINHTML);
    }
}

/**
* html format - could be used for an external cms or something
*
* in case we want to be really specific.
*/
class portfolio_format_plainhtml extends portfolio_format_file {

    public static function mimetypes() {
        return array('text/html');
    }

    public static function conflicts($format) {
        return ($format == PORTFOLIO_FORMAT_RICHHTML
            || $format == PORTFOLIO_FORMAT_FILE);
    }
}

/**
* video format, subtype of file.
*
* for portfolio plugins that support videos specifically
*/
class portfolio_format_video extends portfolio_format_file {
    public static function mimetypes() {
        return array_merge(
            mimeinfo_from_icon('type', 'video', true),
            mimeinfo_from_icon('type', 'avi', true)
        );
    }
}

/**
* class for plain text format.. not sure why we would need this yet
* but since resource module wants to export it... we can
*/
class portfolio_format_text extends portfolio_format_file {
    public static function mimetypes() {
        return array('text/plain');
    }

    public static function conflicts($format ) {
        return ($format == PORTFOLIO_FORMAT_PLAINHTML
            || $format == PORTFOLIO_FORMAT_RICHHTML);
    }
}

/**
 * base class for rich formats.
 * these are multipart - eg things with attachments
 */
abstract class portfolio_format_rich extends portfolio_format {

    public static function mimetypes() {
        return array();
    }

}

/**
 * most commonly used rich format - richhtml - html with attachments
 * eg inline images
 */
class portfolio_format_richhtml extends portfolio_format_rich {
    public static function get_file_directory() {
        return 'site_files/';
    }
    public static function file_output($file, $options=null) {
        $path = self::get_file_directory() . $file->get_filename();
        $attributes = array();
        if (!empty($options['attributes']) && is_array($options['attributes'])) {
            $attributes = $options['attributes'];
        }
        return self::make_tag($file, $path, $attributes);
    }
    public static function conflicts($format) { // TODO revisit the conflict with file, since we zip here
        return ($format == PORTFOLIO_FORMAT_PLAINHTML || $format == PORTFOLIO_FORMAT_FILE);
    }

}

class portfolio_format_leap2a extends portfolio_format_rich {

    public static function get_file_directory() {
        return 'files/';
    }

    public static function file_id_prefix() {
        return 'storedfile';
    }

    /**
     * return the link to a file
     *
     * @param stored_file $file
     * @param array       $options can contain the same as normal, with the addition of:
     *             entry => whether the file is a LEAP2A entry or just a bundled file (default true)
     */
    public static function file_output($file, $options=null) {
        $id = '';
        if (!is_array($options)) {
            $options = array();
        }
        if (!array_key_exists('entry', $options)) {
            $options['entry'] = true;
        }
        if (!empty($options['entry'])) {
            $path = 'portfolio:' . self::file_id_prefix() . $file->get_id();
        } else {
            $path = self::get_file_directory() . $file->get_filename();
        }
        $attributes = array();
        if (!empty($options['attributes']) && is_array($options['attributes'])) {
            $attributes = $options['attributes'];
        }
        $attributes['rel']    = 'enclosure';
        return self::make_tag($file, $path, $attributes);
    }

    public static function leap2a_writer(stdclass $user=null) {
        global $CFG;
        if (empty($user)) {
            global $USER;
            $user = $USER;
        }
        require_once($CFG->libdir . '/portfolio/formats/leap2a/lib.php');
        return new portfolio_format_leap2a_writer($user);
    }

    public static function manifest_name() {
        return 'leap2a.xml';
    }
}


/**
* later.... a moodle plugin might support this.
* it's commented out in portfolio_supported_formats so cannot currently be used.
*/
//class portfolio_format_mbkp extends portfolio_format_rich {}

/**
* 'PDF format', subtype of file.
*
* for portfolio plugins that support PDFs specifically
*/
class portfolio_format_pdf extends portfolio_format_file {
    public static function mimetypes() {
        return array('application/pdf');
    }
}

/**
* 'Document format', subtype of file.
*
* for portfolio plugins that support documents specifically
*/
class portfolio_format_document extends portfolio_format_file {
    public static function mimetypes() {
        return array_merge(
            array('text/plain', 'text/rtf'),
            mimeinfo_from_icon('type', 'word', true),
            mimeinfo_from_icon('type', 'docx', true),
            mimeinfo_from_icon('type', 'odt', true)
        );
    }
}

/**
* 'Spreadsheet format', subtype of file.
*
* for portfolio plugins that support spreadsheets specifically
*/
class portfolio_format_spreadsheet extends portfolio_format_file {
    public static function mimetypes() {
        return array_merge(
            mimeinfo_from_icon('type', 'excel', true),
            mimeinfo_from_icon('type', 'xlsm', true),
            mimeinfo_from_icon('type', 'ods', true)
        );
    }
}

/**
* 'Presentation format', subtype of file.
*
* for portfolio plugins that support presentation specifically
*/
class portfolio_format_presentation extends portfolio_format_file {
    public static function mimetypes() {
        return mimeinfo_from_icon('type', 'powerpoint', true);
    }
}
