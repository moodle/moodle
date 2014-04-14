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
 * This filter provides automatic linking to
 * glossary entries, aliases and categories when
 * found inside every Moodle text.
 *
 * @package    filter
 * @subpackage glossary
 * @copyright  2004 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Glossary linking filter class.
 *
 * NOTE: multilang glossary entries are not compatible with this filter.
 */
class filter_glossary extends moodle_text_filter {
    /** @var int $cachecourseid cache invalidation flag in case content from multiple courses displayed. */
    protected $cachecourseid = null;
    /** @var int $cacheuserid cache invalidation flag in case user is switched. */
    protected $cacheuserid = null;
    /** @var array $cacheconceptlist page level filter cache, this should be always faster than MUC */
    protected $cacheconceptlist = null;

    public function setup($page, $context) {
        // This only requires execution once per request.
        static $jsinitialised = false;
        if (empty($jsinitialised)) {
            $page->requires->yui_module(
                    'moodle-filter_glossary-autolinker',
                    'M.filter_glossary.init_filter_autolinking',
                    array(array('courseid' => 0)));
            $page->requires->strings_for_js(array('ok'), 'moodle');
            $jsinitialised = true;
        }
    }

    public function filter($text, array $options = array()) {
        global $CFG, $USER, $GLOSSARY_EXCLUDEENTRY;

        // Try to get current course.
        $coursectx = $this->context->get_course_context(false);
        if (!$coursectx) {
            // Only global glossaries will be linked.
            $courseid = 0;
        } else {
            $courseid = $coursectx->instanceid;
        }

        if ($this->cachecourseid != $courseid or $this->cacheuserid != $USER->id) {
            // Invalidate the page cache.
            $this->cacheconceptlist = null;
        }

        if (is_array($this->cacheconceptlist) and empty($GLOSSARY_EXCLUDEENTRY)) {
            if (empty($this->cacheconceptlist)) {
                return $text;
            }
            return filter_phrases($text, $this->cacheconceptlist);
        }

        list($glossaries, $allconcepts) = \mod_glossary\local\concept_cache::get_concepts($courseid);

        if (!$allconcepts) {
            $this->cacheuserid = $USER->id;
            $this->cachecourseid = $courseid;
            $this->cacheconcepts = array();
            return $text;
        }

        $strcategory = get_string('category', 'glossary');

        $conceptlist = array();
        $excluded = false;

        foreach ($allconcepts as $concepts) {
            foreach ($concepts as $concept) {
                if (!empty($GLOSSARY_EXCLUDEENTRY) and $concept->id == $GLOSSARY_EXCLUDEENTRY) {
                    $excluded = true;
                    continue;
                }
                if ($concept->category) { // Link to a category.
                    // TODO: Fix this string usage.
                    $title = $glossaries[$concept->glossaryid] . ': ' . $strcategory . ' ' . $concept->concept;
                    $link = new moodle_url('/mod/glossary/view.php', array('g' => $concept->glossaryid, 'mode' => 'cat', 'hook' => $concept->id));
                    $attributes = array(
                        'href'  => $link,
                        'title' => $title,
                        'class' => 'glossary autolink category glossaryid' . $concept->glossaryid);

                } else { // Link to entry or alias
                    $title = $glossaries[$concept->glossaryid] . ': ' . $concept->concept;
                    // Hardcoding dictionary format in the URL rather than defaulting
                    // to the current glossary format which may not work in a popup.
                    // for example "entry list" means the popup would only contain
                    // a link that opens another popup.
                    $link = new moodle_url('/mod/glossary/showentry.php', array('eid' => $concept->id, 'displayformat' => 'dictionary'));
                    $attributes = array(
                        'href'  => $link,
                        'title' => str_replace('&amp;', '&', $title), // Undo the s() mangling.
                        'class' => 'glossary autolink concept glossaryid' . $concept->glossaryid);
                }
                // This flag is optionally set by resource_pluginfile()
                // if processing an embedded file use target to prevent getting nested Moodles.
                if (!empty($CFG->embeddedsoforcelinktarget)) {
                    $attributes['target'] = '_top';
                }
                $href_tag_begin = html_writer::start_tag('a', $attributes);

                $conceptlist[] = new filterobject($concept->concept, $href_tag_begin, '</a>',
                    $concept->casesensitive, $concept->fullmatch);
            }
        }

        usort($conceptlist, 'filter_glossary::sort_entries_by_length');

        if (!$excluded) {
            // Do not cache the excluded list here, it is used once per page only.
            $this->cacheuserid = $USER->id;
            $this->cachecourseid = $courseid;
            $this->cacheconceptlist = $conceptlist;
        }

        if (empty($conceptlist)) {
            return $text;
        }
        return filter_phrases($text, $conceptlist);   // Actually search for concepts!
    }

    private static function sort_entries_by_length($entry0, $entry1) {
        $len0 = strlen($entry0->phrase);
        $len1 = strlen($entry1->phrase);

        if ($len0 < $len1) {
            return 1;
        } else if ($len0 > $len1) {
            return -1;
        } else {
            return 0;
        }
    }
}
