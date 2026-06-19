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

namespace core_user\output\user_action_menu;

use core\output\{pix_icon, renderer_base};
use core\url;

/**
 * Link action menu item
 *
 * @package     core_user
 * @copyright   2026 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class link extends base {
    /**
     * Constructor
     *
     * @param url $url
     * @param string $title
     * @param string|null $titleattribute
     * @param pix_icon|null $pixicon
     * @param string|null $imgsrc
     * @param array $classes
     * @param array $attributes
     */
    public function __construct(
        /** @var url */
        private url $url,
        /** @var string */
        private string $title,
        /** @var string|null */
        private ?string $titleattribute = null,
        /** @var pix_icon|null */
        private ?pix_icon $pixicon = null,
        /** @var string|null */
        private ?string $imgsrc = null,
        array $classes = [],
        array $attributes = [],
    ) {
        parent::__construct($classes, $attributes);
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        return [
            'url' => (string) $this->url,
            'title' => $this->title,
            'titleattribute' => (string) $this->titleattribute,
            'pixicon' => $this->pixicon?->export_for_pix(),
            'imgsrc' => (string) $this->imgsrc,
        ] + parent::export_for_template($output);
    }
}
