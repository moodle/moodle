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


namespace core_contentbank\output;

use context;
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
     * Get the content of the "More" dropdown in the tertiary navigation
     *
     * @return array|null The options to be displayed in a dropdown in the tertiary navigation
     * @throws \moodle_exception
     */
    protected function get_edit_actions_dropdown(): ?array {
        global $PAGE;
        $options = [];
        if ($this->contenttype->can_manage($this->content)) {
            // Add the visibility item to the menu.
            switch($this->content->get_visibility()) {
                case content::VISIBILITY_UNLISTED:
                    $visibilitylabel = get_string('visibilitysetpublic', 'core_contentbank');
                    $newvisibility = content::VISIBILITY_PUBLIC;
                    break;
                case content::VISIBILITY_PUBLIC:
                    $visibilitylabel = get_string('visibilitysetunlisted', 'core_contentbank');
                    $newvisibility = content::VISIBILITY_UNLISTED;
                    break;
                default:
                    $url = new \moodle_url('/contentbank/index.php', ['contextid' => $this->content->get_contextid()]);
                    throw new moodle_exception('contentvisibilitynotfound', 'error', $url, $this->content->get_visibility());
            }

            if ($visibilitylabel) {
                $options[$visibilitylabel] = [
                    'data-action' => 'setcontentvisibility',
                    'data-visibility' => $newvisibility,
                    'data-contentid' => $this->content->get_id(),
                ];
            }

            // Add the rename content item to the menu.
            $options[get_string('rename')] = [
                'data-action' => 'renamecontent',
                'data-contentname' => $this->content->get_name(),
                'data-contentid' => $this->content->get_id(),
            ];

            if ($this->contenttype->can_upload()) {
                $options[get_string('replacecontent', 'contentbank')] = ['data-action' => 'upload'];

                $PAGE->requires->js_call_amd(
                    'core_contentbank/upload',
                    'initModal',
                    [
                        '[data-action=upload]',
                        \core_contentbank\form\upload_files::class,
                        $this->content->get_contextid(),
                        $this->content->get_id()
                    ]
                );
            }
        }

        if ($this->contenttype->can_download($this->content)) {
            $url = new moodle_url($this->contenttype->get_download_url($this->content));
            $options[get_string('download')] = [
                'url' => $url->out()
            ];
        }

        if ($this->contenttype->can_copy($this->content)) {
            // Add the copy content item to the menu.
            $options[get_string('copycontent', 'contentbank')] = [
                'data-action' => 'copycontent',
                'data-contentname' => get_string('copyof', 'contentbank', $this->content->get_name()),
                'data-contentid' => $this->content->get_id(),
            ];
        }

        // Add the delete content item to the menu.
        if ($this->contenttype->can_delete($this->content)) {
            $options[get_string('delete')] = [
                'data-action' => 'deletecontent',
                'data-contentname' => $this->content->get_name(),
                'data-uses' => count($this->content->get_uses()),
                'data-contentid' => $this->content->get_id(),
                'data-contextid' => $this->content->get_contextid(),
                'class' => 'text-danger',
                ];
        }

        $dropdown = [];
        if ($options) {
            foreach ($options as $key => $attribs) {
                $url = $attribs['url'] ?? '#';
                $extraclasses = $attribs['class'] ?? '';
                $dropdown['options'][] = [
                    'label' => $key,
                    'url' => $url,
                    'extraclasses' => $extraclasses,
                    'attributes' => array_map(function ($key, $value) {
                        return [
                            'name' => $key,
                            'value' => $value
                        ];
                    }, array_keys($attribs), $attribs)
                ];
            }
        }

        return $dropdown;
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

        // Close/exit link for those users who can access that context.
        $context = context::instance_by_id($this->content->get_contextid());
        if (has_capability('moodle/contentbank:access', $context)) {
            $closeurl = new moodle_url('/contentbank/index.php', ['contextid' => $context->id]);
            $data->closeurl = $closeurl->out(false);
        }

        $data->actionmenu = $this->get_edit_actions_dropdown();
        $data->heading = $this->content->get_name();
        if ($this->content->get_visibility() == content::VISIBILITY_UNLISTED) {
            $data->heading = get_string('visibilitytitleunlisted', 'contentbank', $data->heading);
        }

        return $data;
    }
}
