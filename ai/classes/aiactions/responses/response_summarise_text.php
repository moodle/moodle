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

namespace core_ai\aiactions\responses;

use core_ai\aiactions\responses\response_base;

/**
 * Summarise text action response class.
 * Any method that processes an action must return an instance of this class.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response_summarise_text extends response_base {

    /** @var string|null A unique identifier for the chat completion, returned by the AI. */
    private ?string $id = null;

    /** @var string|null This fingerprint represents the backend configuration that the model runs with. */
    private ?string $fingerprint = null;

    /** @var string|null The contents of the generated message. */
    private ?string $generatedcontent = null;

    /** @var string|null The reason the model stopped generating tokens. */
    private ?string $finishreason = null;

    /** @var string|null Number of tokens in the prompt. */
    private ?string $prompttokens = null;

    /** @var string|null Number of tokens in the generated completion. */
    private ?string $completiontokens = null;

    /**
     * Set the response data returned by the AI provider.
     *
     * @param array $response The response data returned by the AI provider.
     * @return void
     */
    public function set_response(array $response): void {
        $this->id = $response['id'] ?? null;
        $this->fingerprint = $response['fingerprint'] ?? null;
        $this->generatedcontent = $response['generatedcontent'] ?? null;
        $this->finishreason = $response['finishreason'] ?? null;
        $this->prompttokens = $response['prompttokens'] ?? null;
        $this->completiontokens = $response['completiontokens'] ?? null;
    }

    /**
     * Get the response data returned by the AI provider.
     *
     * @return array
     */
    public function get_response(): array {
        return [
            'id' => $this->id,
            'fingerprint' => $this->fingerprint,
            'generatedcontent' => $this->generatedcontent,
            'finishreason' => $this->finishreason,
            'prompttokens' => $this->prompttokens,
            'completiontokens' => $this->completiontokens,
        ];
    }
}
