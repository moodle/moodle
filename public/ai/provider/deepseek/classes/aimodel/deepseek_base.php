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

namespace aiprovider_deepseek\aimodel;

/**
 * DeepSeek base AI model interface.
 *
 * @package    aiprovider_deepseek
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface deepseek_base {
    /** @var int MODEL_TYPE_TEXT Text model type. */
    public const MODEL_TYPE_TEXT = 1;

    /**
     * Get model type.
     *
     * @return int Model type.
     */
    public function model_type(): int;
}
