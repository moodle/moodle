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
 * Renderable for the main page header.
 *
 * @package core
 * @category output
 * @since 2.9
 * @copyright 2015 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_header implements renderable, templatable {
    /** @var string $heading Main heading */
    public $heading;

    /** @var int $headinglevel Main heading 'h' tag level */
    public $headinglevel;

    /** @var string|null $imagedata HTML code for the picture in the page header */
    public $imagedata;

    /**
     * @var array $additionalbuttons Additional buttons for the header e.g. Messaging button for the user header.
     *      array elements - title => alternate text for the image, or if no image is available the button text.
     *                       url => Link for the button to head to. Should be a moodle_url.
     *                       image => location to the image, or name of the image in /pix/t/{image name}.
     *                       linkattributes => additional attributes for the <a href> element.
     *                       page => page object. Don't include if the image is an external image.
     */
    public $additionalbuttons;

    /** @var string $prefix A string that is before the title */
    public $prefix;

    /**
     * Constructor.
     *
     * @param string $heading Main heading data.
     * @param int $headinglevel Main heading 'h' tag level.
     * @param string|null $imagedata HTML code for the picture in the page header.
     * @param string $additionalbuttons Buttons for the header e.g. Messaging button for the user header.
     * @param string $prefix Text that precedes the heading.
     */
    public function __construct($heading = null, $headinglevel = 1, $imagedata = null, $additionalbuttons = null, $prefix = null) {
        $this->heading = $heading;
        $this->headinglevel = $headinglevel;
        $this->imagedata = $imagedata;
        $this->additionalbuttons = $additionalbuttons;
        // If we have buttons then format them.
        if (isset($this->additionalbuttons)) {
            $this->format_button_images();
        }
        $this->prefix = $prefix;
    }

    /**
     * Adds an array element for a formatted image.
     */
    protected function format_button_images() {

        foreach ($this->additionalbuttons as $buttontype => $button) {
            $page = $button['page'];
            // If no image is provided then just use the title.
            if (!isset($button['image'])) {
                $this->additionalbuttons[$buttontype]['formattedimage'] = $button['title'];
            } else {
                // Check to see if this is an internal Moodle icon.
                $internalimage = $page->theme->resolve_image_location('t/' . $button['image'], 'moodle');
                if ($internalimage) {
                    $this->additionalbuttons[$buttontype]['formattedimage'] = 't/' . $button['image'];
                } else {
                    // Treat as an external image.
                    $this->additionalbuttons[$buttontype]['formattedimage'] = $button['image'];
                }
            }

            if (isset($button['linkattributes']['class'])) {
                $class = $button['linkattributes']['class'] . ' btn';
            } else {
                $class = 'btn';
            }
            // Add the bootstrap 'btn' class for formatting.
            $this->additionalbuttons[$buttontype]['linkattributes'] = array_merge(
                $button['linkattributes'],
                ['class' => $class]
            );
        }
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        // Heading.
        $heading = '';
        $headingtext = isset($this->heading) ? $this->heading : $output->get_page()->heading;
        if ('' !== $headingtext) {
            $heading = $output->heading($headingtext, $this->headinglevel, "h2 mb-0");
        }

        // Buttons.
        if (isset($this->additionalbuttons)) {
            $additionalbuttons = [];
            foreach ($this->additionalbuttons as $button) {
                if (!isset($button->page)) {
                    // Include js for messaging.
                    if ($button['buttontype'] === 'togglecontact') {
                        \core_message\helper::togglecontact_requirejs();
                    }
                    if ($button['buttontype'] === 'message') {
                        \core_message\helper::messageuser_requirejs();
                    }
                }
                foreach ($button['linkattributes'] as $key => $value) {
                    $button['attributes'][] = ['name' => $key, 'value' => $value];
                }
                $additionalbuttons[] = $button;
            }
        }

        return [
            'heading' => $heading,
            'headinglevel' => $this->headinglevel,
            'imagedata' => $this->imagedata,
            'prefix' => $this->prefix,
            'hasadditionalbuttons' => !empty($additionalbuttons),
            'additionalbuttons' => $additionalbuttons ?? [],
        ];
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(context_header::class, \context_header::class);
