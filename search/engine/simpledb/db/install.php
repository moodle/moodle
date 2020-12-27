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
 * Post installation and migration code.
 *
 * @package    search_simpledb
 * @copyright  2016 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Post installation code.
 *
 * @package    search_simpledb
 * @copyright  2016 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_search_simpledb_install() {
    global $DB;

    switch ($DB->get_dbfamily()) {
        case 'postgres':
            // There are a few other ways of doing this which avoid the need for individual indexes.
            $DB->execute("CREATE INDEX {search_simpledb_title} ON {search_simpledb_index} " .
                "USING gin(to_tsvector('simple', title))");
            $DB->execute("CREATE INDEX {search_simpledb_content} ON {search_simpledb_index} " .
                "USING gin(to_tsvector('simple', content))");
            $DB->execute("CREATE INDEX {search_simpledb_description1} ON {search_simpledb_index} " .
                "USING gin(to_tsvector('simple', description1))");
            $DB->execute("CREATE INDEX {search_simpledb_description2} ON {search_simpledb_index} " .
                "USING gin(to_tsvector('simple', description2))");
            break;
        case 'mysql':
            if ($DB->is_fulltext_search_supported()) {
                $DB->execute("CREATE FULLTEXT INDEX {search_simpledb_index_index}
                              ON {search_simpledb_index} (title, content, description1, description2)");
            }
            break;
        case 'mssql':
            if ($DB->is_fulltext_search_supported()) {

                $catalogname = $DB->get_prefix() . 'search_simpledb_catalog';
                if (!$DB->record_exists_sql('SELECT * FROM sys.fulltext_catalogs WHERE name = ?', array($catalogname))) {
                    $DB->execute("CREATE FULLTEXT CATALOG {search_simpledb_catalog} WITH ACCENT_SENSITIVITY=OFF");
                }

                if (defined('PHPUNIT_UTIL') and PHPUNIT_UTIL) {
                    // We want manual tracking for phpunit because the fulltext index does get auto populated fast enough.
                    $changetracking = 'MANUAL';
                } else {
                    $changetracking = 'AUTO';
                }
                $DB->execute("CREATE FULLTEXT INDEX ON {search_simpledb_index} (title, content, description1, description2)
                              KEY INDEX {searsimpinde_id_pk} ON {search_simpledb_catalog} WITH CHANGE_TRACKING $changetracking");
            }
            break;
    }
}

