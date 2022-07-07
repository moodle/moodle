<?php
// This file keeps track of upgrades to
// the glossary module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_glossary_upgrade($oldversion) {
    global $DB;

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018051401) {

        // Fetch the module ID for the glossary module.
        $glossarymoduleid = $DB->get_field('modules', 'id', ['name' => 'glossary']);

        // Get id of section 1 on the frontpage course.
        $fpsection1 = $DB->get_field('course_sections', 'id', ['course' => SITEID, 'section' => 1]);

        // Fetch sections for the frontpage not matching 1.
        $sectionselect = 'course = :course AND section <> 1';
        $sitesections = $DB->get_recordset_select('course_sections', $sectionselect, ['course' => SITEID]);
        $newsection1cmids = [];
        foreach ($sitesections as $section) {
            // Check if we have anything to process for this section.
            if (empty($section->sequence)) {
                // If there's none, ignore.
                continue;
            }
            // Fetch the course module IDs of the course modules in the section.
            $cmids = explode(',', $section->sequence);
            $nonglossarycmids = [];
            // Update the section in the course_modules table for glossary instances if necessary.
            foreach ($cmids as $cmid) {
                $params = [
                    'id' => $cmid,
                    'module' => $glossarymoduleid
                ];
                // Check if the record in the course_modules tables is that of a glossary activity.
                if ($DB->record_exists('course_modules', $params)) {
                    // If so, move it to front page's section 1.
                    $DB->set_field('course_modules', 'section', $fpsection1, $params);
                    $newsection1cmids[] = $cmid;
                } else {
                    // Otherwise, ignore this course module as we only want to update glossary items.
                    $nonglossarycmids[] = $cmid;
                }
            }
            // Check if we need to update the section record or we can delete it.
            if (!empty($nonglossarycmids)) {
                // Update the section record with a sequence that now excludes the glossary instance(s) (if it changed).
                $sequence = implode(',', $nonglossarycmids);
                if ($sequence != $section->sequence) {
                    $DB->set_field('course_sections', 'sequence', $sequence, ['id' => $section->id]);
                }
            } else {
                // This section doesn't contain any items anymore, we can remove this.
                $DB->delete_records('course_sections', ['id' => $section->id]);
            }
        }
        $sitesections->close();

        // Update the sequence field for the site's section 1 if necessary.
        if (!empty($newsection1cmids)) {
            $section1params = [
                'course' => SITEID,
                'section' => 1
            ];
            $section1sequence = $DB->get_field('course_sections', 'sequence', $section1params);
            $newsection1sequence = implode(',', array_merge([$section1sequence], $newsection1cmids));
            // Update the sequence field of the first section for the front page.
            $DB->set_field('course_sections', 'sequence', $newsection1sequence, $section1params);
        }

        upgrade_mod_savepoint(true, 2018051401, 'glossary');
    }

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.10.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
