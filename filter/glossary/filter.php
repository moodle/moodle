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
 * found inside every Moodle text
 *
 * @package    filter
 * @subpackage glossary
 * @copyright  2004 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Glossary filtering
 *
 * TODO: erase the $GLOSSARY_EXCLUDECONCEPTS global => require format_text()
 *       to be able to pass arbitrary $options['filteroptions']['glossary'] to filter_text()
 */
class filter_glossary extends moodle_text_filter {

    public function setup($page, $context) {
        // This only requires execution once per request.
        static $jsinitialised = false;
        if (empty($jsinitialised)) {
            $page->requires->yui_module(
                    'moodle-filter_glossary-autolinker',
                    'M.filter_glossary.init_filter_autolinking',
                    array(array('courseid' => 0)));
            $jsinitialised = true;
        }
    }

    public function filter($text, array $options = array()) {
        global $CFG, $DB, $GLOSSARY_EXCLUDECONCEPTS;

        // Trivial-cache - keyed on $cachedcontextid
        static $cachedcontextid;
        static $conceptlist;

        static $nothingtodo;         // To avoid processing if no glossaries / concepts are found

        // Try to get current course
        if (!$courseid = get_courseid_from_context($this->context)) {
            $courseid = 0;
        }

        // Initialise/invalidate our trivial cache if dealing with a different context
        if (!isset($cachedcontextid) || $cachedcontextid !== $this->context->id) {
            $cachedcontextid = $this->context->id;
            $conceptlist = array();
            $nothingtodo = false;
        }

        if (($nothingtodo === true) || (!has_capability('mod/glossary:view', $this->context))) {
            return $text;
        }

        // Create a list of all the concepts to search for.  It may be cached already.
        if (empty($conceptlist)) {

            // Find all the glossaries we need to examine
            if (!$glossaries = $DB->get_records_sql_menu('
                    SELECT g.id, g.name
                      FROM {glossary} g, {course_modules} cm, {modules} m
                     WHERE m.name = \'glossary\'
                       AND cm.module = m.id
                       AND cm.visible = 1
                       AND g.id = cm.instance
                       AND g.usedynalink != 0
                       AND (g.course = ? OR g.globalglossary = 1)
                  ORDER BY g.globalglossary, g.id', array($courseid))) {
                $nothingtodo = true;
                return $text;
            }

            // Make a list of glossary IDs for searching
            $glossarylist = implode(',', array_keys($glossaries));

            // Pull out all the raw data from the database for entries, categories and aliases
            $entries = $DB->get_records_select('glossary_entries',
                    'glossaryid IN ('.$glossarylist.') AND usedynalink != 0 AND approved != 0 ', null, '',
                    'id,glossaryid, concept, casesensitive, 0 AS category, fullmatch');

            $categories = $DB->get_records_select('glossary_categories',
                    'glossaryid IN ('.$glossarylist.') AND usedynalink != 0', null, '',
                    'id,glossaryid,name AS concept, 1 AS casesensitive, 1 AS category, 1 AS fullmatch');

            $aliases = $DB->get_records_sql('
                    SELECT ga.id, ge.id AS entryid, ge.glossaryid,
                           ga.alias AS concept, ge.concept AS originalconcept,
                           casesensitive, 0 AS category, fullmatch
                      FROM {glossary_alias} ga,
                           {glossary_entries} ge
                      WHERE ga.entryid = ge.id
                        AND ge.glossaryid IN ('.$glossarylist.')
                        AND ge.usedynalink != 0
                        AND ge.approved != 0', null);

            // Combine them into one big list
            $concepts = array();
            if ($entries and $categories) {
                $concepts = array_merge($entries, $categories);
            } else if ($categories) {
                $concepts = $categories;
            } else if ($entries) {
                $concepts = $entries;
            }

            if ($aliases) {
                $concepts = array_merge($concepts, $aliases);
            }

            if (!empty($concepts)) {
                foreach ($concepts as $key => $concept) {
                    // Trim empty or unlinkable concepts
                    $currentconcept = trim(strip_tags($concept->concept));

                    // Concept must be HTML-escaped, so do the same as print_string
                    // to turn ampersands into &amp;.
                    $currentconcept = replace_ampersands_not_followed_by_entity($currentconcept);

                    if (empty($currentconcept)) {
                        unset($concepts[$key]);
                        continue;
                    } else {
                        $concepts[$key]->concept = $currentconcept;
                    }

                    // Rule out any small integers.  See bug 1446
                    $currentint = intval($currentconcept);
                    if ($currentint && (strval($currentint) == $currentconcept) && $currentint < 1000) {
                        unset($concepts[$key]);
                    }
                }
            }

            if (empty($concepts)) {
                $nothingtodo = true;
                return $text;
            }

            usort($concepts, 'filter_glossary::sort_entries_by_length');

            $strcategory = get_string('category', 'glossary');

            // Loop through all the concepts, setting up our data structure for the filter
            $conceptlist = array();    // We will store all the concepts here

            foreach ($concepts as $concept) {
                $glossaryname = str_replace(':', '-', $glossaries[$concept->glossaryid]);
                if ($concept->category) {       // Link to a category
                    // TODO: Fix this string usage
                    $title = strip_tags($glossaryname.': '.$strcategory.' '.$concept->concept);
                    $href_tag_begin = '<a class="glossary autolink category glossaryid'.$concept->glossaryid.'" title="'.$title.'" '.
                                      'href="'.$CFG->wwwroot.'/mod/glossary/view.php?g='.$concept->glossaryid.
                                      '&amp;mode=cat&amp;hook='.$concept->id.'">';
                } else { // Link to entry or alias
                    if (!empty($concept->originalconcept)) {  // We are dealing with an alias (so show and point to original)
                        $title = str_replace('"', "'", html_entity_decode(
                                strip_tags($glossaryname.': '.$concept->originalconcept)));
                        $concept->id = $concept->entryid;
                    } else { // This is an entry
                        // We need to remove entities from the content here because it
                        // will be escaped by html_writer below.
                        $title = str_replace('"', "'", html_entity_decode(
                                strip_tags($glossaryname.': '.$concept->concept)));
                    }
                    // hardcoding dictionary format in the URL rather than defaulting
                    // to the current glossary format which may not work in a popup.
                    // for example "entry list" means the popup would only contain
                    // a link that opens another popup.
                    $link = new moodle_url('/mod/glossary/showentry.php', array('courseid'=>$courseid, 'eid'=>$concept->id, 'displayformat'=>'dictionary'));
                    $attributes = array(
                        'href' => $link,
                        'title'=> $title,
                        'class'=> 'glossary autolink concept glossaryid'.$concept->glossaryid);

                    // this flag is optionally set by resource_pluginfile()
                    // if processing an embedded file use target to prevent getting nested Moodles
                    if (isset($CFG->embeddedsoforcelinktarget) && $CFG->embeddedsoforcelinktarget) {
                        $attributes['target'] = '_top';
                    }

                    $href_tag_begin = html_writer::start_tag('a', $attributes);
                }
                $conceptlist[] = new filterobject($concept->concept, $href_tag_begin, '</a>',
                    $concept->casesensitive, $concept->fullmatch);
            }

            $conceptlist = filter_remove_duplicates($conceptlist);
        }

        if (!empty($GLOSSARY_EXCLUDECONCEPTS)) {
            $reducedconceptlist=array();
            foreach($conceptlist as $concept) {
                if(!in_array($concept->phrase,$GLOSSARY_EXCLUDECONCEPTS)) {
                    $reducedconceptlist[]=$concept;
                }
            }
            return filter_phrases($text, $reducedconceptlist);
        }

        return filter_phrases($text, $conceptlist);   // Actually search for concepts!
    }


    private static function sort_entries_by_length($entry0, $entry1) {
        $len0 = strlen($entry0->concept);
        $len1 = strlen($entry1->concept);

        if ($len0 < $len1) {
            return 1;
        } else if ($len0 > $len1) {
            return -1;
        } else {
            return 0;
        }
    }
}
