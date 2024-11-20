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

/**
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../local/framework_selector/lib.php');

/**
 * base class for selecting frameworks of a company
 */
abstract class company_framework_selector_base extends framework_selector_base {

    protected $companyid;

    //overridden to include the sortorder field
    protected $requiredfields = array('id', 'shortname');

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['file']    = 'blocks/iomad_company_admin/lib/framework_selectors.php';
        return $options;
    }
}

class current_company_frameworks_selector extends company_framework_selector_base {
    /**
     * Company frameworks
     * @param <type> $search
     * @return array
     */
    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];

        // Default for shared is true.
        if (isset($options['shared'])) {
            $this->shared = $options['shared'];
        } else {
            $this->shared = true;
        }
        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['file']    = 'blocks/iomad_company_admin/lib/framework_selectors.php';
        $options['shared'] = $this->shared;
        return $options;
    }

    public function find_frameworks($search) {
        global $CFG, $DB;
        // By default wherecondition retrieves all frameworks except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'cf');
        $params['companyid'] = $this->companyid;
        $fields      = 'SELECT DISTINCT ' . $this->required_fields_sql('cf');
        $countfields = 'SELECT COUNT(1)';


        // Deal with shared frameworks.
        if ($this->shared) {
            $sharedsql = " FROM {competency_framework} cf
                           INNER JOIN {iomad_frameworks} if
                           ON cf.id=if.frameworkid
                           WHERE if.shared = 1";
        } else {
            $sharedsql = " FROM {competency_framework} cf WHERE 1 = 2";
        }
        $sql = " FROM {competency_framework} cf
                INNER JOIN {company_comp_frameworks} ccf ON (cf.id = ccf.frameworkid AND ccf.companyid = :companyid)
                WHERE $wherecondition";

        $order = ' ORDER BY cf.shortname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params) +
                                     $DB->count_records_sql($countfields . $sharedsql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_frameworks) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableframeworks = $DB->get_records_sql($fields . $sql . $order, $params) +
                            $DB->get_records_sql($fields . $sharedsql . $order, $params);

        if (empty($availableframeworks)) {
            return array();
        }

        // Set up empty return.
        $frameworkarray = array();
        if (!empty($availableframeworks)) {
            if ($search) {
                $groupname = get_string('currcompanyframeworksmatching', 'block_iomad_company_admin', $search);
            } else {
                $groupname = get_string('currcompanyframeworks', 'block_iomad_company_admin');
            }
            $frameworkarray[$groupname] = $availableframeworks;
        }

        return $frameworkarray;
    }
}


class potential_company_frameworks_selector extends company_framework_selector_base {
    /**
     * Potential company manager frameworks
     * @param <type> $search
     * @return array
     */
    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];

        if (!empty($options['shared'])) {
            $this->shared = $options['shared'];
        } else {
            $this->shared = false;
        }
        if (!empty($options['partialshared'])) {
            $this->partialshared = $options['partialshared'];
        } else {
            $this->partialshared = false;
        }
        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['file']    = 'blocks/iomad_company_admin/lib/framework_selectors.php';
        $options['shared'] = $this->shared;
        $options['partialshared'] = $this->partialshared;
        return $options;
    }

    public function find_frameworks($search) {
        global $CFG, $DB, $SITE;
        // By default wherecondition retrieves all frameworks except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'cf');
        $params['companyid'] = $this->companyid;

        // Deal with shared frameworks.  Cannot be added to a company in this manner.
        $sharedsql = " AND cf.id NOT IN (
                         SELECT frameworkid FROM {iomad_frameworks}
                         WHERE shared = 1 )
                        AND cf.id NOT IN (
                         SELECT frameworkid FROM {company_comp_frameworks}
                         WHERE companyid = :companyid)";

        $fields      = 'SELECT ' . $this->required_fields_sql('cf');
        $countfields = 'SELECT COUNT(1)';

        $distinctfields      = 'SELECT DISTINCT cf.id,' . $this->required_fields_sql('cf');
        $distinctcountfields = 'SELECT COUNT(DISTINCT cf.id) ';

        $sqldistinct = " FROM {competency_framework} cf
                        WHERE $wherecondition
                        $sharedsql";

        $sql = " FROM {competency_framework} cf
                 WHERE $wherecondition
                 $sharedsql";

        $order = ' ORDER BY cf.shortname ASC';
        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params) +
            $DB->count_records_sql($distinctcountfields . $sqldistinct, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_frameworks) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $allframeworks = $DB->get_records_sql($fields . $sql . $order, $params) +
        $DB->get_records_sql($distinctfields . $sqldistinct . $order, $params);

        // Only show one list of frameworks
        $availableframeworks = array();
        foreach ($allframeworks as $framework) {
            $availableframeworks[$framework->id] = $framework;
        }

        if (empty($availableframeworks)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potframeworksmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('potframeworks', 'block_iomad_company_admin');
        }

        return array($groupname => $availableframeworks);
    }
}

