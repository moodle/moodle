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
 * @package moodlecore
 * @subpackage backup-helper
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class used to decode links back to their original form
 *
 * This class allows each restore task to specify the changes that
 * will be applied to any encoded (by backup) link to revert it back
 * to its original form, recoding any parameter as needed.
 *
 * TODO: Complete phpdocs
 */
class restore_decode_rule {

    protected $linkname;    // How the link has been encoded in backup (CHOICEVIEWBYID, COURSEVIEWBYID...)
    protected $urltemplate; // How the original URL looks like, with dollar placeholders
    protected $mappings;    // Which backup_ids mappings do we need to apply for replacing the placeholders
    protected $restoreid;   // The unique restoreid we are executing
    protected $sourcewwwroot; // The original wwwroot of the backup file
    protected $targetwwwroot; // The targer wwwroot of the restore operation

    protected $cregexp;     // Calculated regular expresion we'll be looking for matches

    /** @var bool $urlencoded Whether to use urlencode() on the final URL. */
    protected bool $urlencoded;

    /**
     * Constructor
     *
     * @param string $linkname How the link has been encoded in backup (CHOICEVIEWBYID, COURSEVIEWBYID...)
     * @param string $urltemplate How the original URL looks like, with dollar placeholders
     * @param array|string $mappings Which backup_ids mappings do we need to apply for replacing the placeholders
     * @param bool $urlencoded Whether to use urlencode() on the final URL (defaults to false)
     */
    public function __construct(string $linkname, string $urltemplate, $mappings, bool $urlencoded = false) {
        // Validate all the params are ok
        $this->mappings = $this->validate_params($linkname, $urltemplate, $mappings);
        $this->linkname = $linkname;
        $this->urltemplate = $urltemplate;
        $this->restoreid = 0;
        $this->sourcewwwroot = '';
        $this->targetwwwroot = ''; // yes, uses to be $CFG->wwwroot, and? ;-)
        $this->urlencoded = $urlencoded;
        $this->cregexp = $this->get_calculated_regexp();
    }

    public function set_restoreid($restoreid) {
        $this->restoreid = $restoreid;
    }

    public function set_wwwroots($sourcewwwroot, $targetwwwroot) {
        $this->sourcewwwroot = $sourcewwwroot;
        $this->targetwwwroot = $targetwwwroot;
    }

    public function decode($content) {
        if (preg_match_all($this->cregexp, $content, $matches) === 0) { // 0 matches, nothing to change
            return $content;
        }
        // Have found matches, iterate over them
        foreach ($matches[0] as $key => $tosearch) {
            $mappingsok = true;             // To detect if any mapping has failed
            $placeholdersarr   = array();   // The placeholders to be replaced
            $mappingssourcearr = array();   // To store the original mappings values
            $mappingstargetarr = array();   // To store the target mappings values
            $toreplace = $this->urltemplate;// The template used to build the replacement
            foreach ($this->mappings as $mappingkey => $mappingsource) {
                $source = $matches[$mappingkey][$key];          // get source
                $mappingssourcearr[$mappingkey] = $source;      // set source arr
                $mappingstargetarr[$mappingkey] = 0;            // apply default mapping
                $placeholdersarr[$mappingkey] = '$'.$mappingkey;// set the placeholders arr
                if (!$mappingsok) {                             // already missing some mapping, continue
                    continue;
                }
                if (!$target = $this->get_mapping($mappingsource, $source)) {// mapping not found, mark and continue
                    $mappingsok = false;
                    continue;
                }
                $mappingstargetarr[$mappingkey] = $target;       // store found mapping
            }
            $toreplace = $this->apply_modifications($toreplace, $mappingsok); // Apply other changes before replacement
            if (!$mappingsok) { // Some mapping has failed, apply original values to the template
                $toreplace = str_replace($placeholdersarr, $mappingssourcearr, $toreplace);

            } else {            // All mappings found, apply target values to the template
                $toreplace = str_replace($placeholdersarr, $mappingstargetarr, $toreplace);
            }
            if ($this->urlencoded) {
                $toreplace = urlencode($toreplace);
            }
            // Finally, perform the replacement in original content
            $content = str_replace($tosearch, $toreplace, $content);
        }
        return $content; // return the decoded content, pointing to original or target values
    }

// Protected API starts here

    /**
     * Looks for mapping values in backup_ids table, simple wrapper over get_backup_ids_record
     */
    protected function get_mapping($itemname, $itemid) {
        // Check restoreid is set
        if (!$this->restoreid) {
            throw new restore_decode_rule_exception('decode_rule_restoreid_not_set');
        }
        if (!$found = restore_dbops::get_backup_ids_record($this->restoreid, $itemname, $itemid)) {
            return false;
        }
        return $found->newitemid;
    }

    /**
     * Apply other modifications, based in the result of $mappingsok before placeholder replacements
     *
     * Right now, simply prefix with the proper wwwroot (source/target)
     */
    protected function apply_modifications($toreplace, $mappingsok) {
        // Check wwwroots are set
        if (!$this->targetwwwroot || !$this->sourcewwwroot) {
            throw new restore_decode_rule_exception('decode_rule_wwwroots_not_set');
        }
        return ($mappingsok ? $this->targetwwwroot : $this->sourcewwwroot) . $toreplace;
    }

    /**
     * Perform all the validations and checks on the rule attributes
     */
    protected function validate_params($linkname, $urltemplate, $mappings) {
        // Check linkname is A-Z0-9
        if (empty($linkname) || preg_match('/[^A-Z0-9]/', $linkname)) {
            throw new restore_decode_rule_exception('decode_rule_incorrect_name', $linkname);
        }
        // Look urltemplate starts by /
        if (empty($urltemplate) || substr($urltemplate, 0, 1) != '/') {
            throw new restore_decode_rule_exception('decode_rule_incorrect_urltemplate', $urltemplate);
        }
        if (!is_array($mappings)) {
            $mappings = array($mappings);
        }
        // Look for placeholders in template
        $countph = preg_match_all('/(\$\d+)/', $urltemplate, $matches);
        $countma = count($mappings);
        // Check mappings number matches placeholders
        if ($countph != $countma) {
            $a = new stdClass();
            $a->placeholders = $countph;
            $a->mappings     = $countma;
            throw new restore_decode_rule_exception('decode_rule_mappings_incorrect_count', $a);
        }
        // Verify they are consecutive (starting on 1)
        $smatches = str_replace('$', '', $matches[1]);
        sort($smatches, SORT_NUMERIC);
        if (reset($smatches) != 1 || end($smatches) != $countma) {
            throw new restore_decode_rule_exception('decode_rule_nonconsecutive_placeholders', implode(', ', $smatches));
        }
        // No dupes in placeholders
        if (count($smatches) != count(array_unique($smatches))) {
            throw new restore_decode_rule_exception('decode_rule_duplicate_placeholders', implode(', ', $smatches));
        }

        // Return one array of placeholders as keys and mappings as values
        return array_combine($smatches, $mappings);
    }

    /**
     * based on rule definition, build the regular expression to execute on decode
     */
    protected function get_calculated_regexp() {
        $regexp = '/\$@' . $this->linkname;
        foreach ($this->mappings as $key => $value) {
            $regexp .= '\*(\d+)';
        }
        $regexp .= '@\$/';
        return $regexp;
    }
}

/*
 * Exception class used by all the @restore_decode_rule stuff
 */
class restore_decode_rule_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        return parent::__construct($errorcode, $a, $debuginfo);
    }
}
