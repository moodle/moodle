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
 * @package    moodle
 * @subpackage portfolio
 * @author     Penny Leach <penny@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This file contains all the class definitions of the export formats.
 * They are implemented in php classes rather than just a simpler hash
 * Because it provides an easy way to do subtyping using php inheritance.
 */

/**
* the most basic type - pretty much everything is a subtype
*/
class portfolio_format_file {

    /**
     * array of mimetypes this format supports
     */
    public static function mimetypes() {
        return array(null);
    }

    /**
     * for multipart formats, eg html with attachments,
     * we need to have a directory to place associated files in
     * inside the zip file. this is the name of that directory
     */
    public static function get_file_directory() {
        return null;
    }

    /**
     * given a file, return a snippet of markup in whatever format
     * to link to that file.
     * usually involves the path given by {@link get_file_directory}
     */
    public static function file_output($file) {
        return '';
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
        return mimeinfo_from_icon('type', 'image.gif', true);
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
}

/**
* video format, subtype of file.
*
* for portfolio plugins that support videos specifically
*/
class portfolio_format_video extends portfolio_format_file {
    public static function mimetypes() {
        return array_merge(
            mimeinfo_from_icon('type', 'video.gif', true),
            mimeinfo_from_icon('type', 'avi.gif', true)
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
}

/**
 * base class for rich formats.
 * these are multipart - eg things with attachments
 */
class portfolio_format_rich {
    public static function mimetypes() {
        return array(null);
    }
}

/**
 * most commonly used rich format - richhtml - html with attachments
 * eg inline images
 */
class portfolio_format_richhtml extends portfolio_format_rich {
    public static function get_file_directory() {
        return 'site_files';
    }
    public static function file_output($file) {
        $path = self::get_file_directory() . '/' . $file->get_filename();
        if (in_array($file->get_mimetype(), portfolio_format_image::mimetypes())) {
            return '<img src="' . $path . '" alt="' . $file->get_filename() . '" />';
        }
        return '<a href="' . $path . '">' . $file->get_filename() . '</a>';
    }
}

class portfolio_format_leap extends portfolio_format_rich { }


/**
* later.... a moodle plugin might support this.
* it's commented out in portfolio_supported_formats so cannot currently be used.
*/
class portfolio_format_mbkp extends portfolio_format_rich {}
