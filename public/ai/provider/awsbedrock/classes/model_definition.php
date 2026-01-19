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

namespace aiprovider_awsbedrock;

use core_ai\aimodel\base;
use MoodleQuickForm;

/**
 * Generic AWS Bedrock model definition.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2026 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class model_definition extends base {
    /** @var int MODEL_TYPE_TEXT Text model type. */
    public const MODEL_TYPE_TEXT = 1;
    /** @var int MODEL_TYPE_IMAGE Image model type. */
    public const MODEL_TYPE_IMAGE = 2;
    /** @var string Model id. */
    private readonly string $modelname;
    /** @var int Model type. */
    private readonly int $modeltype;
    /** @var array Model settings schema. */
    private readonly array $settings;

    /**
     * Constructor.
     *
     * @param string $modelname Model id.
     * @param int $modeltype Model type.
     * @param array $settings Model settings schema.
     */
    public function __construct(
        string $modelname,
        int $modeltype,
        array $settings = [],
    ) {
        $this->modelname = $modelname;
        $this->modeltype = $modeltype;
        $this->settings = $settings;
    }

    #[\Override]
    public function get_model_name(): string {
        return $this->modelname;
    }

    #[\Override]
    public function get_model_display_name(): string {
        return get_string("model_{$this->modelname}", 'aiprovider_awsbedrock');
    }

    #[\Override]
    public function get_model_settings(): array {
        return $this->settings;
    }

    #[\Override]
    public function add_model_settings(MoodleQuickForm $mform): void {
        foreach ($this->settings as $key => $setting) {
            $mform->addElement(
                $setting['elementtype'],
                $key,
                get_string($setting['label']['identifier'], $setting['label']['component']),
            );
            $mform->setType($key, $setting['type']);
            if (isset($setting['help'])) {
                $mform->addHelpButton(
                    elementname: $key,
                    identifier: $setting['help']['identifier'],
                    component: $setting['help']['component'],
                    a: !empty($setting['help']['a']) ? $setting['help']['a'] : [],
                );
            }
            if (!empty($setting['required'])) {
                $mform->addRule($key, get_string('required'), 'required', null, 'client');
            }
        }
    }

    /**
     * Get the model type.
     *
     * @return int
     */
    public function model_type(): int {
        return $this->modeltype;
    }
}
