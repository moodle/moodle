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

declare(strict_types=1);

namespace customfield_number\hook;

use customfield_number\provider_base;
use customfield_number\field_controller;

/**
 * Hook for adding custom providers to the provider_base.
 *
 * @package    customfield_number
 * @copyright  2024 Ilya Tregubov <ilya.tregubov@proton.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('This hook allows adding custom providers to calculate custom field automatically like price for course')]
class add_custom_providers {

    /** @var provider_base[] $providers */
    protected array $providers = [];

    /**
     * Constructor.
     *
     * @param field_controller $field the custom field controller
     */
    public function __construct(
        /** @var field_controller the custom field controller */
        public readonly field_controller $field,
    ) {
    }

    /**
     * Add a provider to the hook.
     *
     * @param provider_base $provider
     */
    public function add_provider(provider_base $provider): void {
        $this->providers[] = $provider;
    }

    /**
     * Get the list of providers added through the hook.
     *
     * @return provider_base[]
     */
    public function get_providers(): array {
        return $this->providers;
    }
}
