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

namespace aiprovider_gemini\aimodel;

use core_ai\aimodel\base;

/**
 * Gemini 3.1 Flash Image AI model.
 *
 * @package    aiprovider_gemini
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gemini31flashimage extends base implements gemini_base {
    #[\Override]
    public function get_model_name(): string {
        return 'gemini-3.1-flash-image';
    }

    #[\Override]
    public function get_model_display_name(): string {
        return 'Gemini 3.1 Flash Image';
    }

    #[\Override]
    public function has_model_settings(): bool {
        return false;
    }

    #[\Override]
    public function model_type(): array {
        return [self::MODEL_TYPE_IMAGE];
    }

    #[\Override]
    public function get_model_endpoint(): string {
        return 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-image:generateContent';
    }
}
