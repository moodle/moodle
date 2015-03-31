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
 * Private imscp module utility functions
 *
 * @package mod_imscp
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/mod/imscp/lib.php");
require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");

/**
 * Print IMSCP content to page.
 *
 * @param stdClass $imscp module instance.
 * @param stdClass $cm course module.
 * @param stdClass $course record.
 */
function imscp_print_content($imscp, $cm, $course) {
    global $PAGE, $CFG;

    $items = unserialize($imscp->structure);
    $first = reset($items);
    $context = context_module::instance($cm->id);
    $urlbase = "$CFG->wwwroot/pluginfile.php";
    $path = '/'.$context->id.'/mod_imscp/content/'.$imscp->revision.'/'.$first['href'];
    $firsturl = file_encode_url($urlbase, $path, false);

    echo '<div id="imscp_layout">';
    echo '<div id="imscp_toc">';
    echo '<div id="imscp_tree"><ul>';
    foreach ($items as $item) {
        echo imscp_htmllize_item($item, $imscp, $cm);
    }
    echo '</ul></div>';
    echo '<div id="imscp_nav" style="display:none">';
    echo '<button id="nav_skipprev">&lt;&lt;</button><button id="nav_prev">&lt;</button><button id="nav_up">^</button>';
    echo '<button id="nav_next">&gt;</button><button id="nav_skipnext">&gt;&gt;</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    $PAGE->requires->js_init_call('M.mod_imscp.init');
    return;
}

/**
 * Internal function - creates htmls structure suitable for YUI tree.
 */
function imscp_htmllize_item($item, $imscp, $cm) {
    global $CFG;

    if ($item['href']) {
        if (preg_match('|^https?://|', $item['href'])) {
            $url = $item['href'];
        } else {
            $context = context_module::instance($cm->id);
            $urlbase = "$CFG->wwwroot/pluginfile.php";
            $path = '/'.$context->id.'/mod_imscp/content/'.$imscp->revision.'/'.$item['href'];
            $url = file_encode_url($urlbase, $path, false);
        }
        $result = "<li><a href=\"$url\">".$item['title'].'</a>';
    } else {
        $result = '<li>'.$item['title'];
    }
    if ($item['subitems']) {
        $result .= '<ul>';
        foreach ($item['subitems'] as $subitem) {
            $result .= imscp_htmllize_item($subitem, $imscp, $cm);
        }
        $result .= '</ul>';
    }
    $result .= '</li>';

    return $result;
}

/**
 * Parse an IMS content package's manifest file to determine its structure
 * @param object $imscp
 * @param object $context
 * @return array
 */
function imscp_parse_structure($imscp, $context) {
    $fs = get_file_storage();

    if (!$manifestfile = $fs->get_file($context->id, 'mod_imscp', 'content', $imscp->revision, '/', 'imsmanifest.xml')) {
        return null;
    }

    return imscp_parse_manifestfile($manifestfile->get_content(), $imscp, $context);
}

/**
 * Parse the contents of a IMS package's manifest file.
 * @param string $manifestfilecontents the contents of the manifest file
 * @return array
 */
