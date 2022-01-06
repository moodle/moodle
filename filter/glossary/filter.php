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
    /** @var null|cache_store cache used to store the terms for this course. */
    protected $cache = null;

    public function setup($page, $context) {
        if ($page->requires->should_create_one_time_item_now('filter_glossary_autolinker')) {
            $page->requires->yui_module(
                    'moodle-filter_glossary-autolinker',
                    'M.filter_glossary.init_filter_autolinking',
                    array(array('courseid' => 0)));
            $page->requires->strings_for_js(array('ok'), 'moodle');
        }
    }

    /**
     * Get all the concepts for this context.
     * @return filterobject[] the concepts, and filterobjects.
     */
    protected function get_all_concepts() {
        global $USER;

        if ($this->cache === null) {
            $this->cache = cache::make_from_params(cache_store::MODE_REQUEST, 'filter', 'glossary');
        }

        // Try to get current course.
        $coursectx = $this->context->get_course_context(false);
        if (!$coursectx) {
            // Only global glossaries will be linked.
            $courseid = 0;
        } else {
            $courseid = $coursectx->instanceid;
        }

        $cached = $this->cache->get('concepts');
        if ($cached !== false && ($cached->cachecourseid != $courseid || $cached->cacheuserid != $USER->id)) {
            // Invalidate the page cache.
            $cached = false;
        }

        if ($cached !== false && is_array($cached->cacheconceptlist)) {
            return $cached->cacheconceptlist;
        }

        list($glossaries, $allconcepts) = \mod_glossary\local\concept_cache::get_concepts($courseid);

        if (!$allconcepts) {
            $tocache = new stdClass();
            $tocache->cacheuserid = $USER->id;
            $tocache->cachecourseid = $courseid;
            $tocache->cacheconceptlist = [];
            $this->cache->set('concepts', $tocache);
            return [];
        }

        $conceptlist = array();

        foreach ($allconcepts as $concepts) {
            foreach ($concepts as $concept) {
                $conceptlist[] = new filterobject($concept->concept, null, null,
                        $concept->casesensitive, $concept->fullmatch, null,
                        [$this, 'filterobject_prepare_replacement_callback'], [$concept, $glossaries]);
            }
        }

        // We sort longest first, so that when we replace the terms,
        // the longest ones are replaced first. This does the right thing
        // when you have two terms like 'Moodle' and 'Moodle 3.5'. You want the longest match.
        usort($conceptlist, [$this, 'sort_entries_by_length']);

        $conceptlist = filter_prepare_phrases_for_filtering($conceptlist);

        $tocache = new stdClass();
        $tocache->cacheuserid = $USER->id;
        $tocache->cachecourseid = $courseid;
        $tocache->cacheconceptlist = $conceptlist;
        $this->cache->set('concepts', $tocache);

        return $conceptlist;
    }

    /**
     * Callback used by filterobject / filter_phrases.
     *
     * @param object $concept the concept that is being replaced (from get_all_concepts).
     * @param array $glossaries the list of glossary titles (from get_all_concepts).
     * @return array [$hreftagbegin, $hreftagend, $replacementphrase] for filterobject.
     */
    public function filterobject_prepare_replacement_callback($concept, $glossaries) {
        global $CFG;

        if ($concept->category) { // Link to a category.
            $title = get_string('glossarycategory', 'filter_glossary',
                    ['glossary' => $glossaries[$concept->glossaryid], 'category' => $concept->concept]);
            $link = new moodle_url('/mod/glossary/view.php',
                    ['g' => $concept->glossaryid, 'mode' => 'cat', 'hook' => $concept->id]);
            $attributes = array(
                    'href'  => $link,
                    'title' => $title,
                    'class' => 'glossary autolink category glossaryid' . $concept->glossaryid);

        } else { // Link to entry or alias.
            $title = get_string('glossaryconcept', 'filter_glossary',
                    ['glossary' => $glossaries[$concept->glossaryid], 'concept' => $concept->concept]);
            // Hardcoding dictionary format in the URL rather than defaulting
            // to the current glossary format which may not work in a popup.
            // for example "entry list" means the popup would only contain
            // a link that opens another popup.
            $link = new moodle_url('/mod/glossary/showentry.php',
                    ['eid' => $concept->id, 'displayformat' => 'dictionary']);
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

        return [html_writer::start_tag('a', $attributes), '</a>', null];
    }

    public function filter($text, array $options = array()) {
        global $GLOSSARY_EXCLUDEENTRY;

        $conceptlist = $this->get_all_concepts();

        if (empty($conceptlist)) {
            return $text;
        }

        if (!empty($GLOSSARY_EXCLUDEENTRY)) {
            foreach ($conceptlist as $key => $filterobj) {
                // The original concept object was stored here in when $filterobj was constructed in
                // get_all_concepts(). Get it back out now so we can check to see if it is excluded.
                $concept = $filterobj->replacementcallbackdata[0];
                if (!$concept->category && $concept->id == $GLOSSARY_EXCLUDEENTRY) {
                    unset($conceptlist[$key]);
                }
            }
        }

        if (empty($conceptlist)) {
            return $text;
        }

        return filter_phrases($text, $conceptlist, null, null, false, true);
    }

    /**
     * usort helper used in get_all_concepts above.
     * @param filterobject $filterobject0 first item to compare.
     * @param filterobject $filterobject1 second item to compare.
     * @return int -1, 0 or 1.
     */
    private function sort_entries_by_length($filterobject0, $filterobject1) {
        return strlen($filterobject1->phrase) <=> strlen($filterobject0->phrase);
    }
}
