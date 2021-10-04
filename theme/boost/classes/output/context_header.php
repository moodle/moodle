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

namespace theme_boost\output;

use moodle_url;
use get_string;

defined('MOODLE_INTERNAL') || die;

/**
 * The context header for the boost theme.
 *
 * @package    theme_boost
 * @copyright  2021 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_header implements \renderable, \templatable {

    /** \moodle_page This current page information. */
    protected $page;

    /** array Header data. */
    protected $headerinfo;

    /** int What level the header should be i.e. 1 = <h1>. */
    protected $headinglevel;

    /**
     * Constructs a new instance.
     *
     * @param \moodle_page $page This current page information.
     * @param array $headerinfo Header data.
     * @param int $headinglevel What level the header should be i.e. 1 = <h1>.
     */
    public function __construct(\moodle_page $page, array $headerinfo = null, int $headinglevel = 1) {
        $this->page = $page;
        $this->headerinfo = $headerinfo;
        $this->headinglevel = $headinglevel;
    }

    /**
     * Adds an array element for a formatted image.
     *
     * @param array $buttons Buttons to format.
     * @return array The formatted buttons.
     */
    protected function format_button_images(array $buttons): array {
        foreach ($buttons as $key => $button) {
            // If no image is provided then just use the title.
            if (!isset($button['image'])) {
                $buttons[$key]['formattedimage'] = $button['title'];
            } else {
                // Check to see if this is an internal Moodle icon.
                $internalimage = $this->page->theme->resolve_image_location('t/' . $button['image'], 'moodle');
                if ($internalimage) {
                    $buttons[$key]['formattedimage'] = 't/' . $button['image'];
                } else {
                    // Treat as an external image.
                    $buttons[$key]['formattedimage'] = $button['image'];
                }
            }

            if (isset($button['linkattributes']['class'])) {
                $class = $button['linkattributes']['class'] . ' btn';
            } else {
                $class = 'btn';
            }
            // Add the bootstrap 'btn' class for formatting.
            $buttons[$key]['linkattributes'] = array_merge($button['linkattributes'], ['class' => $class]);
            $temp = '';
            foreach ($buttons[$key]['linkattributes'] as $index => $value) {
                $temp .= "{$index}=\"{$value}\" ";
            }
            $buttons[$key]['linkattributes'] = $temp;
        }
        return $buttons;
    }

    /**
     * Gets the logo data.
     *
     * @param \renderer_base $output Renderer output data.
     * @return array Data to display a logo.
     */
    protected function get_logo_data(\renderer_base $output): array {
        global $SITE;
        if (!$output->should_display_main_logo($this->headinglevel)) {
            return [];
        }

        $sitename = format_string($SITE->fullname, true, ['context' => \context_course::instance(SITEID)]);
        if (!isset($heading)) {
            $heading = $output->heading($this->page->heading, $this->headinglevel, 'sr-only');
        } else {
            $heading = $output->heading($heading, $this->headinglevel, 'sr-only');
        }

        return [
            'heading' => $heading,
            'image' => [
                'src' => $output->get_logo_url(null, 150),
                'alt' => get_string('logoof', '', $sitename)
            ]
        ];
    }

    /**
     * Organises all of the relevant data to display the context header.
     *
     * @param \renderer_base $output Renderer output data.
     * @return array Data to display the context header.
     */
    public function export_for_template(\renderer_base $output): array {
        global $DB, $USER, $CFG;

        $page = $this->page;
        $context = $page->context;
        $heading = null;
        $imagedata = null;
        $userbuttons = null;

        // Make sure to use the heading if it has been set.
        if (isset($this->headerinfo['heading'])) {
            $heading = $this->headerinfo['heading'];
        } else {
            $heading = $page->heading;
        }

        // The user context currently has images and buttons. Other contexts may follow.
        if (isset($this->headerinfo['user']) || $context->contextlevel == CONTEXT_USER) {
            if (isset($this->headerinfo['user'])) {
                $user = $this->headerinfo['user'];
            } else {
                // Look up the user information if it is not supplied.
                $user = $DB->get_record('user', ['id' => $context->instanceid]);
            }

            // If the user context is set, then use that for capability checks.
            if (isset($this->headerinfo['usercontext'])) {
                $context = $this->headerinfo['usercontext'];
            }

            // Only provide user information if the user is the current user, or a user which the current user can view.
            // When checking user_can_view_profile(), either:
            // If the page context is course, check the course context (from the page object) or;
            // If page context is NOT course, then check across all courses.
            $course = ($context->contextlevel == CONTEXT_COURSE) ? $page->course : null;

            if (user_can_view_profile($user, $course)) {
                // Use the user's full name if the heading isn't set.
                if (empty($heading)) {
                    $heading = fullname($user);
                }

                $imagedata = $output->user_picture($user, ['size' => 100]);

                // Check to see if we should be displaying a message button.
                if (!empty($CFG->messaging) && has_capability('moodle/site:sendmessage', $context)) {
                    $userbuttons[] = [
                        'buttontype' => 'message',
                        'title' => get_string('message', 'message'),
                        'url' => new moodle_url('/message/index.php', ['id' => $user->id]),
                        'image' => 'message',
                        'linkattributes' => \core_message\helper::messageuser_link_params($user->id),
                        'page' => (isset($page))
                    ];
                    \core_message\helper::togglecontact_requirejs();

                    if ($USER->id != $user->id) {
                        $iscontact = \core_message\api::is_contact($USER->id, $user->id);
                        $contacttitle = $iscontact ? 'removefromyourcontacts' : 'addtoyourcontacts';
                        $contacturlaction = $iscontact ? 'removecontact' : 'addcontact';
                        $contactimage = $iscontact ? 'removecontact' : 'addcontact';
                        $userbuttons[] = [
                                'buttontype' => 'togglecontact',
                                'title' => get_string($contacttitle, 'message'),
                                'url' => new moodle_url('/message/index.php', [
                                        'user1' => $USER->id,
                                        'user2' => $user->id,
                                        $contacturlaction => $user->id,
                                        'sesskey' => sesskey()
                                ]),
                                'image' => $contactimage,
                                'linkattributes' => \core_message\helper::togglecontact_link_params($user, $iscontact),
                                'page' => (isset($page))
                            ];
                        \core_message\helper::messageuser_requirejs();
                    }

                    $page->requires->string_for_js('changesmadereallygoaway', 'moodle');
                    $userbuttons = $this->format_button_images($userbuttons);
                }
            } else {
                $heading = null;
            }
        }

        $prefix = null;
        if ($context->contextlevel == CONTEXT_MODULE) {
            $heading = $page->cm->get_formatted_name();
            $imagedata = $output->pix_icon('icon', '', $page->activityname);
            $prefix = get_string('modulename', $page->activityname);
        }

        $logo = $this->get_logo_data($output);
        if (empty($logo)) {
            $logo = '';
            $headingdisplay = $this->headinglevel + 1;
            $headingdisplay = 'h' . $headingdisplay;
            if (!isset($heading)) {
                $heading = $output->heading($page->heading, $this->headinglevel, $headingdisplay);
            } else {
                $heading = $output->heading($heading, $this->headinglevel, $headingdisplay);
            }
        }

        return [
            'logo' => $logo,
            'heading' => $heading,
            'imagedata' => $imagedata,
            'userbuttons' => $userbuttons,
            'prefix' => $prefix
        ];
    }
}
