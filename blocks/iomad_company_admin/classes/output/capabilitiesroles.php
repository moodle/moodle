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
 * Output class for company capabilities role select
 *
 * @package    block_iomad_company_admin
 * @copyright  2019 Howard Miller <howardsmiller@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\output;

defined('MOODLE_INTERNAL') || die;

use renderable;
use renderer_base;
use templatable;

/**
 * Class contains data for company capabilties role select
 *
 * @copyright  2019 Howard Miller <howardsmiller@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class capabilitiesroles implements renderable, templatable {

    protected $roles;

    protected $companyid;

    protected $templateid;

    protected $linkurl;

    protected $saveurl;

    protected $manageurl;

    protected $backurl;
 
    protected $templatesaved;

    /**
     * @param array $roles
     * @param int $companyid
     * @param int $templateid
     */
    public function __construct($roles, $companyid, $templateid, $linkurl, $saveurl, $manageurl, $backurl, $templatesaved) {
        array_walk($roles, function(&$role) use ($linkurl) {
            $linkurl->params(['roleid' => $role->id]);
            $role->link = $linkurl->out();
        });
        $this->roles = $roles;
        $this->companyid = $companyid;
        $this->templateid = $templateid;
        $this->linkurl = $linkurl;
        $this->saveurl = $saveurl;
        $this->manageurl = $manageurl;
        $this->backurl = $backurl;
        $this->templatesaved = $templatesaved;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $DB;

        // Get company info for heading.
        if (empty($this->templateid)) {
            $company = $DB->get_record('company', ['id' => $this->companyid], '*', MUST_EXIST);
            $title = $company->name;
        } else {
            $template = $DB->get_record('company_role_templates', ['id' => $this->templateid], '*', MUST_EXIST);
            $title = get_string('roletemplate', 'block_iomad_company_admin') . ' ' . $template->name;
        }

        return [
            'title' => $title,
            'roles' => array_values($this->roles),
            'companyid' => $this->companyid,
            'templateid' => $this->templateid,
            'saveurl' => $this->saveurl,
            'manageurl' => $this->manageurl,
            'backurl' => $this->backurl,
            'templatesaved' => $this->templatesaved,
        ];
    }

}
