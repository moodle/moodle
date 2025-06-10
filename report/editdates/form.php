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
 * This is form to display the modules for editdates reports
 *
 * @package   report_editdates
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once(dirname(__FILE__) . '/lib.php');


/**
 * This is form to display the modules for editdates reports
 *
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_editdates_form extends moodleform {
    /**
     * @see lib/moodleform#definition()
     */
    public function definition() {
        global $CFG, $DB, $PAGE;
        $mform = $this->_form;

        $modinfo       = $this->_customdata['modinfo'];
        $course        = $this->_customdata['course'];
        $activitytype  = $this->_customdata['activitytype'];
        $config = get_config('report_editdates');

        $coursehasavailability = !empty($CFG->enableavailability);
        $coursehascompletion   = !empty($CFG->enablecompletion) && !empty($course->enablecompletion);

        // Context instance of the course.
        $coursecontext = context_course::instance($course->id);

        // Store current activity type.
        $mform->addElement('hidden', 'activitytype', $activitytype);
        $mform->setType('activitytype', PARAM_PLUGIN);

        // Invisible static element. Used as the holder for a validation message sometimes.
        $mform->addElement('static', 'topvalidationsite', '', '');

        // Add action button to the top of the form.
        $addactionbuttons = false;
        $this->add_action_buttons();

        // Course start date.
        $mform->addElement('header', 'coursestartdateheader', get_string('coursestartdateheader', 'report_editdates'));
        $mform->setExpanded('coursestartdateheader', false);

        $mform->addElement('date_time_selector', 'coursestartdate', get_string('startdate'));
        $mform->addHelpButton('coursestartdate', 'startdate');
        $mform->setDefault('coursestartdate', $course->startdate);

        $mform->addElement('date_time_selector', 'courseenddate', get_string('enddate'), array('optional' => true));
        $mform->addHelpButton('courseenddate', 'enddate');
        $mform->setDefault('courseenddate', $course->enddate);

        // If user is not capable, make it read only.
        if (!has_capability('moodle/course:update', $coursecontext)) {
            $mform->hardFreeze('coursestartdate');
        } else {
            $addactionbuttons = true;
        }

        // Var to count the number of elements in the course/sections.
        // It will be used to decide whether to show save action button
        // at the bottom of the form page.
        $elementadded = 0;

        // Default -1 to display header for 0th section.
        $prevsectionnum = -1;

        // Cycle through all the sections in the course.
        $cms = $modinfo->get_cms();
        $sections = $modinfo->get_section_info_all();
        $timeline = array();
        foreach ($sections as $sectionnum => $section) {
            $ismodadded = false;
            $sectionname = '';

            // Skip if section isn't visible to the user.
            if (!$section->uservisible) {
                continue;
            }

            // New section, create header.
            if ($prevsectionnum != $sectionnum) {
                $sectionname = get_section_name($course, $section);
                $headername = 'section' . $sectionnum . 'header';
                $mform->addElement('header', $headername, $sectionname);
                $mform->setExpanded($headername, false);
                $prevsectionnum = $sectionnum;
            }

            if ($sectionnum > 0 && $coursehasavailability) {
                $editsettingurl = new moodle_url('/course/editsection.php', array('id' => $section->id));
                if ($section->availability) {
                    // If there are retricted access date settings.
                    if (strpos($section->availability, '"type":"date"') !== false) {
                        $editsettingurltext = html_writer::tag('a',
                                get_string('editrestrictedaccess', 'report_editdates'),
                                        array('href' => $editsettingurl->out(false),
                                        'target' => '_blank',
                                        'class' => 'editdates_highlight'));
                        $mform->addElement('static', '',
                                get_string('hasrestrictedaccess', 'report_editdates', ($sectionname)),
                                        $editsettingurltext);
                        $iconmarkup = html_writer::tag('i', '', array('class' => 'icon fa fa-folder-open',
                                                                      'style' => 'margin: 4px;'));
                        $timeline['section'.$sectionnum] = array('type' => 'section',
                                                                 'name' => $sectionname,
                                                                 'icon' => $iconmarkup,
                                                                 'url' => $editsettingurl,
                                                                 'restrict' => $section->availability,
                                                                 'color' => 'rgb(' . mt_rand( 0, 255 ) . ',' .
                                                                                     mt_rand( 0, 255 ) . ',' .
                                                                                     mt_rand( 0, 255 ) . ', .5)'
                                                            );
                    }
                } else {
                    $editsettingurltext = html_writer::tag('a',
                                                           get_string('addrestrictedaccess', 'report_editdates', ($sectionname)),
                                                           array('href' => $editsettingurl->out(false),
                                                                 'target' => '_blank'));
                    $mform->addElement('static', '',
                                       get_string('norestrictedaccess', 'report_editdates', ($sectionname)),
                                       $editsettingurltext);
                }
            }

            // Cycle through each module in a section.
            if (isset($modinfo->sections[$sectionnum])) {
                foreach ($modinfo->sections[$sectionnum] as $cmid) {
                    $cm = $cms[$cmid];

                    // No need to display/continue if this module is not visible to user.
                    if (!$cm->uservisible) {
                        continue;
                    }

                    // If activity filter is on, then filter module by activity type.
                    if ($activitytype && ($cm->modname != $activitytype && $activitytype != "all")) {
                        continue;
                    }

                    // Check if the user has capability to edit this module settings.
                    $modulecontext = context_module::instance($cm->id);
                    $ismodreadonly = !has_capability('moodle/course:manageactivities', $modulecontext);

                    // Display activity name.
                    $iconmarkup = html_writer::empty_tag('img', array(
                            'src' => $cm->get_icon_url(), 'class' => 'activityicon', 'alt' => ''));
                    $stractivityname = html_writer::tag('strong' , $iconmarkup . ' ' . $cm->name . '<hr />');
                    $mform->addElement('html', $stractivityname);
                    $isdateadded = false;
                    $timeline[$cm->id] = array('type' => $cm->modname,
                                               'name' => $cm->name,
                                               'icon' => $iconmarkup,
                                               'url' => new moodle_url('/course/modedit.php', array('update' => $cm->id)),
                                               'color' => 'rgb(' . mt_rand( 0, 255 ) . ',' .
                                                                   mt_rand( 0, 255 ) . ',' .
                                                                   mt_rand( 0, 255 ) . ', .5)'
                                         );

                    // Call get_settings method for the acitivity/module.
                    // Get instance of the mod's date exractor class.
                    $mod = report_editdates_mod_date_extractor::make($cm->modname, $course);
                    if ($mod && ($cmdatesettings = $mod->get_settings($cm))) {
                        // Added activity name on the form.
                        foreach ($cmdatesettings as $cmdatetype => $cmdatesetting) {
                            $elname = 'date_mod_'.$cm->id.'_'.$cmdatetype;
                            $mform->addElement($cmdatesetting->type, $elname,
                                    $cmdatesetting->label, array(
                                    'optional' => $cmdatesetting->isoptional,
                                    'step' => $cmdatesetting->getstep));
                            $mform->setDefault($elname, $cmdatesetting->currentvalue);
                            $timeline[$cm->id] = array_merge($timeline[$cm->id],
                                                             array($cmdatesetting->label => $cmdatesetting->currentvalue));
                            if ($ismodreadonly) {
                                $mform->hardFreeze($elname);
                            }
                            $elementadded++;

                            $isdateadded = true;
                        }
                    }

                    // Completion tracking.
                    if ($coursehascompletion && isset($cm->completionexpected)) {
                        $elname = 'date_mod_'.$cm->id.'_completionexpected';
                        $mform->addElement('date_time_selector', $elname,
                                get_string('completionexpected', 'completion'),
                                array('optional' => true));
                        $mform->addHelpButton($elname, 'completionexpected', 'completion');
                        $mform->setDefault($elname, $cm->completionexpected);
                        $timeline[$cm->id] = array_merge($timeline[$cm->id],
                                                         array('completionexpected' => $cm->completionexpected));
                        if ($ismodreadonly) {
                            $mform->hardFreeze($elname);
                        }
                        $elementadded++;

                        $isdateadded = true;
                    }

                    if ($coursehasavailability) {
                        if ($cm->availability) {
                            // If there are retricted access date settings.
                            if (strpos($cm->availability, '"type":"date"') !== false) {
                                $timeline[$cm->id] = array_merge($timeline[$cm->id], array('restrict' => $cm->availability));
                                $editsettingurl = new moodle_url('/course/modedit.php', array('update' => $cm->id));
                                $editsettingurltext = html_writer::tag('a',
                                        get_string('editrestrictedaccess', 'report_editdates'),
                                                array('href' => $editsettingurl->out(false),
                                                'target' => '_blank',
                                                'class' => 'editdates_highlight'));
                                $mform->addElement('static', '',
                                        get_string('hasrestrictedaccess', 'report_editdates', ($cm->name)),
                                                $editsettingurltext);
                            }
                        } else {
                            $editsettingurl = new moodle_url('/course/modedit.php', array('update' => $cm->id));
                            $editsettingurltext = html_writer::tag('a',
                                    get_string('addrestrictedaccess', 'report_editdates'),
                                            array('href' => $editsettingurl->out(false), 'target' => '_blank'));
                            if ($isdateadded) {
                                $mform->addElement('static', 'modrestrict' . $cm->id,
                                        get_string('norestrictedaccess', 'report_editdates', ($cm->name)),
                                                $editsettingurltext);
                            }
                        }
                    }

                    if ($isdateadded) {
                        $ismodadded = true;
                        $addactionbuttons = true;
                        $mform->addElement('static', 'moddivider' . $cm->id, '');
                    }
                } // End of modules loop.
            }
        } // End of sections loop.

        // Fetching all the blocks added directly under the course.
        // That is, parentcontextid = coursecontextid.
        $courseblocks = $DB->get_records('block_instances', array('parentcontextid' => $coursecontext->id));

        // Check capability of current user.
        $canmanagesiteblocks = has_capability('moodle/site:manageblocks', $coursecontext);

        $anyblockadded = false;
        if ($courseblocks) {
            // Header for blocks.
            $mform->addElement('header', 'blockdatesection');

            // Iterate though blocks array.
            foreach ($courseblocks as $blockid => $block) {
                $blockdatextrator = report_editdates_block_date_extractor::make($block->blockname, $course);
                if ($blockdatextrator) {
                    // Create the block instance.
                    $blockobj = block_instance($block->blockname, $block, $PAGE);
                    // If get_settings returns a valid array.
                    if ($blockdatesettings = $blockdatextrator->get_settings($blockobj)) {
                        $anyblockadded = true;
                        $addactionbuttons = true;
                        // Adding block's Title on page.
                        $mform->addElement('static', 'blocktitle', $blockobj->title);
                        foreach ($blockdatesettings as $blockdatetype => $blockdatesetting) {
                            $elname = 'date_block_'.$block->id.'_'.$blockdatetype;
                            // Add element.
                            $mform->addElement($blockdatesetting->type, $elname,
                                    $blockdatesetting->label,
                                    array('optional' => $blockdatesetting->isoptional,
                                    'step' => $blockdatesetting->getstep));
                            $mform->setDefault($elname, $blockdatesetting->currentvalue);
                            if (!$canmanagesiteblocks || !$blockobj->user_can_edit()) {
                                $mform->hardFreeze($elname);
                            }
                            $elementadded++;
                        }
                    }
                }
            }
        }

        if (!$anyblockadded && $mform->elementExists('blockdatesection')) {
            $mform->removeElement('blockdatesection');
        }

        // Adding submit/cancel buttons @ the end of the form.
        if ($addactionbuttons && $elementadded > 0) {
            $this->add_action_buttons();
        } else {
            // Remove top action button.
            $mform->removeElement('buttonar');
        }

        if ($config->timelinemax) { // Create timeline view.
            $mform->closeHeaderBefore('timelineview');
            $mform->addElement('static', 'timelineview', '');
            $mform->addElement('html', self::render_timeline_view($timeline));
        }
    }

    public function validation($data, $files) {
        global $CFG;
        $errors = parent::validation($data, $files);

        $modinfo = $this->_customdata['modinfo'];
        $course = $this->_customdata['course'];
        $coursecontext = context_course::instance($course->id);

        $moddatesettings = array();
        $forceddatesettings = array();
        foreach ($data as $key => $value) {
            if ($key == "coursestartdate") {
                continue;
            }

            $cmsettings = explode('_', $key);
            // The array should have 4 keys.
            if (count($cmsettings) != 4) {
                continue;
            }

            // Ignore 0th position, it will be 'date'
            // 1st position should be the mod type
            // 2nd will be the id of module
            // 3rd will be property of module
            // ensure that the name is proper.
            if (isset($cmsettings['1']) && isset($cmsettings['2']) && isset($cmsettings['3'])) {
                // Check if its mod date settings.
                if ($cmsettings['1'] == 'mod') {
                    // Check if config date settings are forced
                    // and this is one of the forced date setting.
                    if (($CFG->enableavailability || $CFG->enablecompletion )
                            && in_array($cmsettings['3'], array('completionexpected', 'availablefrom', 'availableuntil'))) {
                        $forceddatesettings[$cmsettings['2']][$cmsettings['3']] = $value;
                    } else {
                        // It is module date setting.
                        $moddatesettings[$cmsettings['2']][$cmsettings['3']] = $value;
                    }
                }
            }
        }

        $cms = $modinfo->get_cms();

        // Validating forced date settings.
        foreach ($forceddatesettings as $modid => $datesettings) {
            // Course module object.
            $cm = $cms[$modid];
            $moderrors = array();
            if (isset($datesettings['availablefrom']) && isset($datesettings['availableuntil'])
                    && $datesettings['availablefrom'] != 0 && $datesettings['availableuntil'] != 0
                    && $datesettings['availablefrom'] > $datesettings['availableuntil'] ) {
                $errors['date_mod_'.$modid.'_availableuntil'] =
                    get_string('badavailabledates', 'condition');
            }
        }

        // Validating mod date settings.
        foreach ($moddatesettings as $modid => $datesettings) {
            // Course module object.
            $cm = $cms[$modid];
            $moderrors = array();

            if ($mod = report_editdates_mod_date_extractor::make($cm->modname, $course)) {
                $moderrors = $mod->validate_dates($cm, $datesettings);
                if (!empty($moderrors)) {
                    foreach ($moderrors as $errorfield => $errorstr) {
                        $errors['date_mod_'.$modid.'_'.$errorfield] = $errorstr;
                    }
                }
            }
        }

        if (!empty($errors)) {
            // If there are any validation errors, which may be hidden a long way down this
            // very big form, put a message at the top too.
            $errors['topvalidationsite'] = get_string('changesnotsaved', 'report_editdates');
        }

        return $errors;
    }

    public function render_timeline_view($data) {
        $data = self::sort_timeline_data($data);
        $config = get_config('report_editdates');

        $first = reset($data);
        $last = end($data);
        $output = "";
        if (isset($first["time"]) && isset($last["time"])) {
            $first["time"] -= 86400; // Timeline view starts 1 day before first activity time.
            $last["time"] += 86400; // Timeline view ends 1 day after last activity time.
            $current = usergetdate($first["time"]);

            $datediff = $last["time"] - $first["time"];
            $days = round($datediff / (60 * 60 * 24)) + 1; // Timeine view expanded by 1 day.

            $output .= '<div class="vertical-text-container">';
            if ($days < ($config->timelinemax * 365)) { // Timeline day span visibility.
                $output .= '<table><tr style="height: 65px;">';
                for ($d = 0; $d <= $days; $d++) { // Create timeline vertical header.
                    $date = usergetdate($first["time"] + ($d * (60 * 60 * 24)));
                    $output .= '<th class="vertical-text"><div><span>' .
                                    $date['mon'] . '/' . $date['mday'] . '/' . $date['year'] .
                                '</span></div></th>';
                }
                $output .= '</tr><tr>';
                for ($d = 0; $d <= $days; $d++) {  // Check day by day for activity.
                    $date = usergetdate($first["time"] + ($d * (60 * 60 * 24)));
                    $style = $date["wday"] == 0 || $date["wday"] == 6 ? 'background: rgb(0,250,0,.1);' : '';
                    $output .= '<td style="' . $style . '">';
                    foreach ($data as $item) {  // Check each item to see if it occurs on the day.
                        $current = usergetdate($item["time"]);
                        if ($current["year"] . $current["yday"] == $date["year"] . $date["yday"]) {
                            $output .= '<a target="_blank" class="timelineitem" style="background:' . $item["color"] . '" ' .
                                           'href="' . $item["url"] . '" title="' . $item["name"] . '">' .
                                            $item["icon"] .
                                       '</a>';
                        }
                    }
                    $output .= '</td>';
                }
                $output .= '</tr></table>';
            } else {
                $output .= '<h3><strong>' . get_string('toomuchtime', 'report_editdates', $config->timelinemax) . '</strong></h3>';
            }

            $output .= '</div>';
        }
        return $output;
    }

    private function sort_timeline_data($data) {
        $sorted = array();
        // Find earliest and latest date.
        foreach ($data as $mod) {
            foreach ($mod as $key => $value) {
                if ($key == "restrict") { // Process restriction dates.
                    $objects = json_decode($value);
                    foreach ($objects->c as $obj) {
                        if (property_exists($obj, 't') && is_numeric($obj->t) && $obj->t > 0) {
                            $sorted[] = array('type' => $mod["type"],
                                              'restric' => true,
                                              'name' => $mod["name"] . ": Restrict Access",
                                              'icon' => $mod["icon"],
                                              'url' => $mod["url"],
                                              'color' => $mod["color"],
                                              'time' => $obj->t);
                        }
                    }
                } else if (is_numeric($value) && $value > 0 && $key !== "name") {
                    $sorted[] = array('type' => $mod["type"],
                                      'name' => $mod["name"] . ": $key",
                                      'icon' => $mod["icon"],
                                      'url' => $mod["url"],
                                      'color' => $mod["color"],
                                      'time' => $value);
                }
            }
        }

        usort($sorted, function ($a, $b) {
                           return (int) $a["time"] <=> (int) $b["time"];
        });
        return $sorted;
    }
}
