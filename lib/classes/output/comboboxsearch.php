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

use core\exception\moodle_exception;

/**
 * Renderable class for the comboboxsearch.
 *
 * @package    core
 * @copyright  2022 Mathew May <Mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comboboxsearch implements renderable, named_templatable {
    /** @var bool $renderlater Should the dropdown render straightaway? We sometimes need to output the component without all of the
     * data and leave the rendering of any defaults and actual data to the caller. We will give you a basic placeholder that can
     * then be easily replaced.*/
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

    /** @var bool $usesbutton Whether to provide a A11y button. */
    protected $usesbutton;

    /** @var null|string $label The label of the combobox. */
    protected $label;

    /** @var null|string $name The name of the input element representing the combobox. */
    protected $name;

    /** @var null|string $value The value of the input element representing the combobox. */
    protected $value;

    /**
     * The class constructor.
     *
     * @param bool $renderlater How we figure out if we should render the template instantly.
     * @param string $buttoncontent What gets placed in the button.
     * @param ?string $dropdowncontent What will be placed in the dropdown if we are rendering now.
     * @param ?string $parentclasses The classes that can be added that the bootstrap events are attached to.
     * @param ?string $buttonclasses Any special classes that may be needed.
     * @param ?string $dropdownclasses Any special classes that may be needed.
     * @param ?string $buttonheader Sometimes we want extra context for a button before it is shown, basically a pseudo header.
     * @param ?bool $usebutton If we want the mustache to add the button roles for us or do we have another aria role node?
     * @param ?string $label The label of the combobox.
     * @param ?string $name The name of the input element representing the combobox.
     * @param ?string $value The value of the input element representing the combobox.
     * @throws moodle_exception If the implementor incorrectly calls this module.
     */
    public function __construct(
        bool $renderlater,
        string $buttoncontent,
        ?string $dropdowncontent = null,
        ?string $parentclasses = null,
        ?string $buttonclasses = null,
        ?string $dropdownclasses = null,
        ?string $buttonheader = null,
        ?bool $usebutton = true,
        ?string $label = null,
        ?string $name = null,
        ?string $value = null
    ) {
        // Ensure implementors cant request to render the content now and not provide us any to show.
        if (!$renderlater && empty($dropdowncontent)) {
            throw new moodle_exception(
                'incorrectdropdownvars',
                'core',
                '',
                null,
                'Dropdown content must be set to render later.'
            );
        }

        if ($usebutton && !$label) {
            debugging(
                'You have requested to use the button but have not provided a label for the combobox.',
                DEBUG_DEVELOPER
            );
        }

        if ($usebutton && !$name) {
            debugging(
                'You have requested to use the button but have not provided a name for the input element.',
                DEBUG_DEVELOPER
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
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'renderlater' => $this->renderlater,
            'buttoncontent' => $this->buttoncontent,
            'dropdowncontent' => $this->dropdowncontent,
            'parentclasses' => $this->parentclasses,
            'buttonclasses' => $this->buttonclasses,
            'dropdownclasses' => $this->dropdownclasses,
            'buttonheader' => $this->buttonheader,
            'usebutton' => $this->usesbutton,
            'instance' => rand(), // Template uniqid is per render out so sometimes these conflict.
            'label' => $this->label,
            'name' => $this->name,
            'value' => $this->value,
        ];
    }

    /**
     * Returns the standard template for the dropdown.
     *
     * @deprecated since Moodle 4.5. {@see named_templatable::get_template_name() instead}
     * @return string
     */
    public function get_template(): string {
        debugging('get_template is deprecated. Please use get_template_name instead');

        return 'core/comboboxsearch';
    }

    public function get_template_name(renderer_base $renderer): string {
        return 'core/comboboxsearch';
    }
}
