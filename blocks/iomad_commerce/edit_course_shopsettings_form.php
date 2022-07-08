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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user create a course for a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib/course_selectors.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');
require_once('lib.php');

require_commerce_enabled();

class course_edit_form extends moodleform {

    protected $isadding;
    protected $shopsettingsid = 0;
    protected $context = null;
    protected $course = null;
    protected $currency = '';
    protected $priceblocks = null;

    public function __construct($actionurl, $isadding, $shopsettingsid, $course, $priceblocks, $editoroptions) {
        global $CFG;

        $this->isadding = $isadding;
        $this->shopsettingsid = $shopsettingsid;
        $this->course = $course;
        $this->priceblocks = $priceblocks;
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);
        $this->editoroptions = $editoroptions;

        if (!empty($CFG->commerce_admin_currency)) {
            $this->currency = get_string($CFG->commerce_admin_currency, 'core_currencies');
        } else {
            $this->currency = get_string('GBP', 'core_currencies');
        }
        if ($isadding) {
            $options = array('context' => $this->context,
                             'multiselect' => false,
                             'selectedid' => $shopsettingsid,
                             'searchanywhere' => true,
                             'file' => '/blocks/iomad_commerce/lib/course_selectors.php');
            $this->currentcourses = new nonshopcourse_selector('currentcourses', $options);
            $this->currentcourses->set_rows(1);
        }

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'shopsettingsid', $this->shopsettingsid);
        $mform->setType('shopsettingsid', PARAM_INT);
        $mform->addElement('hidden', 'currency', $this->currency);
        $mform->setType('currency', PARAM_TEXT);
        $mform->addElement('hidden', 'deletedBlockPrices', 0);
        $mform->setType('deletedBlockPrices', PARAM_INT);

        // Adding the elements in the definition_after_data function rather than in the definition function
        // so that when the currentcourses or potentialcourses get changed in the process function, the
        // changes get displayed, rather than the lists as they are before processing.

        if (!$this->isadding || count($this->currentcourses->find_courses(''))) {
            if ($this->isadding) {
                $mform->addElement('html', "<div class='fitem'><div class='fitemtitle'>" .
                                            get_string('selectcoursetoadd', 'block_iomad_commerce') .
                                            "</div><div class='felement'>");
                $mform->addElement('html', $this->currentcourses->display(true));
                $mform->addElement('html', "</div></div>");
            } else {
                $mform->addElement('static', 'coursefullname', get_string('Course', 'block_iomad_commerce'),
                                              $this->course->fullname);
            }

            $mform->addElement('selectyesno', 'enabled', get_string('course_shop_enabled', 'block_iomad_commerce'));
            $mform->addHelpButton('enabled', 'course_shop_enabled', 'block_iomad_commerce');

            $mform->addElement('editor', 'short_summary_editor', get_string('course_short_summary', 'block_iomad_commerce'),
                                          null, $this->editoroptions);
            $mform->setType('short_summary_editor', PARAM_RAW);
            $mform->addRule('short_summary_editor', get_string('missingshortsummary', 'block_iomad_commerce'),
                                                    'required', null, 'client');

            $mform->addElement('editor', 'summary_editor', get_string('course_long_description', 'block_iomad_commerce'),
                                          null, $this->editoroptions);
            $mform->setType('summary_editor', PARAM_RAW);

            $mform->addElement('header', 'header', get_string('single_purchase', 'block_iomad_commerce'));

            $mform->addElement('selectyesno', 'allow_single_purchase', get_string('allow_single_purchase', 'block_iomad_commerce'));
            $mform->addHelpButton('allow_single_purchase', 'allow_single_purchase', 'block_iomad_commerce');

            $mform->addElement('text', 'single_purchase_price',
                                        get_string('single_purchase_price', 'block_iomad_commerce') . ' (' . $this->currency . ')');
            $mform->addRule('single_purchase_price',
                             get_string('decimalnumberonly', 'block_iomad_commerce'), 'numeric');
            $mform->setType('single_purchase_price', PARAM_TEXT);
            $mform->addHelpButton('single_purchase_price', 'single_purchase_price', 'block_iomad_commerce');

            $mform->addElement('text', 'single_purchase_validlength',
                                        get_string('single_purchase_validlength', 'block_iomad_commerce'));
            $mform->setType('single_purchase_validlength', PARAM_INT);
            $mform->addHelpButton('single_purchase_validlength', 'single_purchase_validlength', 'block_iomad_commerce');

            $mform->addElement('text', 'single_purchase_shelflife',
                                        get_string('single_purchase_shelflife', 'block_iomad_commerce'));
            $mform->setType('single_purchase_shelflife', PARAM_INT);
            $mform->addHelpButton('single_purchase_shelflife', 'single_purchase_shelflife', 'block_iomad_commerce');

            /****** license blocks *********/
            $mform->addElement('header', 'header', get_string('licenseblocks', 'block_iomad_commerce'));

            $mform->addElement('selectyesno', 'allow_license_blocks', get_string('allow_license_blocks', 'block_iomad_commerce'));
            $mform->addHelpButton('allow_license_blocks', 'allow_license_blocks', 'block_iomad_commerce');

            $table = new html_table();
            $table->id = "licenseblockstable";
            $table->head = array (get_string('licenseblock_start', 'block_iomad_commerce'),
                                  get_string('licenseblock_price', 'block_iomad_commerce') . " (" . $this->currency . ")",
                                  get_string('licenseblock_validlength', 'block_iomad_commerce'),
                                  get_string('licenseblock_shelflife', 'block_iomad_commerce'),
                                  "");
            $table->align = array ("left", "left", "left", "left");
            $table->width = "95%";

            $mform->addElement('static', 'priceblockerrors');

            $strdelete = get_string('delete');
            $mform->addElement('html', '<span id="deleteText" style="display:none">' . $strdelete . '</span>');

            if ($this->priceblocks) {
                $i = 1;
                foreach ($this->priceblocks as $priceblock) {
                    $table->data[] = array('<input name="block_start_'.$i.'" type="text" value="' .
                                           $priceblock->price_bracket_start .
                                           '" size="5" />',
                                           '<input name="block_price_'.$i.'" type="text" value="' .
                                           $priceblock->price .
                                           '" size="5" />',
                                           '<input name="block_valid_'.$i.'" type="text" value="' .
                                           $priceblock->validlength .
                                           '"  size="5" />',
                                           '<input name="block_shelflife_'.$i.'" type="text" value="' .
                                           $priceblock->shelflife .
                                           '"  size="5" />',
                                           '<a href="#" onclick="iomad.removeLicenseBlock(this)">' .
                                           $strdelete .
                                           '</a>');
                    $i++;
                }

                $mform->addElement('hidden', 'blockPrices', count($this->priceblocks));
                $mform->setType('blockPrices', PARAM_INT);

            } else {
                $table->data[] = array('<input name="block_start_1" type="text" value="1" size="5" />',
                                       '<input name="block_price_1" type="text" value="" size="5" />',
                                       '<input name="block_valid_1" type="text" value="" size="5" />',
                                       '<input name="block_shelflife_1" type="text" value="" size="5" />',
                                       '<a href="#" onclick="iomad.removeLicenseBlock(this)">' . $strdelete . '</a>');
                $mform->addElement('hidden', 'blockPrices', 1);
                $mform->setType('blockPrices', PARAM_INT);
            }

            if (!empty($table)) {
                    $mform->addElement('html', "<div class='fitem'><div class='fitemtitle'>" .
                                                get_string('licenseblocks', 'block_iomad_commerce') .
                                                "</div><div class='felement'>");
                    $mform->addElement('html', html_writer::table($table));
                    $mform->addElement('html', "<input type='button' onclick='iomad.addLicenseBlock(this)' value='" .
                                                get_string('add_more_license_blocks', 'block_iomad_commerce') . "' />");
                    $mform->addElement('html', "</div></div>");

                    global $PAGE;
                    $PAGE->requires->js('/blocks/iomad_commerce/module.js');
            }
            /******** end license blocks *********/

            /******** tags **************/
            $mform->addElement('header', 'header', get_string('categorization', 'block_iomad_commerce'));

            $mform->addElement('textarea', 'tags', get_string('tags', 'block_iomad_commerce'), array('rows' => 5, 'cols' => 60));
            $mform->addHelpButton('tags', 'tags', 'block_iomad_commerce');
            $mform->setType('tags', PARAM_NOTAGS);

            $vars = get_shop_tags();
            $options = "<option value=''>" . get_string('select_tag', 'block_iomad_commerce') . "</option>";
            foreach ($vars as $i) {
                $options .= "<option value='{$i}'>$i</option>";
            }

            $select = "<select class='tags' onchange='iomad.onSelectTag(this)'>$options</select>";
            $html = "<div class='fitem'><div class='fitemtitle'></div><div class='felement'>$select</div></div>";

            $mform->addElement('html', $html);

            global $PAGE;
            $PAGE->requires->js('/blocks/iomad_commerce/module.js');

            /******** end tags **********/

            $submitlabel = null; // Default.
            if ($this->isadding) {
                $submitlabel = get_string('add_course_to_shop', 'block_iomad_commerce');
                $mform->addElement('hidden', 'createnew', 1);
                $mform->setType('createnew', PARAM_INT);
            }

            $this->add_action_buttons(true, $submitlabel);
        } else {
            $mform->addElement('html', get_string('nocoursesnotontheshop', 'block_iomad_commerce'));
        }
    }

    public function get_data() {
        $data = parent::get_data();

        if ($data !== null && isset($this->currentcourses)) {
            $data->selectedcourse = $this->currentcourses->get_selected_course();
            $data->courseid = $data->selectedcourse->id;
        }

        if ($data) {
            if ($data->short_summary_editor) {
                $data->short_description = $data->short_summary_editor["text"];
            }
            if ($data->summary_editor) {
                $data->long_description = $data->summary_editor["text"];
            }
        }

        return $data;
    }

    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);

        if ($data['allow_single_purchase']) {
            if (floatval($data['single_purchase_price']) <= 0) {
                $errors['single_purchase_price'] = get_string('error_singlepurchaseprice', 'block_iomad_commerce');
            }
            if (intval($data['single_purchase_validlength']) <= 0) {
                $errors['single_purchase_validlength'] = get_string('error_singlepurchasevalidlength', 'block_iomad_commerce');
            }
        }

        if ($data['allow_license_blocks']) {
            // Figure out if some of the block price data is invalid.
            $bracketstartinvalid = false;
            $priceinvalid = false;
            $validlengthinvalid = false;
            $shelflifeinvalid = false;

            $brackets = array();

            foreach ($this->priceblocks as $priceblock) {
                $bracketstart = intval($priceblock->price_bracket_start);
                $brackets[] = $bracketstart;

                $bracketstartinvalid = $bracketstartinvalid || ($bracketstart < 1);
                $priceinvalid = $priceinvalid || (floatval($priceblock->price) <= 0);
                $validlengthinvalid = $validlengthinvalid || (intval($priceblock->validlength) < 1);
                $shelflifeinvalid = $shelflifeinvalid || (intval($priceblock->shelflife) < 1);
            }

            $priceblockerrors = array();
            if (count($brackets) != count(array_unique($brackets))) {
                $priceblockerrors[] = get_string('error_duplicateblockstarts', 'block_iomad_commerce');;
            }
            if ($bracketstartinvalid) {
                $priceblockerrors[] = get_string('error_invalidblockstarts', 'block_iomad_commerce');;
            }
            if ($priceinvalid) {
                $priceblockerrors[] = get_string('error_invalidblockprices', 'block_iomad_commerce');;
            }
            if ($validlengthinvalid) {
                $priceblockerrors[] = get_string('error_invalidblockvalidlengths', 'block_iomad_commerce');;
            }
            if ($shelflifeinvalid) {
                $priceblockerrors[] = get_string('error_invalidblockshelflives', 'block_iomad_commerce');;
            }

            if (count($priceblockerrors)) {
                $errors['priceblockerrors'] = '<ul><li>' . implode('</li><li>', $priceblockerrors) . '</li></ul>';
            }
        }

        return $errors;
    }

}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$shopsettingsid = optional_param('shopsettingsid', 0, PARAM_INTEGER);
$new = optional_param('createnew', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
$PAGE->set_context($context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/blocks/iomad_commerce/courselist.php', $urlparams);

$priceblocks = array();

if (!$new) {
    $isadding = false;
    $shopsettings = $DB->get_record('course_shopsettings', array('id' => $shopsettingsid), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $shopsettings->courseid), 'id, fullname, shortname', MUST_EXIST);
    $course->fullname = $course->fullname." ($course->shortname))";

    if (!$_POST || !array_key_exists('block_start_1', $_POST)) {
        $priceblocks = $DB->get_records('course_shopblockprice',
                                         array('courseid' => $shopsettings->courseid), 'price_bracket_start');
    }

    $shopsettings->tags = get_course_tags($course->id);
    $shopsettings->short_summary_editor = array('text' => $shopsettings->short_description);
    $shopsettings->summary_editor = array('text' => $shopsettings->long_description);

    iomad::require_capability('block/iomad_commerce:edit_course', $context);
} else {
    $isadding = true;
    $shopsettingsid = 0;
    $shopsettings = new stdClass;
    $course = null;
    $priceblocks = null;

    iomad::require_capability('block/iomad_commerce:add_course', $context);


}

if (array_key_exists('blockPrices', $_POST)) {
    $priceblocks = array();

    $nblocks = intval($_POST['blockPrices']);
    for ($i = 0; $i < $nblocks; $i++) {
        $k = $i + 1;
        $price_bracket_start_key = "block_start_$k";
        if (array_key_exists($price_bracket_start_key, $_POST)) {
            $price_key = "block_price_$k";
            $validlength_key = "block_valid_$k";
            $shelflife_key = "block_shelflife_$k";

            $priceblocks[] = (object) array(
                'price_bracket_start' => $_POST[$price_bracket_start_key],
                'price' => $_POST[$price_key],
                'validlength' => $_POST[$validlength_key],
                'shelflife' => $_POST[$shelflife_key]
            );
        }
    }
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('course_list_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/courselist.php');

$title = 'edit_course_shopsettings';
if ($isadding) {
    $title = 'addnewcourse';
}

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string($title, 'block_iomad_commerce'));

/* next line copied from /course/edit.php */
$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $CFG->maxbytes, 'trusttext' => false, 'noclean' => true);

