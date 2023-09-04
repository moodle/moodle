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
 * Auth IOMAD SAML2 metadata refresh scheduled task.
 *
 * @package    auth_iomadsaml2
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_iomadsaml2\task;

use auth_iomadsaml2\admin\setting_idpmetadata;
use auth_iomadsaml2\idp_parser;
use auth_iomadsaml2\metadata_fetcher;
use auth_iomadsaml2\metadata_parser;
use auth_iomadsaml2\metadata_writer;
use moodle_exception;

/**
 * Auth IOMAD SAML2 metadata refresh scheduled task.
 *
 * @package    auth_iomadsaml2
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metadata_refresh extends \core\task\scheduled_task {
    /**
     * @var metadata_fetcher
     */
    private $fetcher;

    /**
     * @var metadata_parser
     */
    private $parser;

    /**
     * @var metadata_writer
     */
    private $writer;

    /**
     * @var idp_parser
     */
    private $idpparser;

    /**
     * @var setting_idpmetadata
     */
    private $idpmetadata;

    /**
     * Get name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskmetadatarefresh', 'auth_iomadsaml2');
    }

    /**
     * Execute refresh of metadata.
     *
     * @param bool $force
     */
    public function execute($force = false) {
        $config = get_config('auth_iomadsaml2');

        if (empty($config->idpmetadata)) {
            mtrace('IdP metadata not configured.');
            return false;
        }

        if (!$force && empty($config->idpmetadatarefresh)) {
            $str = 'IdP metadata refresh is not configured. Enable it in the auth settings or disable this scheduled task';
            mtrace($str);
            return false;
        }

        if (!$this->idpparser instanceof idp_parser) {
            $this->idpparser = new idp_parser();
        }

        if ($this->idpparser->check_xml($config->idpmetadata) == true) {
            mtrace('IdP metadata config not a URL, nothing to refresh.');
            return false;
        }

        if (!$this->idpmetadata instanceof setting_idpmetadata) {
            $this->idpmetadata = new setting_idpmetadata();
        }

        $this->idpmetadata->validate($config->idpmetadata);

        mtrace('IdP metadata refresh completed successfully.');
        return true;
    }

    /**
     * Set fetcher.
     *
     * @param metadata_fetcher $fetcher
     */
    public function set_fetcher(metadata_fetcher $fetcher) {
        $this->fetcher = $fetcher;
    }

    /**
     * Set parser.
     *
     * @param metadata_parser $parser
     */
    public function set_parser(metadata_parser $parser) {
        $this->parser = $parser;
    }

    /**
     * Set writer.
     *
     * @param metadata_writer $writer
     */
    public function set_writer(metadata_writer $writer) {
        $this->writer = $writer;
    }

    /**
     * Set idp metadata.
     *
     * @param setting_idpmetadata $idpmetadata
     */
    public function set_idpmetadata(setting_idpmetadata $idpmetadata) {
        $this->idpmetadata = $idpmetadata;
    }
}