function imscp_parse_manifestfile($manifestfilecontents, $imscp, $context) {
    $doc = new DOMDocument();
    $oldentities = libxml_disable_entity_loader(true);
    if (!$doc->loadXML($manifestfilecontents, LIBXML_NONET)) {
        return null;
    }
    libxml_disable_entity_loader($oldentities);

    // We put this fake URL as base in order to detect path changes caused by xml:base attributes.
    $doc->documentURI = 'http://grrr/';

    $xmlorganizations = $doc->getElementsByTagName('organizations');
    if (empty($xmlorganizations->length)) {
        return null;
    }
    $default = null;
    if ($xmlorganizations->item(0)->attributes->getNamedItem('default')) {
        $default = $xmlorganizations->item(0)->attributes->getNamedItem('default')->nodeValue;
    }
    $xmlorganization = $doc->getElementsByTagName('organization');
    if (empty($xmlorganization->length)) {
        return null;
    }
    $organization = null;
    foreach ($xmlorganization as $org) {
        if (is_null($organization)) {
            // Use first if default nor found.
            $organization = $org;
        }
        if (!$org->attributes->getNamedItem('identifier')) {
            continue;
        }
        if ($default === $org->attributes->getNamedItem('identifier')->nodeValue) {
            // Found default - use it.
            $organization = $org;
            break;
        }
    }

    // Load all resources.
    $resources = array();

    $xmlresources = $doc->getElementsByTagName('resource');
    foreach ($xmlresources as $res) {
        if (!$identifier = $res->attributes->getNamedItem('identifier')) {
            continue;
        }
        $identifier = $identifier->nodeValue;
        if ($xmlbase = $res->baseURI) {
            // Undo the fake URL, we are interested in relative links only.
            $xmlbase = str_replace('http://grrr/', '/', $xmlbase);
            $xmlbase = rtrim($xmlbase, '/').'/';
        } else {
            $xmlbase = '';
        }
        if (!$href = $res->attributes->getNamedItem('href')) {
            // If href not found look for <file href="help.htm"/>.
            $fileresources = $res->getElementsByTagName('file');
            foreach ($fileresources as $file) {
                $href = $file->getAttribute('href');
            }
            if (pathinfo($href, PATHINFO_EXTENSION) == 'xml') {
                $href = imscp_recursive_href($href, $imscp, $context);
            }
            if (empty($href)) {
                continue;
            }
        } else {
            $href = $href->nodeValue;
        }
        if (strpos($href, 'http://') !== 0) {
            $href = $xmlbase.$href;
        }
        // Item href cleanup - Some packages are poorly done and use \ in urls.
        $href = ltrim(strtr($href, "\\", '/'), '/');
        $resources[$identifier] = $href;
    }

    $items = array();
    foreach ($organization->childNodes as $child) {
        if ($child->nodeName === 'item') {
            if (!$item = imscp_recursive_item($child, 0, $resources)) {
                continue;
            }
            $items[] = $item;
        }
    }

    return $items;
}

function imscp_recursive_href($manifestfilename, $imscp, $context) {
    $fs = get_file_storage();

    $dirname = dirname($manifestfilename);
    $filename = basename($manifestfilename);

    if ($dirname !== '/') {
        $dirname = "/$dirname/";
    }

    if (!$manifestfile = $fs->get_file($context->id, 'mod_imscp', 'content', $imscp->revision, $dirname, $filename)) {
        return null;
    }

    $doc = new DOMDocument();
    $oldentities = libxml_disable_entity_loader(true);
    if (!$doc->loadXML($manifestfile->get_content(), LIBXML_NONET)) {
        return null;
    }
    libxml_disable_entity_loader($oldentities);

    $xmlresources = $doc->getElementsByTagName('resource');
    foreach ($xmlresources as $res) {
        if (!$href = $res->attributes->getNamedItem('href')) {
            $fileresources = $res->getElementsByTagName('file');
            foreach ($fileresources as $file) {
                $href = $file->getAttribute('href');
                if (pathinfo($href, PATHINFO_EXTENSION) == 'xml') {
                    $href = imscp_recursive_href($href, $imscp, $context);
                }

                if (pathinfo($href, PATHINFO_EXTENSION) == 'htm' || pathinfo($href, PATHINFO_EXTENSION) == 'html') {
                    return $href;
                }
            }
        }
    }

    return $href;
}

