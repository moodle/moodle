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
 * Usage reporter.
 *
 * @package    block_xp
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\plugin;

use block_xp\local\config\config;
use curl;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * Usage reporter class.
 *
 * @package    block_xp
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usage_reporter {

    /** @var config The config. */
    protected $config;
    /** @var usage_report_maker The maker. */
    protected $maker;

    /**
     * Constructor.
     *
     * @param config $config The config.
     * @param usage_report_maker $maker The usage report maker.
     */
    public function __construct(config $config, usage_report_maker $maker) {
        $this->config = $config;
        $this->maker = $maker;
    }

    /**
     * Make usage report.
     *
     * @return object Where keys represent usage.
     * @return bool Whether successful or not.
     */
    public function report() {
        $apiroot = rtrim($this->config->get('apiroot'), '/');
        $usage = $this->maker->make();

        $localsiteid = $this->config->get('usagereportid');
        if (!empty($localsiteid)) {
            $usage->local_site_id = $localsiteid;
        }

        $curl = new curl();
        $curl->setHeader(['Content-Type: application/json']);
        $resp = $curl->post($apiroot . '/v1/xp/usage', json_encode($usage));
        if ($curl->get_errno()) {
            return false;
        }

        $this->config->set('lastusagereport', time());
        $respdata = json_decode($resp);
        if ($respdata && !empty($respdata->local_site_id) && $respdata->local_site_id !== $localsiteid) {
            $this->config->set('usagereportid', $respdata->local_site_id);
        }
        return true;
    }

}
