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

namespace aiprovider_anthropic\aimodel;

use core_ai\aimodel\base;

/**
 * Claude Opus 5 AI model.
 *
 * Anthropic's current flagship model for complex agentic and reasoning tasks.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class claudeopus5 extends base implements claude_base {
    use claude_model_trait;

    #[\Override]
    public function get_model_name(): string {
        return 'claude-opus-5';
    }

    #[\Override]
    public function get_model_display_name(): string {
        return 'Claude Opus 5';
    }
}
