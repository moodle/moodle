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

namespace mod_board\local;

use mod_board\board;
use stdClass;

/**
 * Template helper class.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class template {
    /**
     * Create template.
     *
     * @param stdClass $data
     * @return stdClass template record
     */
    public static function create(stdClass $data): stdClass {
        global $DB;

        $syscontext = \context_system::instance();
        $context = \context::instance_by_id($data->contextid ?? $syscontext->id);

        if (trim($data->name ?? '') === '') {
            throw new \core\exception\invalid_parameter_exception('name is required');
        }
        $name = \core_text::substr(trim($data->name), 0, 100);

        $description = $data->description_editor['text'] ?? $data->description ?? '';

        $columns = self::fix_columns($data->columns ?? '');

        $settings = [];
        foreach (self::get_all_settings() as $field => $setting) {
            if ($setting['type'] === 'select') {
                if (isset($data->$field)) {
                    $value = (string)$data->$field;
                    if ($value === '-1') {
                        continue;
                    }
                    if (isset($setting['options'][$value])) {
                        $settings[$field] = $value;
                    }
                }
            } else if ($setting['type'] === 'html') {
                $editor = $field . '_editor';
                if (isset($data->$editor) || isset($data->$field)) {
                    $value = ($data->$editor)['text'] ?? $data->$field;
                    if (!is_string($value) || trim($value) === '') {
                        continue;
                    }
                    $settings[$field] = $value;
                }
            }
        }

        $record = (object)[
            'name' => $name,
            'contextid' => $context->id,
            'description' => $description,
            'columns' => $columns,
            'jsonsettings' => json_encode($settings),
            'timecreated' => time(),
        ];

        $id = $DB->insert_record('board_templates', $record);

        return $DB->get_record('board_templates', ['id' => $id], '*', MUST_EXIST);
    }

    /**
     * Update template.
     *
     * @param stdClass $data
     * @return stdClass template record
     */
    public static function update(stdClass $data): stdClass {
        global $DB;

        $oldrecrod = $DB->get_record('board_templates', ['id' => $data->id], '*', MUST_EXIST);

        $record = (object)[
            'id' => $oldrecrod->id,
        ];

        if (property_exists($data, 'contextid')) {
            $context = \context::instance_by_id($data->contextid);
            $record->contextid = $context->id;
        }

        if (trim($data->name ?? '') === '') {
            throw new \core\exception\invalid_parameter_exception('name is required');
        }
        $record->name = \core_text::substr(trim($data->name), 0, 100);

        if (property_exists($data, 'description_editor')) {
            $record->description = $data->description_editor['text'];
        } else if (property_exists($data, 'description')) {
            $record->description = $data->description;
        }

        $record->columns = self::fix_columns($data->columns);

        $settings = [];
        foreach (self::get_all_settings() as $field => $setting) {
            if ($setting['type'] === 'select') {
                if (isset($data->$field)) {
                    $value = (string)$data->$field;
                    if ($value === '-1') {
                        continue;
                    }
                    if (isset($setting['options'][$value])) {
                        $settings[$field] = $value;
                    }
                }
            } else if ($setting['type'] === 'html') {
                $editor = $field . '_editor';
                if (isset($data->$editor) || isset($data->$field)) {
                    $value = ($data->$editor)['text'] ?? $data->$field;
                    if (!is_string($value) || trim($value) === '') {
                        continue;
                    }
                    $settings[$field] = $value;
                }
            }
        }

        $record->jsonsettings = json_encode($settings);

        $DB->update_record('board_templates', $record);

        return $DB->get_record('board_templates', ['id' => $oldrecrod->id], '*', MUST_EXIST);
    }

    /**
     * Delete template.
     *
     * @param int $id template it
     */
    public static function delete(int $id): void {
        global $DB;

        $record = $DB->get_record('board_templates', ['id' => $id]);
        if (!$record) {
            return;
        }

        $DB->delete_records('board_templates', ['id' => $record->id]);
    }

    /**
     * Fix columns value to be column names separated by "\n"
     *
     * NOTE: html tags are not allowed here
     *
     * @param string|null $columns
     * @return string
     */
    public static function fix_columns(?string $columns): string {
        if ($columns === null || trim($columns) === '') {
            return '';
        }
        $columns = str_replace("\r\n", "\n", $columns ?? '');
        $columns = str_replace("\r", '', $columns);
        $columns = explode("\n", $columns);
        $columns = array_map('trim', $columns);
        $columns = array_map('strip_tags', $columns);
        $columns = array_filter($columns, function ($v, $k) {
            return $v !== '';
        }, ARRAY_FILTER_USE_BOTH);
        return implode("\n", $columns);
    }

    /**
     * Format columns value for display.
     *
     * @param string $columns
     * @return string HTML text
     */
    public static function format_columns(string $columns): string {
        return str_replace("\n", '<br />', $columns);
    }

    /**
     * Returns list of context options for template creation or update.
     *
     * @param int $contextid current or default contextid
     * @return array
     */
    public static function get_context_menu(int $contextid): array {
        global $DB;

        $result = [];

        $syscontext = \context_system::instance();
        $result[$syscontext->id] = $syscontext->get_context_name(false);

        $sql = "SELECT ctx.id, cc.name
                  FROM {course_categories} cc
                  JOIN {context} ctx ON ctx.instanceid = cc.id AND ctx.contextlevel = :catlevel
                 WHERE cc.parent = 0
              ORDER BY cc.name ASC, cc.id ASC";
        $params = ['catlevel' => CONTEXT_COURSECAT];
        $cats = $DB->get_records_sql_menu($sql, $params);
        foreach ($cats as $cxtid => $catname) {
            $result[$cxtid] = format_string($catname);
        }

        if ($contextid && !isset($cats[$contextid])) {
            $context = \context::instance_by_id($contextid, IGNORE_MISSING);
            if ($context) {
                $result[$context->id] = $context->get_context_name(false);
            } else {
                $result[$contextid] = get_string('error');
            }
        }

        return $result;
    }

    /**
     * Returns list of all configurable board settings.
     *
     * NOTE: this does not include columns.
     *
     * @return array
     */
    public static function get_all_settings(): array {
        $result = [
            'intro' => [
                'name' => get_string('moduleintro'),
                'type' => 'html',
            ],
            'addrating' => [
                'name' => get_string('addrating', 'mod_board'),
                'type' => 'select',
                'options' => [
                    -1 => get_string('choosedots'),
                    board::RATINGDISABLED => get_string('addrating_none', 'mod_board'),
                    board::RATINGBYSTUDENTS => get_string('addrating_students', 'mod_board'),
                    board::RATINGBYTEACHERS => get_string('addrating_teachers', 'mod_board'),
                    board::RATINGBYALL => get_string('addrating_all', 'mod_board'),
                ],
            ],
            'hideheaders' => [
                'name' => get_string('hideheaders', 'mod_board'),
                'type' => 'select',
                'options' => [
                    -1 => get_string('choosedots'),
                    1 => get_string('yes'),
                    0 => get_string('no'),
                ],
            ],
            'sortby' => [
                'name' => get_string('sortby', 'mod_board'),
                'type' => 'select',
                'options' => [
                    -1 => get_string('choosedots'),
                    board::SORTBYNONE => get_string('sortbynone', 'mod_board'),
                    board::SORTBYDATE => get_string('sortbydate', 'mod_board'),
                    board::SORTBYRATING => get_string('sortbyrating', 'mod_board'),
                ],
            ],
            'singleusermode' => [
                'name' => get_string('singleusermode', 'mod_board'),
                'type' => 'select',
                'options' => [
                    -1 => get_string('choosedots'),
                    board::SINGLEUSER_DISABLED => get_string('singleusermodenone', 'mod_board'),
                    board::SINGLEUSER_PRIVATE => get_string('singleusermodeprivate', 'mod_board'),
                    board::SINGLEUSER_PUBLIC => get_string('singleusermodepublic', 'mod_board'),
                ],
            ],
            'userscanedit' => [
                'name' => get_string('userscanedit', 'mod_board'),
                'type' => 'select',
                'options' => [
                    -1 => get_string('choosedots'),
                    1 => get_string('yes'),
                    0 => get_string('no'),
                ],
            ],
            'enableblanktarget' => [
                'name' => get_string('enableblanktarget', 'mod_board'),
                'type' => 'select',
                'options' => [
                    -1 => get_string('choosedots'),
                    1 => get_string('yes'),
                    0 => get_string('no'),
                ],
            ],
            'embed' => [
                'name' => get_string('embedboard', 'mod_board'),
                'type' => 'select',
                'options' => [
                    -1 => get_string('choosedots'),
                    1 => get_string('yes'),
                    0 => get_string('no'),
                ],
            ],
            'hidename' => [
                'name' => get_string('hidename', 'mod_board'),
                'type' => 'select',
                'options' => [
                    -1 => get_string('choosedots'),
                    1 => get_string('yes'),
                    0 => get_string('no'),
                ],
            ],
        ];

        // Only add the embed setting, if embedding is allowed globally.
        if (!get_config('mod_board', 'embed_allowed')) {
            unset($result['embed']);
            unset($result['hidename']);
        }

        // Ignore disallowed single user modes.
        $modes = get_config('mod_board', 'allowed_singleuser_modes');
        if ($modes !== false) {
            [$allowprivate, $allowpublic] = str_split(get_config('mod_board', 'allowed_singleuser_modes'));
            if (!$allowprivate) {
                unset($result['singleusermode']['options'][board::SINGLEUSER_PRIVATE]);
            }
            if (!$allowpublic) {
                unset($result['singleusermode']['options'][board::SINGLEUSER_PUBLIC]);
            }
        }

        return $result;
    }

    /**
     * Returns values of supported template settings with a valid value.
     *
     * @param string $jsonsettings
     * @return array [field => value]
     */
    public static function get_settings(string $jsonsettings): array {
        $allsettings = self::get_all_settings();
        $settings = json_decode($jsonsettings);
        $result = [];
        foreach ($allsettings as $field => $setting) {
            if (!isset($settings->$field)) {
                continue;
            }
            $value = (string)$settings->$field;
            if ($setting['type'] === 'select') {
                if ($value === '-1') {
                    continue;
                }
                if (isset($setting['options'][$value])) {
                    $result[$field] = $value;
                }
            } else if ($setting['type'] === 'html') {
                if (trim($value) === '') {
                    continue;
                }
                $result[$field] = clean_text($value);
            }
        }

        return $result;
    }

    /**
     * Format template settings for display.
     *
     * @param string $jsonsettings
     * @return string html markup
     */
    public static function format_settings(string $jsonsettings): string {
        $allsettings = self::get_all_settings();
        $settings = json_decode($jsonsettings);
        $result = [];
        foreach ($allsettings as $field => $setting) {
            if (!isset($settings->$field)) {
                continue;
            }
            $value = (string)$settings->$field;
            if ($setting['type'] === 'select') {
                if ($value === '-1') {
                    continue;
                }
                if (isset($setting['options'][$value])) {
                    $result[] = $setting['name'] . ': ' . $setting['options'][$value];
                }
            } else if ($setting['type'] === 'html') {
                if (trim($value) === '') {
                    continue;
                }
                $result[] = $setting['name'] . ': ' . clean_text(strip_tags(shorten_text($value)));
            }
        }
        return implode('<br />', $result);
    }

    /**
     * Returns export template file name.
     *
     * @param stdClass $template
     * @return string string
     */
    public static function get_export_filename(stdClass $template): string {
        $name = \core_text::strtolower($template->name);
        $name = str_replace(' ', '_', $name);
        $name = str_replace('.', '_', $name);
        return clean_filename('board_' . $name . '.json');
    }

    /**
     * Returns template data for export.
     *
     * @param stdClass $template
     * @return string JSON string
     */
    public static function get_export_json(stdClass $template): string {
        $result = [
            'name' => $template->name,
            'description' => $template->description,
            'columns' => $template->columns,
        ];
        $result = $result + self::get_settings($template->jsonsettings);

        return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Decode JSON file and validate it.
     *
     * @param string $content
     * @return stdClass|null null means error, template with expanded settings if data ok
     */
    public static function decode_import_file(string $content): ?stdClass {
        try {
            $data = json_decode($content, false, 10, JSON_THROW_ON_ERROR);
        } catch (\Exception $ex) {
            return null;
        }

        if (!isset($data->name) || !isset($data->description)) {
            return null;
        }

        // Sanitise data from the template file.
        $data = fix_utf8($data);

        $template = new stdClass();
        $template->name = clean_text(strip_tags($data->name));
        $template->description = clean_text($data->description);
        $template->columns = self::fix_columns($data->columns ?? '');

        foreach (self::get_all_settings() as $field => $setting) {
            if (!isset($data->$field)) {
                continue;
            }
            $value = (string)$data->$field;
            if ($setting['type'] === 'select') {
                if ($value === '-1') {
                    continue;
                }
                if (isset($setting['options'][$value])) {
                    $template->$field = $value;
                }
            } else if ($setting['type'] === 'html') {
                if (trim($value) === '') {
                    continue;
                }
                $template->$field = clean_text($value);
            }
        }

        return $template;
    }

    /**
     * Fetch list of templates that can be applied in given context.
     *
     * @param \context $context course or activity context
     * @return array
     */
    public static function get_applicable_templates(\context $context): array {
        global $DB;

        $contextids = $context->get_parent_context_ids();
        $contextids = array_slice($contextids, -2);

        [$select, $params] = $DB->get_in_or_equal($contextids);
        $sql = "SELECT t.id, t.name
                  FROM {board_templates} t
                 WHERE t.contextid $select
              ORDER BY t.name ASC, t.id ASC";
        return $DB->get_records_sql_menu($sql, $params);
    }

    /**
     * Apply a template to existing board.
     *
     * @param int $boardid
     * @param int $templateid
     * @return stdClass board record
     */
    public static function apply(int $boardid, int $templateid): stdClass {
        global $DB;

        $board = board::get_board($boardid, MUST_EXIST);
        $template = $DB->get_record('board_templates', ['id' => $templateid], '*', MUST_EXIST);
        $cm = board::coursemodule_for_board($board);
        $context = board::context_for_board($board);

        if (board::board_has_notes($board->id)) {
            throw new \core\exception\invalid_parameter_exception('board already has notes, cannot apply template');
        }

        $trans = $DB->start_delegated_transaction();

        if ($template->columns !== '') {
            $records = $DB->get_records('board_columns', ['boardid' => $board->id], 'sortorder ASC');
            $records = array_values($records);
            $columns = explode("\n", $template->columns);
            foreach ($columns as $i => $columnname) {
                if (isset($records[$i])) {
                    $record = $records[$i];
                    column::update($record->id, $columnname);
                    unset($records[$i]);
                } else {
                    column::create($board->id, $columnname);
                }
            }
            // Delete any extra columns.
            foreach ($records as $record) {
                column::delete($record->id);
            }
        }

        $settings = self::get_settings($template->jsonsettings);
        if ($settings) {
            $settings['id'] = $board->id;
            if (isset($settings['intro'])) {
                // Any preexisting images will be kept, but not shown.
                $settings['introformat'] = FORMAT_HTML;
            }
            $DB->update_record('board', $settings);
        }

        $trans->allow_commit();

        // This is all a bit hacky, so let's at least trigger event here correctly.
        $cm->name = $board->name;
        \core\event\course_module_updated::create_from_cm($cm, $context)->trigger();

        return board::get_board($board->id, MUST_EXIST);
    }
}
