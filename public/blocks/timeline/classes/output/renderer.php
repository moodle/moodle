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

namespace block_timeline\output;

use plugin_renderer_base;

/**
 * Timeline block renderer.
 *
 * Emits a React mount point directly — no Mustache template is used.
 * The React component (`@moodle/lms/block_timeline/Timeline`) is mounted
 * automatically by `core/react_autoinit` when it detects the
 * `data-react-component` attribute.
 *
 * @package    block_timeline
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Render the timeline block as a React mount point.
     *
     * A static `import` in a `type="module"` script causes the browser's
     * preload scanner to discover and fetch the full Timeline module graph
     * (including React and ReactDOM) in one parallel round-trip during HTML
     * parsing. Without this hint, `core/react_autoinit` uses a dynamic
     * `import()` whose specifier string is opaque to the preload scanner,
     * causing a sequential 5-level waterfall that significantly delays first
     * render.
     *
     * @param main $main The main renderable.
     * @return string HTML string containing the preload hint and React mount point.
     */
    public function render_main(main $main): string {
        $props = json_encode(
            $main->export_for_template($this),
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
        );
        return \html_writer::div($this->render_loading_skeleton(), '', [
            'data-react-component' => '@moodle/lms/block_timeline/Timeline',
            'data-react-props'     => $props,
        ]);
    }

    /**
     * Render a full Timeline skeleton matching React's initial DOM.
     *
     * The skeleton mirrors the complete structure Timeline.tsx renders while
     * loading (nav bar + DatesViewSkeleton), so there is zero layout shift when
     * React mounts and replaces these children. Without the nav bar placeholder,
     * React's rendered nav bar (~38 px) appears on mount and pushes the event
     * list down, causing CLS. aria-hidden prevents screen readers from
     * announcing the decorative placeholder elements.
     *
     * @return string HTML string for the full skeleton.
     */
    private function render_loading_skeleton(): string {
        $pulse = fn(string $cls, string $style = '') => \html_writer::div(
            '',
            $cls,
            $style !== '' ? ['style' => $style] : []
        );

        // Nav bar — matches the DayFilter + ViewSelector + Search row in Timeline.tsx.
        $navbtn = \html_writer::tag('button', '', [
            'type'        => 'button',
            'class'       => 'btn btn-outline-secondary dropdown-toggle icon-no-margin',
            'tabindex'    => '-1',
            'aria-hidden' => 'true',
        ]);
        $searchinput = \html_writer::div(
            \html_writer::div(
                \html_writer::div(
                    \html_writer::empty_tag('input', [
                        'type'        => 'text',
                        'class'       => 'form-control withclear rounded',
                        'tabindex'    => '-1',
                        'aria-hidden' => 'true',
                    ]),
                    'input-group searchbar w-100'
                ),
                'd-flex flex-wrap align-items-center simplesearchform'
            ),
            'w-100'
        );
        $nav = \html_writer::div(
            \html_writer::div(
                \html_writer::div(\html_writer::div($navbtn, 'dropdown mb-1')) .
                \html_writer::div(\html_writer::div($navbtn, 'dropdown mb-1')) .
                \html_writer::div($searchinput, 'flex-grow-1 d-flex justify-content-end nav-search'),
                'd-flex flex-wrap gap-1 g-0'
            ) .
            \html_writer::div('', 'pb-3 px-2 border-bottom'),
            'p-0 px-2'
        );

        // DatesViewSkeleton — matches Skeleton.tsx DatesViewSkeleton exactly.
        $item = \html_writer::tag(
            'li',
            \html_writer::div(
                \html_writer::div(
                    \html_writer::div(
                        $pulse('bg-pulse-grey rounded-circle', 'height:32px;width:32px') .
                        \html_writer::div(
                            $pulse('bg-pulse-grey w-100', 'height:15px') .
                            $pulse('bg-pulse-grey w-75 mt-1', 'height:10px'),
                            'd-flex flex-column ps-2',
                            ['style' => 'flex:1']
                        ),
                        'd-flex flex-row align-items-center',
                        ['style' => 'height:32px']
                    ),
                    'col-8 pe-0'
                ) .
                \html_writer::div(
                    \html_writer::div(
                        $pulse('bg-pulse-grey w-75', 'height:15px'),
                        'd-flex flex-row justify-content-end',
                        ['style' => 'height:32px;padding-top:2px']
                    ),
                    'col-4 pe-3'
                ),
                'row'
            ),
            ['class' => 'list-group-item px-2']
        );
        $dateskeleton = \html_writer::div(
            \html_writer::tag('ul', str_repeat($item, 5), ['class' => 'ps-0 list-group list-group-flush']) .
            \html_writer::div(
                $pulse('w-25 bg-pulse-grey', 'height:35px'),
                'pt-3 pb-2 d-flex justify-content-between'
            ),
            '',
            ['data-region' => 'event-list-loading-placeholder']
        );
        $viewdates = \html_writer::div(
            $dateskeleton,
            '',
            ['data-region' => 'view-dates', 'role' => 'tabpanel']
        );

        // Full Timeline skeleton matching React's initial DOM structure.
        return \html_writer::div(
            $nav . \html_writer::div($viewdates, 'p-0'),
            'block-timeline',
            ['data-region' => 'timeline', 'aria-hidden' => 'true']
        );
    }
}
