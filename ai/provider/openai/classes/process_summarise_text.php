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

namespace aiprovider_openai;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Class process text summarisation.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_summarise_text extends process_generate_text {
    #[\Override]
    protected function get_endpoint(): UriInterface {
        return new Uri(get_config('aiprovider_openai', 'action_summarise_text_endpoint'));
    }

    #[\Override]
    protected function get_model(): string {
        return get_config('aiprovider_openai', 'action_summarise_text_model');
    }

    #[\Override]
    protected function get_system_instruction(): string {
        return get_config('aiprovider_openai', 'action_summarise_text_systeminstruction');
    }
}
