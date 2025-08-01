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

namespace core_courseformat\output\local\overview;

use core\url;
use stdClass;
use core\output\pix_icon;
use core\output\externable;
use core\output\renderable;
use core\output\action_link;
use core\output\renderer_base;
use core\output\named_templatable;
use core\external\action_link_exporter;
use core\output\local\properties\button;
use core\output\actions\component_action;
use core_courseformat\external\overviewaction_exporter;

/**
 * Data structure describing html link with a buble and special action attached.
 *
 * @package    core_courseformat
 * @category   output
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 5.1
 */
class overviewaction extends action_link implements externable, named_templatable {
    /**
     * Creates a new overview action.
     *
     * @param url $url The URL for the action.
     * @param string $text The content to represent the action.
     * @param null|array $attributes Any attributes associated with the action.
     * @param string|null $badgevalue The content of the badge to display.
     * @param string|null $badgetitle The badge title. Ignored if badgevalue is null.
     * @param \core\output\local\properties\badge|null $badgestyle The badge style to apply. Ignored if badgevalue is null.
     */
    public function __construct(
        url $url,
        string $text,
        ?array $attributes = null,
        /** @var string|null $badgevalue The content of the badge to display */
        protected ?string $badgevalue = null,
        /** @var string|null $badgetitle The badge title */
        protected ?string $badgetitle = null,
        /** @var \core\output\local\properties\badge|null $badgestyle The badge style to apply */
        protected ?\core\output\local\properties\badge $badgestyle = null,
    ) {
        if ($attributes === null) {
            $attributes = ['class' => button::BODY_OUTLINE->classes()];
        } else if (array_key_exists('class', $attributes) === false) {
            $attributes['class'] = button::BODY_OUTLINE->classes();
        }

        if ($badgevalue !== null) {
            $this->badgetitle = $this->badgetitle ?? '';
            $this->badgestyle = $this->badgestyle ?? \core\output\local\properties\badge::PRIMARY;
        }

        parent::__construct(
            url: $url,
            text: $text,
            attributes: $attributes,
        );
    }

    /**
     * Gets the badge value.
     *
     * @return string|null The badge value or null if not set.
     */
    public function get_badgevalue(): ?string {
        return $this->badgevalue;
    }

    /**
     * Gets the badge title.
     *
     * @return string|null The badge title or null if not set.
     */
    public function get_badgetitle(): ?string {
        return $this->badgetitle;
    }

    /**
     * Gets the badge style.
     *
     * @return \core\output\local\properties\badge|null The badge style or null if not set.
     */
    public function get_badgestyle(): ?\core\output\local\properties\badge {
        return $this->badgestyle;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = parent::export_for_template($output);

        $data->onlytext = $this->text;
        if ($this->badgevalue !== null) {
            /** @var \core_renderer $renderer */
            $renderer = $output;
            $badge = $renderer->notice_badge(
                contents: $this->badgevalue,
                badgestyle: $this->badgestyle,
                title: $this->badgetitle,
            );
            $data->text .= $badge;
            $data->badge = [
                'value' => $this->badgevalue,
                'title' => $this->badgetitle,
                'style' => $this->badgestyle,
            ];
        }

        return $data;
    }

    #[\Override]
    public function get_template_name(\renderer_base $renderer): string {
        return 'core/action_link';
    }

    #[\Override]
    public function get_exporter(?\core\context $context = null): action_link_exporter {
        $context = $context ?? \core\context\system::instance();
        return new overviewaction_exporter($this, ['context' => $context]);
    }

    #[\Override]
    public static function get_read_structure(
        int $required = VALUE_REQUIRED,
        mixed $default = null
    ): \core_external\external_single_structure {
        return overviewaction_exporter::get_read_structure($required, $default);
    }

    #[\Override]
    public static function read_properties_definition(): array {
        return overviewaction_exporter::read_properties_definition();
    }
}
