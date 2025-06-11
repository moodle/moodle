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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Inlcude the requisite helpers functionality.
require_once($CFG->dirroot . '/blocks/pu/classes/helpers.php');

class block_pu extends block_list {
    public $course;
    public $user;
    public $content;
    public $coursecontext;
    public $pu_codetotals;
    public $pu_invalidtotals;
    public $pu_usedcount;
    public $pu_invalidcount;
    public $pu_totalcount;

    public function init() {
        global $CFG, $PAGE;

        $this->title = get_string('pluginname', 'block_pu');
        $this->set_course();
        $this->set_user();
        $this->set_course_context();
        $PAGE->requires->js(new moodle_url('/blocks/pu/js.js'));

        // Set these up for sanity's sake.
        if (isset($CFG->block_pu_defaultcodes)) {
            $this->pu_codetotals    = $this->codetotals($this->course->id)->codecount;
            $this->pu_invalidtotals = $this->codetotals($this->course->id)->invalidcount;
            $this->pu_usedcount     = $this->usedcount($uv="used");
            $this->pu_invalidcount  = $this->usedcount($uv="invalid");
            $this->pu_totalcount    = $this->usedcount($uv="total");
        }
    }

    /**
     * Returns the course object
     *
     * @return @object
     */
    public function set_course() {
        global $COURSE;
        $this->course = $COURSE;
    }

    /**
     * Returns the user object
     *
     * @return @object
     */
    public function set_user() {
        global $USER;
        $this->user = $USER;
    }

    /**
     * Sets and returns this course's context
     *
     * @return @context
     */
    private function set_course_context() {
        $this->coursecontext = context_course::instance($this->course->id);
    }

    /**
     * Indicates which pages types this block may be added to
     *
     * @return @array
     */
    public function applicable_formats() {
        return array(
             'site-index' => false,
            'course-view' => true 
        );
    }

