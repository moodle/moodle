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
 * @package   mod-wiki
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function wiki_add_wiki_fields() {
    global $DB;

    upgrade_set_timeout();
    $dbman = $DB->get_manager();
    /// Define table wiki to be created
    $table = new xmldb_table('wiki');

    // Adding fields to wiki table
    $wikitable = new xmldb_table('wiki');

    // in MOODLE_20_SABLE branch, summary field is renamed as intro
    // so we renamed it back to summary to keep upgrade going as moodle 1.9
    $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null);
    if ($dbman->field_exists($wikitable, $field)) {
        $dbman->rename_field($wikitable, $field, 'summary');
    }
    $dbman->add_field($wikitable, $field);

    $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
    if (!$dbman->field_exists($wikitable, $field)) {
        $dbman->add_field($wikitable, $field);
    }

    $field = new xmldb_field('firstpagetitle', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'First Page', null);
    $dbman->add_field($wikitable, $field);

    $field = new xmldb_field('wikimode', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'collaborative', null);
    $dbman->add_field($wikitable, $field);

    $field = new xmldb_field('defaultformat', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'creole', null);
    $dbman->add_field($wikitable, $field);

    $field = new xmldb_field('forceformat', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', null);
    $dbman->add_field($wikitable, $field);

    $field = new xmldb_field('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
    $dbman->add_field($wikitable, $field);

    $field = new xmldb_field('editbegin', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
    $dbman->add_field($wikitable, $field);

    $field = new xmldb_field('editend', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', null);
    $dbman->add_field($wikitable, $field);

}

/**
 * Install wiki 2.0 tables
 */
function wiki_upgrade_install_20_tables() {
    global $DB;
    upgrade_set_timeout();
    $dbman = $DB->get_manager();

    /// Define table wiki_subwikis to be created
    $table = new xmldb_table('wiki_subwikis');

    /// Adding fields to table wiki_subwikis
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('wikiid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('groupid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table wiki_subwikis
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('wikiidgroupiduserid', XMLDB_KEY_UNIQUE, array('wikiid', 'groupid', 'userid'));
    $table->add_key('wikifk', XMLDB_KEY_FOREIGN, array('wikiid'), 'wiki', array('id'));

    /// Conditionally launch create table for wiki_subwikis
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    /// Define table wiki_pages to be created
    $table = new xmldb_table('wiki_pages');

    /// Adding fields to table wiki_pages
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('subwikiid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'title');
    $table->add_field('cachedcontent', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('timerendered', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('pageviews', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('readonly', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table wiki_pages
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('subwikititleuser', XMLDB_KEY_UNIQUE, array('subwikiid', 'title', 'userid'));
    $table->add_key('subwikifk', XMLDB_KEY_FOREIGN, array('subwikiid'), 'wiki_subwiki', array('id'));

    /// Conditionally launch create table for wiki_pages
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    /// Define table wiki_versions to be created
    $table = new xmldb_table('wiki_versions');

    /// Adding fields to table wiki_versions
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('pageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('content', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
    $table->add_field('contentformat', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'creole');
    $table->add_field('version', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table wiki_versions
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('pagefk', XMLDB_KEY_FOREIGN, array('pageid'), 'wiki_pages', array('id'));

    /// Conditionally launch create table for wiki_versions
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    /// Define table wiki_synonyms to be created
    $table = new xmldb_table('wiki_synonyms');

    /// Adding fields to table wiki_synonyms
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('subwikiid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('pageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('pagesynonym', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'Pagesynonym');

    /// Adding keys to table wiki_synonyms
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('pageidsyn', XMLDB_KEY_UNIQUE, array('pageid', 'pagesynonym'));

    /// Conditionally launch create table for wiki_synonyms
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    /// Define table wiki_links to be created
    $table = new xmldb_table('wiki_links');

    /// Adding fields to table wiki_links
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('subwikiid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('frompageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('topageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('tomissingpage', XMLDB_TYPE_CHAR, '255', null, null, null, null);

    /// Adding keys to table wiki_links
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('frompageidfk', XMLDB_KEY_FOREIGN, array('frompageid'), 'wiki_pages', array('id'));
    $table->add_key('subwikifk', XMLDB_KEY_FOREIGN, array('subwikiid'), 'wiki_subwiki', array('id'));

    /// Conditionally launch create table for wiki_links
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    /// Define table wiki_locks to be created
    $table = new xmldb_table('wiki_locks');

    /// Adding fields to table wiki_locks
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('pageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('sectionname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
    $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('lockedat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table wiki_locks
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for wiki_locks
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Migrating wiki pages history
 */
function wiki_upgrade_migrate_versions() {
    global $DB, $CFG, $OUTPUT;
    require_once($CFG->dirroot . '/mod/wiki/db/migration/lib.php');
    // need to move the binary data in db
    $fs = get_file_storage();
    // select all wiki pages history
    $sql = "SELECT po.id AS oldpage_id, po.pagename AS oldpage_pagename, po.version, po.flags,
                   po.content, po.author, po.userid AS oldpage_userid, po.created, po.lastmodified, po.refs, po.meta, po.hits, po.wiki,
                   p.id AS newpage_id, p.subwikiid, p.title, p.cachedcontent, p.timecreated, p.timemodified AS newpage_timemodified,
                   p.timerendered, p.userid AS newpage_userid, p.pageviews, p.readonly, e.id AS entry_id, e.wikiid, e.course AS entrycourse,
                   e.groupid, e.userid AS entry_userid, e.pagename AS entry_pagename, e.timemodified AS entry_timemodified,
                   w.id AS wiki_id, w.course AS wiki_course, w.name, w.summary AS summary, w.pagename AS wiki_pagename, w.wtype,
                   w.ewikiprinttitle, w.htmlmode, w.ewikiacceptbinary, w.disablecamelcase, w.setpageflags, w.strippages, w.removepages,
                   w.revertchanges, w.initialcontent, w.timemodified AS wiki_timemodified,
                   cm.id AS cmid
              FROM {wiki_pages_old} po
              LEFT OUTER JOIN {wiki_entries_old} e ON e.id = po.wiki
              LEFT OUTER JOIN {wiki} w ON w.id = e.wikiid
              LEFT OUTER JOIN {wiki_subwikis} s ON e.groupid = s.groupid AND e.wikiid = s.wikiid AND e.userid = s.userid
              LEFT OUTER JOIN {wiki_pages} p ON po.pagename = p.title AND p.subwikiid = s.id
              JOIN {modules} m ON m.name = 'wiki'
              JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = w.id)";

    $pagesinfo = $DB->get_recordset_sql($sql, array());

    foreach ($pagesinfo as $pageinfo) {
        upgrade_set_timeout();

        $mimetype = ewiki_mime_magic($pageinfo->content);
        if (!empty($mimetype)) {
            // if mimetype is not empty, means this is a file stored in db
            $context = get_context_instance(CONTEXT_MODULE, $pageinfo->cmid);
            // clean up file name
            $filename = clean_param($pageinfo->oldpage_pagename, PARAM_FILE);
            $filerecord = array('contextid' => $context->id,
                                'component' => 'mod_wiki',
                                'filearea'  => 'attachments',
                                'itemid'    => $pageinfo->subwikiid,
                                'filepath'  => '/',
                                'filename'  => $filename,
                                'userid'    => $pageinfo->oldpage_userid);
            if (!$fs->file_exists($context->id, 'mod_wiki', 'attachments', $pageinfo->subwikiid, '/', $pageinfo->pagename)) {
                $storedfile = $fs->create_file_from_string($filerecord, $pageinfo->content);
            }

            // replace page content to a link point to the file
            $pageinfo->content = "<a href='@@PLUGINFILE@@/$filename'>$pageinfo->oldpage_pagename</a>";
        }

        $oldpage = new StdClass();
        $oldpage->id = $pageinfo->oldpage_id;
        $oldpage->pagename = $pageinfo->oldpage_pagename;
        $oldpage->version = $pageinfo->version;
        $oldpage->flags = $pageinfo->flags;
        $oldpage->content = $pageinfo->content;
        $oldpage->author = $pageinfo->author;
        $oldpage->userid = $pageinfo->oldpage_userid;
        $oldpage->created = $pageinfo->created;
        $oldpage->lastmodified = $pageinfo->lastmodified;
        $oldpage->refs = $pageinfo->refs;
        $oldpage->meta = $pageinfo->meta;
        $oldpage->hits = $pageinfo->hits;
        $oldpage->wiki = $pageinfo->wiki;

        $page = new StdClass();
        $page->id = $pageinfo->newpage_id;
        $page->subwikiid = $pageinfo->subwikiid;
        $page->title = $pageinfo->title;
        $page->cachedcontent = $pageinfo->cachedcontent;
        $page->timecreated = $pageinfo->timecreated;
        $page->timemodified = $pageinfo->newpage_timemodified;
        $page->timerendered = $pageinfo->timerendered;
        $page->userid = $pageinfo->newpage_userid;
        $page->pageviews = $pageinfo->pageviews;
        $page->readonly = $pageinfo->readonly;

        $entry = new StdClass();
        $entry->id = $pageinfo->entry_id;
        $entry->wikiid = $pageinfo->wikiid;
        $entry->course = $pageinfo->entrycourse;
        $entry->groupid = $pageinfo->groupid;
        $entry->userid = $pageinfo->entry_userid;
        $entry->pagename = $pageinfo->entry_pagename;
        $entry->timemodified = $pageinfo->entry_timemodified;

        $wiki = new StdClass();
        $wiki->id = $pageinfo->wiki_id;
        $wiki->course = $pageinfo->wiki_course;
        $wiki->name = $pageinfo->name;
        $wiki->summary = $pageinfo->summary;
        $wiki->pagename = $pageinfo->wiki_pagename;
        $wiki->wtype = $pageinfo->wtype;
        $wiki->ewikiprinttitle = $pageinfo->ewikiprinttitle;
        $wiki->htmlmode = $pageinfo->htmlmode;
        $wiki->ewikiacceptbinary = $pageinfo->ewikiacceptbinary;
        $wiki->disablecamelcase = $pageinfo->disablecamelcase;
        $wiki->setpageflags = $pageinfo->setpageflags;
        $wiki->strippages = $pageinfo->strippages;
        $wiki->removepages = $pageinfo->removepages;
        $wiki->revertchanges = $pageinfo->revertchanges;
        $wiki->initialcontent = $pageinfo->initialcontent;
        $wiki->timemodified = $pageinfo->wiki_timemodified;

        $version = new StdClass();
        $version->pageid = $page->id;
        // convert wiki content to html format
        $version->content = wiki_ewiki_2_html($entry, $oldpage, $wiki);
        $version->contentformat = 'html';
        $version->version = $oldpage->version;
        $version->timecreated = $oldpage->lastmodified;
        $version->userid = $oldpage->userid;
        if ($version->version == 1) {
            // The oldest version of page in moodle 2.0 is 0 which has empty content
            // so we need to insert an extra record
            try {
                $content = $version->content;
                $version->version = 0;
                $version->content = '';
                $DB->insert_record('wiki_versions', $version);
                $version->version = 1;
                $version->content = $content;
                $DB->insert_record('wiki_versions', $version);
            } catch (dml_exception $e) {
                debugging($e->getMessage());
            }
        } else {
            try {
                $DB->insert_record('wiki_versions', $version);
            } catch (dml_exception $e) {
                debugging($e->getMessage());
            }
        }
    }

    $pagesinfo->close();
}
