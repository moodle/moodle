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

/**
 * H5P content type manager class
 *
 * @package    contenttype_h5p
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace contenttype_h5p;

use core\event\contentbank_content_viewed;
use html_writer;

/**
 * H5P content bank manager class
 *
 * @package    contenttype_h5p
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contenttype extends \core_contentbank\contenttype {

    /**
     * Delete this content from the content_bank and remove all the H5P related information.
     *
     * @param  content $content The content to delete.
     * @return boolean true if the content has been deleted; false otherwise.
     */
    public function delete_content(\core_contentbank\content $content): bool {
        // Delete the H5P content.
        $factory = new \core_h5p\factory();
        if (!empty($content->get_file_url())) {
            \core_h5p\api::delete_content_from_pluginfile_url($content->get_file_url(), $factory);
        }

        // Delete the content from the content_bank.
        return parent::delete_content($content);
    }

    /**
     * Returns the HTML content to add to view.php visualizer.
     *
     * @param  content $content The content to be displayed.
     * @return string            HTML code to include in view.php.
     */
    public function get_view_content(\core_contentbank\content $content): string {
        // Trigger an event for viewing this content.
        $event = contentbank_content_viewed::create_from_record($content->get_content());
        $event->trigger();

        $fileurl = $content->get_file_url();
        $html = html_writer::tag('h2', $content->get_name());
        $html .= \core_h5p\player::display($fileurl, new \stdClass(), true);
        return $html;
    }

    /**
     * Returns the HTML code to render the icon for H5P content types.
     *
     * @param string $contentname   The contentname to add as alt value to the icon.
     * @return string            HTML code to render the icon
     */
    public function get_icon(string $contentname): string {
        global $OUTPUT;
        return $OUTPUT->pix_icon('f/h5p-64', $contentname, 'moodle', ['class' => 'iconsize-big']);
    }

    /**
     * Return an array of implemented features by this plugin.
     *
     * @return array
     */
    protected function get_implemented_features(): array {
        return [self::CAN_UPLOAD];
    }

    /**
     * Return an array of extensions this contenttype could manage.
     *
     * @return array
     */
    public function get_manageable_extensions(): array {
        return ['.h5p'];
    }

    /**
     * Returns user has access capability for the content itself.
     *
     * @return bool     True if content could be accessed. False otherwise.
     */
    protected function is_access_allowed(): bool {
        return true;
    }
}
