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

namespace block_iomad_commerce\forms;

use \moodleform;
use \context_system;
use \block_iomad_commerce\helper;
use lang_string;

class product_edit_form extends moodleform {

    protected $isadding;
    protected $shopsettingsid = 0;
    protected $context = null;
    protected $courses = [];
    protected $currency = '';
    protected $priceblocks = null;

    public function __construct($actionurl, $isadding, $shopsettingsid, $courses = [], $priceblocks = null, $editoroptions = []) {
        global $CFG;

        $this->isadding = $isadding;
        $this->shopsettingsid = $shopsettingsid;
        $this->courses = $courses;
        $this->priceblocks = $priceblocks;
        $this->context = context_system::instance();
        $this->editoroptions = $editoroptions;

        // Get the supported currencies.
        $codes = \core_payment\helper::get_supported_currencies();

        $currencies = [];
        foreach ($codes as $c) {
            $currencies[$c] = new lang_string($c, 'core_currencies');
        }

        uasort($currencies, function($a, $b) {
            return strcmp($a, $b);
        });
        $this->currencies = $currencies;

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $DB;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'shopsettingsid', $this->shopsettingsid);
        $mform->setType('shopsettingsid', PARAM_INT);
        $mform->addElement('hidden', 'companyid');
        $mform->setType('companyid', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'default');
        $mform->setType('default', PARAM_BOOL);
        $mform->addElement('hidden', 'deletedBlockPrices', 0);
        $mform->setType('deletedBlockPrices', PARAM_INT);

        // Adding the elements in the definition_after_data function rather than in the definition function
        // so that when the currentcourses or potentialcourses get changed in the process function, the
        // changes get displayed, rather than the lists as they are before processing.

