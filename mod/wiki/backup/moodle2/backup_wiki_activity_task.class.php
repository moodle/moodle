<?php

require_once($CFG->dirroot . '/mod/wiki/backup/moodle2/backup_wiki_stepslib.php'); // Because it exists (must)
require_once($CFG->dirroot . '/mod/wiki/backup/moodle2/backup_wiki_settingslib.php'); // Because it exists (optional)

/**
 * wiki backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_wiki_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
        }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Wiki only has one structure step
        $this->add_step(new backup_wiki_activity_structure_step('wiki_structure', 'wiki.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of wikis
        $search = "/(" . $base . "\/mod\/wiki\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@WIKIINDEX*$2@$', $content);

        // Link to wiki view by moduleid
        $search = "/(" . $base . "\/mod\/wiki\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@WIKIVIEWBYID*$2@$', $content);

        // Link to wiki view by pageid
        $search = "/(" . $base . "\/mod\/wiki\/view.php\?pageid\=)([0-9]+)/";
        $content = preg_replace($search, '$@WIKIPAGEBYID*$2@$', $content);

        return $content;
    }
}
