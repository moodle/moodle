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
 * AI data generator for tests.
 *
 * @package    core_ai
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_ai_generator extends component_generator_base {

    /**
     * Creates AI action registry records.
     *
     * @param array $data
     */
    public function create_ai_actions(array $data): void {
        global $DB;

        if (!isset($data['actionname'])) {
            throw new Exception('\'ai actions\' requires the field \'actionname\' to be specified');
        }
        if (!isset($data['success'])) {
            throw new Exception('\'ai actions\' requires the field \'success\' to be specified');
        }
        if (!isset($data['userid'])) {
            throw new Exception('\'ai actions\' requires the field \'user\' to be specified');
        }
        if (!isset($data['contextid'])) {
            throw new Exception('\'ai actions\' requires the field \'contextid\' to be specified');
        }
        if (!isset($data['provider'])) {
            throw new Exception('\'ai actions\' requires the field \'provider\' to be specified');
        }

        // Create the child action record.
        $child = new stdClass();
        $child->prompt = 'Prompt text';

        // Generate image actions need to be structured differently.
        if ($data['actionname'] === 'generate_image') {
            $child->numberimages = 1;
            $child->quality = 'hd';
            $child->aspectratio = 'landscape';
            $child->style = 'vivid';
            $child->sourceurl = 'http://localhost/yourimage';
            $child->revisedprompt = 'Revised prompt';
        } else {
            // Generate text (and variants).
            $child->generatedcontent = 'Your generated content';
            $child->prompttokens = $data['prompttokens'] ?? 111;
            $child->completiontoken = $data['completiontokens'] ?? 222;
        }

        // Simulate an error.
        if ($data['success'] == 0) {
            $data['errorcode'] = 403;
            $data['errormessage'] = 'Forbidden';
            // Unset some values that won't be present with an error.
            unset($child->generatedcontent);
            unset($child->revisedprompt);
            unset($child->prompttokens);
            unset($child->completiontoken);
        }

        $childid = $DB->insert_record("ai_action_{$data['actionname']}", $child);

        // Finalise some fields before inserting.
        $data['actionid'] = $childid;
        $data['timecreated'] = time();
        $data['timecompleted'] = time() + 1;
        $DB->insert_record('ai_action_register', $data);
    }

    /**
     * Creates AI provider instance.
     *
     * @param array $data
     * @return void
     */
    public function create_ai_provider(array $data) {
        $manager = \core\di::get(\core_ai\manager::class);
        $classname = $data['provider'] . '\\' . 'provider';
        $name = $data['name'];
        $enabled = $data['enabled'];
        unset($data['provider'], $data['name'], $data['enabled']);
        $manager->create_provider_instance(
            classname: $classname,
            name: $name,
            enabled: $enabled,
            config: $data,
        );
    }
}