$mform = new course_edit_form(new moodle_url('/blocks/iomad_commerce/edit_course_shopsettings_form.php'), $isadding, $shopsettingsid, $course, $priceblocks, $editoroptions);
$mform->set_data($shopsettings);

if ($mform->is_cancelled()) {
    redirect($companylist);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;

    $transaction = $DB->start_delegated_transaction();

    if ($isadding) {
        $data->single_purchase_currency = $CFG->commerce_admin_currency;
        $shopsettingsid = $DB->insert_record('course_shopsettings', $data);
    } else {
        $data->id = $shopsettingsid;
        $data->single_purchase_currency = $CFG->commerce_admin_currency;
        $DB->update_record('course_shopsettings', $data);
    }

    if (!isset($data->courseid) && isset($course->id)) {
        $data->courseid = $course->id;
    }

    $DB->delete_records('course_shopblockprice', array('courseid' => $data->courseid));
    foreach ($priceblocks as $priceblock) {
        $priceblock->courseid = $data->courseid;
        $priceblock->currency = $CFG->commerce_admin_currency;

        $DB->insert_record('course_shopblockprice', $priceblock, false, false);
    }

    // Delete course_shoptag records.
    $DB->delete_records('course_shoptag', array('courseid' => $data->courseid));

    // Find shoptag ids.
    $tags = preg_split('/\s*,\s*/', $data->tags);
    $newcourseshoptagrecord = new stdClass;
    $newcourseshoptagrecord->courseid = $data->courseid;
    foreach ($tags as $tag) {
        if (!$st = $DB->get_record('shoptag', array('tag' => $tag))) {
            $st = new stdClass;
            $st->id = $DB->insert_record('shoptag', (object) array('tag' => $tag), true);
        }

        $newcourseshoptagrecord->shoptagid = $st->id;
        $DB->insert_record('course_shoptag', $newcourseshoptagrecord);
    }

    $transaction->allow_commit();

    redirect($companylist);

} else {

    echo $OUTPUT->header();

    $mform->display();

    echo $OUTPUT->footer();
}

