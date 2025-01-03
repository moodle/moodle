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

namespace local_ai_manager\local;

use local_ai_manager\base_instance;
use local_ai_manager\hook\custom_tenant;

/**
 * Class for managing the configuration of tenants.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access_manager {

    /**
     * Creates the access_manager object.
     *
     * @param tenant $tenant the tenant the access manager should use
     */
    public function __construct(
        /** @var tenant $tenant the tenant the access manager should use */
            private readonly tenant $tenant
    ) {
    }

    /**
     * Requires the current user to be a manager of the current tenant.
     *
     * @throws \moodle_exception in case of the current user does not have sufficient permissions for managing the current tenant
     */
    public function require_tenant_manager(): void {
        if (!$this->is_tenant_manager()) {
            // phpcs:disable moodle.Commenting.TodoComment.MissingInfoInline
            // TODO Make a clean require_capability_exception out of this.
            // phpcs:enable moodle.Commenting.TodoComment.MissingInfoInline
            throw new \moodle_exception('exception_notenantmanagerrights', 'local_ai_manager');
        }
    }

    /**
     * Determines if a user is a tenant manager.
     *
     * @param int $userid the user id of the user to check, or empty/0 if current user should be used
     * @param ?tenant $tenant the tenant to use, if not passed or null the currently used tenant is being used
     * @return bool true if the current user is a tenant manager
     */
    public function is_tenant_manager(int $userid = 0, ?tenant $tenant = null): bool {
        global $USER;

        if (empty($userid)) {
            $userid = $USER->id;
        }
        $capabilities = [
            'local/ai_iomad_manager:manage',
            'local/ai_manager:manage'
        ];
        if (has_capability('local/ai_manager:managetenants', \context_system::instance(), $userid)) {
            return true;
        }
        if (is_null($tenant)) {
            $tenant = $this->tenant;
        }

        $customtenant = new custom_tenant($tenant);
        \core\di::get(\core\hook\manager::class)->dispatch($customtenant);

        // In case of default tenant we get system context here, admin should have all capabilities, so we need no admin check.
        $tenantcontext = $tenant->get_context();

        $user = empty($userid) ? $USER : \core_user::get_user($userid);
        if ($tenantcontext === \context_system::instance()) {
            $tenantfield = get_config('local_ai_manager', 'tenantcolumn');
            return $this->check_tenant_allowed($capabilities, $tenantcontext, $user, $tenant, $tenantfield);
        }
        return $this->check_tenant_allowed($capabilities, $tenantcontext, $user, $tenant);
    }

    private function check_tenant_allowed(array $capabilities, $tenantcontext, $user, $tenant, $tenantfield = null): bool {
        foreach ($capabilities as $capability) {
            if ($tenantfield !== null) {
                if ((has_capability($capability, $tenantcontext, $user) && $tenant->is_tenant_allowed()
                    && $user->{$tenantfield} === $tenant->get_sql_identifier()) || $this->hasIOMADManagerAccess()) {
                    return true;
                    break;
                }
            } else {
                if (has_capability($capability, $tenantcontext, $user) && $tenant->is_tenant_allowed() || $this->hasIOMADManagerAccess()) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    public function check_tenant_capability(array $capabilities, $tenantcontext, $user): bool {
        foreach ($capabilities as $capability) {
            if (has_capability($capability, $tenantcontext, $user)) {
                return true;
                break;
            }
        }
        return false;
    }

    public function hasIOMADManagerAccess() {
        global $USER;
        $usercompany = \company::get_company_byuserid($USER->id);
        $parentcompany = new \company($usercompany->id);
        $result = $parentcompany->get_company_managers();
        return array_key_exists($USER->id, $result);
    }

    /**
     * Requires the current user to be a member of the currently set tenant.
     *
     * @throws \moodle_exception if the tenant is not allowed or the user must not use this tenant
     */
    public function require_tenant_member(): void {
        global $USER;
        if (!$this->tenant->is_tenant_allowed()) {
            throw new \moodle_exception('exception_tenantnotallowed', 'local_ai_manager');
        }
        if ($this->tenant->is_default_tenant() && has_capability('local/ai_manager:use', $this->tenant->get_context())) {
            return;
        }

        $customtenant = new custom_tenant($this->tenant);
        \core\di::get(\core\hook\manager::class)->dispatch($customtenant);

        $tenantfield = get_config('local_ai_manager', 'tenantcolumn');
        if (empty($USER->{$tenantfield}) || $USER->{$tenantfield} !== $this->tenant->get_sql_identifier()) {
            $usercompany = \company::get_company_byuserid($USER->id);
            if($usercompany->code !== $this->tenant->get_sql_identifier()){
                throw new \moodle_exception('exception_tenantaccessdenied', 'local_ai_manager', '', $this->tenant->get_identifier());
            }
        }

    }

    /**
     * Helper function to determine if the current user has the capability to manage a connector instance.
     *
     * @param base_instance $instance The connector instance the capability should be checked for
     * @return bool true if the current user is allowed to manage the instance
     */
    public function can_manage_connectorinstance(base_instance $instance) {
        global $USER;
        if (has_capability('local/ai_manager:managetenants', \context_system::instance())) {
            return true;
        }
        if ($this->is_tenant_manager($USER->id, new tenant($instance->get_tenant()))) {
            return $this->check_tenant_capability(['local/ai_iomad_manager:manage', 'local/ai_manager:manage'], $this->tenant->get_context(), $USER) || $this->hasIOMADManagerAccess();
            // return has_capability('local/ai_iomad_manager:manage', $this->tenant->get_context());
        }
        return false;
    }
}
