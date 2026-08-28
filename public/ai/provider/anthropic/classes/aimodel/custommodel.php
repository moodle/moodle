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
 * Custom (unlisted) Claude AI model.
 *
 * Backs the "Custom" option in the model selector, letting an admin point an action at a
 * Claude model that this plugin does not bundle - for example a model released after this
 * version of the plugin. It also provides the generation settings for any stored model name
 * that no bundled model class matches.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custommodel extends base implements claude_base {
    use claude_model_trait;

    /** @var string MODEL_NAME The selector value that means "an admin-entered model name". */
    public const MODEL_NAME = 'custom';

    #[\Override]
    public function get_model_name(): string {
        return self::MODEL_NAME;
    }

    #[\Override]
    public function get_model_display_name(): string {
        return get_string('custom', 'core_form');
    }

    /**
     * Whether this model accepts the temperature sampling parameter.
     *
     * Unlike the bundled models, the capabilities of an admin-entered model are unknown to
     * this plugin, so the field is offered and the admin decides. Leaving it empty - the
     * default - sends no temperature at all, which is safe on every Claude model.
     *
     * @return bool True, so the temperature field is offered.
     */
    #[\Override]
    public function supports_temperature(): bool {
        return true;
    }
}
