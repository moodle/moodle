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

function xmldb_search_simpledb_install() {
    global $DB;

    switch ($DB->get_dbfamily()) {
        case 'postgres':
            // TODO: There are a few other ways of doing this which avoid the need for individual indicies..
            $DB->execute("CREATE INDEX psql_search_title ON {search_simpledb_index} USING gin(to_tsvector('simple', title))");
            $DB->execute("CREATE INDEX psql_search_content ON {search_simpledb_index} USING gin(to_tsvector('simple', content))");
            $DB->execute("CREATE INDEX psql_search_description1 ON {search_simpledb_index} USING gin(to_tsvector('simple', description1))");
            $DB->execute("CREATE INDEX psql_search_description2 ON {search_simpledb_index} USING gin(to_tsvector('simple', description2))");
            break;
        case 'mysql':
            $DB->execute("CREATE FULLTEXT INDEX mysql_search_index
                          ON {search_simpledb_index} (title, content, description1, description2)");
            break;
        case 'mssql':
            //TODO: workout if fulltext search is installed... select SERVERPROPERTY('IsFullTextInstalled')
            $DB->execute("CREATE FULLTEXT CATALOG {search_simpledb_catalog}");
            $DB->execute("CREATE FULLTEXT INDEX ON {search_simpledb_index} (title, content, description1, description2)
                          KEY INDEX {searsimpinde_id_pk} ON {search_simpledb_catalog}");
            break;
    }
}

