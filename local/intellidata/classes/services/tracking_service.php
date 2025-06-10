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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\services;

use local_intellidata\helpers\PageParamsHelper;
use local_intellidata\helpers\UserAccessHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\repositories\tracking\tracking_repository;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class tracking_service {

    /** @var tracking_repository|null */
    protected $repo = null;
    /** @var bool */
    public $enabled = false;
    /** @var int */
    public $ajaxfrequency = 30;
    /** @var int */
    public $inactivity = 0;
    /** @var bool */
    public $trackadmin = false;
    /** @var bool */
    public $mediatrack = false;
    /** @var string */
    public $path = '';
    /** @var bool */
    public $ajaxrequest = false;
    /** @var string */
    public $trackparameters = '';
    /** @var bool */
    public $trackable = false;
    /** @var null|array */
    public $pageparams = null;

    /**
     * Main method for tracking service.
     *
     * @param false $ajaxrequest
     * @param array $trackparameters
     * @throws \dml_exception
     */
    public function __construct($ajaxrequest = false, $trackparameters = []) {

        // Get plugin config.
        $this->enabled          = SettingsHelper::get_setting('enabledtracking');
        $this->ajaxfrequency    = (int) SettingsHelper::get_setting('ajaxfrequency');
        $this->inactivity       = (int) SettingsHelper::get_setting('inactivity');
        $this->trackadmin       = SettingsHelper::get_setting('trackadmin');
        $this->mediatrack       = SettingsHelper::get_setting('trackmedia');
        $this->path             = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';

        $this->ajaxrequest      = $ajaxrequest;
        $this->trackparameters  = $trackparameters;

        $this->trackable = $this->istrackable();

        $this->repo = new tracking_repository();
    }

    /**
     * Validate is user trackable.
     *
     * @return bool
     */
    protected function istrackable() {

        if (!$this->enabled) {
            return false;
        }

        if (strpos($this->path, 'cron.php') !== false) {
            return false;
        }

        if (!UserAccessHelper::is_logged_in()) {
            return false;
        }

        if (is_siteadmin() && !$this->trackadmin) {
            return false;
        }

        return true;
    }

    /**
     * Method to create tracking record.
     *
     * @return false|void
     */
    public function track() {
        global $SESSION;

        // Validate tracking enabled.
        if (!$this->trackable) {
            return false;
        }

        // Prepare page params.
        $this->preparepageparams();

        // Save last tracked time.
        $SESSION->local_intellidata_last_tracked_time = time();

        // Create tracking record.
        $this->repo->create_record($this->pageparams, $this->ajaxrequest);

        // Init JS tracking script.
        if (!$this->ajaxrequest) {
            $this->init();
        }
    }

    /**
     * Method to prepare tracking params.
     *
     * @throws \coding_exception
     */
    public function preparepageparams() {

        $pageparams = [];
        if (!empty($this->trackparameters['page']) &&
            !empty($this->trackparameters['param']) &&
            !empty($this->trackparameters['time'])) {

            $pageparams['page'] = $this->trackparameters['page'];
            $pageparams['param'] = $this->trackparameters['param'];
            $pageparams['time'] = $this->trackparameters['time'];
        } else if (!$this->ajaxrequest) {
            $pageparams  = PageParamsHelper::get_params($this->trackparameters);
        } else {
            $pageparams['page'] = (isset($_COOKIE['intellidatapage']))
                ? clean_param($_COOKIE['intellidatapage'], PARAM_ALPHANUMEXT) : '';
            $pageparams['param'] = (isset($_COOKIE['intellidataparam']))
                ? clean_param($_COOKIE['intellidataparam'], PARAM_INT) : 0;
            $pageparams['time'] = (isset($_COOKIE['intellidatatime']))
                ? clean_param($_COOKIE['intellidatatime'], PARAM_INT) : 0;
        }

        $this->pageparams = $pageparams;
    }

    /**
     * Tracking initialization.
     */
    protected function init() {
        global $PAGE;

        $params = new \stdClass();
        $params->inactivity     = $this->inactivity;
        $params->ajaxfrequency  = $this->ajaxfrequency;
        $params->period         = 1000;
        $params->page           = $this->pageparams['page'];
        $params->param          = $this->pageparams['param'];
        $params->mediatrack     = $this->mediatrack;

        $PAGE->requires->js_call_amd('local_intellidata/tracking', 'init', [$params]);
    }
}
