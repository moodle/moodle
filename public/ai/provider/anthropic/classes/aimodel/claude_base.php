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

/**
 * Claude base AI model interface.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface claude_base {
    /** @var int DEFAULT_MAX_TOKENS The default max_tokens value used when a model does not override it. */
    public const DEFAULT_MAX_TOKENS = 8096;

    /**
     * Get the default max_tokens value for this model.
     *
     * The Anthropic Messages API requires max_tokens on every request, so models with a
     * lower output ceiling can override this.
     *
     * @return int The default maximum number of tokens to generate.
     */
    public function get_default_max_tokens(): int;

    /**
     * Whether this model accepts the temperature sampling parameter.
     *
     * Starting with Claude Opus 4.7, and spreading to Sonnet-class models from Claude
     * Sonnet 5, newer Claude models reject temperature (and top_p/top_k) with a 400 error.
     *
     * @return bool True if the model accepts a temperature value.
     */
    public function supports_temperature(): bool;
}
