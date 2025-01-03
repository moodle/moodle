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

namespace local_ai_manager\hook;

use local_ai_manager\local\tenant;

/**
 * Hook for providing information for the rights config table filter.
 *
 * This hook will be dispatched when it's rendering the rights config table.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label(
        'Allows plugins to provide a list of options for the filter of the user rights management table in the tenant config.')]
#[\core\attribute\tags('local_ai_manager')]
class usertable_filter {

    /** @var array associative array for providing filter options to the filter component of the rights config table */
    private array $filteroptions = [];

    /** @var string String for providing a label for the filter selection form element */
    private string $filterlabel = '';

    /**
     * Constructor for the hook.
     * @param tenant $tenant the tenant for which the user table is being shown
     */
    public function __construct(
            /** @var tenant $tenant the tenant for which the user table is being shown */
            private tenant $tenant
    ) {
    }

    /**
     * Standard getter.
     *
     * @return tenant the tenant for which the table is being shown
     */
    public function get_tenant(): tenant {
        return $this->tenant;
    }

    /**
     * Standard getter.
     *
     * @return array filter options array
     */
    public function get_filter_options(): array {
        return $this->filteroptions;
    }

    /**
     * Standard setter to allow the hook callbacks to store the filter options.
     *
     * @param array $filteroptions associative array with the filter options of the form ['key' => 'displayname', ...] where 'key'
     *  is the key which is being submitted when submitting the filter form, 'displayname' is the (localized) name to show in the
     *  filter
     */
    public function set_filter_options(array $filteroptions): void {
        $this->filteroptions = $filteroptions;
    }

    /**
     * Standard getter for retrieving the label which should be shown above the filter form element.
     *
     * @return string the localized string to show above the filter form element
     */
    public function get_filter_label(): string {
        return $this->filterlabel;
    }

    /**
     * Standard setter for the label which should be shown above the filter form element.
     *
     * @param string $label The localized string to show above the filter form element.
     */
    public function set_filter_label(string $label): void {
        $this->filterlabel = $label;
    }
}
