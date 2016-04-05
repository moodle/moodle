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
 * Class for exporting user_evidence data.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency\external;

use moodle_url;
use renderer_base;

/**
 * Class for exporting user_evidence data.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_evidence_exporter extends persistent_exporter {

    protected static function define_class() {
        return 'core_competency\\user_evidence';
    }

    protected static function define_other_properties() {
        return array(
            'canmanage' => array(
                'type' => PARAM_BOOL
            ),
            'competencycount' => array(
                'type' => PARAM_INT
            ),
            'competencies' => array(
                'type' => competency_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'filecount' => array(
                'type' => PARAM_INT
            ),
            'files' => array(
                'type' => stored_file_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'hasurlorfiles' => array(
                'type' => PARAM_BOOL
            ),
            'urlshort' => array(
                'type' => PARAM_TEXT
            ),
        );
    }

    protected static function define_related() {
        return array(
            'context' => 'context',
            'competencies' => 'core_competency\\competency[]'
        );
    }

    protected function get_other_values(renderer_base $output) {
        $contextcache = array();

        $competencies = array();
        foreach ($this->related['competencies'] as $competency) {
            if (!isset($contextcache[$competency->get_competencyframeworkid()])) {
                $contextcache[$competency->get_competencyframeworkid()] = $competency->get_context();
            }
            $context = $contextcache[$competency->get_competencyframeworkid()];

            $compexporter = new competency_exporter($competency, array('context' => $context));
            $competencies[] = $compexporter->export($output);
        }

        $urlshort = '';
        $url = $this->persistent->get_url();
        if (!empty($url)) {
            $murl = new moodle_url($url);
            $shorturl = preg_replace('@^https?://(www\.)?@', '', $murl->out(false));
            $urlshort = shorten_text($shorturl, 30, true);
        }

        $files = array();
        $storedfiles = $this->persistent->get_files();
        if (!empty($storedfiles)) {
            foreach ($storedfiles as $storedfile) {
                $fileexporter = new stored_file_exporter($storedfile, array('context' => $this->related['context']));
                $files[] = $fileexporter->export($output);
            }
        }

        $values = array(
            'canmanage' => $this->persistent->can_manage(),
            'competencycount' => count($competencies),
            'competencies' => $competencies,
            'filecount' => count($files),
            'files' => $files,
            'hasurlorfiles' => !empty($files) || !empty($url),
            'urlshort' => $urlshort
        );

        return $values;
    }

}
