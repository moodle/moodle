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
 * Main class for course listing
 *
 * @package    block_iomad_company_admin
 * @copyright  2018 Howard Miller <howardsmiller@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\output;

defined('MOODLE_INTERNAL') || die;

use renderable;
use renderer_base;
use templatable;

/**
 * Class contains data for course_overview
 *
 * @copyright  2017 Howard Miller <howardsmiller@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminblock implements renderable, templatable {

    protected $logourl;
 
    protected $companyselect;

    protected $tabhtml;

    protected $menu;

    public function __construct($logourl, $companyselect, $tabhtml, $menu) {
        $this->logourl = $logourl;
        $this->companyselect = $companyselect;
        $this->tabhtml = $tabhtml;
        $this->menu = $menu;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        return [
            'logourl' => $this->logourl,
            'companyselect' => $this->companyselect,
            'tabs' => $this->tabhtml,
            'menu' => $this->menu,
        ];
    }

}
