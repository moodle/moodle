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

namespace core_enrol\output;

use core\output\named_templatable;
use core\output\renderable;
use core\output\single_button;

/**
 * Allows to render a widget provided by enrol_plugin::enrol_page_hook()
 *
 * @package    core_enrol
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_page implements named_templatable, renderable {

    /**
     * Constructor
     *
     * @param \stdClass $instance
     * @param string|null $header
     * @param string|null $body
     * @param array $buttons
     */
    public function __construct(
        /** @var \stdClass */
        protected \stdClass $instance,
        /** @var string|null */
        protected ?string $header = null,
        /** @var string|null */
        protected ?string $body = null,
        /** @var single_button[] */
        protected array $buttons = []
        ) {
    }

    #[\Override]
    public function export_for_template(\core\output\renderer_base $output) {
        return [
            'enrol' => $this->instance->enrol,
            'instanceid' => $this->instance->id,
            'header' => $this->header,
            'body' => $this->body,
            'buttons' => array_map(fn($b) => $b->export_for_template($output), $this->buttons),
            'hasbuttons' => !empty($this->buttons),
        ];
    }

    #[\Override]
    public function get_template_name(\core\output\renderer_base $renderer): string {
        return 'core_enrol/enrol_page';
    }
}