        if (!empty($this->courses)) {
            $mform->addElement('text', 'name', get_string('name'));
            $mform->setType('name', PARAM_NOTAGS);

            $mform->addElement('selectyesno', 'enabled', get_string('course_shop_enabled', 'block_iomad_commerce'));
            $mform->addHelpButton('enabled', 'course_shop_enabled', 'block_iomad_commerce');

            $mform->addElement('autocomplete', 'itemcourses', get_string('courses'), $this->courses, ['multiple' => true]);

            $mform->addElement('editor', 'short_summary_editor', get_string('course_short_summary', 'block_iomad_commerce'),
                                          null, $this->editoroptions);
            $mform->setType('short_summary_editor', PARAM_RAW);
            $mform->addRule('short_summary_editor', get_string('missingshortsummary', 'block_iomad_commerce'),
                                                    'required', null, 'client');

            $mform->addElement('editor', 'summary_editor', get_string('course_long_description', 'block_iomad_commerce'),
                                          null, $this->editoroptions);
            $mform->setType('summary_editor', PARAM_RAW);

            $mform->addElement('selectyesno', 'program', get_string('licenseprogram', 'block_iomad_company_admin'));
            $mform->addHelpButton('program', 'licenseprogram', 'block_iomad_company_admin');

            $mform->addElement('selectyesno', 'instant', get_string('licenseinstant', 'block_iomad_company_admin'));
            $mform->addHelpButton('instant', 'licenseinstant', 'block_iomad_company_admin');

            $mform->addElement('duration', 'single_purchase_validlength',
                                        get_string('single_purchase_validlength', 'block_iomad_commerce'),
                                        ['defaultunit' => DAYSECS]);
            $mform->addHelpButton('single_purchase_validlength', 'single_purchase_validlength', 'block_iomad_commerce');

            $mform->addElement('duration', 'single_purchase_shelflife',
                                        get_string('single_purchase_shelflife', 'block_iomad_commerce'),
                                        ['defaultunit' => DAYSECS]);
            $mform->addHelpButton('single_purchase_shelflife', 'single_purchase_shelflife', 'block_iomad_commerce');

            $mform->addElement('duration', 'cutofftime',
                                        get_string('licensecutoffdate', 'block_iomad_company_admin'),
                                        ['optional' => true, 'defaultunit' => DAYSECS]);
            $mform->addHelpButton('cutofftime', 'licensecutoffdate', 'block_iomad_company_admin');

            $mform->addElement('advcheckbox', 'clearonexpire', get_string('clearonexpire', 'block_iomad_company_admin'));

            $mform->addHelpButton('clearonexpire', 'clearonexpire', 'block_iomad_company_admin');
            $mform->disabledIf('clearonexpire', 'cutoffdate[enabled]');

            $mform->addElement('select', 'currency', get_string('currency'), $this->currencies);

            $mform->addElement('header', 'header', get_string('single_purchase', 'block_iomad_commerce'));

            $mform->addElement('selectyesno', 'allow_single_purchase', get_string('allow_single_purchase', 'block_iomad_commerce'));
            $mform->addHelpButton('allow_single_purchase', 'allow_single_purchase', 'block_iomad_commerce');

            $mform->addElement('text', 'single_purchase_price',
                                        get_string('single_purchase_price', 'block_iomad_commerce'));
            $mform->addRule('single_purchase_price',
                             get_string('decimalnumberonly', 'block_iomad_commerce'), 'numeric');
            $mform->disabledIf('single_purchase_price', 'allow_single_purchase', 'eq', 0);
                             
            $mform->setType('single_purchase_price', PARAM_TEXT);
            $mform->addHelpButton('single_purchase_price', 'single_purchase_price', 'block_iomad_commerce');

            /****** license blocks *********/
            $mform->addElement('header', 'header', get_string('licenseblocks', 'block_iomad_commerce'));

            $licenseblockarray = [
                $mform->createElement('html', '<tr><td style="text-align: right;">'),
                $mform->createElement('text', 'item_block_start'),
                $mform->createElement('html', '</td><td style="text-align: right;">'),
                $mform->createElement('text', 'item_block_price'),
                $mform->createElement('html', '</td></tr>')
            ];

            // Set the default number to be repeated.
            if ($repeatno = $DB->count_records('course_shopblockprice', ['itemid' => $this->shopsettingsid])) {
                $repeatno++;
            } else {
                $repeatno = 1;
            }

            // Set up the options for the repeated item.
            $repeatoptions = ['item_block_start' => ['rule' => 'numeric', 'type' => PARAM_INT],
                              'item_block_price' => ['rule' => 'numeric', 'type' => PARAM_LOCALISEDFLOAT]];

            $mform->addElement('html', '<table id="licenseblockstable" class="generaltable" width="95%">
                                        <tr><th style="text-align: right;">' . get_string('licenseblock_start', 'block_iomad_commerce') . '</th>
                                        <th style="text-align: right;">' . get_string('licenseblock_price', 'block_iomad_commerce') . '</th></tr>');
            $this->repeat_elements($licenseblockarray,
                                   $repeatno,
                                   $repeatoptions,
                                   'option_repeats',
                                   'option_add_fields',
                                   1,
                                   null,
                                   true);
            $mform->addElement('html', '</table>');

            /******** tags **************/
            $mform->addElement('header', 'header', get_string('categorization', 'block_iomad_commerce'));

            $mform->addElement('textarea', 'tags', get_string('tags', 'block_iomad_commerce'), array('rows' => 5, 'cols' => 60));
            $mform->addHelpButton('tags', 'tags', 'block_iomad_commerce');
            $mform->setType('tags', PARAM_NOTAGS);

            $vars = helper::get_shop_tags();
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

        if (!empty($data['allow_single_purchase']) && empty($data['program']) && count($data['itemcourses']) > 1) {
            $errors['allow_single_purchase'] = get_string('error_incompatibletype', 'block_iomad_commerce');
        }

        if (count($data['itemcourses']) == 0) {
            $errors['itemcourses'] = get_string('required');
        } 

        return $errors;
    }

}