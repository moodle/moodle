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

namespace local_ai_manager\output;

use local_ai_manager\base_purpose;
use renderable;
use renderer_base;
use stdClass;

/**
 * Navbar for tenant config pages.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tenantnavbar implements renderable, \templatable {

    /**
     * Constructor.
     *
     * @param string $relativeactiveurl the base url (without parameters) relative to '/local/ai_manager/'
     *   which should be shown as active, for example 'tenant_config.php' or 'purpose_config.php'
     *   or 'statistics.php?purpose=chat'
     *
     */
    public function __construct(
            /**
             * @var string $relativeactiveurl the base url (without parameters) relative to '/local/ai_manager/'
             *    which should be shown as active, for example 'tenant_config.php' or 'purpose_config.php'
             *    or 'statistics.php?purpose=chat'
             */
            private string $relativeactiveurl
    ) {
    }

    #[\Override]
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $tenant = \core\di::get(\local_ai_manager\local\tenant::class);
        $data->tenantidentifier = $tenant->get_identifier();

        $data->homeactive = $this->relativeactiveurl === 'tenant_config.php';
        $data->purposeconfigactive = $this->relativeactiveurl === 'purpose_config.php';
        $data->quotaconfigactive = $this->relativeactiveurl === 'quota_config.php';
        $data->rightsconfigactive = $this->relativeactiveurl === 'rights_config.php';
        $data->statisticsoverviewactive = $this->relativeactiveurl === 'statistics.php';

        $data->showstatistics = has_capability('local/ai_manager:viewstatistics', $tenant->get_context());
        $data->showuserstatistics = has_capability('local/ai_manager:viewuserstatistics', $tenant->get_context());
        $statisticspurposes = [];
        foreach (base_purpose::get_all_purposes() as $purpose) {
            $statisticspurposes[] = [
                    'pluginname' => $purpose,
                    'fullname' => get_string('pluginname', 'aipurpose_' . $purpose),
                    'active' => $this->relativeactiveurl === 'statistics.php?purpose=' . $purpose,
            ];
        }
        $data->statisticspurposes = $statisticspurposes;

        $data->userconfigactive = $data->quotaconfigactive || $data->rightsconfigactive;
        $data->statisticsactive = $data->statisticsoverviewactive
                || array_reduce($statisticspurposes, fn($current, $node) => $current || $node['active'], false);

        return $data;
    }
}
