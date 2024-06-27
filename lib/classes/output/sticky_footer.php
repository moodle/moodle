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

namespace core\output;

/**
 * Class to render a sticky footer element.
 *
 * Sticky footer can be rendered at any moment if the page (even inside a form) but
 * it will be displayed at the bottom of the page.
 *
 * Important: note that pages can only display one sticky footer at once.
 *
 * Important: not all themes are compatible with sticky footer. If the current theme
 * is not compatible it will be rendered as a standard div element.
 *
 * @package    core
 * @category   output
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sticky_footer implements named_templatable, renderable {
    /**
     * @var string content of the sticky footer.
     */
    protected $stickycontent = '';

    /**
     * @var string extra CSS classes. By default, elements are justified to the end.
     */
    protected $stickyclasses = 'justify-content-end';

    /**
     * @var bool if the footer should auto enable or not.
     */
    protected $autoenable = true;

    /**
     * @var array extra HTML attributes (attribute => value).
     */
    protected $attributes = [];

    /**
     * Constructor.
     *
     * @param string $stickycontent the footer content
     * @param string|null $stickyclasses extra CSS classes
     * @param array $attributes extra html attributes (attribute => value)
     */
    public function __construct(string $stickycontent = '', ?string $stickyclasses = null, array $attributes = []) {
        $this->stickycontent = $stickycontent;
        if ($stickyclasses !== null) {
            $this->stickyclasses = $stickyclasses;
        }
        $this->attributes = $attributes;
    }

    /**
     * Set the footer contents.
     *
     * @param string $stickycontent the footer content
     */
    public function set_content(string $stickycontent) {
        $this->stickycontent = $stickycontent;
    }

    /**
     * Set the auto enable value.
     *
     * @param bool $autoenable the footer content
     */
    public function set_auto_enable(bool $autoenable) {
        $this->autoenable = $autoenable;
    }

    /**
     * Add extra classes to the sticky footer.
     *
     * @param string $stickyclasses the extra classes
     */
    public function add_classes(string $stickyclasses) {
        if (!empty($this->stickyclasses)) {
            $this->stickyclasses .= ' ';
        }
        $this->stickyclasses = $stickyclasses;
    }

    /**
     * Add extra attributes to the sticky footer element.
     *
     * @param string $atribute the attribute
     * @param string $value the value
     */
    public function add_attribute(string $atribute, string $value) {
        $this->attributes[$atribute] = $value;
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {
        $extras = [];
        foreach ($this->attributes as $attribute => $value) {
            $extras[] = [
                'attribute' => $attribute,
                'value' => $value,
            ];
        }
        $data = [
            'stickycontent' => (string)$this->stickycontent,
            'stickyclasses' => $this->stickyclasses,
            'extras' => $extras,
        ];
        if (!$this->autoenable) {
            $data['disable'] = true;
        }
        return $data;
    }

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param \renderer_base $renderer The renderer requesting the template name
     * @return string the template name
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'core/sticky_footer';
    }
}
