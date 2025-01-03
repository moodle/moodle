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

use local_ai_manager\hook\custom_tenant;

/**
 * Data object class for handling usage information when using an AI tool.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tenant {

    /** @var string identifier of the default tenant */
    public const DEFAULT_IDENTIFIER = 'default';

    /** @var string The identifier of the current tenant */
    private string $identifier;

    /**
     * Tenant class constructor.
     *
     * @param string $identifier the tenant identifier; if left empty, the default tenant is being used
     */
    public function __construct(string $identifier = '') {
        global $USER;
        if (empty($identifier)) {
            $tenantfield = get_config('local_ai_manager', 'tenantcolumn');
            $identifier = !empty($USER->{$tenantfield}) ? $USER->{$tenantfield} : '';
            if (empty($identifier)) {
                $usercompany = \company::get_company_byuserid($USER->id);
                $identifier = $usercompany->shortname ?? self::DEFAULT_IDENTIFIER;
            }
        }

        error_log("identifier " . json_encode($identifier));
        $this->identifier = $identifier;
    }

    /**
     * Get the tenant identifier.
     *
     * @return string the tenant identifier
     */
    public function get_identifier(): string {
        return $this->identifier;
    }

    /**
     * Returns the identifier which needs to be used in SQL statements.
     *
     * @return string the tenant identifier for SQL statements
     */
    public function get_sql_identifier(): string {
        return $this->is_default_tenant() ? '' : $this->identifier;
    }

    /**
     * Returns if the current tenant of this object is the default tenant.
     *
     * @return bool true if this tenant is the default tenant
     */
    public function is_default_tenant(): bool {
        return $this->identifier === self::DEFAULT_IDENTIFIER;
    }

    /**
     * Get the tenant context.
     *
     * @return \context the context the tenant is associated with
     */
    public function get_context(): \context {
        $customtenant = new custom_tenant($this);
        \core\di::get(\core\hook\manager::class)->dispatch($customtenant);
        return $customtenant->get_tenant_context();
    }

    /**
     * Returns if the tenant is allowed.
     *
     * In this context "allowed" means that the tenant is not being restricted by an admin setting.
     *
     * @return bool true if the tenant is allowed
     */
    public function is_tenant_allowed(): bool {
        global $USER;
        $restricttenants = !empty(get_config('local_ai_manager', 'restricttenants'));
        if (!$restricttenants) {
            return true;
        }
        $allowedtenantsconfig = get_config('local_ai_manager', 'allowedtenants');
        $allowedtenantsconfig = explode(PHP_EOL, $allowedtenantsconfig);

        $allIOMADCompanies = \company::get_company_byuserid($USER->id);
        if ($allIOMADCompanies->code) {
            $allowedtenantsconfig[] = $allIOMADCompanies->code;
        }
        foreach ($allowedtenantsconfig as $tenant) {
            if ($this->get_identifier() === trim($tenant)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Getter for retrieving/calculating the display name of the tenant.
     *
     * @return string the display name of this tenant
     */
    public function get_fullname(): string {
        $customtenant = new custom_tenant($this);
        \core\di::get(\core\hook\manager::class)->dispatch($customtenant);
        return $customtenant->get_fullname();
    }

    /**
     * Returns the default context for a tenant which is the system context.
     *
     * @return \context the default context of a tenant
     */
    public function get_defaultcontext(): \context {
        return \context_system::instance();
    }

    /**
     * Returns the default fullname for a tenant if not customized.
     * @return string the default tenant fullname
     */
    public function get_defaultfullname(): string {
        return $this->identifier === self::DEFAULT_IDENTIFIER
                ? get_string('defaulttenantname', 'local_ai_manager')
                : $this->identifier;
    }
}
