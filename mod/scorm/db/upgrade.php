<?php  //$Id$

// This file keeps track of upgrades to 
// the scorm module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_scorm_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2006103100) {
        /// Create the new sco optionals data table

        /// Define table scorm_scoes_data to be created
        $table = new XMLDBTable('scorm_scoes_data');

        /// Adding fields to table scorm_scoes_data
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('scoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('value', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);

        /// Adding keys to table scorm_scoes_data
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        /// Adding indexes to table scorm_scoes_data
        $table->addIndexInfo('scoid', XMLDB_INDEX_NOTUNIQUE, array('scoid'));

        /// Launch create table for scorm_scoes_data
        $result = $result && create_table($table);

        /// The old fields used in scorm_scoes
        $fields = array('parameters' => '',
                        'prerequisites' => '',
                        'maxtimeallowed' => '',
                        'timelimitaction' => '',
                        'datafromlms' => '',
                        'masteryscore' => '',
                        'next' => '0',
                        'previous' => '0');

        /// Retrieve old datas
        if ($scorms = get_records('scorm')) {
            foreach ($scorms as $scorm) {
                if ($olddatas = get_records('scorm_scoes','scorm', $scorm->id)) {
                    foreach ($olddatas as $olddata) {
                        $newdata = new stdClass();
                        $newdata->scoid = $olddata->id;
                        foreach ($fields as $field => $value) {
                            if ($olddata->$field != $value) {
                                $newdata->name = addslashes($field);
                                $newdata->value = addslashes($olddata->$field);
                                $id = insert_record('scorm_scoes_data', $newdata);
                                $result = $result && ($id != 0);
                            }
                        }
                    }
                }
            }
        }

        /// Remove no more used fields
        $table = new XMLDBTable('scorm_scoes');

        foreach ($fields as $field => $value) {
            $field = new XMLDBField($field);
            $result = $result && drop_field($table, $field);
        }
    }

    if ($result && $oldversion < 2006120900) {
    /// Define table scorm_seq_objective to be created
        $table = new XMLDBTable('scorm_seq_objective');

    /// Adding fields to table scorm_seq_objective
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('scoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('primaryobj', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('objectiveid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('satisfiedbymeasure', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '1');
        $table->addFieldInfo('minnormalizedmeasure', XMLDB_TYPE_FLOAT, '11, 4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0.0000');

    /// Adding keys to table scorm_seq_objective
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('scorm_objective_uniq', XMLDB_KEY_UNIQUE, array('scoid', 'id'));
        $table->addKeyInfo('scorm_objective_scoid', XMLDB_KEY_FOREIGN, array('scoid'), 'scorm_scoes', array('id'));

    /// Launch create table for scorm_seq_objective
        $result = $result && create_table($table);

    /// Define table scorm_seq_mapinfo to be created
        $table = new XMLDBTable('scorm_seq_mapinfo');

    /// Adding fields to table scorm_seq_mapinfo
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('scoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('objectiveid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('targetobjectiveid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('readsatisfiedstatus', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '1');
        $table->addFieldInfo('readnormalizedmeasure', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '1');
        $table->addFieldInfo('writesatisfiedstatus', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('writenormalizedmeasure', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '0');

    /// Adding keys to table scorm_seq_mapinfo
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('scorm_mapinfo_uniq', XMLDB_KEY_UNIQUE, array('scoid', 'id', 'objectiveid'));
        $table->addKeyInfo('scorm_mapinfo_scoid', XMLDB_KEY_FOREIGN, array('scoid'), 'scorm_scoes', array('id'));
        $table->addKeyInfo('scorm_mapinfo_objectiveid', XMLDB_KEY_FOREIGN, array('objectiveid'), 'scorm_seq_objective', array('id'));

    /// Launch create table for scorm_seq_mapinfo
        $result = $result && create_table($table);

    /// Define table scorm_seq_ruleconds to be created
        $table = new XMLDBTable('scorm_seq_ruleconds');

    /// Adding fields to table scorm_seq_ruleconds
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('scoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('conditioncombination', XMLDB_TYPE_CHAR, '3', null, XMLDB_NOTNULL, null, null, null, 'all');
        $table->addFieldInfo('ruletype', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('action', XMLDB_TYPE_CHAR, '25', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table scorm_seq_ruleconds
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('scorm_ruleconds_un', XMLDB_KEY_UNIQUE, array('scoid', 'id'));
        $table->addKeyInfo('scorm_ruleconds_scoid', XMLDB_KEY_FOREIGN, array('scoid'), 'scorm_scoes', array('id'));

    /// Launch create table for scorm_seq_ruleconds
        $result = $result && create_table($table);

   /// Define table scorm_seq_rulecond to be created
        $table = new XMLDBTable('scorm_seq_rulecond');

    /// Adding fields to table scorm_seq_rulecond
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('scoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('ruleconditionsid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('refrencedobjective', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('measurethreshold', XMLDB_TYPE_FLOAT, '11, 4', null, XMLDB_NOTNULL, null, null, null, '0.0000');
        $table->addFieldInfo('operator', XMLDB_TYPE_CHAR, '5', null, XMLDB_NOTNULL, null, null, null, 'noOp');
        $table->addFieldInfo('cond', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, 'always');

    /// Adding keys to table scorm_seq_rulecond
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('scorm_rulecond_uniq', XMLDB_KEY_UNIQUE, array('id', 'scoid', 'ruleconditionsid'));
        $table->addKeyInfo('scorm_rulecond_scoid', XMLDB_KEY_FOREIGN, array('scoid'), 'scorm_scoes', array('id'));
        $table->addKeyInfo('scorm_rulecond_ruleconditionsid', XMLDB_KEY_FOREIGN, array('ruleconditionsid'), 'scorm_seq_ruleconds', array('id'));

    /// Launch create table for scorm_seq_rulecond
        $result = $result && create_table($table);

   /// Define table scorm_seq_rolluprule to be created
        $table = new XMLDBTable('scorm_seq_rolluprule');

    /// Adding fields to table scorm_seq_rolluprule
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('scoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('childactivityset', XMLDB_TYPE_CHAR, '15', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('minimumcount', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('minimumpercent', XMLDB_TYPE_FLOAT, '11, 4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0.0000');
        $table->addFieldInfo('conditioncombination', XMLDB_TYPE_CHAR, '3', null, XMLDB_NOTNULL, null, null, null, 'all');
        $table->addFieldInfo('action', XMLDB_TYPE_CHAR, '15', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table scorm_seq_rolluprule
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('scorm_rolluprule_uniq', XMLDB_KEY_UNIQUE, array('scoid', 'id'));
        $table->addKeyInfo('scorm_rolluprule_scoid', XMLDB_KEY_FOREIGN, array('scoid'), 'scorm_scoes', array('id'));

    /// Launch create table for scorm_seq_rolluprule
        $result = $result && create_table($table);

    /// Define table scorm_seq_rolluprulecond to be created
        $table = new XMLDBTable('scorm_seq_rolluprulecond');

    /// Adding fields to table scorm_seq_rolluprulecond
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('scoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('rollupruleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('operator', XMLDB_TYPE_CHAR, '5', null, XMLDB_NOTNULL, null, null, null, 'noOp');
        $table->addFieldInfo('cond', XMLDB_TYPE_CHAR, '25', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table scorm_seq_rolluprulecond
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('scorm_rulluprulecond_uniq', XMLDB_KEY_UNIQUE, array('scoid', 'rollupruleid', 'id'));
        $table->addKeyInfo('scorm_rolluprulecond_scoid', XMLDB_KEY_FOREIGN, array('scoid'), 'scorm_scoes', array('id'));
        $table->addKeyInfo('scorm_rolluprulecond_rolluprule', XMLDB_KEY_FOREIGN, array('rollupruleid'), 'scorm_seq_rolluprule', array('id'));

    /// Launch create table for scorm_seq_rolluprulecond
        $result = $result && create_table($table);
    }
    
    //Adding new field to table scorm
    if ($result && $oldversion < 2007011800) {

    /// Define field format to be added to data_comments
        $table = new XMLDBTable('scorm');
        $field = new XMLDBField('md5_result');
        $field->setAttributes(XMLDB_TYPE_CHAR, '32' , null, null, null, null, null, null, null);

    /// Launch add field format
        $result = $result && add_field($table, $field);

        $field = new XMLDBField('external');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', null);

        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2007012400) {

    /// Rename field external on table scorm to updatefreq
        $table = new XMLDBTable('scorm');
        $field = new XMLDBField('external');

        if (field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'maxattempt');

         /// Launch rename field updatefreq
            $result = $result && rename_field($table, $field, 'updatefreq');
        } else {
            $field = new XMLDBField('updatefreq');
            $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'maxattempt');

            $result = $result && add_field($table, $field);
        }

    /// Rename field md5_result on table scorm to md5hash
        $field = new XMLDBField('md5_result');
        if (field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, null, null, 'updatefreq');

        /// Launch rename field md5hash
            $result = $result && rename_field($table, $field, 'md5hash');
        } else {
            $field = new XMLDBField('md5hash');
            $field->setAttributes(XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, null, '', 'updatefreq');
            
            $result = $result && add_field($table, $field);
        }
    }

    if ($result && $oldversion < 2007031300) {
        if ($scorms = get_records('scorm')) {
            foreach ($scorms as $scorm) {
                if ($scoes = get_records('scorm_scoes','scorm',$scorm->id)) {
                    foreach ($scoes as $sco) {
                        if ($tracks = get_records('scorm_scoes_track','scoid',$sco->id)) {
                            foreach ($tracks as $track) {
                                $element = preg_replace('/\.N(\d+)\./',".\$1.",$track->element);
                                if ($track->element != $element) {
                                    $track->element = $element;
                                    update_record('scorm_scoes_track',$track);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    if ($result && $oldversion < 2007081001) {
        require_once($CFG->dirroot.'/mod/scorm/lib.php');
        // too much debug output
        $db->debug = false;
        scorm_update_grades();
        $db->debug = true;
    }  

	// Adding missing 'version' field to table scorm
    if ($result && $oldversion < 2007110500) {
        $table = new XMLDBTable('scorm');
        $field = new XMLDBField('version');
        $field->setAttributes(XMLDB_TYPE_CHAR, '9', null, XMLDB_NOTNULL, null, null, null, 'scorm_12', 'summary');

        $result = $result && add_field($table, $field);
    }

   // Adding missing 'whatgrade' field to table scorm
    if ($result && $oldversion < 2007110501) {
        $table = new XMLDBTable('scorm');
        $field = new XMLDBField('whatgrade');
        
        /// Launch add field whatgrade
        if (!field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'grademethod');
            $result = $result && add_field($table, $field);
            /// fix bad usage of whatgrade/grading method.
            $scorms = get_records('scorm');
            foreach ($scorms as $scorm) {
                $scorm->whatgrade = $scorm->grademethod/10;
                update_record('scorm', $scorm);
            }
            set_config('whatgradefixed', '1', 'scorm'); //set this so that when upgrade to Moodle 2.0 we don't do this again.
        }
    }
    if ($result && $oldversion < 2007110503) {
        /// fix bad usage of whatgrade/grading method
        $scorms = get_records('scorm');
        foreach ($scorms as $scorm) {
            $scorm->grademethod = $scorm->grademethod%10;
            update_record('scorm', $scorm);
        }
        set_config('grademethodfixed', '1', 'scorm'); //set this so that when upgrade to Moodle 2.0 we don't do this again.
    }
    
//===== 1.9.0 upgrade line ======//

    return $result;
}

?>
