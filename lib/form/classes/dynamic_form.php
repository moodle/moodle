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

namespace core_form;

use context;
use core_external\external_api;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Class modal
 *
 * Extend this class to create a form that can be used in a modal dialogue.
 *
 * @package     core_form
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class dynamic_form extends \moodleform {

    /**
     * Constructor for modal forms can not be overridden, however the same form can be used both in AJAX and normally
     *
     * @param string $action
     * @param array $customdata
     * @param string $method
     * @param string $target
     * @param array $attributes
     * @param bool $editable
     * @param array $ajaxformdata Forms submitted via ajax, must pass their data here, instead of relying on _GET and _POST.
     * @param bool $isajaxsubmission whether the form is called from WS and it needs to validate user access and set up context
     */
    final public function __construct(
        ?string $action = null,
        ?array $customdata = null,
        string $method = 'post',
        string $target = '',
        ?array $attributes = [],
        bool $editable = true,
        ?array $ajaxformdata = null,
        bool $isajaxsubmission = false
    ) {
        global $PAGE, $CFG;

        $this->_ajaxformdata = $ajaxformdata;
        if ($isajaxsubmission) {
            // This form was created from the WS that needs to validate user access to it and set page context.
            // It has to be done before calling parent constructor because elements definitions may need to use
            // format_string functions and other methods that expect the page to be set up.
            external_api::validate_context($this->get_context_for_dynamic_submission());
            $PAGE->set_url($this->get_page_url_for_dynamic_submission());
            $this->check_access_for_dynamic_submission();
        }
        $attributes = ['data-random-ids' => 1] + ($attributes ?: []);
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    /**
     * Returns context where this form is used
     *
     * This context is validated in {@see external_api::validate_context()}
     *
     * If context depends on the form data, it is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * Example:
     *     $cmid = $this->optional_param('cmid', 0, PARAM_INT);
     *     return context_module::instance($cmid);
     *
     * @return context
     */
    abstract protected function get_context_for_dynamic_submission(): context;

    /**
     * Checks if current user has access to this form, otherwise throws exception
     *
     * Sometimes permission check may depend on the action and/or id of the entity.
     * If necessary, form data is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * Example:
     *     require_capability('dosomething', $this->get_context_for_dynamic_submission());
     */
    abstract protected function check_access_for_dynamic_submission(): void;

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * This method can return scalar values or arrays that can be json-encoded, they will be passed to the caller JS.
     *
     * Submission data can be accessed as: $this->get_data()
     *
     * Example:
     *     $data = $this->get_data();
     *     file_postupdate_standard_filemanager($data, ....);
     *     api::save_entity($data); // Save into the DB, trigger event, etc.
     *
     * @return mixed
     */
    abstract public function process_dynamic_submission();

    /**
     * Load in existing data as form defaults
     *
     * Can be overridden to retrieve existing values from db by entity id and also
     * to preprocess editor and filemanager elements
     *
     * Example:
     *     $id = $this->optional_param('id', 0, PARAM_INT);
     *     $data = api::get_entity($id); // For example, retrieve a row from the DB.
     *     file_prepare_standard_filemanager($data, ...);
     *     $this->set_data($data);
     */
    abstract public function set_data_for_dynamic_submission(): void;

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * This is used in the form elements sensitive to the page url, such as Atto autosave in 'editor'
     *
     * If the form has arguments (such as 'id' of the element being edited), the URL should
     * also have respective argument.
     *
     * Example:
     *     $id = $this->optional_param('id', 0, PARAM_INT);
     *     return new moodle_url('/my/page/where/form/is/used.php', ['id' => $id]);
     *
     * @return moodle_url
     */
    abstract protected function get_page_url_for_dynamic_submission(): moodle_url;
}
