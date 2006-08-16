<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// This class generate SQL code to be used against MSSQL
/// It extends XMLDBgenerator so everything can be
/// overriden as needed to generate correct SQL.

class XMLDBmssql extends XMLDBgenerator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    var $number_type = 'DECIMAL';    // Proper type for NUMBER(x) in this DB

    var $unsigned_allowed = false;    // To define in the generator must handle unsigned information
    var $default_for_char = '';      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    var $foreign_keys = false; // Does the generator build foreign keys

    var $primary_index = false;// Does the generator need to build one index for primary keys
    var $unique_index = false;  // Does the generator need to build one index for unique keys
    var $foreign_index = true; // Does the generator need to build one index for foreign keys

    var $sequence_extra_code = false; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = 'IDENTITY(1,1)'; //Particular name for inline sequences in this generator
    var $sequence_only = false; //To avoid to output the rest of the field specs, leaving only the name and the sequence_name variable

    var $enum_inline_code = false; //Does the generator need to add inline code in the column definition

    var $add_table_comments  = false;  // Does the generator need to add code for table comments

    /**
     * Creates one new XMLDBmssql
     */
    function XMLDBmssql() {
        parent::XMLDBgenerator();
        $this->prefix = '';
        $this->reserved_words = $this->getReservedWords();
    }

    /**
     * Given one XMLDB Type, lenght and decimals, returns the DB proper SQL type
     */
    function getTypeSQL ($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {

        switch ($xmldb_type) {
            case XMLDB_TYPE_INTEGER:    // From http://msdn.microsoft.com/library/en-us/tsqlref/ts_da-db_7msw.asp?frame=true
                if (empty($xmldb_length)) {
                    $xmldb_length = 10;
                }
                if ($xmldb_length > 9) {
                    $dbtype = 'BIGINT';
                } else if ($xmldb_length > 4) {
                    $dbtype = 'INTEGER';
                } else {
                    $dbtype = 'SMALLINT';
                }
                break;
            case XMLDB_TYPE_NUMBER:
                $dbtype = $this->number_type;
                if (!empty($xmldb_length)) {
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    }
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_FLOAT:
                $dbtype = 'FLOAT';
                if (!empty($xmldb_length)) {
                    $dbtype .= '(' . $xmldb_length;
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_CHAR:
                $dbtype = 'NVARCHAR';
                if (empty($xmldb_length)) {
                    $xmldb_length='255';
                }
                $dbtype .= '(' . $xmldb_length . ')';
                break;
            case XMLDB_TYPE_TEXT:
                $dbtype = 'NTEXT';
                break;
            case XMLDB_TYPE_BINARY:
                $dbtype = 'IMAGE';
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'DATETIME';
                break;
        }
        return $dbtype;
    }

    /**         
     * Returns the code needed to create one enum for the xmldb_table and xmldb_field passes
     */     
    function getEnumExtraSQL ($xmldb_table, $xmldb_field) {
        
        $sql = 'CONSTRAINT ' . $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'ck');
        $sql.= ' CHECK (' . $this->getEncQuoted($xmldb_field->getName()) . ' IN (' . implode(', ', $xmldb_field->getEnumValues()) . ')),';

        return $sql;
    }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    function getReservedWords() {
    /// This file contains the reserved words for MSSQL databases
    /// from http://msdn.microsoft.com/library/en-us/tsqlref/ts_ra-rz_9oj7.asp
        $reserved_words = array (
            'absolute', 'action', 'ada', 'add', 'admin', 'after', 'aggregate',
            'alias', 'all', 'allocate', 'alter', 'and', 'any', 'are', 'array', 'as',
            'asc', 'assertion', 'at', 'authorization', 'avg', 'backup', 'before',
            'begin', 'between', 'binary', 'bit', 'bit_length', 'blob', 'boolean',
            'both', 'breadth', 'break', 'browse', 'bulk', 'by', 'call', 'cascade',
            'cascaded', 'case', 'cast', 'catalog', 'char', 'character',
            'character_length', 'char_length', 'check', 'checkpoint', 'class',
            'clob', 'close', 'clustered', 'coalesce', 'collate', 'collation',
            'column', 'commit', 'completion', 'compute', 'connect',
            'connection', 'constraint', 'constraints', 'constructor', 'contains',
            'containstable', 'continue', 'convert', 'corresponding', 'count',
            'create', 'cross', 'cube', 'current', 'current_date', 'current_path',
            'current_role', 'current_time', 'current_timestamp', 'current_user',
            'cursor', 'cycle', 'data', 'database', 'date', 'day', 'dbcc', 'deallocate',
            'dec', 'decimal', 'declare', 'default', 'deferrable', 'deferred', 'delete',
            'deny', 'depth', 'deref', 'desc', 'describe', 'descriptor', 'destroy',
            'destructor', 'deterministic', 'diagnostics', 'dictionary', 'disconnect',
            'disk', 'distinct', 'distributed', 'domain', 'double', 'drop', 'dummy',
            'dump', 'dynamic', 'each', 'else', 'end', 'end-exec', 'equals', 'errlvl',
            'escape', 'every', 'except', 'exception', 'exec', 'execute', 'exists',
            'exit', 'external', 'extract', 'false', 'fetch', 'file', 'fillfactor', 'first',
            'float', 'for', 'foreign', 'fortran', 'found', 'free', 'freetext', 'freetexttable',
            'from', 'full', 'function', 'general', 'get', 'global', 'go', 'goto', 'grant',
            'group', 'grouping', 'having', 'holdlock', 'host', 'hour', 'identity',
            'identitycol', 'identity_insert', 'if', 'ignore', 'immediate', 'in',
            'include', 'index', 'indicator', 'initialize', 'initially', 'inner', 'inout',
            'input', 'insensitive', 'insert', 'int', 'integer', 'intersect', 'interval',
            'into', 'is', 'isolation', 'iterate', 'join', 'key', 'kill', 'language', 'large',
            'last', 'lateral', 'leading', 'left', 'less', 'level', 'like', 'limit', 'lineno',
            'load', 'local', 'localtime', 'localtimestamp', 'locator', 'lower', 'map',
            'match', 'max', 'min', 'minute', 'modifies', 'modify', 'module', 'month',
            'names', 'national', 'natural', 'nchar', 'nclob', 'new', 'next', 'no',
            'nocheck', 'nonclustered', 'none', 'not', 'null', 'nullif', 'numeric',
            'object', 'octet_length', 'of', 'off', 'offsets', 'old', 'on', 'only', 'open',
            'opendatasource', 'openquery', 'openrowset', 'openxml', 'operation',
            'option', 'or', 'order', 'ordinality', 'out', 'outer', 'output', 'over',
            'overlaps', 'pad', 'parameter', 'parameters', 'partial', 'pascal', 'path',
            'percent', 'plan', 'position', 'postfix', 'precision', 'prefix', 'preorder',
            'prepare', 'preserve', 'primary', 'print', 'prior', 'privileges', 'proc',
            'procedure', 'public', 'raiserror', 'read', 'reads', 'readtext', 'real',
            'reconfigure', 'recursive', 'ref', 'references', 'referencing', 'relative',
            'replication', 'restore', 'restrict', 'result', 'return', 'returns', 'revoke',
            'right', 'role', 'rollback', 'rollup', 'routine', 'row', 'rowcount', 'rowguidcol',
            'rows', 'rule', 'save', 'savepoint', 'schema', 'scope', 'scroll', 'search',
            'second', 'section', 'select', 'sequence', 'session', 'session_user', 'set',
            'sets', 'setuser', 'shutdown', 'size', 'smallint', 'some', 'space', 'specific',
            'specifictype', 'sql', 'sqlca', 'sqlcode', 'sqlerror', 'sqlexception', 'sqlstate',
            'sqlwarning', 'start', 'state', 'statement', 'static', 'statistics', 'structure',
            'substring', 'sum', 'system_user', 'table', 'temporary', 'terminate', 'textsize',
            'than', 'then', 'time', 'timestamp', 'timezone_hour', 'timezone_minute',
            'to', 'top', 'trailing', 'tran', 'transaction', 'translate', 'translation', 'treat',
            'trigger', 'trim', 'true', 'truncate', 'tsequal', 'under', 'union', 'unique',
            'unknown', 'unnest', 'update', 'updatetext', 'upper', 'usage', 'use', 'user',
            'using', 'value', 'values', 'varchar', 'variable', 'varying', 'view', 'waitfor',
            'when', 'whenever', 'where', 'while', 'with', 'without', 'work', 'write',
            'writetext', 'year', 'zone'
        );  
        return $reserved_words;
    }
}

?>