function imscp_recursive_item($xmlitem, $level, $resources) {
    $identifierref = '';
    if ($identifierref = $xmlitem->attributes->getNamedItem('identifierref')) {
        $identifierref = $identifierref->nodeValue;
    }

    $title = '?';
    $subitems = array();

    foreach ($xmlitem->childNodes as $child) {
        if ($child->nodeName === 'title') {
            $title = $child->textContent;

        } else if ($child->nodeName === 'item') {
            if ($subitem = imscp_recursive_item($child, $level + 1, $resources)) {
                $subitems[] = $subitem;
            }
        }
    }

    return array('href'     => isset($resources[$identifierref]) ? $resources[$identifierref] : '',
                 'title'    => $title,
                 'level'    => $level,
                 'subitems' => $subitems,
                );
}

/**
 * File browsing support class
 *
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class imscp_file_info extends file_info {
    protected $course;
    protected $cm;
    protected $areas;
    protected $filearea;

    public function __construct($browser, $course, $cm, $context, $areas, $filearea) {
        parent::__construct($browser, $context);
        $this->course   = $course;
        $this->cm       = $cm;
        $this->areas    = $areas;
        $this->filearea = $filearea;
    }

    /**
     * Returns list of standard virtual file/directory identification.
     * The difference from stored_file parameters is that null values
     * are allowed in all fields
     * @return array with keys contextid, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array('contextid' => $this->context->id,
                     'component' => 'mod_imscp',
                     'filearea'  => $this->filearea,
                     'itemid'    => null,
                     'filepath'  => null,
                     'filename'  => null);
    }

    /**
     * Returns localised visible name.
     * @return string
     */
    public function get_visible_name() {
        return $this->areas[$this->filearea];
    }

    /**
     * Can I add new files or directories?
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Is directory?
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Returns list of children.
     * @return array of file_info instances
     */
    public function get_children() {
        return $this->get_filtered_children('*', false, true);
    }

    /**
     * Help function to return files matching extensions or their count
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @param bool|int $countonly if false returns the children, if an int returns just the
     *    count of children but stops counting when $countonly number of children is reached
     * @param bool $returnemptyfolders if true returns items that don't have matching files inside
     * @return array|int array of file_info instances or the count
     */
    private function get_filtered_children($extensions = '*', $countonly = false, $returnemptyfolders = false) {
        global $DB;
        $params = array('contextid' => $this->context->id,
            'component' => 'mod_imscp',
            'filearea' => $this->filearea);
        $sql = 'SELECT DISTINCT itemid
                    FROM {files}
                    WHERE contextid = :contextid
                    AND component = :component
                    AND filearea = :filearea';
        if (!$returnemptyfolders) {
            $sql .= ' AND filename <> :emptyfilename';
            $params['emptyfilename'] = '.';
        }
        list($sql2, $params2) = $this->build_search_files_sql($extensions);
        $sql .= ' '.$sql2;
        $params = array_merge($params, $params2);
        if ($countonly !== false) {
            $sql .= ' ORDER BY itemid';
        }

        $rs = $DB->get_recordset_sql($sql, $params);
        $children = array();
        foreach ($rs as $record) {
            if ($child = $this->browser->get_file_info($this->context, 'mod_imscp', $this->filearea, $record->itemid)) {
                $children[] = $child;
                if ($countonly !== false && count($children) >= $countonly) {
                    break;
                }
            }
        }
        $rs->close();
        if ($countonly !== false) {
            return count($children);
        }
        return $children;
    }

    /**
     * Returns list of children which are either files matching the specified extensions
     * or folders that contain at least one such file.
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @return array of file_info instances
     */
    public function get_non_empty_children($extensions = '*') {
        return $this->get_filtered_children($extensions, false);
    }

    /**
     * Returns the number of children which are either files matching the specified extensions
     * or folders containing at least one such file.
     *
     * @param string|array $extensions, for example '*' or array('.gif','.jpg')
     * @param int $limit stop counting after at least $limit non-empty children are found
     * @return int
     */
    public function count_non_empty_children($extensions = '*', $limit = 1) {
        return $this->get_filtered_children($extensions, $limit);
    }

    /**
     * Returns parent file_info instance
     * @return file_info or null for root
     */
    public function get_parent() {
        return $this->browser->get_file_info($this->context);
    }
}
