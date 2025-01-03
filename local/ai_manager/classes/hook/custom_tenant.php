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

namespace local_ai_manager\hook;

use local_ai_manager\local\tenant;

/**
 * Hook for customizing the tenant.
 *
 * This hook will be dispatched whenever local_ai_manager needs to access the tenant. Other plugins can use this hook to customize
 * the tenant which is being used.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins to customize the tenant being used by local_ai_manager.')]
#[\core\attribute\tags('local_ai_manager')]
class custom_tenant {

    /** @var \context the current context of the tenant */
    private \context $context;
    /** @var string the current fullname of the tenant */
    private string $fullname;

    /**
     * Constructor for the hook.
     *
     * @param tenant $tenant the tenant object
     */
    public function __construct(
        /** @var tenant The tenant instance */
            public readonly tenant $tenant
    ) {
        $this->context = $this->tenant->get_defaultcontext();
        $this->fullname = $this->tenant->get_defaultfullname();
    }

    /**
     * Get the current tenant identifier.
     *
     * @return string the tenant identifier
     */
    public function get_tenantidentifier(): string {
        return $this->tenant->get_identifier();
    }

    /**
     * Getter for the current tenant context.
     *
     * @return \context the context the tenant is associated with
     */
    public function get_tenant_context(): \context {
        return $this->context;
    }

    /**
     * Getter for the current tenant full name.
     *
     * @return string the full name of the tenant
     */
    public function get_fullname(): string {
        return $this->fullname;
    }

    /**
     * Getter for determining if the current tenant is the default tenant.
     *
     * @return bool true if the current tenant is the default tenant
     */
    public function is_default_tenant(): bool {
        return $this->tenant->is_default_tenant();
    }

    /**
     * Hook function for overriding the tenant context.
     *
     * The specified context will be used for setting the context of the management page as well as checking capabilities.
     *
     * @param \context $context the context to use for the tenant
     */
    public function set_tenant_context(\context $context): void {
        $this->context = $context;
    }

    /**
     * Hook function for overriding the displayname of the tenant.
     *
     * @param string $fullname
     */
    public function set_fullname(string $fullname): void {
        $this->fullname = $fullname;
    }
}
