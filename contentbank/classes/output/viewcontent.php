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
 * Class containing data for a content view.
 *
 * @package    core_contentbank
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank\output;

use core_contentbank\content;
use core_contentbank\contenttype;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Class containing data for the content view.
 *
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class viewcontent implements renderable, templatable {
    /**
     * @var contenttype Content bank content type.
     */
    private $contenttype;

    /**
     * @var stdClass Record of the contentbank_content table.
     */
    private $content;

    /**
     * Construct this renderable.
     *
     * @param contenttype $contenttype Content bank content type.
     * @param content $content Record of the contentbank_content table.
     */
    public function __construct(contenttype $contenttype, content $content) {
        $this->contenttype = $contenttype;
        $this->content = $content;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();

        // Get the content type html.
        $contenthtml = $this->contenttype->get_view_content($this->content);
        $data->contenthtml = $contenthtml;

        // Check if the user can edit this content type.
        if ($this->contenttype->can_edit($this->content)) {
            $data->usercanedit = true;
            $urlparams = [
                'contextid' => $this->content->get_contextid(),
                'plugin' => $this->contenttype->get_plugin_name(),
                'id' => $this->content->get_id()
            ];
            $editcontenturl = new moodle_url('/contentbank/edit.php', $urlparams);
            $data->editcontenturl = $editcontenturl->out(false);
        }

        $closeurl = new moodle_url('/contentbank/index.php', ['contextid' => $this->content->get_contextid()]);
        $data->closeurl = $closeurl->out(false);

        return $data;
    }
}
