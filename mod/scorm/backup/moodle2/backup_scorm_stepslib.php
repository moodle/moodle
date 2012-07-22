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
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_scorm_activity_task
 */

/**
 * Define the complete scorm structure for backup, with file and id annotations
 */
class backup_scorm_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $scorm = new backup_nested_element('scorm', array('id'), array(
            'name', 'scormtype', 'reference', 'intro',
            'introformat', 'version', 'maxgrade', 'grademethod',
            'whatgrade', 'maxattempt', 'forcecompleted', 'forcenewattempt',
            'lastattemptlock', 'displayattemptstatus', 'displaycoursestructure', 'updatefreq',
            'sha1hash', 'md5hash', 'revision', 'launch',
            'skipview', 'hidebrowse', 'hidetoc', 'hidenav',
            'auto', 'popup', 'options', 'width',
            'height', 'timeopen', 'timeclose', 'timemodified',
            'completionstatusrequired', 'completionscorerequired'));

        $scoes = new backup_nested_element('scoes');

        $sco = new backup_nested_element('sco', array('id'), array(
            'manifest', 'organization', 'parent', 'identifier',
            'launch', 'scormtype', 'title'));

        $scodatas = new backup_nested_element('sco_datas');

        $scodata = new backup_nested_element('sco_data', array('id'), array(
            'name', 'value'));

        $seqruleconds = new backup_nested_element('seq_ruleconds');

        $seqrulecond = new backup_nested_element('seq_rulecond', array('id'), array(
            'conditioncombination', 'ruletype', 'action'));

        $seqrulecondsdatas = new backup_nested_element('seq_rulecond_datas');

        $seqrulecondsdata = new backup_nested_element('seq_rulecond_data', array('id'), array(
            'refrencedobjective', 'measurethreshold', 'operator', 'cond'));

        $seqrolluprules = new backup_nested_element('seq_rolluprules');

        $seqrolluprule = new backup_nested_element('seq_rolluprule', array('id'), array(
            'childactivityset', 'minimumcount', 'minimumpercent', 'conditioncombination',
            'action'));

        $seqrollupruleconds = new backup_nested_element('seq_rollupruleconds');

        $seqrolluprulecond = new backup_nested_element('seq_rolluprulecond', array('id'), array(
            'cond', 'operator'));

        $seqobjectives = new backup_nested_element('seq_objectives');

        $seqobjective = new backup_nested_element('seq_objective', array('id'), array(
            'primaryobj', 'objectiveid', 'satisfiedbymeasure', 'minnormalizedmeasure'));

        $seqmapinfos = new backup_nested_element('seq_mapinfos');

        $seqmapinfo = new backup_nested_element('seq_mapinfo', array('id'), array(
            'targetobjectiveid', 'readsatisfiedstatus', 'readnormalizedmeasure', 'writesatisfiedstatus',
            'writenormalizedmeasure'));

        $scotracks = new backup_nested_element('sco_tracks');

        $scotrack = new backup_nested_element('sco_track', array('id'), array(
            'userid', 'attempt', 'element', 'value',
            'timemodified'));

        // Build the tree
        $scorm->add_child($scoes);
        $scoes->add_child($sco);

        $sco->add_child($scodatas);
        $scodatas->add_child($scodata);

        $sco->add_child($seqruleconds);
        $seqruleconds->add_child($seqrulecond);

        $seqrulecond->add_child($seqrulecondsdatas);
        $seqrulecondsdatas->add_child($seqrulecondsdata);

        $sco->add_child($seqrolluprules);
        $seqrolluprules->add_child($seqrolluprule);

        $seqrolluprule->add_child($seqrollupruleconds);
        $seqrollupruleconds->add_child($seqrolluprulecond);

        $sco->add_child($seqobjectives);
        $seqobjectives->add_child($seqobjective);

        $seqobjective->add_child($seqmapinfos);
        $seqmapinfos->add_child($seqmapinfo);

        $sco->add_child($scotracks);
        $scotracks->add_child($scotrack);

        // Define sources
        $scorm->set_source_table('scorm', array('id' => backup::VAR_ACTIVITYID));

        // Use set_source_sql for other calls as set_source_table returns records in reverse order
        // and order is important for several SCORM fields - esp scorm_scoes.
        $sco->set_source_sql('
                SELECT *
                FROM {scorm_scoes}
                WHERE scorm = :scorm
                ORDER BY id',
            array('scorm' => backup::VAR_PARENTID));

        $scodata->set_source_sql('
                SELECT *
                FROM {scorm_scoes_data}
                WHERE scoid = :scoid
                ORDER BY id',
            array('scoid' => backup::VAR_PARENTID));

        $seqrulecond->set_source_sql('
                SELECT *
                FROM {scorm_seq_ruleconds}
                WHERE scoid = :scoid
                ORDER BY id',
            array('scoid' => backup::VAR_PARENTID));

        $seqrulecondsdata->set_source_sql('
                SELECT *
                FROM {scorm_seq_rulecond}
                WHERE ruleconditionsid = :ruleconditionsid
                ORDER BY id',
            array('ruleconditionsid' => backup::VAR_PARENTID));

        $seqrolluprule->set_source_sql('
                SELECT *
                FROM {scorm_seq_rolluprule}
                WHERE scoid = :scoid
                ORDER BY id',
            array('scoid' => backup::VAR_PARENTID));

        $seqrolluprulecond->set_source_sql('
                SELECT *
                FROM {scorm_seq_rolluprulecond}
                WHERE rollupruleid = :rollupruleid
                ORDER BY id',
            array('rollupruleid' => backup::VAR_PARENTID));

        $seqobjective->set_source_sql('
                SELECT *
                FROM {scorm_seq_objective}
                WHERE scoid = :scoid
                ORDER BY id',
            array('scoid' => backup::VAR_PARENTID));

        $seqmapinfo->set_source_sql('
                SELECT *
                FROM {scorm_seq_mapinfo}
                WHERE objectiveid = :objectiveid
                ORDER BY id',
            array('objectiveid' => backup::VAR_PARENTID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $scotrack->set_source_sql('
                SELECT *
                FROM {scorm_scoes_track}
                WHERE scoid = :scoid
                ORDER BY id',
                array('scoid' => backup::VAR_PARENTID));
        }

        // Define id annotations
        $scotrack->annotate_ids('user', 'userid');

        // Define file annotations
        $scorm->annotate_files('mod_scorm', 'intro', null); // This file area hasn't itemid
        $scorm->annotate_files('mod_scorm', 'content', null); // This file area hasn't itemid
        $scorm->annotate_files('mod_scorm', 'package', null); // This file area hasn't itemid

        // Return the root element (scorm), wrapped into standard activity structure
        return $this->prepare_activity_structure($scorm);
    }
}
