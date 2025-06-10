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
 * GitHub helper class
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_configurable_reports;

/**
 * Class github
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class github extends \curl {

    /**
     * @var string
     */
    protected string $repo = '';

    /**
     * Set repository
     *
     * @param string $repo
     * @return void
     */
    public function set_repo($repo) {
        $this->repo = $repo;
    }

    /**
     * Set a basic auth header.
     *
     * @param string $username The username to use.
     * @param string $password The password to use.
     */
    public function set_basic_auth(string $username, string $password): bool {
        $value = 'Basic ' . base64_encode($username . ':' . $password);
        $this->setHeader('Authorization:' . $value);

        return true;
    }

    /**
     * Get
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @return bool|string
     */
    public function get($url, $params = [], $options = []) {
        $repolink = 'https://api.github.com/repos/';
        $repolink .= $this->repo;
        $repolink .= $url;

        return parent::get($repolink, $params, $options);
    }

}
