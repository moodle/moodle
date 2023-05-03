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

namespace core_grades\output;

use moodle_exception;
use renderable;
use renderer_base;
use templatable;

/**
 * Renderable class for the dropdown in the gradebook pages.
 *
 * We have opted to have this as a class as opposed to a renderable for prosperity
 * in the case that custom handling is required by the calling code.
 *
 * This could become a abstract class if other components require similar functionality and wish to extend the base here.
 *
 * @package    core_grades
 * @copyright  2022 Mathew May <Mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradebook_dropdown implements renderable, templatable {

    /** @var bool $renderlater Should the dropdown render straightaway? */
    protected $renderlater;

    /** @var string $buttoncontent What is the content of the "Button" that users will always see. */
    protected $buttoncontent;

    /** @var null|string $dropdowncontent The content that can be passed in to render immediately. */
    protected $dropdowncontent;

    /** @var null|string $parentclasses Any special classes to put on the HTMLElement that contains the BS events. */
    protected $parentclasses;

    /** @var null|string $buttonclasses Any special classes to put on the HTMLElement that triggers the dropdown. */
    protected $buttonclasses;

    /** @var null|string $dropdownclasses Any special classes to put on the HTMLElement that contains the actual dropdown. */
    protected $dropdownclasses;

    /** @var null|string $buttonheader If the button item in the tertiary nav needs an extra top header for context. */
    protected $buttonheader;

    /** @var boolean $usesbutton Whether to provide a A11y button. */
    protected $usesbutton;

    /**
     * The class constructor.
     *
     * @param bool $renderlater How we figure out if we should render the template instantly.
     * @param string $buttoncontent What gets placed in the button.
     * @param ?string $dropdowncontent What can be placed in the dropdown if we are rendering now.
     * @param ?string $parentclasses The classes that can be added that the bootstrap events are attached to.
     * @param ?string $buttonclasses Any special classes that may be needed.
     * @param ?string $dropdownclasses Any special classes that may be needed.
     * @param ?string $buttonheader If the button item in the tertiary nav needs an extra top header for context.
     * @param bool $usebutton If we want the mustache to add the button roles for us or do we have another aria role node?
     * @throws moodle_exception If the implementor incorrectly call this module.
     */
    public function __construct(
        bool $renderlater,
        string $buttoncontent,
        ?string $dropdowncontent = null,
        ?string $parentclasses = null,
        ?string $buttonclasses = null,
        ?string $dropdownclasses = null,
        ?string $buttonheader = null,
        ?bool $usebutton = true
    ) {
        // Ensure implementors cant request to render the content now and not provide us any to show.
        if (!$renderlater && empty($dropdowncontent)) {
            throw new moodle_exception(
                'incorrectdropdownvars',
                'core_grades',
                '', null,
                'Dropdown content must be set to render later.'
            );
        }

        $this->renderlater = $renderlater;
        $this->buttoncontent = $buttoncontent;
        $this->dropdowncontent = $dropdowncontent;
        $this->parentclasses = $parentclasses;
        $this->buttonclasses = $buttonclasses;
        $this->dropdownclasses = $dropdownclasses;
        $this->buttonheader = $buttonheader;
        $this->usesbutton = $usebutton;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'rtl' => right_to_left(),
            'renderlater' => $this->renderlater,
            'buttoncontent' => $this->buttoncontent ,
            'dropdowncontent' => $this->dropdowncontent,
            'parentclasses' => $this->parentclasses,
            'buttonclasses' => $this->buttonclasses,
            'dropdownclasses' => $this->dropdownclasses,
            'buttonheader' => $this->buttonheader,
            'usebutton' => $this->usesbutton,
        ];
    }

    /**
     * Returns the standard template for the dropdown.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/tertiary_navigation_dropdown';
    }
}