    /**
     * Indicates that this block has its own configuration settings
     *
     * @return @bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the content to be rendered when displaying this block
     *
     * @return @object
     */
    public function get_content() {
        if (!empty($this->content)) {
            return $this->content;
        }

        // Create a fresh content container.
        $this->content = $this->get_new_content_container();

        // Hmm. Didn't I declare this earlier?
        $coursecontext = context_course::instance($this->course->id);

        // Course-level Features.
        // If we have a count of 1.
        if ($this->pu_totalcount > 0 && $this->pu_totalcount < 2) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_block_intro_one', 'block_pu', ['coursename' => $this->course->fullname])
            ]);
        // If we have a count of more than 1.
        } else if ($this->pu_totalcount > 1) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_block_intro_multi', 'block_pu', ['numassigned' => $this->pu_totalcount, 'coursename' => $this->course->fullname])
            ]);
        } else {
            // We have no coupon codes requested.
            $this->add_item_to_content([
                'lang_key' => get_string('pu_new', 'block_pu'),
                'page' => 'coder',
                'query_string' => ['courseid' => $this->course->id, 'pcmid' => 0, 'function' => 'new'],
                'attributes' => array('onclick' => 'processClick();', 'id' => 'nodbl', 'class' => 'btn btn-outline-secondary btn-sm pu_new')
            ]);
        }

        $countpast = 0;
        $countcurrent = 0;

        // Loop through the codes to find the assigned but unused code to display it on top.
        foreach ($this->mapped_codes() AS $mappedcode) {
            if ($mappedcode->valid == 1 && $mappedcode->used == 0 && $this->pu_usedcount < $this->pu_totalcount) {
                $pcmidnew = $mappedcode->pcmid;
                $countcurrent++;
                $this->add_item_to_content([
                    'lang_key' => $mappedcode->couponcode,
                    'pcmid' => $mappedcode->pcmid,
                    'attributes' => array('class' => 'pu_active')
                ]);
            }
        }

        // Loop through the used codes and display them in order.
        foreach ($this->mapped_codes() AS $mappedcode) {
            if ($mappedcode->valid == 1 && $mappedcode->used == 1) {

                $pcmidolds[] = $mappedcode->pcmid;

                $countpast++;
                $this->add_item_to_content([
                    'lang_key' => get_string('pu_past', 'block_pu') . $countpast . ': ' . $mappedcode->couponcode,
                    'attributes' => array('class' => 'pu_past')
                ]);
            }
        }

        // Add some language.
        $this->add_item_to_content([
            'lang_key' => get_string('pu_docs_intro', 'block_pu'),
            'attributes' => array('class' => 'intro')
        ]);


        // Don't show the block if the course override is set to 0 codes (ignoring invalid count).
        if ($this->pu_codetotals == 0) {
            unset($this->content);
            return "";
        }

        // Depending on codecount and code status, display the correct stuff.
        if (isset($pcmidnew) && $this->pu_totalcount < $this->pu_codetotals && ($this->pu_usedcount > 0 || $this->pu_totalcount > 0)) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_touse', 'block_pu'),
                'attributes' => array('class' => 'litem')
            ]);
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_used', 'block_pu'),
                'attributes' => array('class' => 'litem')
            ]);

        } else if (isset($pcmidnew) && $this->pu_totalcount >= $this->pu_codetotals && ($this->pu_usedcount > 0 || $this->pu_totalcount > 0)) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_touse', 'block_pu'),
                'attributes' => array('class' => 'litem')
            ]);
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_requestedall', 'block_pu'),
                'attributes' => array('class' => 'litem')
            ]);
        } 

        // If we have a new code and more than zero but less than the limit of total codes, do stuff.
        if (isset($pcmidnew) && $this->pu_totalcount > 0 && $this->pu_totalcount <= $this->pu_codetotals) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_used', 'block_pu'),
                'page' => 'coder',
                'query_string' => ['courseid' => $this->course->id, 'pcmid' => $pcmidnew, 'function' => 'used'],
                'attributes' => array('onclick' => 'processClick()', 'id' => 'nodbl', 'class' => 'btn btn-outline-secondary btn-sm pu_used')
            ]);
        }

        // If we DO NOT have a new code but still have more than 0 and less than the limit of total codes, do different stuff.
        if (!isset($pcmidnew) && $this->pu_totalcount > 0 && $this->pu_totalcount < $this->pu_codetotals) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_new', 'block_pu'),
                'page' => 'coder',
                'query_string' => ['courseid' => $this->course->id, 'pcmid' => 0, 'function' => 'new'],
                'attributes' => array('onclick' => 'processClick();', 'id' => 'nodbl', 'class' => 'btn btn-outline-secondary btn-sm pu_new')
            ]);
        }

        // If we have ONLY an unused code.
        if ($this->pu_totalcount > 0 && $this->pu_usedcount == 0) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_allocatednum', 'block_pu', ['numallocated' => $this->pu_totalcount, 'numtotal' => $this->pu_codetotals]),
                'attributes' => array('class' => 'litem')
            ]);
        // If we have used codes less than the total allowed.
        } else if ($this->pu_usedcount > 0 && $this->pu_usedcount < $this->pu_codetotals) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_usednum', 'block_pu', ['numused' => $this->pu_usedcount, 'numtotal' => $this->pu_codetotals]),
                'attributes' => array('class' => 'litem')
            ]);

        // If we have used codes EXACTLY equaling the total allowed.
        } else if ($this->pu_totalcount > 0 && $this->pu_usedcount >= $this->pu_codetotals) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_noneleft', 'block_pu'),
                'attributes' => array('class' => 'litem')
            ]);

        // We have something else.
        } else {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_intronone', 'block_pu', ['numtotal' => $this->pu_codetotals]),
                'attributes' => array('class' => 'litem')
            ]);
        }

        // We have a new code with an invalid count below the allowed invalids and the total code count is more than 0.
        if (isset($pcmidnew) && $this->pu_invalidcount < $this->pu_invalidtotals && $this->pu_totalcount > 0) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_invalid', 'block_pu'),
	            'attributes' => array('class' => 'litem')
            ]);

            $this->add_item_to_content([
                    'lang_key' => get_string('pu_replace', 'block_pu'),
                    'page' => 'replace',
                    'query_string' => ['courseid' => $this->course->id, 'pcmid' => $pcmidnew],
                    'attributes' => array('class' => 'btn btn-outline-secondary btn-sm pu_retry')
            ]);

            if ($this->pu_invalidcount < $this->pu_invalidtotals && $this->pu_invalidcount > 0) {
                $this->add_item_to_content([
                    'lang_key' => get_string('pu_docs_invalidsused', 'block_pu', ['numused' => $this->pu_invalidcount, 'numtotal' => $this->pu_invalidtotals]),
                    'attributes' => array('class' => 'litem')
                ]);
            }
        }

        // We have used our invalid codes and have at least 1 invalid.
        if ($this->pu_invalidcount >= $this->pu_invalidtotals && $this->pu_invalidtotals > 0) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_invalidsfull', 'block_pu', ['numused' => $this->pu_invalidcount, 'numtotal' => $this->pu_invalidtotals]),
                'attributes' => array('class' => 'litem')
            ]);

        // We have a 0 invalid allowed situation.
        } else if ($this->pu_invalidcount >= $this->pu_invalidtotals) {
            $this->add_item_to_content([
                'lang_key' => get_string('pu_docs_invalidsnone', 'block_pu'),
                'attributes' => array('class' => 'litem')
            ]);
        }

        return $this->content;
    }

    /**
     * Checks to see if a user is a GUILD user in the given course context
     *
     * @param  array $params  [user_id, course_id]
     * @return bool
     */
    private function guilduser($params) {
        block_pu_helpers::guilduser_check($params);
    }

    // Get the code totals.
    private function codetotals($courseid) {
        $codetotals = block_pu_helpers::pu_codetotals($params = array('course_id' => $courseid));

        return $codetotals;
    }


    /**
     * Retreives the code mappings for a user/course from a helper function.
     *
     * @return @array
     */
    private function mapped_codes() {
        // Set up the course id for later.
        $cid = $this->course->id;

        // Set up the user id for later.
        $uid = $this->user->id;

        // Build the array(s).
        $mapped = block_pu_helpers::codemappings(array('course_id' => $cid, 'user_id' => $uid, 'pcmid' => null));

        // Return the data.
        return $mapped;
    }

    /**
     * Instantiates the public method in the private context.
     *
     * Counts the number of coupon codes assigned to a person in a course context.
     *
     * @param  @array $uv
     * @return @int
     */
    private function usedcount($uv="used") {
        global $DB;

        // Set up the course id for later.
        $cid = $this->course->id;

        // Set up the user id for later.
        $uid = $this->user->id;

        // Get the used count.
        $count = block_pu_helpers::pu_uvcount(array('course_id' => $cid, 'user_id' => $uid, 'uv' => $uv));

        // Return the count.
        return $count;
    }

    /**
     * Builds and adds an item to the content container for the given params
     *
     * @param  @array $params
     */
    private function add_item_to_content($params) {
        if (!array_key_exists('query_string', $params)) {
            $params['query_string'] = [];
        }

        // Build the item.
        $item = $this->build_item($params);

        if (block_pu_helpers::guilduser_check($params = array('course_id' => $this->course->id, 'user_id' => $this->user->id))) {
            $this->content->items[] = $item;
        }
    }

    /**
     * Builds a content item (link) for the given params
     *
     * @param  @array $params
     * @return @string
     */
    private function build_item($params) {
        global $OUTPUT;

        // Set the label from the params.
        $label = $params['lang_key'];

        // Set the icon if we use one (which we don't but JIC), if not, blank.
        $icon = isset($params['icon_key']) ? $icon = $OUTPUT->pix_icon($params['icon_key'], $label, 'moodle', ['class' => 'icon']) : null;

        // If we're using any attrributes, populate them.
        $attrs = isset($params['attributes']) ? $params['attributes'] : null;

        // We're using spans here.
        $tag = 'span';

        // If this item is a link to a specific page.
        if (isset($params['page'])) {
            // Build the item.
            $item = html_writer::link(
                        new moodle_url('/blocks/pu/' . $params['page'] . '.php', $params['query_string']),
                        $icon . $label, $attrs
            );
        } else {
            // Build the item.
            $item = html_writer::tag(
                    $tag,
                    $icon . $label,
                    $attrs
                );
         }

         // return the item.
         return $item;
    }

    /**
     * Returns an empty "block list" content container to be filled with content
     *
     * @return @object
     */
    private function get_new_content_container() {
        $content = new stdClass;
        $content->items = [];
        $content->icons = [];
        $content->footer = '';

        return $content;
    }
}
