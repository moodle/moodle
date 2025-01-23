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

namespace core_admin\admin;

use admin_setting;

/**
 * Render a template as part of other admin settings.
 * Use for rendering additional html in settings.
 *
 * @package    core_admin
 * @subpackage admin
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_template_render extends admin_setting {
    /**
     * Constructor.
     *
     * @param string $name The name of the setting.
     * @param string $templatename The name of the template to render.
     * @param array|\stdClass $context The context to pass to the template.
     */
    public function __construct(
        string $name,
        /** @var string The name of the template to render. */
        protected string $templatename,
        /** @var array|\stdClass The context to pass to the template. */
        protected array|\stdClass $context
    ) {
        $this->nosave = true;

        parent::__construct($name, $templatename, '', '');
    }

    #[\Override]
    public function get_setting(): bool {
        return true;
    }

    #[\Override]
    public function get_defaultsetting(): bool {
        return true;
    }

    #[\Override]
    // phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
    public function write_setting($data): string {
        // Do not write any setting.
        return '';
    }

    #[\Override]
    public function output_html($data, $query = ''): string {
        global $OUTPUT;

        return $OUTPUT->render_from_template($this->templatename, $this->context);
    }
}
