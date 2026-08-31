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

namespace core\reportbuilder\local\systemreports;

use core\api\entity\api_token_entity;
use core\reportbuilder\local\entities\api_token;
use core\url;
use core_reportbuilder\output\report_action;
use core_reportbuilder\system_report;
use lang_string;

/**
 * Lists the personal access tokens belonging to the current user.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_tokens extends system_report {
    #[\Override]
    protected function initialise(): void {
        global $USER;

        $entity = new api_token();
        $alias = $entity->get_table_alias('rest_api_tokens');

        $this->set_main_table('rest_api_tokens', $alias);
        $this->add_entity($entity);

        // The report only ever shows the viewer their own tokens. This is what enforces that, not
        // the page which happens to embed it.
        $this->add_base_condition_simple("{$alias}.userid", $USER->id);

        // Nothing in core revokes a personal access token, and this interface deletes rather than
        // revokes. Excluding them means a revoked row can never be presented as a live one.
        $this->add_base_condition_simple("{$alias}.revoked", api_token_entity::REVOKED_NO);

        // Selected unconditionally: the actions substitute :id into their links, and choose
        // between revoking and deleting based on whether the token has lapsed.
        $this->add_base_fields("{$alias}.id, {$alias}.name, {$alias}.expirytime");

        $this->add_columns_from_entities([
            'api_token:name',
            'api_token:scopes',
            'api_token:status',
            'api_token:timecreated',
            'api_token:expirytime',
            'api_token:lastaccess',
            'api_token:actions',
        ]);

        $this->add_filters_from_entities([
            'api_token:name',
            'api_token:status',
            'api_token:expirytime',
            'api_token:lastaccessed',
        ]);

        // Placed through the report's own action slot, which renders it to the left of Filters.
        $this->set_report_action(new report_action(
            get_string('pat_create'),
            [
                'href' => (new url('/user/personalaccesstokens.php', ['action' => 'create']))->out(false),
                'class' => 'btn btn-primary',
            ],
            'a',
        ));

        $this->set_default_no_results_notice(new lang_string('pat_none'));
        $this->set_initial_sort_column('api_token:name', SORT_ASC);
        $this->set_downloadable(false);
    }

    /**
     * Only the owner of the tokens may see them, and only where tokens are theirs to create.
     *
     * @return bool
     */
    #[\Override]
    protected function can_view(): bool {
        return has_capability('moodle/api:createtoken', \core\context\system::instance());
    }
}
