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
 * Steps definitions for behat theme.
 *
 * @package   theme_snap
 * @category  test
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode,
    Behat\Mink\Element\NodeElement,
    Behat\Mink\Exception\ExpectationException,
    Behat\MinkExtension\Context\MinkContext,
    Moodle\BehatExtension\Exception\SkippedException,
    core\message\message;

/**
 * Choice activity definitions.
 *
 * @package   theme_snap
 * @category  test
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap extends behat_base {

    /**
     * Ensures that the provided node is visible and we can interact with it.
     *
     * @throws ExpectationException
     * @param NodeElement $node
     * @return void Throws an exception if it times out without the element being visible
     */
    protected function ensure_node_not_visible($node) {

        if (!$this->running_javascript()) {
            return;
        }

        // Exception if it timesout and the element is still there.
        $msg = 'The "' . $node->getXPath() . '" xpath node is visible and it should be hidden';
        $exception = new ExpectationException($msg, $this->getSession());

        // It will stop spinning once the isVisible() method returns false.
        $this->spin(
            function($context, $args) {
                if (!$args->isVisible()) {
                    return true;
                }
                return false;
            },
            $node,
            behat_base::get_extended_timeout(),
            $exception,
            true
        );
    }

    /**
     * Ensures that the provided element is not visible or does not exist.
     *
     * @throws ExpectationException
     * @param string $element
     * @param string $selectortype
     * @return void
     */
    protected function ensure_element_not_visible($element, $selectortype) {

        if (!$this->running_javascript()) {
            return;
        }

        try {
            $node = $this->get_selected_node($selectortype, $element);
        } catch (ElementNotFoundException $e) {
            // Element is not found - that's good enough!
            return;
        }

        $this->ensure_node_not_visible($node);

        return $node;
    }

    /**
     * Checks if running in a Open LMS system, skips the test if not.
     *
     * @Given /^I am using Open LMS$/
     * @return void
     */
    public function i_am_using_blackboard_open_lms() {
        global $CFG;
        if (!file_exists($CFG->dirroot.'/local/mrooms')) {
            throw new SkippedException("Skipping tests of Open LMS specific functionality");
        }
    }

    /**
     * Waits until the provided element selector is visible.
     *
     * @Given /^I wait until "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" is visible$/
     * @param string $element
     * @param string $selectortype
     * @return void
     */
    public function i_wait_until_is_visible($element, $selectortype) {
        $this->ensure_element_is_visible($element, $selectortype);
    }

    /**
     * Waits until the provided element selector is not visible.
     *
     * @Given /^I wait until "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" is not visible$/
     * @param string $element
     * @param string $selectortype
     * @return void
     */
    public function i_wait_until_not_visible($element, $selectortype) {
        $this->ensure_element_not_visible($element, $selectortype);
    }

    protected function upload_file($fixturefilename, $selector) {
        global $CFG;
        $fixturefilename = clean_param($fixturefilename, PARAM_FILE);
        $filepath = $CFG->dirroot.'/theme/snap/tests/fixtures/'.$fixturefilename;
        $file = $this->find('css', $selector);
        $file->attachFile($filepath);
    }

    /**
     * @param string $fixturefilename this is a filename relative to the snap fixtures folder.
     * @param int $section
     *
     * @Given /^I upload file "(?P<fixturefilename_string>(?:[^"]|\\")*)" to section (?P<section_int>(?:\d+))$/
     */
    public function i_upload_file($fixturefilename, $section = 1) {
        $this->upload_file($fixturefilename, '#snap-drop-file-'.$section);
    }

    /**
     * @param string $fixturefilename this is a filename relative to the snap fixtures folder.
     *
     * @Given /^I upload cover image "(?P<fixturefilename_string>(?:[^"]|\\")*)"$/
     */
    public function i_upload_cover_image($fixturefilename) {
        $this->upload_file($fixturefilename, '#snap-coverfiles');
        $this->getSession()->executeScript('jQuery( "#snap-coverfiles" ).trigger( "change" );');
    }

    /**
     * @param int $section
     * @Given /^I go to single course section (\d+)$/
     */
    public function i_go_to_single_course_section($section) {
        $generalcontext = behat_context_helper::get('behat_general');
        $generalcontext->wait_until_the_page_is_ready();
        $currenturl = $this->getSession()->getCurrentUrl();
        if (stripos($currenturl, 'course/view.php') === false) {
            throw new ExpectationException('Current page is not a course page!', $this->getSession());
        }
        if (strpos($currenturl, '?') !== false) {
            $glue = '&';
        } else {
            $glue = '?';
        }
        $newurl = $currenturl.$glue.'section='.$section;
        $this->getSession()->visit($newurl);
    }

    /**
     * @param int $section
     * @Given /^I go to course section (\d+)$/
     */
    public function i_go_to_course_section($section) {
        $generalcontext = behat_context_helper::get('behat_general');
        $generalcontext->wait_until_the_page_is_ready();
        $session = $this->getSession();
        $currenturl = $session->getCurrentUrl();
        if (stripos($currenturl, 'course/view.php') === false) {
            throw new ExpectationException('Current page is not a course page!', $session);
        }
        $session->executeScript('location.hash = "'.'section-'.$section.'";');
        $this->i_wait_until_is_visible('#section-'.$section, 'css_element');
    }

    /**
     * @param string $shortname
     * @return array
     * @Given  /^I can see course "(?P<shortname>(?:[^"]|\\")*)" in all sections mode$/
     */
    public function i_can_see_course_in_all_sections_mode($shortname) {
        $this->i_am_on_course_page($shortname);
        $this->i_go_to_single_course_section(1);

        // In the selector below, .section-navigation.navigationtitle relates to the element which contains the single
        // section at a time navigation. Visually you would see a link on the left entitled "General" and a link on the
        // right entitled "Topic 2"
        // This test ensures you do not see those elements. If you swap to clean theme in a single section mode at a
        // time course you will see that navigation after clicking on topic 1.
        $this->execute('behat_general::should_not_exist', ['.section-navigation.navigationtitle', 'css_element']);
    }

    /**
     * @param string $shortname - course shortname
     * @Given /^I create a new section in course "(?P<shortname>(?:[^"]|\\")*)"$/
     * @return array
     */
    public function i_create_a_new_section_in_course($shortname) {

        $this->i_am_on_course_page($shortname);

        $this->execute('behat_general::i_change_window_size_to', ['window', '600x1000']);
        $this->execute('behat_general::i_click_on', ['#sidebar-toc-mobile-toggle', 'css_element']);
        $this->execute('behat_general::click_link', ['Create a new section']);
        $this->execute('behat_forms::i_set_the_field_to', ['Title', 'New section title']);
        $this->execute('behat_general::i_click_on', ['Create section', 'button']);
        $this->execute('behat_general::i_change_window_size_to', ['window', 'medium']);
    }

    /**
     * @param string $shortname - course shortname
     * @Given /^I create a new section in course "(?P<shortname>(?:[^"]|\\")*)" with content$/
     * @return array
     */
    public function i_create_a_new_section_in_course_with_content($shortname) {
        global $USER, $CFG;

        $origuser = $USER;
        $USER = $this->get_session_user();

        $context = context_user::instance($USER->id);

        $fs = get_file_storage();
        // Prepare file record object.
        $fileinfo = array(
            'contextid' => $context->id,
            'component' => 'user',
            'filearea' => 'private',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'test.png', );

        $fs->create_file_from_pathname($fileinfo, $CFG->dirroot . "/theme/snap/tests/fixtures/testpng.png");

        $this->i_am_on_course_page($shortname);
        $this->execute('behat_general::i_change_window_size_to', ['window', '600x1000']);
        $this->execute('behat_general::i_click_on', ['#sidebar-toc-mobile-toggle', 'css_element']);
        $this->execute('behat_general::i_click_on', ['.col-lg-3 .toc-footer #snap-new-section', 'css_element']);
        $this->execute('behat_forms::i_set_the_field_to', ['Title', 'New section with content']);
        $javascript = "var value = document.getElementById('summary-editor_ifr').contentDocument.querySelectorAll('body');";
        $javascript .= "document.getElementById('summary-editor_ifr').contentDocument.body.innerHTML = '<p>New section contents</p>';";
        $this->getSession()->executeScript($javascript);
        $this->execute('behat_general::i_change_window_size_to', ['window', 'medium']);
        $this->execute('behat_general::i_click_on', ['Image', 'button']);
        $this->execute('behat_general::i_click_on', ['Browse repositories', 'button']);
        $this->execute('behat_general::i_click_on_in_the', ['Private files', 'link', '.fp-repo-area', 'css_element']);
        $this->execute('behat_general::i_click_on', ['test.png', 'link']);
        $this->execute('behat_general::i_click_on', ['Select this file', 'button']);
        $this->execute('behat_forms::i_set_the_field_to', ["How would you describe this image to someone who can't see it?", 'File test']);
        $this->execute('behat_general::wait_until_the_page_is_ready');
        $javascript = "document.querySelector('button.tiny_image_urlentrysubmit').click()";
        $this->getSession()->executeScript($javascript);
        $javascript = "document.querySelector('input[type=\"submit\"].btn.btn-primary[name=\"addtopic\"][value=\"Create section\"]').click();";
        $this->getSession()->executeScript($javascript);
        $USER = $origuser;
    }

    /**
     * @param string $assignmentname
     * @param string $shortname
     * @param string $grade
     * @param string $username
     * @Given /^I grade the assignment "(?P<assign>(?:[^"]|\\")*)" in course "(?P<shortname>(?:[^"]|\\")*)" as follows:$/
     */
    public function i_grade_the_assignment_as_follows($assignmentname, $shortname, TableNode $data) {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot.'/mod/assign/locallib.php');

        $origuser = $USER;
        $USER = $this->get_session_user();

        $course = $DB->get_record('course', ['shortname' => $shortname]);
        $assign = $DB->get_record('assign', ['course' => $course->id, 'name' => $assignmentname]);
        $rows = $data->getHash();

        $cm = get_coursemodule_from_instance('assign', $assign->id);
        $cm = cm_info::create($cm);

        $assign = new \assign($cm->context, $cm, $course);
        $gradeitem = $assign->get_grade_item();
        $gradeitem->update();
        $assignrow = $assign->get_instance();
        $grades = array();

        $commentsplugin = $assign->get_feedback_plugin_by_type('comments');
        if ($commentsplugin->is_visible()) {
            $commentsplugin->enable();
        }

        foreach ($rows as $row) {
            $user = $DB->get_record('user', ['username' => $row['username']]);
            $grades[$user->id] = (object) [
                'rawgrade' => $row['grade'],
                'userid' => $user->id,
            ];

            $assignrow->cmidnumber = null;
            assign_grade_item_update($assignrow, $grades);

            if (!empty($row['feedback'])) {
                if ($commentsplugin->is_visible()) {
                    $formdata = (object)[
                        'id' => $cm->id,
                        'assignfeedbackcomments_editor[text]' => $row['feedback'],
                        'assignfeedbackcomments_editor[format]' => FORMAT_HTML,
                    ];
                    if (!$commentsplugin->save_settings($formdata)) {
                        throw new moodle_exception($commentsplugin->get_error());
                        $USER = $origuser;
                        return false;
                    }
                }
            }
        }

        grade_regrade_final_grades($course->id);

        $USER = $origuser;
    }

    /**
     * Checks that the provided node is visible.
     *
     * @throws ExpectationException
     * @param NodeElement $node
     * @param int $timeout
     * @param null|ExpectationException $exception
     * @return bool
     */
    protected function is_node_visible(NodeElement $node,
                                       $timeout = null,
                                       ExpectationException $exception = null) {
        $timeout = $timeout == null ? behat_base::get_extended_timeout() : $timeout;
        // If an exception isn't specified then don't throw an error if visibility can't be evaluated.
        $dontthrowerror = empty($exception);

        // Exception for timeout checking visibility.
        $msg = 'Something went wrong whilst checking visibility';
        $exception = new ExpectationException($msg, $this->getSession());

        $visible = false;

        try {
            $visible = $this->spin(
                function ($context, $args) {
                    if ($args->isVisible()) {
                        return true;
                    }
                    return false;
                },
                $node,
                $timeout,
                $exception,
                true
            );
        } catch (Exception $e) {
            if (!$dontthrowerror) {
                throw $exception;
            }
        }
        return $visible;
    }

    /**
     * Clicks link with specified id|title|alt|text.
     *
     * @When /^I follow visible link "(?P<link_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $link
     */
    public function click_visible_link($link) {
        $linknode = $this->find_link($link);
        if (!$linknode) {
            $msg = 'The "' . $link . '" link could not be found';
            throw new ExpectationException($msg, $this->getSession());
        }

        // See if the first node is visible and if so click it.
        if ($this->is_node_visible($linknode, behat_base::get_reduced_timeout())) {
            $linknode->click();
            return;
        }

        /** @var NodeElement[] $linknodes */
        $linknodes = $this->find_all('named_partial', ['link', behat_context_helper::escape($link)]);

        // Cycle through all nodes and if just one of them is visible break loop.
        foreach ($linknodes as $node) {
            if ($node === $linknode) {
                // We've already tested the first node, skip it.
                continue;
            }
            if ($node->isVisible()) {
                $node->click();
                return;
            }
        }

        // Oh dear, none of the links were visible.
        throw new ExpectationException('At least one node should be visible for the xpath "'.$xpath.'"', $this->getSession());
    }


    /**
     * List steps required for adding a date restriction
     * @param int $datetime
     * @param string $savestr
     */
    protected function add_date_restriction($datetime, $savestr) {

        $year = date('Y', $datetime);
        $month = date('n', $datetime);
        $day = date('j', $datetime);

        /** @var behat_forms $formcontext */
        $formcontext = behat_context_helper::get('behat_forms');

        /** @var behat_general $generalcontext */
        $generalcontext = behat_context_helper::get('behat_general');

        $formcontext->i_expand_all_fieldsets();
        $generalcontext->i_click_on('Add restriction...', 'button');
        $generalcontext->should_be_visible('Add restriction...', 'dialogue');
        $generalcontext->i_click_on_in_the('Date', 'button', 'Add restriction...', 'dialogue');
        $formcontext->i_set_the_field_to('day', $day);
        $formcontext->i_set_the_field_with_xpath_to('//select[@name=\'x[month]\']', $month);
        $formcontext->i_set_the_field_to('year', $year);
        $formcontext->press_button($savestr);
    }

    /**
     * Whilst editing a section, set the section name.
     * @param string $name
     * @Given /^I set the section name to "(?P<name_string>(?:[^"]|\\")*)"$/
     */
    public function i_set_section_name_to($name) {
        $this->execute('behat_forms::i_set_the_field_to', ['name', $name]);
    }

    /**
     * Whilst editing a section, set the section summary.
     * @param string $name
     * @Given /^I set the section summary to "(?P<summary_string>(?:[^"]|\\")*)"$/
     */
    public function i_set_section_summary_to($summary) {
        $this->execute('behat_forms::i_set_the_field_to', ['summary_editor[text]', $summary]);
    }

    /**
     * Restrict a course section by date.
     * @param int $section
     * @param string $date
     * @Given /^I restrict course section (?P<section_int>(?:\d+)) by date to "(?P<date_string>(?:[^"]|\\")*)"$/
     */
    public function i_restrict_course_section_by_date($section, $date) {
        $datetime = strtotime($date);
        $helper = behat_context_helper::get('behat_general');
        $this->i_go_to_course_section($section);
        $this->execute('behat_general::i_click_on', ['#section-'.$section.' .edit-summary', 'css_element']);
        $helper->wait_until_the_page_is_ready();
        $this->execute('behat_forms::i_set_the_field_to', ['name', 'Topic '.$date.' '.$section]);
        $this->add_date_restriction($datetime, 'Save changes');
    }

    /**
     * Restrict a course asset by date.
     * @param string $assettitle
     * @param string $date
     * @Given /^I restrict course asset "(?P<asset_string>(?:[^"]|\\")*)" by date to "(?P<date_string>(?:[^"]|\\")*)"$/
     */
    public function i_restrict_asset_by_date($assettitle, $date) {
        $datetime = strtotime($date);
        $this->i_follow_asset_link($assettitle);
        $this->execute('behat_general::i_click_on', ['#admin-menu-trigger', 'css_element']);
        $this->i_wait_until_is_visible('.block_settings.state-visible', 'css_element');
        $this->execute('behat_navigation::i_navigate_to_node_in', ['Edit settings', 'Assignment administration']);
        $this->add_date_restriction($datetime, 'Save and return to course');
    }

    /**
     * Restrict an assignment by date.
     * @param string $assigntitle
     * @param string $date
     * @Given /^I restrict assignment "(?P<assigntitle_string>(?:[^"]|\\")*)" by date to "(?P<date_string>(?:[^"]|\\")*)"$/
     */
    public function i_restrict_assign_by_date($assigntitle, $date) {
        $datetime = strtotime($date);
        $xpath = "//li[contains(@class, 'modtype_assign')]//a/p[contains(text(), '{$assigntitle}')]";
        $this->execute('behat_general::i_wait_seconds', [1]);
        $this->execute('behat_general::i_click_on', [$xpath, 'xpath_element']);
        $this->i_wait_until_is_visible('.assign-intro', 'css_element');
        $this->execute('behat_general::i_click_on', ['#admin-menu-trigger', 'css_element']);
        $this->i_wait_until_is_visible('.block_settings.state-visible', 'css_element');
        $this->execute('behat_navigation::i_navigate_to_in_current_page_administration', 'Edit settings');
        $this->add_date_restriction($datetime, 'Save and return to course');
    }

    /**
     * Apply asset completion restriction to section when edit form is shown.
     * @param string $assettitle
     * @Given /^I apply asset completion restriction "(?P<asset_string>(?:[^"]|\\")*)" to section$/
     */
    public function apply_section_completion_restriction($assettitle) {
        $this->apply_completion_restriction($assettitle, 'Save changes');
    }

    /**
     * Apply asset completion restriction when edit form is shown.
     * @param string $assettitle
     * @param string $savestr
     */
    protected function apply_completion_restriction($assettitle, $savestr) {
        /** @var behat_general $helper */
        $helper = behat_context_helper::get('behat_general');
        /** @var behat_forms $formhelper */
        $formhelper = behat_context_helper::get('behat_forms');
        $formhelper->i_expand_all_fieldsets();
        $helper->i_click_on('Add restriction...', 'button');
        $helper->should_be_visible('Add restriction...', 'dialogue');
        $helper->i_click_on_in_the('Activity completion', 'button', 'Add restriction...', 'dialogue');
        $formhelper->i_set_the_field_with_xpath_to('//select[@name=\'cm\']', $assettitle);
        $formhelper->press_button($savestr);
        $helper->wait_until_the_page_is_ready();
    }

    /**
     * Apply asset completion restriction when edit form is shown.
     * @param string $group
     * @param string $savestr
     */
    protected function apply_group_restriction($group, $savestr) {
        /** @var behat_general $helper */
        $helper = behat_context_helper::get('behat_general');
        /** @var behat_forms $formhelper */
        $formhelper = behat_context_helper::get('behat_forms');
        $formhelper->i_expand_all_fieldsets();
        $helper->i_click_on('Add restriction...', 'button');
        $helper->should_be_visible('Add restriction...', 'dialogue');
        $helper->i_click_on_in_the('Group', 'button', 'Add restriction...', 'dialogue');
        $formhelper->i_set_the_field_with_xpath_to('//select[@name=\'id\']', $group);
        $formhelper->press_button($savestr);
        $helper->wait_until_the_page_is_ready();
    }

    /**
     * Restrict a course asset by date.
     * @param string $asset1
     * @param string $asset2
     * @Given /^I restrict course asset "(?P<asset1_string>(?:[^"]|\\")*)" by completion of "(?P<asset2_string>(?:[^"]|\\")*)"$/
     */
    public function i_restrict_asset_by_completion($asset1, $asset2) {
        /** @var behat_general $helper */
        $helper = behat_context_helper::get('behat_general');
        $xpathassetmore = "//p[contains(@class, 'instancename')][contains(text(), '$asset1')]/ancestor::div[contains(@class, 'activityinstance')]//button[contains(@class, 'snap-edit-asset-more')]";
        $helper->i_click_on($xpathassetmore, 'xpath_element');
        $xpathedit = "//p[contains(@class, 'instancename')][contains(text(), '$asset1')]/ancestor::div[contains(@class, 'activityinstance')]//a[contains(@class, 'snap-edit-asset')]";
        $helper->i_click_on($xpathedit, 'xpath_element');
        $this->apply_completion_restriction($asset2, 'Save and return to course');
    }

    /**
     * Restrict a course asset by belonging to a group.
     * @param string $asset1
     * @param string $group1
     * @codingStandardsIgnoreStart
     * @Given /^I restrict course asset "(?P<asset1_string>(?:[^"]|\\")*)" by belong to the group "(?P<group1_string>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     */
    public function i_restrict_asset_by_belong_to_group($asset1, $group1) {
        /** @var behat_general $helper */
        $helper = behat_context_helper::get('behat_general');
        $xpathassetmore = "//p[contains(@class, 'instancename')][contains(text(), '$asset1')]/ancestor::div[contains(@class, 'activityinstance')]//button[contains(@class, 'snap-edit-asset-more')]";
        $helper->i_click_on($xpathassetmore, 'xpath_element');
        $xpathedit = "//p[contains(@class, 'instancename')][contains(text(), '$asset1')]/ancestor::div[contains(@class, 'activityinstance')]//a[contains(@class, 'snap-edit-asset')]";
        $helper->i_click_on($xpathedit, 'xpath_element');
        $this->apply_group_restriction($group1, 'Save and return to course');
    }

    /**
     * @param string $str
     * @param string $baseselector
     * @throws ExpectationException
     * @Given /^I should see availability info "(?P<str>(?:[^"]|\\")*)"$/
     */
    public function i_see_availabilityinfo($str, $baseselector = '') {
        $str = trim($str);
        $nodes = $this->find_all('xpath', $baseselector.'//div[contains(@class, \'snap-conditional-tag\')]');

        // @codingStandardsIgnoreLine
        /** @var NodeElement $node */
        foreach ($nodes as $node) {
            $nodetext = trim($node->getText());
            if (stripos($nodetext, $str) !== false) {
                return;
            }
        }

        $session = $this->getSession();
        throw new ExpectationException('Failed to find availability notice of "'.$str.'"', $session);
    }

    /**
     * Get base selector for availabilityinfo dending on type and elementstr.
     *
     * @param string $type
     * @param string $elementstr
     * @return string
     */
    private function base_selector_availabilityinfo($type, $elementstr) {
        if ($type === 'section') {
            $baseselector = '//li[@id="section-'.$elementstr.'"]';
        } else if ($type === 'asset') {
            $baseselector = '(//li[contains(@class, \'snap-asset\')]'. // Selection when editing teacher.
                '//h3[contains(@class, \'snap-asset-link\')]'.
                '//span[contains(text(), \''.$elementstr.'\')]'.
                '/parent::a/parent::h3/parent::div'.
                '|'.
                '//li[contains(@class, \'snap-asset\')]'. // Selection when anyone else.
                '//h3[contains(@class, \'snap-asset-link\')]'.
                '//*[contains(text(),  \''.$elementstr.'\')]'.
                '/parent::h3/parent::div)';
        } else {
            throw new coding_exception('Unknown element type ('.$type.')');
        }
        return $baseselector;
    }

    /**
     * @param string $str
     * @param string $type
     * @param string $elementstr
     * @throws ExpectationException
     * @codingStandardsIgnoreStart
     * @Given /^I should see availability info "(?P<str>(?:[^"]|\\")*)" in "(?P<elementtype>section|asset)" "(?P<elementstr>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     */
    public function i_see_availabilityinfo_in($str, $type, $elementstr) {
        $this->i_see_availabilityinfo($str, $this->base_selector_availabilityinfo($type, $elementstr));
    }

    /**
     * @param string $str
     * @param string $baseselector
     * @throws ExpectationException
     * @Given /^I should not see availability info "(?P<str>(?:[^"]|\\")*)"$/
     */
    public function i_dont_see_availabilityinfo($str, $baseselector = '') {
        try {
            $nodes = $this->find_all('xpath', $baseselector.'//div[contains(@class, \'snap-conditional-tag\')]');
        } catch (Exception $e) {
            if (empty($nodes)) {
                return;
            }
        }
        // @codingStandardsIgnoreStart
        /** @var NodeElement $node */
        foreach ($nodes as $node) {
            if ($node->getText() === $str) {
                $session = $this->getSession();
                $msg = 'Availability notice found in element '.$node->getXpath().' of "'.$str.'"';
                throw new ExpectationException($msg, $session);
            }
        }
        // @codingStandardsIgnoreEnd
    }

    /**
     * @param string $str
     * @param string $type
     * @param string $elementstr
     * @throws ExpectationException
     * @codingStandardsIgnoreStart
     * @Given /^I should not see availability info "(?P<str>(?:[^"]|\\")*)" in "(?P<elementtype>section|asset)" "(?P<elementstr>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     */
    public function i_dont_see_availabilityinfo_in($str, $type, $elementstr) {
        $this->i_dont_see_availabilityinfo($str, $this->base_selector_availabilityinfo($type, $elementstr));
    }

    /**
     * Check conditional date message in given element.
     * @param string $date
     * @param string $element
     * @param string $selectortype
     * @codingStandardsIgnoreStart
     * @Given /^I should see available from date of "(?P<date_string>(?:[^"]|\\")*)" in "(?P<element_string>(?:[^"]|\\")*)" "(?P<locator_string>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     */
    public function i_should_see_available_from_in_element($date, $element, $selectortype) {
        $datetime = strtotime($date);

        $date = userdate($datetime,
            get_string('strftimedate', 'langconfig'));

        /** @var behat_general $helper */
        $helper = behat_context_helper::get('behat_general');
        $helper->assert_element_contains_text('Available from', $element, $selectortype);
        $helper->assert_element_contains_text($date, $element, $selectortype);

    }

    /**
     * Check conditional date message does not exist in given element.
     * @param string $date
     * @param string $element
     * @param string $selectortype
     * @codingStandardsIgnoreStart
     * @Given /^I should not see available from date of "(?P<date_string>(?:[^"]|\\")*)" in "(?P<element_string>(?:[^"]|\\")*)" "(?P<locator_string>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     */
    public function i_should_not_see_available_from_in_element($date, $element, $selectortype) {
        $datetime = strtotime($date);

        $date = userdate($datetime,
            get_string('strftimedate', 'langconfig'));

        // If node does not exist then all is well - i.e. we can't see the string in the element because the element
        // does not exist!
        try {
            $node = $this->get_selected_node($selectortype, $element);
        } catch (Exception $e) {
            if (empty($node)) {
                return;
            }
        }
        if (empty($node)) {
            return;
        }

        /** @var behat_general $helper */
        $helper = behat_context_helper::get('behat_general');
        $helper->assert_element_not_contains_text('Available from', $element, $selectortype);
        $helper->assert_element_not_contains_text($date, $element, $selectortype);
    }

    /**
     * Check conditional date message in nth asset within section x.
     * @param string $date
     * @param string $nthasset
     * @param int $section
     * @codingStandardsIgnoreStart
     * @Given /^I should see available from date of "(?P<date_string>(?:[^"]|\\")*)" in the (?P<nthasset_string>(?:\d+st|\d+nd|\d+rd|\d+th)) asset within section (?P<section_int>(?:\d+))$/
     * @codingStandardsIgnoreEnd
     */
    public function i_should_see_available_from_in_asset($date, $nthasset, $section) {
        $nthasset = intval($nthasset);
        $elementselector = '#section-'.$section.' li.snap-asset:nth-of-type('.$nthasset.')';
        return $this->i_should_see_available_from_in_element($date, $elementselector, 'css_element');
    }

    /**
     * Check conditional date message not in nth asset within section x.
     * @param string $date
     * @param string $nthasset
     * @param int $section
     * @codingStandardsIgnoreStart
     * @Given /^I should not see available from date of "(?P<date_string>(?:[^"]|\\")*)" in the (?P<nthasset_string>(?:\d+st|\d+nd|\d+rd|\d+th)) asset within section (?P<section_int>(?:\d+))$/
     * @codingStandardsIgnoreStart
     */
    public function i_should_not_see_available_from_in_asset($date, $nthasset, $section) {
        $nthasset = intval($nthasset);
        $elementselector = '#section-'.$section.' li.snap-asset:nth-of-type('.$nthasset.')';
        return $this->i_should_not_see_available_from_in_element($date, $elementselector, 'css_element');
    }

    /**
     * Check conditional date message in section.
     * @param string $date
     * @param int $section
     * @Given /^I should see available from date of "(?P<date_string>(?:[^"]|\\")*)" in section (?P<section_int>(?:\d+))$/
     */
    public function i_should_see_available_from_in_section($date, $section) {
        $elementselector = '#section-'.$section.' > div.content > .snap-conditional-tag';
        return $this->i_should_see_available_from_in_element($date, $elementselector, 'css_element');
    }

    /**
     * Check conditional date message not in section.
     * @param string $date
     * @param int $section
     * @Given /^I should not see available from date of "(?P<date_string>(?:[^"]|\\")*)" in section (?P<section_int>(?:\d+))$/
     */
    public function i_should_not_see_available_from_in_section($date, $section) {
        $elementselector = '#section-'.$section.' > div.content > .snap-conditional-tag';
        return $this->i_should_not_see_available_from_in_element($date, $elementselector, 'css_element');
    }

    /**
     * @param string $text
     * @param int $tocitem
     * @Given /^I should see "(?P<text_string>(?:[^"]|\\")*)" in TOC item (?P<tocitem_int>(?:\d+))$/
     */
    public function i_should_see_in_toc_item($text, $tocitem) {
        $tocitem++; // Ignore introduction item.
        $element = '#chapters h3:nth-of-type('.$tocitem.')';
        $this->execute('behat_general::assert_element_contains_text', [$text, $element, 'css_element']);
    }

    /**
     * @param string $text
     * @param int $tocitem
     * @Given /^I should not see "(?P<text_string>(?:[^"]|\\")*)" in TOC item (?P<tocitem_int>(?:\d+))$/
     */
    public function i_should_not_see_in_toc_item($text, $tocitem) {
        $tocitem++; // Ignore introduction item.
        $element = '#chapters h3:nth-of-type('.$tocitem.')';
        $this->execute('behat_general::assert_element_not_contains_text', [$text, $element, 'css_element']);
    }

    /**
     * Open an assignment or resource based on title.
     *
     * @param string $assettitle
     * @throws ExpectationException
     * @Given /^I follow asset link "(?P<assettitle>(?:[^"]|\\")*)"$/
     */
    public function i_follow_asset_link($assettitle) {
        $xpath = '//a/span[contains(.,"'.$assettitle.'")]';

        // Now get all nodes.
        $linknodes = $this->find_all('xpath', $xpath);

        // Cycle through all nodes and if just one of them is visible break loop.
        foreach ($linknodes as $node) {
            $visible = $this->is_node_visible($node, behat_base::get_reduced_timeout());
            if ($visible) {
                break;
            }
        }

        if (!$visible) {
            // Oh dear, none of the links were visible.
            $msg = 'At least one node should be visible for the xpath "' . $node->getXPath();
            throw new ExpectationException($msg, $this->getSession());
        }

        // Hurray, we found a visible link - let's click it!
        $node->click();
    }

    /**
     * @param string $title
     * @Given /^I can see an input with the value "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function i_can_see_input_with_value($value) {
        $this->i_wait_until_is_visible('input[value="'.$value.'"]', 'css_element');
    }

    /**
     * @Given /^course page should be in edit mode$/
     */
    public function course_page_should_be_in_edit_mode() {
        // @codingStandardsIgnoreLine
        /* @var $generalcontext behat_general */
        $generalcontext = behat_context_helper::get('behat_general');
        $generalcontext->ensure_element_exists('#admin-menu-trigger', 'css_element');
        $generalcontext->ensure_element_exists('body.editing', 'css_element');
    }

    /**
     * @Given /^I follow the page heading course link$/
     */
    public function i_follow_the_page_heading_course_link() {
        // @codingStandardsIgnoreLine
        /** @var behat_general $helper */
        $helper = behat_context_helper::get('behat_general');
        $helper->i_click_on('#page-mast a', 'css_element');
    }

    /**
     * @Given /^I cannot follow the page heading$/
     */
    public function i_cannot_follow_the_page_heading() {
        $this->ensure_element_exists('#page-mast', 'css_element');
        $this->ensure_element_does_not_exist('#page-mast a', 'css_element');
    }

    /**
     * @param string $shortname1
     * @param string $shortname2
     * @Given /^Course card "(?P<shortname1>(?:[^"]|\\")*)" appears before "(?P<shortname2>(?:[^"]|\\")*)"$/
     */
    public function course_card_appears_before($shortname1, $shortname2) {
        // @codingStandardsIgnoreLine
        /* @var behat_general $general */
        $general = behat_context_helper::get('behat_general');

        $preelement = '.coursecard[data-shortname="'.$shortname1.'"]';
        $postelement = '.coursecard[data-shortname="'.$shortname2.'"]';

        $general->should_appear_before($preelement, 'css_element', $postelement, 'css_element');
    }

    /**
     * @param string $shortname
     * @Given /^I toggle course card favorite "(?P<shortname>(?:[^"]|\\")*)"$/
     */
    public function i_toggle_course_card_favorite($shortname) {
        // @codingStandardsIgnoreLine
        /* @var behat_general $general */
        $general = behat_context_helper::get('behat_general');
        $general->i_click_on('.coursecard[data-shortname="'.$shortname.'"] button.favoritetoggle', 'css_element');
    }

    /**
     * @Given /^the message processor "(?P<processorname_string>(?:[^"]|\\")*)" is enabled$/
     * @param string $processorname
     */
    public function i_enable_message_processor($processorname) {
        global $DB;
        $DB->set_field('message_processors', 'enabled', '1', array('name' => $processorname));
    }

    /**
     * @Given /^the message processor "(?P<processorname_string>(?:[^"]|\\")*)" is disabled$/
     * @param string $processorname
     */
    public function i_disable_message_processor($processorname) {
        global $DB;
        $DB->set_field('message_processors', 'enabled', '0', array('name' => $processorname));
    }

    /**
     * @Given /^I am on the course "(?P<shortname_string>(?:[^"]|\\")*)"$/
     * @param string $shortname
     */
    public function i_am_on_the_course($shortname) {
        global $DB;
        $courseid = $DB->get_field('course', 'id', ['shortname' => $shortname]);
        $this->getSession()->visit($this->locate_path('/course/view.php?id='.$courseid));
    }

    /**
     * @Given /^I am on the course category page for category with idnumber "(?P<catid_string>(?:[^"]|\\")*)"$/
     * @param string $idnumber
     */
    public function i_am_on_the_course_category_page($idnumber) {
        global $DB;
        $id = $DB->get_field('course_categories', 'id', ['idnumber' => $idnumber]);
        $this->getSession()->visit($this->locate_path('course/index.php?categoryid='.$id));
    }

    /**
     * Get background image for #page-header css element.
     * @return string
     */
    protected function pageheader_backgroundimage() {
        $session = $this->getSession();
        return $session->evaluateScript(
            "return jQuery('#page-header').css('background-image')"
        );
    }

    /**
     * @Given /^I should see cover image in page header$/
     */
    public function  pageheader_has_cover_image() {
        $bgimage = $this->pageheader_backgroundimage();
        if (empty($bgimage) || $bgimage === 'none') {
            $msg = '#page-header does not have background image ('.$bgimage.')';
            $exception = new ExpectationException($msg, $this->getSession());
            throw $exception;
        }
    }

    /**
     * @Given /^I should not see cover image in page header$/
     */
    public function pageheader_does_not_have_cover_image() {
        $bgimage = $this->pageheader_backgroundimage();
        if (!empty($bgimage) && $bgimage !== 'none') {
            $msg = '#page-header has a background image ('.$bgimage.')';
            $exception = new ExpectationException($msg, $this->getSession());
            throw $exception;
        }
    }

    /**
     * Toggles completion tracking for specific course.
     *
     * @codingStandardsIgnoreStart
     * @When /^completion tracking is "(?P<completion_status_string>Enabled|Disabled)" for course "(?P<course_string>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     * @param string $completionstatus The status, enabled or disabled.
     * @param string $courseshortname The shortname for the course where completion tracking is to be enabled / disabled.
     */
    public function completion_is_toggled_in_course($completionstatus, $courseshortname) {

        global $DB;

        $toggle = strtolower($completionstatus) == 'enabled' ? 1 : 0;

        $course = $DB->get_record('course', ['shortname' => $courseshortname]);
        if ($course) {
            $course->enablecompletion = $toggle;
            $DB->update_record('course', $course);
        }
    }

    /**
     * @Given /^I click on the "(?P<nth_string>(?:[^"]|\\")*)" link in the TOC$/
     *
     * @param string $nth
     */
    public function i_click_on_nth_item_in_toc($nth) {
        $nth = intval($nth);
        /** @var behat_general $helper */
        $helper = behat_context_helper::get('behat_general');
        $helper->i_click_on('#chapters h3:nth-of-type(' . $nth . ')', 'css_element');
    }

    /**
     * Click on an element using javascript.
     * @When I use js to click on :selector
     */
    public function i_use_js_to_click_on($selector) {
        $this->getSession()->executeScript("document.querySelector(`$selector`).click();");
    }

    /**
     * Check navigation in section matches link title and href.
     * @param string $type "next" / "previous"
     * @param int $section
     * @param string $linktitle
     * @param string $linkhref
     */
    protected function check_navigation_for_section($type, $section, $linktitle, $linkhref) {
        $baseselector = '#section-' . $section . ' nav.section_footer a.'.$type.'_section';
        $titleselector = $baseselector.' span';
        $node = $this->find('css', $titleselector);
        $title = $node->getHtml();
        // Title case version of type.
        $ttype = ucfirst($type);
        if ($type == 'next') {
            $sectionnumber = $section + 1;
        } else {
            $sectionnumber = $section - 1;
        }
        $expectedtitle = '<span class="nav_guide" section-number="' . $sectionnumber . '">' . $ttype
            . ' section</span><br>'.htmlentities($linktitle, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);
        if (strtolower($title) !== strtolower($expectedtitle)) {
            $msg = $ttype.' title does not match expected "' . $expectedtitle . '"' . ' V "' . $title .
                    '" - selector = "'.$titleselector.'"';
            throw new ExpectationException($msg, $this->getSession());
        }
        $node = $this->find('css', $baseselector);
        $href = $node->getAttribute('href');
        // Full course link, find the #href only.
        $data = explode('#', $href);
        if (count($data) != 2 || '#' . $data[1] != $linkhref) {
            $msg = $ttype.' navigation href does not match expected "' . $linkhref . '"' . ' V "' . $href .
                        '" - selector = "'.$baseselector.'"';
            throw new ExpectationException($msg, $this->getSession());
        }
    }

    /**
     * @codingStandardsIgnoreStart
     * @Given /^the previous navigation for section "(?P<section_int>(?:[^"]|\\")*)" is for "(?P<title_str>(?:[^"]|\\")*)" linking to "(?P<link_str>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     * @param int $section
     * @param string $linktitle
     * @param string $linkhref
     */
    public function the_previous_navigation_for_section_is($section, $linktitle, $linkhref) {
        $this->check_navigation_for_section('previous', $section, $linktitle, $linkhref);
    }

    /**
     * @codingStandardsIgnoreStart
     * @Given /^the next navigation for section "(?P<section_int>(?:[^"]|\\")*)" is for "(?P<title_str>(?:[^"]|\\")*)" linking to "(?P<link_str>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     * @param int $section
     * @param string $linktitle
     * @param string $linkhref
     */
    public function the_next_navigation_for_section_is($section, $linktitle, $linkhref) {
        $this->check_navigation_for_section('next', $section, $linktitle, $linkhref);
    }

    /**
     * Check navigation in section has hidden state.
     * @param string $type "next" / "previous"
     * @param int $section
     */
    protected function check_navigation_hidden_for_section($type, $section) {
        $selector = '#section-' . $section . ' nav.section_footer a.'.$type.'_section';
        $node = $this->find('css', $selector);
        $class = $node->getAttribute('class');
        $classes = explode(' ', $class);
        if (!in_array('dimmed_text', $classes)) {
            $msg = 'Section link should be hidden - selector "' . $selector . '"';
            throw new ExpectationException($msg, $this->getSession());
        }
    }

    /**
     * @Given /^the previous navigation for section "(?P<section_int>(?:[^"]|\\")*)" shows as hidden$/
     * @param int $section
     */
    public function the_previous_navigation_for_section_is_hidden($section) {
        $this->check_navigation_hidden_for_section('previous', $section);
    }

    /**
     * @Given /^the next navigation for section "(?P<section_int>(?:[^"]|\\")*)" shows as hidden$/
     * @param int $section
     */
    public function the_next_navigation_for_section_is_hidden($section) {
        $this->check_navigation_hidden_for_section('next', $section);
    }

    /**
     * Check navigation in section has visible state.
     * @param string $type "next" / "previous"
     * @param int $section
     */
    protected function check_navigation_visible_for_section($type, $section) {
        $selector = '#section-' . $section . ' nav.section_footer a.'.$type.'_section';
        $node = $this->find('css', $selector);
        $class = $node->getAttribute('class');
        $classes = explode(' ', $class);
        if (in_array('dimmed_text', $classes)) {
            $msg = 'Section link should be visible - selector "' . $selector . '"';
            throw new ExpectationException($msg, $this->getSession());
        }
    }

    /**
     * @Given /^the previous navigation for section "(?P<section_int>(?:[^"]|\\")*)" shows as visible$/
     * @param int $section
     */
    public function the_previous_navigation_for_section_is_visble($section) {
        $this->check_navigation_visible_for_section('previous', $section);
    }

    /**
     * @Given /^the next navigation for section "(?P<section_int>(?:[^"]|\\")*)" shows as visible$/
     * @param int $section
     */
    public function the_next_navigation_for_section_is_visible($section) {
        $this->check_navigation_visible_for_section('next', $section);
    }

    /**
     * Logs out via a separate window so that the current window retains all options that require login.
     * @Given /^I log out via a separate window$/
     */
    public function i_log_out_via_a_separate_window() {
        global $CFG;
        $session = $this->getSession();
        $mainwindow = $session->getWindowName();
        $logoutwindow = 'Log out window';
        $session->executeScript('window.open("'.$CFG->wwwroot.'", "'.$logoutwindow.'")');
        sleep(1); // Allow time for the window to open.
        $session->switchToWindow($logoutwindow);
        $this->execute('behat_auth::i_log_out');
        $session->executeScript('window.close()');
        $session->switchToWindow($mainwindow);
    }

    /**
     * @Given /^the course format for "(?P<shortname_string>(?:[^"]|\\")*)" is set to "(?P<format_string>(?:[^"]|\\")*)"$/
     * @param string $shortname
     * @param string $format
     */
    public function the_course_format_is_set_to($shortname, $format) {
        global $DB;
        $service = theme_snap\services\course::service();
        $course = $service->coursebyshortname($shortname, 'id');
        $DB->set_field('course', 'format', $format, ['id' => intval($course->id)]);
    }

    /**
     * @codingStandardsIgnoreStart
     * @Given /^the course format for "(?P<shortname_string>(?:[^"]|\\")*)" is set to "(?P<format_string>(?:[^"]|\\")*)" with the following settings:$/
     * @codingStandardsIgnoreEnd
     * @param string $shortname
     * @param string $format
     */
    public function the_course_format_is_set_to_and_configured($shortname, $format, TableNode $data) {
        global $DB;
        $service = theme_snap\services\course::service();
        $course = $service->coursebyshortname($shortname, 'id');
        $DB->set_field('course', 'format', $format, ['id' => intval($course->id)]);
        $row = $data->getRowsHash();
        if (isset($row['sectionid'])) {
            $row['sectionid'] = 0;
        }
        $row['courseid'] = $course->id;
        $row['format'] = $format;
        $DB->insert_record('course_format_options', $row);
    }

    /**
     * @Given /^I am on the course main page for "(?P<shortname_string>(?:[^"]|\\")*)"$/
     * @param string $shortname
     */
    public function i_am_on_course_page($shortname) {
        $service = theme_snap\services\course::service();
        $course = $service->coursebyshortname($shortname, 'id');
        $this->getSession()->visit($this->locate_path('/course/view.php?id='.$course->id));
    }

    /**
     * @Given /^I am on the course "(?P<subpage_string>(?:[^"]|\\")*)" page for "(?P<shortname_string>(?:[^"]|\\")*)"$/
     * @param string $shortname
     */
    public function i_am_on_course_subpage($subpage, $shortname) {
        $service = theme_snap\services\course::service();
        $course = $service->coursebyshortname($shortname, 'id');
        $this->getSession()->visit($this->locate_path('/course/'.$subpage.'.php?id='.$course->id));
    }

    /**
     * Get user record by username.
     *
     * @param string $username
     * @return stdClass | false
     * @throws coding_exception
     */
    private function get_user_by_username($username) {
        global $DB;
        $user = $DB->get_record('user', ['username' => $username]);
        if (empty($user)) {
            throw new coding_exception('Invalid username '.$username);
        }
        return $user;
    }

    /**
     * @Given /^force password change is assigned to user "(?P<username_string>(?:[^"]|\\")*)"$/
     * @param string $username
     */
    public function force_password_change_is_assigned_to_user($username) {
        $user = $this->get_user_by_username($username);
        set_user_preference('auth_forcepasswordchange', 1, $user);
    }

    /**
     * Unassigns a role from a user for specific context.
     * Copied from enrol/locallib.php - note, could not include enrol/locallib.php into behat without it complaining.
     *
     * @param int $contextid;
     * @param int $userid
     * @param int $roleid
     * @return bool
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function unassign_role_from_user($contextid, $userid, $roleid) {
        global $DB;
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        $ras = $DB->get_records('role_assignments', array('contextid' => $contextid, 'userid' => $user->id, 'roleid' => $roleid));
        foreach ($ras as $ra) {
            if ($ra->component) {
                if (strpos($ra->component, 'enrol_') !== 0) {
                    continue;
                }
            }
            role_unassign($ra->roleid, $ra->userid, $ra->contextid, $ra->component, $ra->itemid);
        }
        return true;
    }

    /**
     * @codingStandardsIgnoreStart
     * @Given /^the editing teacher role is removed from course "(?P<shortname_string>(?:[^"]|\\")*)" for "(?P<username_string>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     * @param string $shortname
     * @param string $username
     */
    public function the_teacher_role_is_removed_for($shortname, $username) {
        global $DB;

        $service = theme_snap\services\course::service();
        $course = $service->coursebyshortname($shortname, 'id');

        $page = new moodle_page();
        $coursecontext = context_course::instance($course->id);
        $page->set_context($coursecontext);

        $user = $this->get_user_by_username($username);
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher'], 'id');
        $this->unassign_role_from_user($coursecontext->id, $user->id, $teacherrole->id);
    }

    /**
     * @Given /^I should see asset delete dialog$/
     */
    public function i_should_see_asset_delete_dialog() {
        $element = 'div.modal[data-region="modal-container"] .modal-dialog .modal-content';
        $text = 'Are you sure that you want to delete';
        $this->execute('behat_general::assert_element_contains_text', [$text, $element, 'css_element']);
    }

    /**
     * @Given /^I should not see asset delete dialog$/
     */
    public function i_should_not_see_asset_delete_dialog() {
        $element = 'div.modal[data-region="modal-container"] .modal-dialog .modal-content';
        try {
            $nodes = $this->find_all('css', $element);
        } catch (Exception $e) {
            return; // No dialog.
        }
        if (!empty($nodes)) {
            // Make sure dialog does not contain delete asset text.
            $text = 'Are you sure that you want to delete';
            $this->execute('behat_general::assert_element_not_contains_text', [$text, $element, 'css_element']);
        }
    }
    /**
     * @Given /^I should see section delete dialog$/
     */
    public function i_should_see_section_delete_dialog() {
        $element = 'div.modal[data-region="modal-container"] .modal-dialog .modal-content';
        $text = 'Are you absolutely sure you want to completely delete';
        $this->execute('behat_general::assert_element_contains_text', [$text, $element, 'css_element']);
    }

    /**
     * @Given /^I should not see section delete dialog$/
     */
    public function i_should_not_see_section_delete_dialog() {
        $element = 'div.modal[data-region="modal-container"] .modal-dialog .modal-content';
        try {
            $nodes = $this->find_all('css', $element);
        } catch (Exception $e) {
            return; // No dialog.
        }
        if (!empty($nodes)) {
            // Make sure dialog does not contain delete asset text.
            $text = 'Are you absolutely sure you want to completely delete';
            $this->execute('behat_general::assert_element_not_contains_text', [$text, $element, 'css_element']);
        }
    }

    /**
     * @Given /^I cancel dialog$/
     */
    public function i_cancel_dialog() {
        $element = 'div.modal[data-region="modal-container"] .modal-dialog .modal-content button[data-action="cancel"]';
        $this->execute('behat_general::i_click_on', [$element, 'css_element']);
    }

    /**
     * @param string $asset
     * @param boolean $can;
     */
    private function can_or_cant_see_asset_in_course_asset_search($asset, $can = true) {
        /** @var behat_forms $formcontext */
        $formcontext = behat_context_helper::get('behat_forms');

        /** @var behat_general $generalcontext */
        $generalcontext = behat_context_helper::get('behat_general');

        $formcontext->i_set_the_field_with_xpath_to('//input[@id=\'toc-search-input\']', $asset);
        if ($can) {
            $generalcontext->assert_element_contains_text($asset, '#toc-search-results', 'css_element');
        } else {
            $generalcontext->assert_element_not_contains_text($asset, '#toc-search-results', 'css_element');
        }
    }

    /**
     * @Given /^I can see "(?P<asset_string>(?:[^"]|\\")*)" in course asset search$/
     * @param string $asset
     */
    public function i_can_see_asset_in_course_asset_search($asset) {
        $this->can_or_cant_see_asset_in_course_asset_search($asset, true);
    }

    /**
     * @Given /^I cannot see "(?P<asset_string>(?:[^"]|\\")*)" in course asset search$/
     * @param string $asset
     */
    public function i_cannot_see_asset_in_course_asset_search($asset) {
        $this->can_or_cant_see_asset_in_course_asset_search($asset, false);
    }

    /**
     * @Given /^debugging is turned off$/
     */
    public function debugging_is_turned_off() {
        set_config('debug', '0');
        set_config('debugdisplay', '0');
    }

    /**
     * Marks an activity as complete.
     * @param string $activityname
     *
     * @Given /^I mark the activity "(?P<activityname_string>(?:[^"]|\\")*)" as complete$/
     */
    public function i_mark_as_complete($activityname) {
        $imgalt = 'Not completed: '.$activityname.'. Select to mark as complete.';
        $this->execute('behat_general::i_click_on', ['img.icon[alt="'.$imgalt.'"]', 'css_element']);
    }

    /**
     * Marks an activity as incomplete.
     * @param string $activityname
     *
     * @Given /^I mark the activity "(?P<activityname_string>(?:[^"]|\\")*)" as incomplete$/
     */
    public function i_mark_as_incomplete($activityname) {
        $imgalt = 'Completed: '.$activityname.'. Select to mark as not complete.';
        $this->execute('behat_general::i_click_on', ['img.icon[alt="'.$imgalt.'"]', 'css_element']);
    }

    /**
     * Core step copied from completion/tests/behat/behat_completion.php to fix bug MDL-57452
     * Checks if the activity with specified name is marked as complete.
     *
     * @codingStandardsIgnoreStart
     * @Given /^the "(?P<activityname_string>(?:[^"]|\\")*)" "(?P<activitytype_string>(?:[^"]|\\")*)" activity with "(manual|auto)" completion should be marked as complete \(core_fix\)$/
     * @codingStandardsIgnoreEnd
     */
    public function activity_marked_as_complete($activityname, $activitytype, $completiontype) {
        if ($completiontype == "manual") {
            $imgalttext = get_string("completion-alt-manual-y", 'core_completion', $activityname);
        } else {
            $imgalttext = get_string("completion-alt-auto-y", 'core_completion', $activityname);
        }
        $activityxpath = "//li[contains(concat(' ', @class, ' '), ' modtype_" . strtolower($activitytype) . " ')]";
        $activityxpath .= "[descendant::*[contains(text(), '" . $activityname . "')]]";

        $xpathtocheck = $activityxpath .
            "//img[contains(@alt, '$imgalttext')]|//input[@type='image'][contains(@alt, '$imgalttext')]";
        $this->execute("behat_general::should_exist",
            array($xpathtocheck, "xpath_element")
        );
    }

    /**
     * Checks if the activity with specified name is not marked as complete.
     * Core step copied from completion/tests/behat/behat_completion.php to fix bug MDL-57452
     * @codingStandardsIgnoreStart
     * @Given /^the "(?P<activityname_string>(?:[^"]|\\")*)" "(?P<activitytype_string>(?:[^"]|\\")*)" activity with "(manual|auto)" completion should be marked as not complete \(core_fix\)$/
     * @codingStandardsIgnoreEnd
     */
    public function activity_marked_as_not_complete($activityname, $activitytype, $completiontype) {
        if ($completiontype == "manual") {
            $imgalttext = get_string("completion-alt-manual-n", 'core_completion', $activityname);
        } else {
            $imgalttext = get_string("completion-alt-auto-n", 'core_completion', $activityname);
        }
        $activityxpath = "//li[contains(concat(' ', @class, ' '), ' modtype_" . strtolower($activitytype) . " ')]";
        $activityxpath .= "[descendant::*[contains(text(), '" . $activityname . "')]]";

        $xpathtocheck = $activityxpath .
            "//img[contains(@alt, '$imgalttext')]|//input[@type='image'][contains(@alt, '$imgalttext')]";
        $this->execute("behat_general::should_exist",
            array($xpathtocheck, "xpath_element")
        );
    }

    /**
     * @Given /^I should not see an error dialog$/
     */
    public function i_should_not_see_an_error_dialog() {
        $element = '.moodle-dialogue-confirm .confirmation-message';
        try {
            $this->find_all('css', $element);
        } catch (Exception $e) {
            return; // No dialog.
        }
        throw new ExpectationException('An error dialog has been displayed', $this->getSession());
    }

    /**
     * @Given /^I have been redirected to the site policy page$/
     */
    public function i_am_redirected_to_site_policy_page() {
        $currenturl = $this->getSession()->getCurrentUrl();
        if (strpos($currenturl, 'user/policy.php') === false) {
            $msg = 'User has not been redirected to site policy page';
            throw new ExpectationException($msg, $this->getSession());
        }
    }

    /**
     * @Given /^I am currently on the default site home page$/
     */
    public function i_am_currently_on_the_site_home_page() {
        global $CFG;

        $currenturl = $this->getSession()->getCurrentUrl();
        $currenturl = str_replace($CFG->wwwroot, '', $currenturl);
        $currenturl = str_replace('index.php', '', $currenturl);

        $expectedurl = $CFG->defaulthomepage == 0 ? '/' : '/my/';

        if ($currenturl !== $expectedurl) {
            $msg = "Expected user to be on default site home page - currenturl is $currenturl and expected url ";
            $msg .= "is $expectedurl";

            throw new ExpectationException($msg, $this->getSession());
        }
    }

    /**
     * @Given /^I highlight section (?P<section_int>(?:\d+))$/
     * @param int $section
     */
    public function i_highlight_section($section) {
        $this->execute('behat_general::i_click_on', ['#section-'.$section.' .extra-actions-menu', 'css_element']);
        $this->execute('behat_general::i_click_on', ['#section-'.$section.' .extra-actions-menu .snap-highlight', 'css_element']);
    }

    /**
     * @codingStandardsIgnoreStart
     * @Given /^deadline for assignment "(?P<name_string>(?:[^"]|\\")*)" in course "(?P<shortname_string>(?:[^"]|\\")*)" is extended to "(?P<date_string>(?:[^"]|\\")*)" for "(?P<uname_string>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     * @param string $shortname
     * @param string $format
     * #param string $username
     */
    public function deadline_is_extended($assignname, $shortname, $extension, $username) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/vendor/autoload.php');
        require_once($CFG->libdir.'/phpunit/classes/base_testcase.php');
        require_once($CFG->libdir.'/phpunit/classes/advanced_testcase.php');
        require_once($CFG->dirroot.'/mod/assign/tests/base_test.php');

        $service = theme_snap\services\course::service();
        $course = $service->coursebyshortname($shortname, 'id');

        $assign = $DB->get_record('assign', ['course' => $course->id, 'name' => $assignname], 'id');
        if (!$assign) {
            $msg = 'Failed to get assignment '.$assignname. ' for course id '.$course->id;
            throw new ExpectationException($msg, $this->getSession());
        }

        $user = $this->get_user_by_username($username);
        if (!$user) {
            throw new ExpectationException('Couldn\'t find user with username "'.$username.'"', $this->getSession());
        }

        list ($course, $cm) = get_course_and_cm_from_instance($assign->id, 'assign');
        $cm = cm_info::create($cm);

        // Create assignment object and update extension date for user and assignment.
        $assign = new testable_assign($cm->context, $cm, $course);
        $assign->testable_save_user_extension($user->id, $extension);
    }

    /**
     * @param string $name
     * @return string
     */
    private function meta_assign_xpath($name) {
        $xpath = "//p[contains(@class, 'instancename')][contains(text(), '$name')]/ancestor::".
            "div[contains(@class, 'activityinstance')]"."//div[contains(@class, 'snap-completion-meta')]";
        return $xpath;
    }

    /**
     * Get general xpath for course assignment meta data.
     * @param string $name
     * @param string $submitted
     * @return string
     */
    private function meta_assign_submitted_xpath($name, $submitted = 'Not Submitted') {
        $xpath = $this->meta_assign_xpath($name)."/a[contains(text(), '$submitted')]";
        return $xpath;
    }

    /**
     * @Given /^assignment entitled "(?P<assign_string>(?:[^"]|\\")*)" shows as not submitted in metadata$/
     * @param string $name
     */
    public function meta_assign_is_not_submitted($name) {
        $xpath = $this->meta_assign_submitted_xpath($name);
        $this->ensure_element_is_visible($xpath, 'xpath_element');
    }

    /**
     * @Given /^assignment entitled "(?P<assign_string>(?:[^"]|\\")*)" shows as submitted in metadata$/
     * @param string $name
     */
    public function meta_assign_is_submitted($name) {
        $xpath = $this->meta_assign_submitted_xpath($name, 'Submitted');
        $this->ensure_element_is_visible($xpath, 'xpath_element');
    }

    /**
     * @Given /^assignment entitled "(?P<assign_string>(?:[^"]|\\")*)" is overdue in metadata$/
     * @param string $name
     */
    public function meta_assign_overdue($name) {
        $xpath = $this->meta_assign_xpath($name);
        $xpath .= "/a[contains(@class, 'snap-due-date')][contains(@class, 'tag-danger')]";
        $this->ensure_element_is_visible($xpath, 'xpath_element');
    }

    /**
     * @Given /^assignment entitled "(?P<assign_string>(?:[^"]|\\")*)" is not overdue in metadata$/
     * @param string $name
     */
    public function meta_assign_not_overdue($name) {
        $xpath = $this->meta_assign_xpath($name);
        $xpath .= "/a[contains(@class, 'snap-due-date')][contains(@class, 'tag-warning')]";
        $this->ensure_element_is_visible($xpath, 'xpath_element');
        $xpath = $this->meta_assign_xpath($name);
        $xpath .= "/a[contains(@class, 'snap-due-date')][contains(@class, 'tag-danger')]";
        $this->ensure_element_does_not_exist($xpath, 'xpath_element');
    }

    /**
     * @Given /^assignment entitled "(?P<assign_string>(?:[^"]|\\")*)" has feedback metadata$/
     * @param string $name
     */
    public function meta_assign_has_feedback($name) {
        $xpath = $this->meta_assign_xpath($name);
        $xpath .= "/a[contains(text(), 'Feedback available')]";
        $this->ensure_element_is_visible($xpath, 'xpath_element');
    }

    /**
     * @Given /^assignment entitled "(?P<assign_string>(?:[^"]|\\")*)" does not have feedback metadata$/
     * @param string $name
     */
    public function meta_assign_not_has_feedback($name) {
        $xpath = $this->meta_assign_xpath($name);
        $xpath .= "/a[contains(text(), 'Feedback available')]";
        $this->ensure_element_does_not_exist($xpath, 'xpath_element');
    }

    /**
     * Opens index login page.
     *
     * @Given /^I am on login page$/
     */
    public function i_am_on_login_page() {
        $this->getSession()->visit($this->locate_path('/login/index.php'));
    }

    /**
     * Opens forgot password page.
     *
     * @Given /^I am on forgot password page$/
     */
    public function i_am_on_forgot_password_page() {
        $this->getSession()->visit($this->locate_path('/login/forgot_password.php'));
    }

    /**
     * Opens sign up page.
     *
     * @Given /^I am on sign up page$/
     */
    public function i_am_on_signup_page() {
        $this->getSession()->visit($this->locate_path('/login/signup.php'));
    }

    /**
     * @Given /^I check element "(?P<element_string>(?:[^"]|\\")*)" has class "(?P<class_string>(?:[^"]|\\")*)"$/
     * @param string $element
     * @param string $class
     * @throws Exception
     */
    public function i_check_element_has_class($element, $class) {
        $session = $this->getSession();
        $elementhasclass = $session->getDriver()->evaluateScript("$('".$element."').hasClass('".$class."');");
        if (!$elementhasclass) {
            throw new Exception("Class ".$class." was not found in element ".$element.".");
        }
    }
    /**
     * Purge snap caches.
     *
     * @Given /^I purge snap caches$/
     */
    public function i_purge_snap_caches() {
        theme_reset_all_caches();
    }

    /**
     * Add a discussion on a forum.
     *
     * @Given /^I post to the discussion:$/
     * @param TableNode $table
     */
    public function i_post_this_to_the_discussion(TableNode $table) {
        // Fill form and post.
        $this->execute('behat_forms::i_set_the_following_fields_to_these_values', $table);
        $this->execute('behat_forms::press_button', get_string('posttoforum', 'forum'));
        $this->execute('behat_general::i_wait_to_be_redirected');
    }

    /**
     * @param string $activityname - assign name
     * @param string $activity - activity type
     * @Given /^Activity "(?P<activity>(?:[^"]|\\")*)" "(?P<activityname>(?:[^"]|\\")*)" is deleted$/
     * @return array
     */
    public function activity_is_deleted($activity, $activityname) {
        global $DB;
        $activityid = $DB->get_field($activity, 'id', ['name' => $activityname], MUST_EXIST);
        $cm = get_coursemodule_from_instance($activity, $activityid, 0, false, MUST_EXIST);
        course_delete_module($cm->id, true);
    }

    /**
     * Opens the course homepage.
     *
     * @Given /^I am on activity "(?P<activity>(?:[^"]|\\")*)" "(?P<activityname>(?:[^"]|\\")*)" page$/
     * @throws coding_exception
     * @param string $coursefullname The full name of the course.
     * @return void
     */
    public function i_am_on_activity_page($activity, $activityname) {
        global $DB;
        $activityid = $DB->get_field($activity, 'id', ['name' => $activityname], MUST_EXIST);
        $cm = get_coursemodule_from_instance($activity, $activityid, 0, false, MUST_EXIST);
        $url = new moodle_url('/mod/' . $activity . '/view.php', ['id' => $cm->id]);
        $this->getSession()->visit($this->locate_path($url->out_as_local_url(false)));
    }

    /**
     * Checks if a css element have a full width.
     *
     * @Given /^CSS element "(?P<element_string>(?:[^"]|\\")*)" is full width$/
     * @param string $element css element to be checked
     * @throws Exception
     */
    public function css_element_is_full_width($element) {
        $session = $this->getSession();
        $elementwidth = $session->getDriver()->evaluateScript(
            'window.getComputedStyle(document.querySelectorAll("'
            . $element . '")[0], null).getPropertyValue("width");');
        $windowwidth = $session->getDriver()->evaluateScript('window.screen.width;');

        $elementwidth = str_replace("px", "", $elementwidth);

        if ($elementwidth < $windowwidth) {
            throw new Exception("Element " . $element . " is not full width. Expected " .
                $windowwidth . ", actual " . $elementwidth);
        }
    }

    /**
     * Generic field setter.
     *
     * Internal API method, a generic *I set "VALUE" to "FIELD" field*
     * could be created based on it.
     *
     * @param string $fieldlocator The pointer to the field, it will depend on the field type.
     * @param string $value
     * @return void
     */
    protected function set_field_value($fieldlocator, $value) {

        // We delegate to behat_form_field class, it will
        // guess the type properly as it is a select tag.
        $field = behat_field_manager::get_form_field_from_label($fieldlocator, $this);
        $field->set_value($value);
    }

    /**
     * Sets the specified multi-line value to the field
     *
     * @Given /^I set the text field  "(?P<field_string>(?:[^"]|\\")*)" with multi-line text:/
     */
    public function i_set_the_text_field_with_multi_line_text($field, \Behat\Gherkin\Node\PyStringNode $value) {
        $this->set_field_value($field, $value);
    }

    /**
     * Opens My Account default page.
     *
     * @Given /^I am on my account default page$/
     */
    public function i_am_on_myaccount_default_page() {
        $this->getSession()->visit($this->locate_path('/local/myaccount/view.php?controller=default&action=view'));
    }

    /**
     * Scroll page to the bottom.
     *
     * @When I scroll to the bottom
     *
     */
    public function i_scroll_to_bottom() {
        $function = <<<JS
          (function(){
              window.scrollTo(0,document.body.scrollHeight);
              return 1;
          })()
JS;
        try {
            $this->getSession()->wait(5000, $function);
        } catch (Exception $e) {
            throw new \Exception("scrollIntoBottom failed");
        }
    }

    /**
     * Scroll element into view and align bottom of element with the bottom of the visible area.
     *
     * @When I scroll to the base of id :id
     *
     */
    public function i_scroll_into_view_base($id) {
        $function = <<<JS
          (function(){
              var elem = document.getElementById("$id");
              elem.scrollIntoView(false);
              return 1;
          })()
JS;
        try {
            $this->getSession()->wait(5000, $function);
        } catch (Exception $e) {
            throw new \Exception("scrollIntoView failed");
        }
    }

    /**
     * Document should open in a new tab.
     *
     * @When /^The document should open in a new tab$/
     */
    public function document_should_open_in_new_tab() {
        $session     = $this->getSession();
        $windownames = $session->getWindowNames();
        // Need to wait if for some reason the tab have some delay being opened.
        $ttw     = 40;
        while ((count($session->getWindowNames()) < 2 && $ttw > 0) == true) {
            $session->wait(1000);
            $ttw--;
        }
        if (count($windownames) < 2) {
            throw new \ErrorException("Expected to see at least 2 windows opened");
        }

        // Switch to the new window.
        $session->switchToWindow($windownames[1]);
    }

    /**
     * @Given /^I open the user menu$/
     */
    public function i_open_the_user_menu() {
        $this->execute('behat_general::i_click_on', ['.usermenu', 'css_element']);
    }

    /**
     * Normalise the fixture file path relative to the dirroot.
     *
     * @param string $filepath
     * @return string
     */
    protected function normalise_fixture_filepath(string $filepath): string {
        global $CFG;

        $filepath = str_replace('/', DIRECTORY_SEPARATOR, $filepath);
        if (!is_readable($filepath)) {
            $filepath = $CFG->dirroot . DIRECTORY_SEPARATOR . $filepath;
            if (!is_readable($filepath)) {
                throw new ExpectationException('The file to be uploaded does not exist.', $this->getSession());
            }
        }

        return $filepath;
    }

    /**
     * Upload a file in the file picker using the browse repository button.
     *
     * @Given /^I upload "(?P<filepath_string>(?:[^"]|\\")*)" to the file picker for Snap/
     */
    public function i_upload_a_file_in_the_filepicker(string $filepath): void {

        $filepicker = $this->find('dialogue', get_string('filepicker', 'core_repository'));

        $this->execute('behat_general::i_click_on_in_the', [
            get_string('pluginname', 'repository_upload'), 'link',
            $filepicker, 'NodeElement',
        ]);

        $reporegion = $filepicker->find('css', '.fp-repo-items');
        $fileinput = $this->find('field', get_string('attachment', 'core_repository'), false, $reporegion);

        $filepath = $this->normalise_fixture_filepath($filepath);

        $fileinput->attachFile($filepath);
        $this->execute('behat_general::i_click_on_in_the', [
            get_string('upload', 'repository'), 'button',
            $reporegion, 'NodeElement',
        ]);
    }

    /**
     * @codingStandardsIgnoreStart
     * @Given /^I purge all caches in Snap$/
     * @codingStandardsIgnoreEnd
     */
    public function i_purge_all_caches_in_snap() {
        $generalcontext = behat_context_helper::get('behat_general');
        $this->execute('behat_general::i_click_on', ['#admin-menu-trigger', 'css_element']);
        $this->execute("behat_navigation::i_expand_node", 'Site administration');
        $this->execute("behat_navigation::i_expand_node", 'Development');
        $this->execute('behat_general::click_link', ['Purge caches']);
        $this->execute('behat_general::click_link', ['Purge all caches']);
    }

    /**
     * @Given /^I switch edit mode in Snap$/
     */
    public function switch_edit_mode_in_snap() {
        $this->execute('behat_general::i_click_on', ['.editmode-switch-form', 'css_element']);
    }

    /**
     * Disable completion tracking at site level.
     *
     * @Given /^I disable site completion tracking$/
     */
    public function disable_site_completion_tracking(): void {
        set_config('enablecompletion', 0);
    }

    /**
     * Navigate through the Snap administration menu using a path string.
     *
     * @When I go to :path in snap administration
     * @param string $path Path in the format "Parent > Child > Grandchild"
     */
    public function i_go_to_in_snap_administration($path) {
        // First click on the admin menu trigger to open the menu
        $this->execute('behat_general::i_click_on', ['#admin-menu-trigger', 'css_element']);
        $nodes = array_map('trim', explode('>', $path));
        
        foreach ($nodes as $index => $node) {
            // Find the element by its text content
            $this->getSession()->wait(500);
            $xpath = "//span[contains(text(), '$node')]";
            $element = $this->find('xpath', $xpath);
            
            if (!$element) {
                // If it's the last node, try finding it as a link instead
                if ($index === count($nodes) - 1) {
                    $xpath = "//a[contains(text(), '$node')]";
                    $element = $this->find('xpath', $xpath);
                }
                
                if (!$element) {
                    throw new \Exception("Could not find administration node with text: $node");
                }
            }
            // Scroll the element into view
            $this->execute_js_on_node($element, '{{ELEMENT}}.scrollIntoView({ block: "center", inline: "center" })');
            $element->click();
            $this->getSession()->wait(300);
        }
    }
}
