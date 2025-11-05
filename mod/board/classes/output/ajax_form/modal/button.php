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

namespace mod_board\output\ajax_form\modal;

use moodle_url;
use renderer_base;

/**
 * Button that opens modal form.
 *
 * @package     mod_board
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class button extends action {
    /** @var bool */
    protected $primary;

    /**
     * Create button that opens a form in modal dialog.
     *
     * @param moodle_url $formurl
     * @param string $label button label
     * @param bool $primary is this a primary button?
     */
    public function __construct(moodle_url $formurl, string $label, bool $primary = false) {
        parent::__construct($formurl, $label);
        $this->formurl = $formurl;
        $this->label = $label;
        $this->primary = $primary;

        $this->add_class('singlebutton');
    }

    /**
     * Set button as primary.
     *
     * @param bool $value
     * @return static
     */
    public function set_primary(bool $value): static {
        $this->primary = $value;
        return $this;
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        $data = parent::export_for_template($output);
        $data['primary'] = $this->primary;

        return $data;
    }
}
