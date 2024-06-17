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

declare(strict_types=1);

namespace core_reportbuilder\local\audiences;

use core_plugin_manager;
use MoodleQuickForm;
use stdClass;
use core\output\notification;
use core_reportbuilder\external\custom_report_audience_cards_exporter;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\models\audience;
use core_reportbuilder\exception\report_access_exception;

/**
 * Audience base class
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /** @var int Maximim number of multi-select elements to show in description, before appending "plus X more" */
    private const MULTI_SELECT_LIMIT = 5;

    /** @var audience The persistent object associated with this audience */
    protected $audience;

    /**
     * Protected constructor, please use the static instance method.
     */
    protected function __construct() {
    }

    /**
     * Creates an instance of audience type, with persistent. Typically by loading an existing record, however in the absence of
     * specified classname (in the case of {@see custom_report_audience_cards_exporter}) we create one based on the current class
     *
     * @param int $id
     * @param null|stdClass $record
     * @return self|null
     */
    final public static function instance(int $id = 0, ?stdClass $record = null): ?self {
        $persistent = new audience($id, $record);

        // Populate persistent classname automatically if not set (e.g. when not loading an existing instance).
        if (!$classname = $persistent->get('classname')) {
            $classname = get_called_class();
            $persistent->set('classname', $classname);
        }

        // Check if audience type class still exists in the system.
        if (!class_exists($classname)) {
            return null;
        }

        $instance = new $classname();
        $instance->audience = $persistent;
        return $instance;
    }

    /**
     * Creates a new audience and saves it to database
     *
     * @param int $reportid
     * @param array $configdata
     * @return self
     */
    final public static function create(int $reportid, array $configdata): self {
        $record = new stdClass();
        $record->reportid = $reportid;
        $record->classname = get_called_class();
        $record->configdata = json_encode($configdata);
        $instance = self::instance(0, $record);
        $instance->audience->save();
        return $instance;
    }

    /**
     * Return display name of the component the audience belongs to
     *
     * @return string
     */
    final public function get_component_displayname(): string {
        [$component] = explode('\\', get_class($this));

        if ($plugininfo = core_plugin_manager::instance()->get_plugin_info($component)) {
            return $plugininfo->displayname;
        }

        // Return generic site text for those audiences belonging to core subsystems.
        return get_string('site');
    }

    /**
     * Helps to build SQL to retrieve users that matches the current audience
     *
     * Implementations must use {@see database::generate_alias} and {@see database::generate_param_name} for table/column
     * aliases and parameter names
     *
     * @param string $usertablealias
     * @return array array of three elements [$join, $where, $params]
     */
    abstract public function get_sql(string $usertablealias): array;

    /**
     * Returns string for audience category.
     *
     * @return string
     *
     * @deprecated since Moodle 4.2 - please do not use this function any more, {@see get_component_displayname}
     */
    final public function get_category(): string {
        debugging('The function ' . __FUNCTION__ . '() is deprecated, please do not use it any more. ' .
            'See class \'get_component_displayname\' method for replacement', DEBUG_DEVELOPER);

        return $this->get_component_displayname();
    }

    /**
     * If the current user is able to add this audience type
     *
     * @return bool
     */
    abstract public function user_can_add(): bool;

    /**
     * If the current user is able to edit this audience type
     *
     * @return bool
     */
    abstract public function user_can_edit(): bool;

    /**
     * If the current user is able to use this audience type
     *
     * This method needs to return true if audience type is available to user for
     * reasons other than permission check, which is done in {@see user_can_add}.
     * (e.g. user can add cohort audience type only if there is at least one cohort
     * they can access).
     *
     * @return bool
     */
    public function is_available(): bool {
        return true;
    }

    /**
     * Return user friendly name of the audience type
     *
     * @return string
     */
    abstract public function get_name(): string;

    /**
     * Return the description of this audience type
     *
     * @return string
     */
    abstract public function get_description(): string;

    /**
     * Helper to format descriptions for audience types that may contain many selected elements, limiting number show according
     * to {@see MULTI_SELECT_LIMIT} constant value
     *
     * @param array $elements
     * @return string
     */
    protected function format_description_for_multiselect(array $elements): string {
        global $OUTPUT;

        // Warn user if there are no elements (because they may no longer exist).
        $elementcount = count($elements);
        if ($elementcount === 0) {
            $notification = new notification(get_string('nothingtodisplay'), notification::NOTIFY_WARNING);
            return $OUTPUT->render($notification);
        }

        $listseparator = get_string('listsep', 'langconfig') . ' ';
        if ($elementcount > self::MULTI_SELECT_LIMIT) {
            $elements = array_slice($elements, 0, self::MULTI_SELECT_LIMIT);

            // Append overflow element.
            $elementoverflow = $elementcount - self::MULTI_SELECT_LIMIT;
            $params = [
                'elements' => implode($listseparator, $elements),
                'morecount' => $elementoverflow,
            ];
            $description = get_string('audiencemultiselectpostfix', 'core_reportbuilder', $params);
        } else {
            $description = implode($listseparator, $elements);
        }

        return $description;
    }

    /**
     * Adds audience-specific form elements
     *
     * @param MoodleQuickForm $mform The form to add elements to
     */
    abstract public function get_config_form(MoodleQuickForm $mform): void;

    /**
     * Validates the configform of the condition.
     *
     * @param array $data Data from the form
     * @return array Array with errors for each element
     */
    public function validate_config_form(array $data): array {
        return [];
    }

    /**
     * Returns configdata as an associative array
     *
     * @return array decoded configdata
     */
    final public function get_configdata(): array {
        return json_decode($this->audience->get('configdata'), true);
    }

    /**
     * Update configdata in audience persistent
     *
     * @param array $configdata
     */
    final public function update_configdata(array $configdata): void {
        $this->audience->set('configdata', json_encode($configdata));
        $this->audience->save();
    }

    /**
     * Returns $configdata from form data suitable for use in DB record.
     *
     * @param stdClass $data data obtained from $mform->get_data()
     * @return array $configdata
     */
    final public static function retrieve_configdata(stdClass $data): array {
        $configdata = (array) $data;
        $invalidkeys = array_fill_keys(['id', 'reportid', 'classname'], '');
        return array_diff_key($configdata, $invalidkeys);
    }

    /**
     * Return audience persistent.
     *
     * @return audience
     */
    public function get_persistent(): audience {
        return $this->audience;
    }

    /**
     * Require current user is able to add this audience type
     *
     * @throws report_access_exception
     */
    final public function require_user_can_add(): void {
        if (!$this->user_can_add()) {
            throw new report_access_exception('errorreportedit');
        }
    }

    /**
     * Require current user is able to edit this audience type
     *
     * @throws report_access_exception
     */
    final public function require_user_can_edit(): void {
        if (!$this->user_can_edit()) {
            throw new report_access_exception('errorreportedit');
        }
    }
}
