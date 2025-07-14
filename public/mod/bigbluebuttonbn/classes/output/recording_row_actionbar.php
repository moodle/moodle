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

namespace mod_bigbluebuttonbn\output;

use mod_bigbluebuttonbn\recording;
use pix_icon;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderer for recording row actionbar column
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent.david [at] call-learning [dt] fr)
 */
class recording_row_actionbar implements renderable, templatable {
    /**
     * @var $recording
     */
    protected $recording;

    /**
     * @var $tools
     */
    protected $tools;

    /**
     * @var array TOOLS_DEFINITION a list of definition for the the specific tools
     */
    const TOOL_ACTION_DEFINITIONS = [
        'protect' => [
            'action' => 'unprotect',
            'icon' => 'lock',
            'hidewhen' => '!protected',
            'requireconfirmation' => true,
            'disablewhen' => 'imported'
        ],
        'unprotect' => [
            'action' => 'protect',
            'icon' => 'unlock',
            'hidewhen' => 'protected',
            'requireconfirmation' => true,
            'disablewhen' => 'imported'
        ],
        'publish' => [
            'action' => 'publish',
            'icon' => 'show',
            'hidewhen' => 'published',
            'requireconfirmation' => true,
            'disablewhen' => 'imported'
        ],
        'unpublish' => [
            'action' => 'unpublish',
            'icon' => 'hide',
            'hidewhen' => '!published',
            'requireconfirmation' => true,
            'disablewhen' => 'imported'
        ],
        'delete' => [
            'action' => 'delete',
            'icon' => 'trash',
            'requireconfirmation' => true
        ],
        'import' => [
            'action' => 'import',
            'icon' => 'import',
        ]
    ];

    /**
     * recording_row_actionbar constructor.
     *
     * @param recording $recording
     * @param array $tools
     */
    public function __construct(recording $recording, array $tools) {
        $this->recording = $recording;
        $this->tools = $tools;
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $context = new stdClass();
        $context->id = 'recording-actionbar-' . $this->recording->get('id');
        $context->recordingid = $this->recording->get('id');
        $context->tools = [];
        foreach ($this->tools as $tool) {
            if (!empty(self::TOOL_ACTION_DEFINITIONS[$tool])) {
                $buttonpayload = self::TOOL_ACTION_DEFINITIONS[$tool];
                $conditionalhiding = $buttonpayload['hidewhen'] ?? null;
                $disabledwhen = $buttonpayload['disablewhen'] ?? null;
                $this->actionbar_update_diplay($buttonpayload, $disabledwhen, $this->recording, 'disabled');
                $this->actionbar_update_diplay($buttonpayload, $conditionalhiding, $this->recording);
                if (!empty($buttonpayload)) {
                    $iconortext = '';
                    $target = $buttonpayload['action'];
                    if (isset($buttonpayload['target'])) {
                        $target .= '-' . $buttonpayload['target'];
                    }
                    $id = 'recording-' . $target . '-' . $this->recording->get('recordingid');
                    $iconattributes = [
                        'id' => $id,
                        'class' => 'iconsmall',
                    ];
                    $linkattributes = [
                        'id' => $id,
                        'data-action' => $buttonpayload['action'],
                        'data-require-confirmation' => !empty($buttonpayload['requireconfirmation']),
                        'class' => 'action-icon'
                    ];
                    if ($this->recording->get('imported')) {
                        $linkattributes['data-links'] = recording::count_records(
                            [
                                'recordingid' => $this->recording->get('recordingid'),
                                'imported' => true,
                            ]
                        );
                    }
                    if (isset($buttonpayload['disabled'])) {
                        $iconattributes['class'] .= ' fa-' . $buttonpayload['disabled'];
                        $linkattributes['class'] .= ' disabled';
                    }
                    $icon = new pix_icon(
                        'i/' . $buttonpayload['icon'],
                        get_string('view_recording_list_actionbar_' . $buttonpayload['action'], 'bigbluebuttonbn'),
                        'moodle',
                        $iconattributes
                    );
                    $iconortext = $output->render($icon);
                    $actionlink = new \action_link(new \moodle_url('#'), $iconortext, null, $linkattributes);
                    $context->tools[] = $actionlink->export_for_template($output);
                }

            }
        }
        return $context;
    }

    /**
     * Read the settings for this action and disable or hide the tool from the toolbar
     *
     * @param array $buttonpayload
     * @param string $condition
     * @param recording $rec
     * @param string $value
     */
    private function actionbar_update_diplay(&$buttonpayload, $condition, $rec, $value = 'invisible') {
        if ($condition) {
            $negates = $condition[0] === '!';
            $conditionalvariable = ltrim($condition, '!');
            if ($rec->get($conditionalvariable) xor $negates) {
                $buttonpayload['disabled'] = $value;
            }
        }
    }
}
