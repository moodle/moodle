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
 * Imagen 4.0 Generate 001 AI model.
 *
 * @package    aiprovider_gemini
 * @copyright  2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class imagen40generate001 extends base implements gemini_base {
    #[\Override]
    public function get_model_name(): string {
        return 'imagen-4.0-generate-001';
    }

    #[\Override]
    public function get_model_display_name(): string {
        return 'Imagen 4.0 Generate 001';
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
        return 'https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-generate-001:predict';
    }
}
