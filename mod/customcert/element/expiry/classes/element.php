<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * This file contains the customcert element expiry's core interaction API.
 *
 * @package    customcertelement_expiry
 * @copyright  2024 Leon Stringer <leon.stringer@ntlworld.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_expiry;

use mod_customcert\element_helper;

/**
 * The customcert element expiry's core interaction API.
 *
 * @package    customcertelement_expiry
 * @copyright  2024 Leon Stringer <leon.stringer@ntlworld.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \mod_customcert\element {

    /**
     * Date - Relative expiry date of 1 year
     */
    private const EXPIRY_ONE = '-8';

    /**
     * Date - Relative expiry date of 2 year
     */
    private const EXPIRY_TWO = '-9';

    /**
     * Date - Relative expiry date of 3 year
     */
    private const EXPIRY_THREE = '-10';

    /**
     * Date - Relative expiry date of 4 year
     */
    private const EXPIRY_FOUR = '-11';

    /**
     * Date - Relative expiry date of 5 year
     */
    private const EXPIRY_FIVE = '-12';

    /** @var array Map EXPIRY_ consts to strtotime()'s $datetime param. */
    private array $relative = [
        self::EXPIRY_ONE => '+1 year',
        self::EXPIRY_TWO => '+2 years',
        self::EXPIRY_THREE => '+3 years',
        self::EXPIRY_FOUR => '+4 years',
        self::EXPIRY_FIVE => '+5 years',
    ];

    /**
     * This function renders the form elements when adding a customcert element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {
        global $CFG, $COURSE;

        $dateoptions[self::EXPIRY_ONE] = get_string('expirydateone', 'customcertelement_expiry');
        $dateoptions[self::EXPIRY_TWO] = get_string('expirydatetwo', 'customcertelement_expiry');
        $dateoptions[self::EXPIRY_THREE] = get_string('expirydatethree', 'customcertelement_expiry');
        $dateoptions[self::EXPIRY_FOUR] = get_string('expirydatefour', 'customcertelement_expiry');
        $dateoptions[self::EXPIRY_FIVE] = get_string('expirydatefive', 'customcertelement_expiry');

        $mform->addElement('select', 'dateitem', get_string('dateitem', 'customcertelement_expiry'), $dateoptions);
        $mform->addHelpButton('dateitem', 'dateitem', 'customcertelement_expiry');

        $mform->addElement('select', 'dateformat', get_string('dateformat', 'customcertelement_expiry'), self::get_date_formats());
        $mform->addHelpButton('dateformat', 'dateformat', 'customcertelement_expiry');

        $startdates['award'] = get_string('awarddate', 'customcertelement_expiry');

        if ($CFG->enablecompletion && ($COURSE->id == SITEID || $COURSE->enablecompletion)) {
            $startdates['coursecomplete'] = get_string('completiondate', 'customcertelement_expiry');
        }

        $mform->addElement('select', 'startfrom', get_string('startfrom', 'customcertelement_expiry'), $startdates);
        $mform->addHelpButton('startfrom', 'startfrom', 'customcertelement_expiry');

        parent::render_form_elements($mform);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * customcert_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the json encoded array
     */
    public function save_unique_data($data) {
        // Array of data we will be storing in the database.
        $arrtostore = [
            'dateitem' => $data->dateitem,
            'dateformat' => $data->dateformat,
            'startfrom' => $data->startfrom,
        ];

        // Encode these variables before saving into the DB.
        return json_encode($arrtostore);
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     */
    public function render($pdf, $preview, $user) {
        global $DB;

        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        $courseid = element_helper::get_courseid($this->id);
        $dateinfo = json_decode($this->get_data());
        $dateformat = $dateinfo->dateformat;
        $dateitem = $dateinfo->dateitem;
        $date = $this->expiry($user->id, $preview);

        // Ensure that a date has been set.
        if (!empty($date)) {
            if ($dateformat == 'validfor') {
                if ($dateitem == self::EXPIRY_ONE) {
                    element_helper::render_content($pdf, $this, 'Valid for 1 year');
                } else if ($dateitem == self::EXPIRY_TWO) {
                    element_helper::render_content($pdf, $this, 'Valid for 2 years');
                } else if ($dateitem == self::EXPIRY_THREE) {
                    element_helper::render_content($pdf, $this, 'Valid for 3 years');
                } else if ($dateitem == self::EXPIRY_FOUR) {
                    element_helper::render_content($pdf, $this, 'Valid for 4 years');
                } else if ($dateitem == self::EXPIRY_FIVE) {
                    element_helper::render_content($pdf, $this, 'Valid for 5 years');
                }
            } else {
                element_helper::render_content($pdf, $this, element_helper::get_date_format_string($date, $dateformat));
            }
        }
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    public function render_html() {
        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        // Decode the information stored in the database.
        $dateinfo = json_decode($this->get_data());
        $dateformat = $dateinfo->dateformat;
        $dateitem = $dateinfo->dateitem;

        if ($dateformat == 'validfor') {
            if ($dateitem == self::EXPIRY_ONE) {
                return element_helper::render_html_content($this, get_string('validfor1year', 'customcertelement_expiry'));
            } else if ($dateitem == self::EXPIRY_TWO) {
                return element_helper::render_html_content($this, get_string('validfor2years', 'customcertelement_expiry'));
            } else if ($dateitem == self::EXPIRY_THREE) {
                return element_helper::render_html_content($this, get_string('validfor3years', 'customcertelement_expiry'));
            } else if ($dateitem == self::EXPIRY_FOUR) {
                return element_helper::render_html_content($this, get_string('validfor4years', 'customcertelement_expiry'));
            } else if ($dateitem == self::EXPIRY_FIVE) {
                return element_helper::render_html_content($this, get_string('validfor5years', 'customcertelement_expiry'));
            }
        } else {
            return element_helper::render_html_content($this, element_helper::get_date_format_string(
                strtotime($this->relative[$dateitem], time()), $dateformat));
        }
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        // Set the item and format for this element.
        if (!empty($this->get_data())) {
            $dateinfo = json_decode($this->get_data());

            $element = $mform->getElement('dateitem');
            $element->setValue($dateinfo->dateitem);

            $element = $mform->getElement('dateformat');
            $element->setValue($dateinfo->dateformat);

            $element = $mform->getElement('startfrom');
            $element->setValue($dateinfo->startfrom);
        }

        parent::definition_after_data($mform);
    }

    /**
     * This function is responsible for handling the restoration process of the element.
     *
     * We will want to update the course module the date element is pointing to as it will
     * have changed in the course restore.
     *
     * @param \restore_customcert_activity_task $restore
     */
    public function after_restore($restore) {
        global $DB;

        $dateinfo = json_decode($this->get_data());

        $isgradeitem = false;
        $oldid = $dateinfo->dateitem;
        if (strpos($dateinfo->dateitem, 'gradeitem:') === 0) {
            $isgradeitem = true;
            $oldid = str_replace('gradeitem:', '', $dateinfo->dateitem);
        }

        $itemname = $isgradeitem ? 'grade_item' : 'course_module';
        if ($newitem = \restore_dbops::get_backup_ids_record($restore->get_restoreid(), $itemname, $oldid)) {
            $dateinfo->dateitem = '';
            if ($isgradeitem) {
                $dateinfo->dateitem = 'gradeitem:';
            }
            $dateinfo->dateitem = $dateinfo->dateitem . $newitem->newitemid;
            $DB->set_field('customcert_elements', 'data', $this->save_unique_data($dateinfo), ['id' => $this->get_id()]);
        }
    }

    /**
     * Helper function to return all the date formats.
     *
     * @return array the list of date formats
     */
    private static function get_date_formats(): array {
        $dateformats = element_helper::get_date_formats();
        $dateformats['validfor'] = get_string('validfor', 'customcertelement_expiry');

        return $dateformats;
    }

    /**
     * Get expiry date for user.
     *
     * @param int $userid User who has been awarded certificate.
     * @param bool $preview True if it is a preview in which case calculate
     * expiry date from now, false otherwise.
     * @return int Timestamp in Unix format (number of seconds since epoch).
     */
    private function expiry($userid, $preview = false) {
        global $DB;

        $dateinfo = json_decode($this->get_data());
        $dateitem = $dateinfo->dateitem;
        $startfrom = $dateinfo->startfrom;
        $starttime = null;

        if ($preview) {
            $starttime = time();
        } else if ($startfrom == 'coursecomplete') {
            $courseid = \mod_customcert\element_helper::get_courseid($this->id);
            // Get the last completion date.
            $sql = "SELECT MAX(c.timecompleted) as timecompleted
                      FROM {course_completions} c
                     WHERE c.userid = :userid
                       AND c.course = :courseid";
            if ($timecompleted = $DB->get_record_sql($sql, ['userid' => $userid, 'courseid' => $courseid])) {
                if (!empty($timecompleted->timecompleted)) {
                    $starttime = $timecompleted->timecompleted;
                }
            }
        } else { // Expiry date calculated from certificate award date.
            // Get the page.
            $page = $DB->get_record('customcert_pages', ['id' => $this->get_pageid()], '*', MUST_EXIST);
            // Get the customcert this page belongs to.
            $customcert = $DB->get_record('customcert', ['templateid' => $page->templateid], '*', MUST_EXIST);
            // Now we can get the issue for this user.
            $issue = $DB->get_record('customcert_issues', ['userid' => $userid, 'customcertid' => $customcert->id],
                '*', IGNORE_MULTIPLE);
            $starttime = $issue->timecreated;
        }

        if (is_null($starttime)) {
            return 0;
        }

        return strtotime($this->relative[$dateitem], $starttime);
    }

    /**
     * Does this certificate have one or more expiry elements?
     *
     * @param int $customcertid ID of the certificate.
     * @return bool True if this certificate has an expiry element (and thus
     * can show an expiry date for reports), false otherwise.
     */
    public static function has_expiry($customcertid): bool {
        global $DB;
        $sql = "SELECT e.id
                  FROM {customcert_elements} e
                  JOIN {customcert_pages} p ON e.pageid = p.id
                  JOIN {customcert} c ON p.templateid = c.templateid
                 WHERE element = 'expiry' AND c.id = :customcertid";
        return !empty($DB->get_records_sql($sql, ['customcertid' => $customcertid]));
    }

    /**
     * Return the expiry date for this certificate wrapped in a <span>.
     *
     * @param int $customcertid The certificate.
     * @param int $userid The user who has been awarded this certificate.
     * @return string HTML fragment, for example, '<span
     * class="customcertelement-expiry ok">Monday, 6 July 2026, 2:40 PM</span>'
     */
    public static function get_expiry_html(int $customcertid, int $userid): string {
        global $OUTPUT;
        $expiry = self::get_expiry_date($customcertid, $userid);

        // This can happen if the 'startfrom' date is course completion and the
        // student hasn't completed the course but has been awarded a
        // certificate.
        if (empty($expiry)) {
            return '';
        }

        $data = new \stdClass();
        $data->date = userdate($expiry);
        $expired = ($expiry - time()) / DAYSECS;

        if ($expired < 0) {
            $data->expiry = "expired";
        } else if ($expired < 14) {
            $data->expiry = "expire-soon";
        } else {
            $data->expiry = "ok";
        }

        return $OUTPUT->render_from_template('customcertelement_expiry/date', $data);
    }

    /**
     * Return the expiry date for this certificate.  If there are multiple
     * expiry elements for the given certificate then the date is calculated
     * using the settings for the first element returned by the database.
     * (Multiple elements are supported as date elements using dateitem = -8 to
     * -12 are migrated to this element with no restriction on the number of
     * elements).
     *
     * @param int $customcertid The certificate.
     * @param int $userid The user who has been awarded this certificate.
     * @return int Timestamp in Unix format (number of seconds since epoch).
     */
    public static function get_expiry_date(int $customcertid, int $userid): int {
        global $DB;
        $sql = "SELECT e.*
                  FROM {customcert_elements} e
                  JOIN {customcert_pages} p ON e.pageid = p.id
                  JOIN {customcert} c ON p.templateid = c.templateid
                 WHERE element = 'expiry' AND c.id = :customcertid";

        // As it's permitted to have more than one expiry element on a
        // certificate we use the first returned by this query to calculate the
        // expiry date for reporting.
        $expirydata = $DB->get_records_sql($sql, ['customcertid' => $customcertid], 0, 1);
        $element = new self(reset($expirydata));
        return $element->expiry($userid);
    }
}
