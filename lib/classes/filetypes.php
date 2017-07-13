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
 * Class to manage the custom filetypes list that is stored in a config variable.
 *
 * @package core
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * Class to manage the custom filetypes list that is stored in a config variable.
 *
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_filetypes {
    /** @var array Cached MIME types for current request */
    protected static $cachedtypes;

    /**
     * Gets default MIME types that are included as standard.
     *
     * Note: Use the function get_mimetypes_array to access this data including
     * any customisations the user might have made.
     *
     * @return array Default (pre-installed) MIME type information
     */
    protected static function get_default_types() {
        return array(
            'xxx' => array('type' => 'document/unknown', 'icon' => 'unknown'),
            '3gp' => array('type' => 'video/quicktime', 'icon' => 'quicktime', 'groups' => array('video'), 'string' => 'video'),
            '7z' => array('type' => 'application/x-7z-compressed', 'icon' => 'archive',
                    'groups' => array('archive'), 'string' => 'archive'),
            'aac' => array('type' => 'audio/aac', 'icon' => 'audio', 'groups' => array('audio', 'html_audio', 'web_audio'),
                    'string' => 'audio'),
            'accdb' => array('type' => 'application/msaccess', 'icon' => 'base'),
            'ai' => array('type' => 'application/postscript', 'icon' => 'eps', 'groups' => array('image'), 'string' => 'image'),
            'aif' => array('type' => 'audio/x-aiff', 'icon' => 'audio', 'groups' => array('audio'), 'string' => 'audio'),
            'aiff' => array('type' => 'audio/x-aiff', 'icon' => 'audio', 'groups' => array('audio'), 'string' => 'audio'),
            'aifc' => array('type' => 'audio/x-aiff', 'icon' => 'audio', 'groups' => array('audio'), 'string' => 'audio'),
            'applescript' => array('type' => 'text/plain', 'icon' => 'text'),
            'asc' => array('type' => 'text/plain', 'icon' => 'sourcecode'),
            'asm' => array('type' => 'text/plain', 'icon' => 'sourcecode'),
            'au' => array('type' => 'audio/au', 'icon' => 'audio', 'groups' => array('audio'), 'string' => 'audio'),
            'avi' => array('type' => 'video/x-ms-wm', 'icon' => 'avi',
                    'groups' => array('video', 'web_video'), 'string' => 'video'),
            'bmp' => array('type' => 'image/bmp', 'icon' => 'bmp', 'groups' => array('image'), 'string' => 'image'),
            'c' => array('type' => 'text/plain', 'icon' => 'sourcecode'),
            'cct' => array('type' => 'shockwave/director', 'icon' => 'flash'),
            'cpp' => array('type' => 'text/plain', 'icon' => 'sourcecode'),
            'cs' => array('type' => 'application/x-csh', 'icon' => 'sourcecode'),
            'css' => array('type' => 'text/css', 'icon' => 'text', 'groups' => array('web_file')),
            'csv' => array('type' => 'text/csv', 'icon' => 'spreadsheet', 'groups' => array('spreadsheet')),
            'dv' => array('type' => 'video/x-dv', 'icon' => 'quicktime', 'groups' => array('video'), 'string' => 'video'),
            'dmg' => array('type' => 'application/octet-stream', 'icon' => 'unknown'),

            'doc' => array('type' => 'application/msword', 'icon' => 'document', 'groups' => array('document')),
            'bdoc' => array('type' => 'application/x-digidoc', 'icon' => 'document', 'groups' => array('archive')),
            'cdoc' => array('type' => 'application/x-digidoc', 'icon' => 'document', 'groups' => array('archive')),
            'ddoc' => array('type' => 'application/x-digidoc', 'icon' => 'document', 'groups' => array('archive')),
            'docx' => array('type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'icon' => 'document', 'groups' => array('document')),
            'docm' => array('type' => 'application/vnd.ms-word.document.macroEnabled.12', 'icon' => 'document'),
            'dotx' => array('type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                    'icon' => 'document'),
            'dotm' => array('type' => 'application/vnd.ms-word.template.macroEnabled.12', 'icon' => 'document'),

            'dcr' => array('type' => 'application/x-director', 'icon' => 'flash'),
            'dif' => array('type' => 'video/x-dv', 'icon' => 'quicktime', 'groups' => array('video'), 'string' => 'video'),
            'dir' => array('type' => 'application/x-director', 'icon' => 'flash'),
            'dxr' => array('type' => 'application/x-director', 'icon' => 'flash'),
            'eps' => array('type' => 'application/postscript', 'icon' => 'eps'),
            'epub' => array('type' => 'application/epub+zip', 'icon' => 'epub', 'groups' => array('document')),
            'fdf' => array('type' => 'application/vnd.fdf', 'icon' => 'pdf'),
            'flac' => array('type' => 'audio/flac', 'icon' => 'audio', 'groups' => array('audio', 'html_audio', 'web_audio'),
                    'string' => 'audio'),
            'flv' => array('type' => 'video/x-flv', 'icon' => 'flash',
                    'groups' => array('video', 'web_video'), 'string' => 'video'),
            'f4v' => array('type' => 'video/mp4', 'icon' => 'flash', 'groups' => array('video', 'web_video'), 'string' => 'video'),

            'gallery' => array('type' => 'application/x-smarttech-notebook', 'icon' => 'archive'),
            'galleryitem' => array('type' => 'application/x-smarttech-notebook', 'icon' => 'archive'),
            'gallerycollection' => array('type' => 'application/x-smarttech-notebook', 'icon' => 'archive'),
            'gdraw' => array('type' => 'application/vnd.google-apps.drawing', 'icon' => 'image', 'groups' => array('image')),
            'gdoc' => array('type' => 'application/vnd.google-apps.document', 'icon' => 'document', 'groups' => array('document')),
            'gsheet' => array('type' => 'application/vnd.google-apps.spreadsheet', 'icon' => 'spreadsheet',
                    'groups' => array('spreadsheet')),
            'gslides' => array('type' => 'application/vnd.google-apps.presentation', 'icon' => 'powerpoint',
                    'groups' => array('presentation')),
            'gif' => array('type' => 'image/gif', 'icon' => 'gif', 'groups' => array('image', 'web_image'), 'string' => 'image'),
            'gtar' => array('type' => 'application/x-gtar', 'icon' => 'archive',
                    'groups' => array('archive'), 'string' => 'archive'),
            'tgz' => array('type' => 'application/g-zip', 'icon' => 'archive', 'groups' => array('archive'), 'string' => 'archive'),
            'gz' => array('type' => 'application/g-zip', 'icon' => 'archive', 'groups' => array('archive'), 'string' => 'archive'),
            'gzip' => array('type' => 'application/g-zip', 'icon' => 'archive',
                    'groups' => array('archive'), 'string' => 'archive'),
            'h' => array('type' => 'text/plain', 'icon' => 'sourcecode'),
            'hpp' => array('type' => 'text/plain', 'icon' => 'sourcecode'),
            'hqx' => array('type' => 'application/mac-binhex40', 'icon' => 'archive',
                    'groups' => array('archive'), 'string' => 'archive'),
            'htc' => array('type' => 'text/x-component', 'icon' => 'markup'),
            'html' => array('type' => 'text/html', 'icon' => 'html', 'groups' => array('web_file')),
            'xhtml' => array('type' => 'application/xhtml+xml', 'icon' => 'html', 'groups' => array('web_file')),
            'htm' => array('type' => 'text/html', 'icon' => 'html', 'groups' => array('web_file')),
            'ico' => array('type' => 'image/vnd.microsoft.icon', 'icon' => 'image',
                    'groups' => array('image'), 'string' => 'image'),
            'ics' => array('type' => 'text/calendar', 'icon' => 'text'),
            'isf' => array('type' => 'application/inspiration', 'icon' => 'isf'),
            'ist' => array('type' => 'application/inspiration.template', 'icon' => 'isf'),
            'java' => array('type' => 'text/plain', 'icon' => 'sourcecode'),
            'jar' => array('type' => 'application/java-archive', 'icon' => 'archive'),
            'jcb' => array('type' => 'text/xml', 'icon' => 'markup'),
            'jcl' => array('type' => 'text/xml', 'icon' => 'markup'),
            'jcw' => array('type' => 'text/xml', 'icon' => 'markup'),
            'jmt' => array('type' => 'text/xml', 'icon' => 'markup'),
            'jmx' => array('type' => 'text/xml', 'icon' => 'markup'),
            'jnlp' => array('type' => 'application/x-java-jnlp-file', 'icon' => 'markup'),
            'jpe' => array('type' => 'image/jpeg', 'icon' => 'jpeg', 'groups' => array('image', 'web_image'), 'string' => 'image'),
            'jpeg' => array('type' => 'image/jpeg', 'icon' => 'jpeg', 'groups' => array('image', 'web_image'), 'string' => 'image'),
            'jpg' => array('type' => 'image/jpeg', 'icon' => 'jpeg', 'groups' => array('image', 'web_image'), 'string' => 'image'),
            'jqz' => array('type' => 'text/xml', 'icon' => 'markup'),
            'js' => array('type' => 'application/x-javascript', 'icon' => 'text', 'groups' => array('web_file')),
            'latex' => array('type' => 'application/x-latex', 'icon' => 'text'),
            'm' => array('type' => 'text/plain', 'icon' => 'sourcecode'),
            'mbz' => array('type' => 'application/vnd.moodle.backup', 'icon' => 'moodle'),
            'mdb' => array('type' => 'application/x-msaccess', 'icon' => 'base'),
            'mht' => array('type' => 'message/rfc822', 'icon' => 'archive'),
            'mhtml' => array('type' => 'message/rfc822', 'icon' => 'archive'),
            'mov' => array('type' => 'video/quicktime', 'icon' => 'quicktime',
                    'groups' => array('video', 'web_video', 'html_video'), 'string' => 'video'),
            'movie' => array('type' => 'video/x-sgi-movie', 'icon' => 'quicktime', 'groups' => array('video'), 'string' => 'video'),
            'mw' => array('type' => 'application/maple', 'icon' => 'math'),
            'mws' => array('type' => 'application/maple', 'icon' => 'math'),
            'm3u' => array('type' => 'audio/x-mpegurl', 'icon' => 'mp3', 'groups' => array('audio'), 'string' => 'audio'),
            'mp3' => array('type' => 'audio/mp3', 'icon' => 'mp3', 'groups' => array('audio', 'html_audio', 'web_audio'),
                    'string' => 'audio'),
            'mp4' => array('type' => 'video/mp4', 'icon' => 'mpeg', 'groups' => array('html_video', 'video', 'web_video'),
                    'string' => 'video'),
            'm4v' => array('type' => 'video/mp4', 'icon' => 'mpeg', 'groups' => array('html_video', 'video', 'web_video'),
                    'string' => 'video'),
            'm4a' => array('type' => 'audio/mp4', 'icon' => 'mp3', 'groups' => array('audio', 'html_audio', 'web_audio'),
                    'string' => 'audio'),
            'mpeg' => array('type' => 'video/mpeg', 'icon' => 'mpeg', 'groups' => array('video', 'web_video'),
                    'string' => 'video'),
            'mpe' => array('type' => 'video/mpeg', 'icon' => 'mpeg', 'groups' => array('video', 'web_video'),
                    'string' => 'video'),
            'mpg' => array('type' => 'video/mpeg', 'icon' => 'mpeg', 'groups' => array('video', 'web_video'),
                    'string' => 'video'),
            'mpr' => array('type' => 'application/vnd.moodle.profiling', 'icon' => 'moodle'),

            'nbk' => array('type' => 'application/x-smarttech-notebook', 'icon' => 'archive'),
            'notebook' => array('type' => 'application/x-smarttech-notebook', 'icon' => 'archive'),

            'odt' => array('type' => 'application/vnd.oasis.opendocument.text', 'icon' => 'writer', 'groups' => array('document')),
            'ott' => array('type' => 'application/vnd.oasis.opendocument.text-template',
                    'icon' => 'writer', 'groups' => array('document')),
            'oth' => array('type' => 'application/vnd.oasis.opendocument.text-web', 'icon' => 'oth', 'groups' => array('document')),
            'odm' => array('type' => 'application/vnd.oasis.opendocument.text-master', 'icon' => 'writer'),
            'odg' => array('type' => 'application/vnd.oasis.opendocument.graphics', 'icon' => 'draw'),
            'otg' => array('type' => 'application/vnd.oasis.opendocument.graphics-template', 'icon' => 'draw'),
            'odp' => array('type' => 'application/vnd.oasis.opendocument.presentation', 'icon' => 'impress',
                    'groups' => array('presentation')),
            'otp' => array('type' => 'application/vnd.oasis.opendocument.presentation-template', 'icon' => 'impress',
                    'groups' => array('presentation')),
            'ods' => array('type' => 'application/vnd.oasis.opendocument.spreadsheet',
                    'icon' => 'calc', 'groups' => array('spreadsheet')),
            'ots' => array('type' => 'application/vnd.oasis.opendocument.spreadsheet-template',
                    'icon' => 'calc', 'groups' => array('spreadsheet')),
            'odc' => array('type' => 'application/vnd.oasis.opendocument.chart', 'icon' => 'chart'),
            'odf' => array('type' => 'application/vnd.oasis.opendocument.formula', 'icon' => 'math'),
            'odb' => array('type' => 'application/vnd.oasis.opendocument.database', 'icon' => 'base'),
            'odi' => array('type' => 'application/vnd.oasis.opendocument.image', 'icon' => 'draw'),
            'oga' => array('type' => 'audio/ogg', 'icon' => 'audio', 'groups' => array('audio', 'html_audio', 'web_audio'),
                    'string' => 'audio'),
            'ogg' => array('type' => 'audio/ogg', 'icon' => 'audio', 'groups' => array('audio', 'html_audio', 'web_audio'),
                    'string' => 'audio'),
            'ogv' => array('type' => 'video/ogg', 'icon' => 'video', 'groups' => array('html_video', 'video', 'web_video'),
                    'string' => 'video'),

            'pct' => array('type' => 'image/pict', 'icon' => 'image', 'groups' => array('image'), 'string' => 'image'),
            'pdf' => array('type' => 'application/pdf', 'icon' => 'pdf', 'groups' => array('document')),
            'php' => array('type' => 'text/plain', 'icon' => 'sourcecode'),
            'pic' => array('type' => 'image/pict', 'icon' => 'image', 'groups' => array('image'), 'string' => 'image'),
            'pict' => array('type' => 'image/pict', 'icon' => 'image', 'groups' => array('image'), 'string' => 'image'),
            'png' => array('type' => 'image/png', 'icon' => 'png', 'groups' => array('image', 'web_image'), 'string' => 'image'),
            'pps' => array('type' => 'application/vnd.ms-powerpoint', 'icon' => 'powerpoint', 'groups' => array('presentation')),
            'ppt' => array('type' => 'application/vnd.ms-powerpoint', 'icon' => 'powerpoint', 'groups' => array('presentation')),
            'pptx' => array('type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'icon' => 'powerpoint', 'groups' => array('presentation')),
            'pptm' => array('type' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12', 'icon' => 'powerpoint',
                    'groups' => array('presentation')),
            'potx' => array('type' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
                    'icon' => 'powerpoint', 'groups' => array('presentation')),
            'potm' => array('type' => 'application/vnd.ms-powerpoint.template.macroEnabled.12', 'icon' => 'powerpoint',
                    'groups' => array('presentation')),
            'ppam' => array('type' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12', 'icon' => 'powerpoint',
                    'groups' => array('presentation')),
            'ppsx' => array('type' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                    'icon' => 'powerpoint', 'groups' => array('presentation')),
            'ppsm' => array('type' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12', 'icon' => 'powerpoint',
                    'groups' => array('presentation')),
            'ps' => array('type' => 'application/postscript', 'icon' => 'pdf'),
            'pub' => array('type' => 'application/x-mspublisher', 'icon' => 'publisher', 'groups' => array('presentation')),

            'qt' => array('type' => 'video/quicktime', 'icon' => 'quicktime',
                    'groups' => array('video', 'web_video'), 'string' => 'video'),
            'ra' => array('type' => 'audio/x-realaudio-plugin', 'icon' => 'audio',
                    'groups' => array('audio', 'web_audio'), 'string' => 'audio'),
            'ram' => array('type' => 'audio/x-pn-realaudio-plugin', 'icon' => 'audio',
                    'groups' => array('audio'), 'string' => 'audio'),
            'rar' => array('type' => 'application/x-rar-compressed', 'icon' => 'archive',
                    'groups' => array('archive'), 'string' => 'archive'),
            'rhb' => array('type' => 'text/xml', 'icon' => 'markup'),
            'rm' => array('type' => 'audio/x-pn-realaudio-plugin', 'icon' => 'audio',
                    'groups' => array('audio'), 'string' => 'audio'),
            'rmvb' => array('type' => 'application/vnd.rn-realmedia-vbr', 'icon' => 'video',
                    'groups' => array('video'), 'string' => 'video'),
            'rtf' => array('type' => 'text/rtf', 'icon' => 'text', 'groups' => array('document')),
            'rtx' => array('type' => 'text/richtext', 'icon' => 'text'),
            'rv' => array('type' => 'audio/x-pn-realaudio-plugin', 'icon' => 'audio',
                    'groups' => array('video'), 'string' => 'video'),
            'scss' => array('type' => 'text/x-scss', 'icon' => 'text', 'groups' => array('web_file')),
            'sh' => array('type' => 'application/x-sh', 'icon' => 'sourcecode'),
            'sit' => array('type' => 'application/x-stuffit', 'icon' => 'archive',
                    'groups' => array('archive'), 'string' => 'archive'),
            'smi' => array('type' => 'application/smil', 'icon' => 'text'),
            'smil' => array('type' => 'application/smil', 'icon' => 'text'),
            'sqt' => array('type' => 'text/xml', 'icon' => 'markup'),
            'svg' => array('type' => 'image/svg+xml', 'icon' => 'image',
                    'groups' => array('image', 'web_image'), 'string' => 'image'),
            'svgz' => array('type' => 'image/svg+xml', 'icon' => 'image',
                    'groups' => array('image', 'web_image'), 'string' => 'image'),
            'swa' => array('type' => 'application/x-director', 'icon' => 'flash'),
            'swf' => array('type' => 'application/x-shockwave-flash', 'icon' => 'flash', 'groups' => array('video', 'web_video')),
            'swfl' => array('type' => 'application/x-shockwave-flash', 'icon' => 'flash', 'groups' => array('video', 'web_video')),

            'sxw' => array('type' => 'application/vnd.sun.xml.writer', 'icon' => 'writer'),
            'stw' => array('type' => 'application/vnd.sun.xml.writer.template', 'icon' => 'writer'),
            'sxc' => array('type' => 'application/vnd.sun.xml.calc', 'icon' => 'calc'),
            'stc' => array('type' => 'application/vnd.sun.xml.calc.template', 'icon' => 'calc'),
            'sxd' => array('type' => 'application/vnd.sun.xml.draw', 'icon' => 'draw'),
            'std' => array('type' => 'application/vnd.sun.xml.draw.template', 'icon' => 'draw'),
            'sxi' => array('type' => 'application/vnd.sun.xml.impress', 'icon' => 'impress', 'groups' => array('presentation')),
            'sti' => array('type' => 'application/vnd.sun.xml.impress.template', 'icon' => 'impress',
                    'groups' => array('presentation')),
            'sxg' => array('type' => 'application/vnd.sun.xml.writer.global', 'icon' => 'writer'),
            'sxm' => array('type' => 'application/vnd.sun.xml.math', 'icon' => 'math'),

            'tar' => array('type' => 'application/x-tar', 'icon' => 'archive', 'groups' => array('archive'), 'string' => 'archive'),
            'tif' => array('type' => 'image/tiff', 'icon' => 'tiff', 'groups' => array('image'), 'string' => 'image'),
            'tiff' => array('type' => 'image/tiff', 'icon' => 'tiff', 'groups' => array('image'), 'string' => 'image'),
            'tex' => array('type' => 'application/x-tex', 'icon' => 'text'),
            'texi' => array('type' => 'application/x-texinfo', 'icon' => 'text'),
            'texinfo' => array('type' => 'application/x-texinfo', 'icon' => 'text'),
            'tsv' => array('type' => 'text/tab-separated-values', 'icon' => 'text'),
            'txt' => array('type' => 'text/plain', 'icon' => 'text', 'defaulticon' => true),
            'vtt' => array('type' => 'text/vtt', 'icon' => 'text', 'groups' => array('html_track')),
            'wav' => array('type' => 'audio/wav', 'icon' => 'wav', 'groups' => array('audio', 'html_audio', 'web_audio'),
                    'string' => 'audio'),
            'webm' => array('type' => 'video/webm', 'icon' => 'video', 'groups' => array('html_video', 'video', 'web_video'),
                    'string' => 'video'),
            'wmv' => array('type' => 'video/x-ms-wmv', 'icon' => 'wmv', 'groups' => array('video'), 'string' => 'video'),
            'asf' => array('type' => 'video/x-ms-asf', 'icon' => 'wmv', 'groups' => array('video'), 'string' => 'video'),
            'wma' => array('type' => 'audio/x-ms-wma', 'icon' => 'audio', 'groups' => array('audio'), 'string' => 'audio'),

            'xbk' => array('type' => 'application/x-smarttech-notebook', 'icon' => 'archive'),
            'xdp' => array('type' => 'application/vnd.adobe.xdp+xml', 'icon' => 'pdf'),
            'xfd' => array('type' => 'application/vnd.xfdl', 'icon' => 'pdf'),
            'xfdf' => array('type' => 'application/vnd.adobe.xfdf', 'icon' => 'pdf'),

            'xls' => array('type' => 'application/vnd.ms-excel', 'icon' => 'spreadsheet', 'groups' => array('spreadsheet')),
            'xlsx' => array('type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'icon' => 'spreadsheet',
                'groups' => array('spreadsheet')),
            'xlsm' => array('type' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
                    'icon' => 'spreadsheet', 'groups' => array('spreadsheet')),
            'xltx' => array('type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                    'icon' => 'spreadsheet'),
            'xltm' => array('type' => 'application/vnd.ms-excel.template.macroEnabled.12', 'icon' => 'spreadsheet'),
            'xlsb' => array('type' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12', 'icon' => 'spreadsheet'),
            'xlam' => array('type' => 'application/vnd.ms-excel.addin.macroEnabled.12', 'icon' => 'spreadsheet'),

            'xml' => array('type' => 'application/xml', 'icon' => 'markup'),
            'xsl' => array('type' => 'text/xml', 'icon' => 'markup'),

            'zip' => array('type' => 'application/zip', 'icon' => 'archive', 'groups' => array('archive'), 'string' => 'archive')
        );
    }

    /**
     * Given a mimetype - return a valid file extension for it.
     *
     * @param $mimetype string
     * @return string|bool False if the mimetype was not known, a string indicating a valid file extension otherwise. It may not
     *                     be the only valid file extension - just the first one found.
     */
    public static function get_file_extension($mimetype) {
        $types = self::get_types();
        foreach ($types as $extension => $info) {
            if ($info['type'] == $mimetype) {
                return $extension;
            }
        }
        return false;
    }

    /**
     * Gets all the current types.
     *
     * @return array Associative array from extension to array of data about type
     */
    public static function &get_types() {
        // If it was already done in this request, use cache.
        if (self::$cachedtypes) {
            return self::$cachedtypes;
        }

        // Get defaults.
        $mimetypes = self::get_default_types();

        // If there are no custom types, just return.
        $custom = self::get_custom_types();
        if (empty($custom)) {
            return $mimetypes;
        }

        // Check value is an array.
        if (!is_array($custom)) {
            debugging('Invalid $CFG->customfiletypes (not array)', DEBUG_DEVELOPER);
            return $mimetypes;
        }

        foreach ($custom as $customentry) {
            // Each entry is a stdClass object similar to the array values above.
            if (empty($customentry->extension)) {
                debugging('Invalid $CFG->customfiletypes entry (extension field required)',
                        DEBUG_DEVELOPER);
                continue;
            }

            // To delete a standard entry, set 'deleted' to true.
            if (!empty($customentry->deleted)) {
                unset($mimetypes[$customentry->extension]);
                continue;
            }

            // Check required fields.
            if (empty($customentry->type) || empty($customentry->icon)) {
                debugging('Invalid $CFG->customfiletypes entry ' . $customentry->extension .
                        ' (type and icon fields required)', DEBUG_DEVELOPER);
                continue;
            }

            // Build result array.
            $result = array('type' => $customentry->type, 'icon' => $customentry->icon);
            if (!empty($customentry->groups)) {
                if (!is_array($customentry->groups)) {
                    debugging('Invalid $CFG->customfiletypes entry ' . $customentry->extension .
                            ' (groups field not array)', DEBUG_DEVELOPER);
                    continue;
                }
                $result['groups'] = $customentry->groups;
            }
            if (!empty($customentry->string)) {
                if (!is_string($customentry->string)) {
                    debugging('Invalid $CFG->customfiletypes entry ' . $customentry->extension .
                            ' (string field not string)', DEBUG_DEVELOPER);
                    continue;
                }
                $result['string'] = $customentry->string;
            }
            if (!empty($customentry->defaulticon)) {
                if (!is_bool($customentry->defaulticon)) {
                    debugging('Invalid $CFG->customfiletypes entry ' . $customentry->extension .
                            ' (defaulticon field not bool)', DEBUG_DEVELOPER);
                    continue;
                }
                $result['defaulticon'] = $customentry->defaulticon;
            }
            if (!empty($customentry->customdescription)) {
                if (!is_string($customentry->customdescription)) {
                    debugging('Invalid $CFG->customfiletypes entry ' . $customentry->extension .
                            ' (customdescription field not string)', DEBUG_DEVELOPER);
                    continue;
                }
                // As the name suggests, this field is used only for custom entries.
                $result['customdescription'] = $customentry->customdescription;
            }

            // Track whether it is a custom filetype or a modified existing
            // filetype.
            if (array_key_exists($customentry->extension, $mimetypes)) {
                $result['modified'] = true;
            } else {
                $result['custom'] = true;
            }

            // Add result array to list.
            $mimetypes[$customentry->extension] = $result;
        }

        self::$cachedtypes = $mimetypes;
        return self::$cachedtypes;
    }

    /**
     * Gets custom types from config variable, after decoding the JSON if required.
     *
     * @return array Array of custom types (empty array if none)
     */
    protected static function get_custom_types() {
        global $CFG;
        if (!empty($CFG->customfiletypes)) {
            if (is_array($CFG->customfiletypes)) {
                // You can define this as an array in config.php...
                return $CFG->customfiletypes;
            } else {
                // Or as a JSON string in the config table.
                return json_decode($CFG->customfiletypes);
            }
        } else {
            return array();
        }
    }

    /**
     * Sets the custom types into config variable, encoding into JSON.
     *
     * @param array $types Array of custom types
     * @throws coding_exception If the custom types are fixed in config.php.
     */
    protected static function set_custom_types(array $types) {
        global $CFG;
        // Check the setting hasn't been forced.
        if (array_key_exists('customfiletypes', $CFG->config_php_settings)) {
            throw new coding_exception('Cannot set custom filetypes because they ' .
                    'are defined in config.php');
        }
        if (empty($types)) {
            unset_config('customfiletypes');
        } else {
            set_config('customfiletypes', json_encode(array_values($types)));
        }

        // Clear the cached type list.
        self::reset_caches();
    }

    /**
     * Clears the type cache. This is not needed in normal use as the
     * set_custom_types function automatically clears the cache. Intended for
     * use in unit tests.
     */
    public static function reset_caches() {
        self::$cachedtypes = null;
    }

    /**
     * Gets the default types that have been deleted. Returns an array containing
     * the defaults of all those types.
     *
     * @return array Array (same format as get_mimetypes_array)
     */
    public static function get_deleted_types() {
        $defaults = self::get_default_types();
        $deleted = array();
        foreach (self::get_custom_types() as $customentry) {
            if (!empty($customentry->deleted)) {
                $deleted[$customentry->extension] = $defaults[$customentry->extension];
            }
        }
        return $deleted;
    }

    /**
     * Adds a new entry to the list of custom filetypes.
     *
     * @param string $extension File extension without dot, e.g. 'doc'
     * @param string $mimetype MIME type e.g. 'application/msword'
     * @param string $coreicon Core icon to use e.g. 'document'
     * @param array $groups Array of group strings that this type belongs to
     * @param string $corestring Custom lang string name in mimetypes.php
     * @param string $customdescription Custom description (plain text/multilang)
     * @param bool $defaulticon True if this should be the default icon for the type
     * @throws coding_exception If the extension already exists, or otherwise invalid
     */
    public static function add_type($extension, $mimetype, $coreicon,
            array $groups = array(), $corestring = '', $customdescription = '',
            $defaulticon = false) {
        // Check for blank extensions or incorrectly including the dot.
        $extension = (string)$extension;
        if ($extension === '' || $extension[0] === '.') {
            throw new coding_exception('Invalid extension .' . $extension);
        }

        // Check extension not already used.
        $mimetypes = get_mimetypes_array();
        if (array_key_exists($extension, $mimetypes)) {
            throw new coding_exception('Extension ' . $extension . ' already exists');
        }

        // For default icon, check there isn't already something with default icon
        // set for that MIME type.
        if ($defaulticon) {
            foreach ($mimetypes as $type) {
                if ($type['type'] === $mimetype && !empty($type['defaulticon'])) {
                    throw new coding_exception('MIME type ' . $mimetype .
                            ' already has a default icon set');
                }
            }
        }

        // Get existing custom filetype list.
        $customs = self::get_custom_types();

        // Check if there's a 'deleted' entry for the extension, if so then get
        // rid of it.
        foreach ($customs as $key => $custom) {
            if ($custom->extension === $extension) {
                unset($customs[$key]);
            }
        }

        // Set up config record for new type.
        $newtype = self::create_config_record($extension, $mimetype, $coreicon, $groups,
                $corestring, $customdescription, $defaulticon);

        // See if there's a default value with this extension.
        $needsadding = true;
        $defaults = self::get_default_types();
        if (array_key_exists($extension, $defaults)) {
            // If it has the same values, we don't need to add it.
            $defaultvalue = $defaults[$extension];
            $modified = (array)$newtype;
            unset($modified['extension']);
            ksort($defaultvalue);
            ksort($modified);
            if ($modified === $defaultvalue) {
                $needsadding = false;
            }
        }

        // Add to array and set in config.
        if ($needsadding) {
            $customs[] = $newtype;
        }
        self::set_custom_types($customs);
    }

    /**
     * Updates an entry in the list of filetypes in config.
     *
     * @param string $extension File extension without dot, e.g. 'doc'
     * @param string $newextension New file extension (same if not changing)
     * @param string $mimetype MIME type e.g. 'application/msword'
     * @param string $coreicon Core icon to use e.g. 'document'
     * @param array $groups Array of group strings that this type belongs to
     * @param string $corestring Custom lang string name in mimetypes.php
     * @param string $customdescription Custom description (plain text/multilang)
     * @param bool $defaulticon True if this should be the default icon for the type
     * @throws coding_exception If the new extension already exists, or otherwise invalid
     */
    public static function update_type($extension, $newextension, $mimetype, $coreicon,
            array $groups = array(), $corestring = '', $customdescription = '',
            $defaulticon = false) {

        // Extension must exist.
        $extension = (string)$extension;
        $mimetypes = get_mimetypes_array();
        if (!array_key_exists($extension, $mimetypes)) {
            throw new coding_exception('Extension ' . $extension . ' not found');
        }

        // If there's a new extension then this must not exist.
        $newextension = (string)$newextension;
        if ($newextension !== $extension) {
            if ($newextension === '' || $newextension[0] === '.') {
                throw new coding_exception('Invalid extension .' . $newextension);
            }
            if (array_key_exists($newextension, $mimetypes)) {
                throw new coding_exception('Extension ' . $newextension . ' already exists');
            }
        }

        // For default icon, check there isn't already something with default icon
        // set for that MIME type (unless it's this).
        if ($defaulticon) {
            foreach ($mimetypes as $ext => $type) {
                if ($ext !== $extension && $type['type'] === $mimetype &&
                        !empty($type['defaulticon'])) {
                    throw new coding_exception('MIME type ' . $mimetype .
                            ' already has a default icon set');
                }
            }
        }

        // Delete the old extension and then add the new one (may be same). This
        // will correctly handle cases when a default type is involved.
        self::delete_type($extension);
        self::add_type($newextension, $mimetype, $coreicon, $groups, $corestring,
                $customdescription, $defaulticon);
    }

    /**
     * Deletes a file type from the config list (or, for a standard one, marks it
     * as deleted).
     *
     * @param string $extension File extension without dot, e.g. 'doc'
     * @throws coding_exception If the extension does not exist, or otherwise invalid
     */
    public static function delete_type($extension) {
        // Extension must exist.
        $mimetypes = get_mimetypes_array();
        if (!array_key_exists($extension, $mimetypes)) {
            throw new coding_exception('Extension ' . $extension . ' not found');
        }

        // Get existing custom filetype list.
        $customs = self::get_custom_types();

        // Remove any entries for this extension.
        foreach ($customs as $key => $custom) {
            if ($custom->extension === $extension && empty($custom->deleted)) {
                unset($customs[$key]);
            }
        }

        // If it was a standard entry (doesn't have 'custom' set) then add a
        // deleted marker.
        if (empty($mimetypes[$extension]['custom'])) {
            $customs[] = (object)array('extension' => $extension, 'deleted' => true);
        }

        // Save and reset cache.
        self::set_custom_types($customs);
    }

    /**
     * Reverts a file type to the default. May only be called on types that have
     * default values. This will undelete the type if necessary or set its values.
     * If the type is already at default values, does nothing.
     *
     * @param string $extension File extension without dot, e.g. 'doc'
     * @return bool True if anything was changed, false if it was already default
     * @throws coding_exception If the extension is not a default type.
     */
    public static function revert_type_to_default($extension) {
        $extension = (string)$extension;

        // Check it actually is a default type.
        $defaults = self::get_default_types();
        if (!array_key_exists($extension, $defaults)) {
            throw new coding_exception('Extension ' . $extension . ' is not a default type');
        }

        // Loop through all the custom settings.
        $changed = false;
        $customs = self::get_custom_types();
        foreach ($customs as $key => $customentry) {
            if ($customentry->extension === $extension) {
                unset($customs[$key]);
                $changed = true;
            }
        }

        // Save changes if any.
        if ($changed) {
            self::set_custom_types($customs);
        }
        return $changed;
    }

    /**
     * Converts function parameters into a record for storing in the JSON value.
     *
     * @param string $extension File extension without dot, e.g. 'doc'
     * @param string $mimetype MIME type e.g. 'application/msword'
     * @param string $coreicon Core icon to use e.g. 'document'
     * @param array $groups Array of group strings that this type belongs to
     * @param string $corestring Custom lang string name in mimetypes.php
     * @param string $customdescription Custom description (plain text/multilang)
     * @param bool $defaulticon True if this should be the default icon for the type
     * @return stdClass Record matching the parameters
     */
    protected static function create_config_record($extension, $mimetype,
            $coreicon, array $groups, $corestring, $customdescription, $defaulticon) {
        // Construct new entry.
        $newentry = (object)array('extension' => (string)$extension, 'type' => (string)$mimetype,
                'icon' => (string)$coreicon);
        if ($groups) {
            if (!is_array($groups)) {
                throw new coding_exception('Groups must be an array');
            }
            foreach ($groups as $group) {
                if (!is_string($group)) {
                    throw new coding_exception('Groups must be an array of strings');
                }
            }
            $newentry->groups = $groups;
        }
        if ($corestring) {
            $newentry->string = (string)$corestring;
        }
        if ($customdescription) {
            $newentry->customdescription = (string)$customdescription;
        }
        if ($defaulticon) {
            $newentry->defaulticon = true;
        }
        return $newentry;
    }
}
